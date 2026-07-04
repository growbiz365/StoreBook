<?php

namespace App\Helpers;

use App\Models\GeneralVoucher;
use App\Models\PartyLedger;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use Illuminate\Support\Collection;

class PartyLedgerDescriptionHelper
{
    /** @var array<int, string|null> */
    protected static array $saleInvoiceRemarks = [];

    /** @var array<int, string|null> */
    protected static array $purchaseRemarks = [];

    /** @var array<int, string|null> */
    protected static array $saleReturnRemarks = [];

    /** @var array<int, string|null> */
    protected static array $purchaseReturnRemarks = [];

    /** @var array<int, string|null> */
    protected static array $generalVoucherDetails = [];

    /**
     * @param  Collection<int, PartyLedger>  $entries
     */
    public static function preload(Collection $entries): void
    {
        $saleInvoiceIds = [];
        $purchaseIds = [];
        $saleReturnIds = [];
        $purchaseReturnIds = [];
        $generalVoucherIds = [];

        foreach ($entries as $entry) {
            $type = self::normalizeType($entry->voucher_type);
            $id = (int) $entry->voucher_id;

            if (self::isSaleInvoiceType($type)) {
                $saleInvoiceIds[$id] = $id;
            } elseif (self::isPurchaseType($type)) {
                $purchaseIds[$id] = $id;
            } elseif (self::isSaleReturnType($type)) {
                $saleReturnIds[$id] = $id;
            } elseif (self::isPurchaseReturnType($type)) {
                $purchaseReturnIds[$id] = $id;
            } elseif ($type === 'general voucher') {
                $generalVoucherIds[$id] = $id;
            }
        }

        self::loadSaleInvoiceRemarks(array_values($saleInvoiceIds));
        self::loadPurchaseRemarks(array_values($purchaseIds));
        self::loadSaleReturnRemarks(array_values($saleReturnIds));
        self::loadPurchaseReturnRemarks(array_values($purchaseReturnIds));
        self::loadGeneralVoucherDetails(array_values($generalVoucherIds));
    }

    public static function description(PartyLedger $entry): string
    {
        $type = self::normalizeType($entry->voucher_type);

        if ($type === 'party transfer' && $entry->partyTransfer) {
            if ($entry->debit_amount > 0) {
                $text = 'Debit Party: ' . ($entry->partyTransfer->creditParty->name ?? '');
            } else {
                $text = 'Credit Party: ' . ($entry->partyTransfer->debitParty->name ?? '');
            }

            $details = trim((string) ($entry->partyTransfer->details ?? ''));
            if ($details !== '') {
                $text .= ', ' . $details;
            }

            return $text;
        }

        return self::appendRemarks($entry->voucher_type, self::remarksForEntry($entry));
    }

    protected static function remarksForEntry(PartyLedger $entry): ?string
    {
        $type = self::normalizeType($entry->voucher_type);
        $id = (int) $entry->voucher_id;

        if (self::isSaleInvoiceType($type)) {
            return self::$saleInvoiceRemarks[$id] ?? self::fetchSaleInvoiceRemarks($id);
        }

        if (self::isPurchaseType($type)) {
            return self::$purchaseRemarks[$id] ?? self::fetchPurchaseRemarks($id);
        }

        if (self::isSaleReturnType($type)) {
            return self::$saleReturnRemarks[$id] ?? self::fetchSaleReturnRemarks($id);
        }

        if (self::isPurchaseReturnType($type)) {
            return self::$purchaseReturnRemarks[$id] ?? self::fetchPurchaseReturnRemarks($id);
        }

        if ($type === 'general voucher') {
            return self::$generalVoucherDetails[$id] ?? self::fetchGeneralVoucherDetails($id);
        }

        return null;
    }

    protected static function appendRemarks(string $base, ?string $remarks): string
    {
        $remarks = trim((string) $remarks);

        if ($remarks === '') {
            return $base;
        }

        return $base . ' — ' . $remarks;
    }

    protected static function normalizeType(?string $voucherType): string
    {
        return strtolower(trim((string) $voucherType));
    }

    protected static function isSaleInvoiceType(string $type): bool
    {
        if ($type === '' || str_contains($type, 'return')) {
            return false;
        }

        return in_array($type, [
            'sale invoice',
            'sale invoice cancellation',
            'sale invoice reversal',
            'saleinvoice',
        ], true);
    }

