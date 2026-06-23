<?php

namespace App\Console\Commands;

use App\Models\GeneralBatch;
use App\Models\GeneralItem;
use App\Models\GeneralItemStockLedger;
use App\Support\StockQuantity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileBusinessStock extends Command
{
    protected $signature = 'stock:reconcile-business
                            {business : Business ID to reconcile}
                            {--dry-run : Report changes without writing}';

    protected $description = 'Sync batch qty_remaining with stock ledger totals and recalculate running balances';

    public function handle(): int
    {
        $businessId = (int) $this->argument('business');
        $dryRun = (bool) $this->option('dry-run');

        $items = GeneralItem::where('business_id', $businessId)
            ->where('item_kind', GeneralItem::KIND_GOODS)
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            $this->warn("No goods items found for business {$businessId}.");

            return self::SUCCESS;
        }

        $perBatchFixes = 0;
        $itemAlignments = 0;
        $recalculatedItems = 0;

        DB::transaction(function () use ($businessId, $items, $dryRun, &$perBatchFixes, &$itemAlignments, &$recalculatedItems) {
            // Step 1: per-batch net from ledger rows that reference a batch
            $batchLedgers = DB::table('general_items_stock_ledger')
                ->where('business_id', $businessId)
                ->whereNotNull('batch_id')
                ->selectRaw('batch_id, ROUND(SUM(quantity), 2) as net_qty')
                ->groupBy('batch_id')
                ->get();

            foreach ($batchLedgers as $row) {
                $batch = GeneralBatch::find($row->batch_id);
                if (! $batch) {
                    continue;
                }

                $newQty = StockQuantity::normalize(max(0, (float) $row->net_qty));
                if (abs((float) $batch->qty_remaining - $newQty) > 0.001) {
                    if (! $dryRun) {
                        $batch->update(['qty_remaining' => $newQty]);
                    }
                    $perBatchFixes++;
                }
            }

            // Step 2: align total active batch stock to item ledger balance
            foreach ($items as $item) {
                $ledgerBalance = StockQuantity::normalize(
                    (float) DB::table('general_items_stock_ledger')
                        ->where('business_id', $businessId)
                        ->where('general_item_id', $item->id)
                        ->sum('quantity')
                );

                $batches = GeneralBatch::where('item_id', $item->id)
                    ->where('status', 'active')
                    ->orderBy('received_date')
                    ->orderBy('id')
                    ->get();

                $batchTotal = StockQuantity::normalize((float) $batches->sum('qty_remaining'));
                $targetBatchTotal = StockQuantity::normalize(max(0, $ledgerBalance));
                $delta = StockQuantity::normalize($targetBatchTotal - $batchTotal);

                if (abs($delta) < 0.01) {
                    continue;
                }

                if (! $dryRun) {
                    if ($delta > 0) {
                        $batch = $batches->first();
                        if (! $batch) {
                            $batch = GeneralBatch::create([
                                'business_id' => $businessId,
                                'item_id' => $item->id,
                                'batch_code' => 'RECON-'.$item->id.'-'.now()->format('YmdHis'),
                                'qty_received' => $delta,
                                'qty_remaining' => $delta,
                                'unit_cost' => $item->cost_price ?? 0,
                                'received_date' => now()->toDateString(),
                                'status' => 'active',
                            ]);
                        } else {
                            $batch->update([
                                'qty_remaining' => StockQuantity::normalize((float) $batch->qty_remaining + $delta),
                                'qty_received' => StockQuantity::normalize(max((float) $batch->qty_received, (float) $batch->qty_remaining + $delta)),
                            ]);
                        }
                    } else {
                        $remaining = abs($delta);
                        foreach ($batches->sortByDesc('id') as $batch) {
                            if ($remaining <= 0) {
                                break;
                            }
                            $take = min((float) $batch->qty_remaining, $remaining);
                            if ($take <= 0) {
                                continue;
                            }
                            $batch->update([
                                'qty_remaining' => StockQuantity::normalize((float) $batch->qty_remaining - $take),
                            ]);
                            $remaining = StockQuantity::normalize($remaining - $take);
                        }
                    }
                }

                $itemAlignments++;
            }

            foreach ($items as $item) {
                if (! $dryRun) {
                    GeneralItemStockLedger::recalculateBalances($item->id);
                }
                $recalculatedItems++;
            }
        });

        $prefix = $dryRun ? '[DRY RUN] ' : '';
        $this->info("{$prefix}Business {$businessId}: per-batch fixes {$perBatchFixes}, item alignments {$itemAlignments}, ledgers recalculated {$recalculatedItems}.");

        return self::SUCCESS;
    }
}
