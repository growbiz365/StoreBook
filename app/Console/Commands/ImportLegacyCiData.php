<?php

namespace App\Console\Commands;

use App\Models\Bank;
use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\Party;
use App\Support\LegacyCi\SqlInsertValuesParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ImportLegacyCiData extends Command
{
    protected $signature = 'import:legacy-ci
                            {--path= : Directory containing item_type.sql, items.sql, party.sql, banks.sql (default: project root)}
                            {--business=3 : business_id to import into}
                            {--user=3 : user_id for created records}
                            {--force : Allow import when target tables already have rows for this business}
                            {--dry-run : Parse dumps and run preflight only; no database writes}';

    protected $description = 'Import CodeIgniter phpMyAdmin dumps (item_type, items, party, banks) into Storebook';

    public function handle(): int
    {
        $dir = $this->option('path') ?: base_path();
        $businessId = (int) $this->option('business');
        $userId = (int) $this->option('user');

        foreach (['item_type.sql', 'items.sql', 'party.sql', 'banks.sql'] as $f) {
            if (! is_file($dir.DIRECTORY_SEPARATOR.$f)) {
                $this->error("Missing file: {$dir}/{$f}");

                return self::FAILURE;
            }
        }

        if (! Business::query()->whereKey($businessId)->exists()) {
            $this->error("Business id {$businessId} does not exist.");

            return self::FAILURE;
        }

        if (! DB::table('users')->where('id', $userId)->exists()) {
            $this->error("User id {$userId} does not exist.");

            return self::FAILURE;
        }

        foreach (['1110', '1120'] as $code) {
            if (! ChartOfAccount::where('business_id', $businessId)->where('code', $code)->exists()) {
                $this->error("Chart of accounts missing parent code {$code} for business {$businessId}. Seed COA first.");

                return self::FAILURE;
            }
        }

        if (! $this->option('force')) {
            $conflicts = [];
            if (DB::table('item_types')->where('business_id', $businessId)->exists()) {
                $conflicts[] = 'item_types';
            }
            if (DB::table('general_items')->where('business_id', $businessId)->exists()) {
                $conflicts[] = 'general_items';
            }
            if (DB::table('parties')->where('business_id', $businessId)->exists()) {
                $conflicts[] = 'parties';
            }
            if (DB::table('banks')->where('business_id', $businessId)->exists()) {
                $conflicts[] = 'banks';
            }
            if ($conflicts !== []) {
                $msg = 'Target tables already contain data for this business: '.implode(', ', $conflicts).'. Re-run with --force after backup, or truncate those tables.';
                if ($this->option('dry-run')) {
                    $this->warn($msg.' (ignored for --dry-run)');
                } else {
                    $this->error($msg);

                    return self::FAILURE;
                }
            }
        }

        $this->info("Import into business_id={$businessId}, user_id={$userId} from {$dir}");

        if ($this->option('dry-run')) {
            try {
                return $this->performDryRun($dir, $businessId);
            } catch (Throwable $e) {
                $this->error($e->getMessage());

                return self::FAILURE;
            }
        }

        try {
            DB::transaction(function () use ($dir, $businessId, $userId) {
                $this->importItemTypes($dir, $businessId);
                $this->importGeneralItems($dir, $businessId);
                $this->importParties($dir, $businessId, $userId);
                $this->importBanks($dir, $businessId, $userId);
            });
            // ALTER TABLE implicitly commits in MySQL; must run outside DB::transaction().
            $this->syncItemTypesAutoIncrement();
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());

            return self::FAILURE;
        }

        $this->info('Import completed successfully.');

        return self::SUCCESS;
    }

    private function performDryRun(string $dir, int $businessId): int
    {
        $this->warn('DRY RUN: no database writes.');

        $itemTypePath = $dir.DIRECTORY_SEPARATOR.'item_type.sql';
        $itemTypeRows = SqlInsertValuesParser::parseAllRowsFromFile($itemTypePath, 'item_type');
        $seenTypeNames = [];
        foreach ($itemTypeRows as $index => $row) {
            if (count($row) !== 3) {
                throw new \RuntimeException('item_type row #'.($index + 1).' must have 3 columns, got '.count($row));
            }
            [, $name] = $row;
            $norm = Str::upper(html_entity_decode((string) $name, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if (isset($seenTypeNames[$norm])) {
                throw new \RuntimeException("Duplicate item_type name would violate unique (item_type, business_id): {$norm}");
            }
            $seenTypeNames[$norm] = true;
        }

        $itemsPath = $dir.DIRECTORY_SEPARATOR.'items.sql';
        $itemsRows = SqlInsertValuesParser::parseAllRowsFromFile($itemsPath, 'items');
        $usedCodes = [];
        $itemTypeIds = [];
        foreach ($itemsRows as $index => $row) {
            if (count($row) !== 17) {
                throw new \RuntimeException('items row #'.($index + 1).' must have 17 columns, got '.count($row));
            }
            $legacyItemId = (int) $row[0];
            $typeId = (int) $row[14];
            $itemTypeIds[$typeId] = true;
            $this->uniqueItemCode((string) $row[5], $legacyItemId, $usedCodes);
        }

        $definedTypeIds = [];
        foreach ($itemTypeRows as $r) {
            $definedTypeIds[(int) $r[0]] = true;
        }
        $missingTypes = array_values(array_diff(array_keys($itemTypeIds), array_keys($definedTypeIds)));
        if ($missingTypes !== []) {
            sort($missingTypes);
            $this->warn('Items reference item_type_id values not present in item_type dump: '.implode(', ', array_slice($missingTypes, 0, 20)).(count($missingTypes) > 20 ? ', …' : ''));
        }

        $partyPath = $dir.DIRECTORY_SEPARATOR.'party.sql';
        $partyRows = SqlInsertValuesParser::parseAllRowsFromFile($partyPath, 'party');
        foreach ($partyRows as $index => $row) {
            if (count($row) !== 8) {
                throw new \RuntimeException('party row #'.($index + 1).' must have 8 columns, got '.count($row));
            }
        }

        $banksPath = $dir.DIRECTORY_SEPARATOR.'banks.sql';
        $bankRows = SqlInsertValuesParser::parseAllRowsFromFile($banksPath, 'banks');
        $cashBanks = 0;
        $bankBanks = 0;
        foreach ($bankRows as $index => $row) {
            if (count($row) !== 9) {
                throw new \RuntimeException('banks row #'.($index + 1).' must have 9 columns, got '.count($row));
            }
            [, $legacyBankName] = $row;
            if ($this->legacyBankAccountType((string) $legacyBankName) === 'cash') {
                $cashBanks++;
            } else {
                $bankBanks++;
            }
        }

        $this->table(
            ['File', 'Rows', 'Checks'],
            [
                ['item_type.sql', (string) count($itemTypeRows), count($missingTypes) === 0 ? 'OK' : 'WARN: missing type ids'],
                ['items.sql', (string) count($itemsRows), 'item_code uniqueness simulated OK'],
                ['party.sql', (string) count($partyRows), 'OK'],
                ['banks.sql', (string) count($bankRows), "{$cashBanks} cash, {$bankBanks} bank (by name rule)"],
            ]
        );

        $this->info('Dry run OK: dumps parsed; run without --dry-run to import.');

        return self::SUCCESS;
    }

    private function importItemTypes(string $dir, int $businessId): void
    {
        $path = $dir.DIRECTORY_SEPARATOR.'item_type.sql';
        $rows = SqlInsertValuesParser::parseAllRowsFromFile($path, 'item_type');

        $now = now();
        $batch = [];
        foreach ($rows as $row) {
            if (count($row) !== 3) {
                throw new \RuntimeException('item_type row must have 3 columns');
            }
            [$legacyId, $name, $status] = $row;
            $batch[] = [
                'id' => (int) $legacyId,
                'item_type' => Str::upper(html_entity_decode((string) $name, ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'business_id' => $businessId,
                'status' => (int) $status === 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($batch, 100) as $chunk) {
            DB::table('item_types')->insert($chunk);
        }

        $this->info('Item types: '.count($batch));
    }

    private function syncItemTypesAutoIncrement(): void
    {
        $maxId = (int) DB::table('item_types')->max('id');
        if ($maxId < 1) {
            return;
        }
        DB::statement('ALTER TABLE item_types AUTO_INCREMENT = '.($maxId + 1));
    }

    private function importGeneralItems(string $dir, int $businessId): void
    {
        $path = $dir.DIRECTORY_SEPARATOR.'items.sql';
        $rows = SqlInsertValuesParser::parseAllRowsFromFile($path, 'items');

        $now = now();
        $usedCodes = [];
        $batch = [];

        foreach ($rows as $row) {
            if (count($row) !== 17) {
                throw new \RuntimeException('items row must have 17 columns');
            }
            [
                $itemId,
                ,
                $itemName,
                ,
                ,
                $barcode,
                ,
                $saleNet,
                $purchasePrice,
                ,
                ,
                ,
                ,
                ,
                $itemTypeId,
                $minStock,
                $cartonSize,
            ] = $row;

            $name = html_entity_decode((string) $itemName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $code = $this->uniqueItemCode((string) $barcode, (int) $itemId, $usedCodes);

            $batch[] = [
                'item_name' => $name,
                'item_type_id' => (int) $itemTypeId,
                'item_code' => $code,
                'min_stock_limit' => (int) $minStock,
                'carton_or_pack_size' => $cartonSize === null ? null : (string) $cartonSize,
                'cost_price' => round((float) $purchasePrice, 2),
                'opening_stock' => 0,
                'opening_total' => 0,
                'sale_price' => round((float) $saleNet, 2),
                'business_id' => $businessId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($batch, 250) as $chunk) {
            DB::table('general_items')->insert($chunk);
        }

        $this->info('General items: '.count($batch));
    }

    /**
     * @param  array<string, true>  $usedCodes
     */
    private function uniqueItemCode(string $barcode, int $legacyItemId, array &$usedCodes): string
    {
        $base = trim($barcode) !== '' ? trim($barcode) : 'L'.$legacyItemId;
        $base = Str::upper($base);
        $candidate = $base;
        $n = 0;
        while (isset($usedCodes[$candidate])) {
            $n++;
            $candidate = $base.'-'.$legacyItemId.'-'.$n;
        }
        $usedCodes[$candidate] = true;

        return $candidate;
    }

    private function importParties(string $dir, int $businessId, int $userId): void
    {
        $path = $dir.DIRECTORY_SEPARATOR.'party.sql';
        $rows = SqlInsertValuesParser::parseAllRowsFromFile($path, 'party');

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            if (count($row) !== 8) {
                throw new \RuntimeException('party row must have 8 columns');
            }
            [
                ,
                $partyName,
                $contactNo,
                $status,
                ,
                $openingDate,
                ,
                ,
            ] = $row;

            $name = Str::upper(html_entity_decode((string) $partyName, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $coa = ChartOfAccount::createPartyAccount($name, $businessId);

            $openingDateNorm = null;
            if ($openingDate !== null && $openingDate !== '' && $openingDate !== '0000-00-00') {
                $openingDateNorm = (string) $openingDate;
            }

            Party::query()->create([
                'business_id' => $businessId,
                'chart_of_account_id' => $coa->id,
                'name' => $name,
                'address' => null,
                'phone_no' => trim((string) $contactNo) !== '' ? (string) $contactNo : null,
                'whatsapp_no' => null,
                'cnic' => null,
                'ntn' => null,
                'opening_balance' => 0,
                'opening_date' => $openingDateNorm,
                'opening_type' => null,
                'user_id' => $userId,
                'status' => (int) $status === 1,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Parties: '.count($rows));
    }

    private function importBanks(string $dir, int $businessId, int $userId): void
    {
        $path = $dir.DIRECTORY_SEPARATOR.'banks.sql';
        $rows = SqlInsertValuesParser::parseAllRowsFromFile($path, 'banks');

        foreach ($rows as $row) {
            if (count($row) !== 9) {
                throw new \RuntimeException('banks row must have 9 columns');
            }
            [
                ,
                $bankName,
                $accountNumber,
                ,
                ,
                $status,
                ,
                ,
                ,
            ] = $row;

            $accountName = Str::upper(html_entity_decode((string) $bankName, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $accountNumber = (string) $accountNumber;

            $accountType = $this->legacyBankAccountType((string) $bankName);
            $bankNameField = $accountType === 'bank'
                ? $this->inferBankInstitutionName((string) $bankName)
                : null;

            $descriptionParts = [];
            if ($accountNumber !== '') {
                $descriptionParts[] = 'A/C '.$accountNumber;
            }
            $description = $descriptionParts !== [] ? implode('. ', $descriptionParts) : null;

            $chart = $this->createBankChartAccount(
                $businessId,
                $accountType,
                $accountName,
                $bankNameField
            );

            Bank::query()->create([
                'business_id' => $businessId,
                'account_type' => $accountType,
                'account_name' => $accountName,
                'bank_name' => $bankNameField,
                'description' => $description,
                'opening_balance' => 0,
                'chart_of_account_id' => $chart->id,
                'user_id' => $userId,
                'status' => (int) $status === 1,
            ]);
        }

        $this->info('Banks: '.count($rows));
    }

    private function legacyBankAccountType(string $rawName): string
    {
        $u = Str::upper($rawName);

        return str_contains($u, 'CASH COUNTER') ? 'cash' : 'bank';
    }

    private function inferBankInstitutionName(string $rawName): ?string
    {
        $u = Str::upper(trim($rawName));
        if (str_contains($u, 'EASY PAISA') || str_contains($u, 'EASYPAISA')) {
            return 'EASY PAISA';
        }
        if (str_contains($u, 'ALHABIB') || str_contains($u, 'BANK AL HABIB')) {
            return 'BANK AL HABIB';
        }
        if (str_contains($u, 'HABIB METRO') || str_contains($u, 'METRO')) {
            return 'HABIB METRO POLITAN';
        }

        return null;
    }

    private function createBankChartAccount(
        int $businessId,
        string $accountType,
        string $accountName,
        ?string $bankName,
    ): ChartOfAccount {
        $parentCode = $accountType === 'bank' ? '1120' : '1110';
        $parentAccount = ChartOfAccount::where('code', $parentCode)
            ->where('business_id', $businessId)
            ->firstOrFail();

        $lastSibling = ChartOfAccount::where('parent_id', $parentAccount->id)
            ->where('business_id', $businessId)
            ->orderByDesc('code')
            ->first();

        if ($lastSibling) {
            $newCode = str_pad((string) ((int) $lastSibling->code + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newCode = substr($parentCode, 0, -1).'1';
        }

        $combinedName = $accountName;
        if ($accountType === 'bank' && $bankName !== null && $bankName !== '') {
            $combinedName = $accountName.' '.$bankName;
        }

        return ChartOfAccount::query()->create([
            'business_id' => $businessId,
            'parent_id' => $parentAccount->id,
            'name' => Str::upper($combinedName),
            'code' => $newCode,
            'type' => 'asset',
            'is_active' => true,
        ]);
    }
}