    protected static function isPurchaseType(string $type): bool
    {
        if ($type === '' || str_contains($type, 'return')) {
            return false;
        }

        return in_array($type, [
            'purchase',
            'purchase cancellation',
        ], true);
    }

    protected static function isSaleReturnType(string $type): bool
    {
        return in_array($type, [
            'sale return',
            'sale return cancellation',
            'salereturn',
            'salereturncancellation',
        ], true);
    }

    protected static function isPurchaseReturnType(string $type): bool
    {
        return in_array($type, [
            'purchase return',
            'purchase return cancellation',
            'purchasereturn',
            'purchasereturncancellation',
        ], true);
    }

    /**
     * @param  array<int>  $ids
     */
    protected static function loadSaleInvoiceRemarks(array $ids): void
    {
        $missing = array_values(array_diff($ids, array_keys(self::$saleInvoiceRemarks)));
        if ($missing === []) {
            return;
        }

        SaleInvoice::query()
            ->select('id', 'remarks')
            ->whereIn('id', $missing)
            ->get()
            ->each(function (SaleInvoice $invoice) {
                self::$saleInvoiceRemarks[$invoice->id] = $invoice->remarks;
            });
    }

    /**
     * @param  array<int>  $ids
     */
    protected static function loadPurchaseRemarks(array $ids): void
    {
        $missing = array_values(array_diff($ids, array_keys(self::$purchaseRemarks)));
        if ($missing === []) {
            return;
        }

        Purchase::query()
            ->select('id', 'remarks')
            ->whereIn('id', $missing)
            ->get()
            ->each(function (Purchase $purchase) {
                self::$purchaseRemarks[$purchase->id] = $purchase->remarks;
            });
    }

    /**
     * @param  array<int>  $ids
     */
    protected static function loadSaleReturnRemarks(array $ids): void
    {
        $missing = array_values(array_diff($ids, array_keys(self::$saleReturnRemarks)));
        if ($missing === []) {
            return;
        }

        SaleReturn::query()
            ->select('id', 'remarks')
            ->whereIn('id', $missing)
            ->get()
            ->each(function (SaleReturn $saleReturn) {
                self::$saleReturnRemarks[$saleReturn->id] = $saleReturn->remarks;
            });
    }

    /**
     * @param  array<int>  $ids
     */
    protected static function loadPurchaseReturnRemarks(array $ids): void
    {
        $missing = array_values(array_diff($ids, array_keys(self::$purchaseReturnRemarks)));
        if ($missing === []) {
            return;
        }

        PurchaseReturn::query()
            ->select('id', 'remarks')
            ->whereIn('id', $missing)
            ->get()
            ->each(function (PurchaseReturn $purchaseReturn) {
                self::$purchaseReturnRemarks[$purchaseReturn->id] = $purchaseReturn->remarks;
            });
    }

    /**
     * @param  array<int>  $ids
     */
    protected static function loadGeneralVoucherDetails(array $ids): void
    {
        $missing = array_values(array_diff($ids, array_keys(self::$generalVoucherDetails)));
        if ($missing === []) {
            return;
        }

        GeneralVoucher::query()
            ->select('id', 'details')
            ->whereIn('id', $missing)
            ->get()
            ->each(function (GeneralVoucher $voucher) {
                self::$generalVoucherDetails[$voucher->id] = $voucher->details;
            });
    }

    protected static function fetchSaleInvoiceRemarks(int $id): ?string
    {
        self::loadSaleInvoiceRemarks([$id]);

        return self::$saleInvoiceRemarks[$id] ?? null;
    }

    protected static function fetchPurchaseRemarks(int $id): ?string
    {
        self::loadPurchaseRemarks([$id]);

        return self::$purchaseRemarks[$id] ?? null;
    }

    protected static function fetchSaleReturnRemarks(int $id): ?string
    {
        self::loadSaleReturnRemarks([$id]);

        return self::$saleReturnRemarks[$id] ?? null;
    }

    protected static function fetchPurchaseReturnRemarks(int $id): ?string
    {
        self::loadPurchaseReturnRemarks([$id]);

        return self::$purchaseReturnRemarks[$id] ?? null;
    }

    protected static function fetchGeneralVoucherDetails(int $id): ?string
    {
        self::loadGeneralVoucherDetails([$id]);

        return self::$generalVoucherDetails[$id] ?? null;
    }
}
