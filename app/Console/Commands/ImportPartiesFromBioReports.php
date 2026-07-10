<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\Party;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ImportPartiesFromBioReports extends Command
{
    protected $signature = 'parties:import-bio-reports
                            {--business=5 : business_id to import into}
                            {--user=1 : user_id for created parties}
                            {--json= : JSON file produced by scripts/extract_parties_bio_reports.py}
                            {--chunk=250 : rows per database chunk}
                            {--skip-existing : Skip pcodes that already exist for this business}
                            {--dry-run : Parse JSON and show counts only}';

    protected $description = 'Import party pcode + name from Customer/Supplier BioReport xlsx files (via JSON extract)';

    public function handle(): int
    {
        $businessId = (int) $this->option('business');
        $userId = (int) $this->option('user');
        $jsonPath = $this->option('json') ?: storage_path('app/parties_bio_import_b5.json');
        $chunkSize = max(50, (int) $this->option('chunk'));

        if (! is_file($jsonPath)) {
            $this->error("JSON file not found: {$jsonPath}");
            $this->line('Run: python scripts/extract_parties_bio_reports.py');

            return self::FAILURE;
        }

        if (! Business::query()->whereKey($businessId)->exists()) {
            $this->error("Business id {$businessId} does not exist.");

            return self::FAILURE;
        }

        if (! DB::table('users')->where('id', $userId)->exists()) {
            $this->error("User id {$userId} does not exist.");

            return self::FAILURE;
        }

        $payload = json_decode((string) file_get_contents($jsonPath), true);
        if (! is_array($payload) || ! isset($payload['parties']) || ! is_array($payload['parties'])) {
            $this->error('Invalid JSON payload. Expected { parties: [...] }');

            return self::FAILURE;
        }

        $rows = collect($payload['parties'])
            ->map(function (array $row) {
                return [
                    'pcode' => trim((string) ($row['pcode'] ?? '')),
                    'name' => trim((string) ($row['name'] ?? '')),
                    'source' => (string) ($row['source'] ?? ''),
                ];
            })
            ->filter(fn (array $row) => $row['pcode'] !== '' && $row['name'] !== '')
            ->values();

        $existingPcodes = Party::query()
            ->where('business_id', $businessId)
            ->whereNotNull('pcode')
            ->pluck('pcode')
            ->map(fn ($code) => Str::upper((string) $code))
            ->all();
        $existingLookup = array_fill_keys($existingPcodes, true);

        $toImport = $rows->reject(function (array $row) use ($existingLookup) {
            return isset($existingLookup[Str::upper($row['pcode'])]);
        })->values();

        $skippedExisting = $rows->count() - $toImport->count();

        $this->info("Business {$businessId} | JSON rows: {$rows->count()} | to import: {$toImport->count()} | skipped existing pcode: {$skippedExisting}");

        if (isset($payload['stats'])) {
            $this->line('Extract stats: '.json_encode($payload['stats']));
        }

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN complete. No database writes.');

            return self::SUCCESS;
        }

        if ($toImport->isEmpty()) {
            $this->warn('Nothing to import.');

            return self::SUCCESS;
        }

        $nextCode = $this->initialPartyCoaCodeCursor($businessId);
        $now = now();
        $imported = 0;

        $bar = $this->output->createProgressBar($toImport->count());
        $bar->start();

        try {
            foreach ($toImport->chunk($chunkSize) as $chunk) {
                DB::transaction(function () use ($chunk, $businessId, $userId, $now, &$nextCode, &$imported, $bar) {
                    $coaRows = [];
                    $partyMeta = [];

                    foreach ($chunk as $row) {
                        $name = Str::upper($row['name']);
                        $pcode = $row['pcode'];
                        $code = $this->allocatePartyCoaCode($nextCode);

                        $coaRows[] = [
                            'business_id' => $businessId,
                            'parent_id' => null,
                            'code' => $code,
                            'name' => Str::limit($name, 100, ''),
                            'type' => 'liability',
                            'description' => null,
                            'is_default' => false,
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        $partyMeta[] = [
                            'name' => $name,
                            'pcode' => $pcode,
                            'code' => $code,
                        ];
                    }

                    DB::table('chart_of_accounts')->insert($coaRows);

                    $coaIdsByCode = ChartOfAccount::query()
                        ->where('business_id', $businessId)
                        ->whereIn('code', array_column($partyMeta, 'code'))
                        ->pluck('id', 'code');

                    $partyRows = [];
                    foreach ($partyMeta as $meta) {
                        $coaId = $coaIdsByCode[$meta['code']] ?? null;
                        if (! $coaId) {
                            throw new \RuntimeException('Failed to resolve chart of account for code '.$meta['code']);
                        }

                        $partyRows[] = [
                            'business_id' => $businessId,
                            'chart_of_account_id' => $coaId,
                            'name' => $meta['name'],
                            'pcode' => $meta['pcode'],
                            'address' => null,
                            'phone_no' => null,
                            'whatsapp_no' => null,
                            'cnic' => null,
                            'ntn' => null,
                            'opening_balance' => 0,
                            'opening_date' => null,
                            'opening_type' => null,
                            'user_id' => $userId,
                            'status' => 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    DB::table('parties')->insert($partyRows);
                    $imported += count($partyRows);
                    $bar->advance(count($partyRows));
                });
            }
        } catch (Throwable $e) {
            $bar->finish();
            $this->newLine();
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imported {$imported} parties into business_id={$businessId}.");

        return self::SUCCESS;
    }

    private function initialPartyCoaCodeCursor(int $businessId): int
    {
        $maxCode = ChartOfAccount::query()
            ->where('business_id', $businessId)
            ->where('type', 'liability')
            ->whereNull('parent_id')
            ->pluck('code')
            ->map(fn ($code) => (int) $code)
            ->max();

        return max(2110, ($maxCode ?? 2109) + 1);
    }

    private function allocatePartyCoaCode(int &$cursor): string
    {
        $code = str_pad((string) $cursor, 4, '0', STR_PAD_LEFT);
        $cursor++;

        return $code;
    }
}
