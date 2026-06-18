<?php

namespace App\Helpers;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\SaleInvoice;

class VoucherDisplayHelper
{
    /** @var array<int, string> */
    protected static array $purchaseNumbers = [];

    /** @var array<int, string> */
    protected static array $purchaseReturnNumbers = [];

    /** @var array<int, string> */
    protected static array $saleInvoiceNumbers = [];

    public static function displayVoucherId(?string $voucherType, $voucherId): string
    {
        if ($voucherId === null || $voucherId === '') {
            return '';
        }

        $type = strtolower(trim((string) $voucherType));

        if (self::isPurchaseReturnVoucherType($type)) {
            return self::purchaseReturnDisplayNumber((int) $voucherId);
        }

        if (self::isPurchaseVoucherType($type)) {
            return self::purchaseDisplayNumber((int) $voucherId);
        }

        if (self::isSaleInvoiceVoucherType($type)) {
            return self::saleInvoiceDisplayNumber((int) $voucherId);
        }

        return (string) $voucherId;
    }

    public static function purchaseDisplayNumber(int $purchaseId): string
    {
        if (isset(self::$purchaseNumbers[$purchaseId])) {
            return self::$purchaseNumbers[$purchaseId];
        }

        $purchase = Purchase::query()
            ->select('id', 'purchase_number')
            ->find($purchaseId);

        $number = $purchase
            ? (string) ($purchase->purchase_number ?? $purchase->id)
            : (string) $purchaseId;

        return self::$purchaseNumbers[$purchaseId] = $number;
    }

    public static function purchaseReturnDisplayNumber(int $purchaseReturnId): string
    {
        if (isset(self::$purchaseReturnNumbers[$purchaseReturnId])) {
            return self::$purchaseReturnNumbers[$purchaseReturnId];
        }

        $purchaseReturn = PurchaseReturn::query()
            ->select('id', 'return_number')
            ->find($purchaseReturnId);

        $number = $purchaseReturn
            ? (string) ($purchaseReturn->getAttributes()['return_number'] ?? $purchaseReturn->id)
            : (string) $purchaseReturnId;

        return self::$purchaseReturnNumbers[$purchaseReturnId] = $number;
    }

    /**
     * @param  array<int>  $purchaseIds
     */
    public static function preloadPurchaseNumbers(array $purchaseIds): void
    {
        $missing = array_values(array_diff(
            array_filter(array_map('intval', $purchaseIds)),
            array_keys(self::$purchaseNumbers)
        ));

        if ($missing === []) {
            return;
        }

        Purchase::query()
            ->select('id', 'purchase_number')
            ->whereIn('id', $missing)
            ->get()
            ->each(function (Purchase $purchase) {
                self::$purchaseNumbers[$purchase->id] = (string) ($purchase->purchase_number ?? $purchase->id);
            });
    }

    /**
     * @param  array<int>  $purchaseReturnIds
     */
    public static function preloadPurchaseReturnNumbers(array $purchaseReturnIds): void
    {
        $missing = array_values(array_diff(
            array_filter(array_map('intval', $purchaseReturnIds)),
            array_keys(self::$purchaseReturnNumbers)
        ));

        if ($missing === []) {
            return;
        }

        PurchaseReturn::query()
            ->select('id', 'return_number')
            ->whereIn('id', $missing)
            ->get()
            ->each(function (PurchaseReturn $purchaseReturn) {
                self::$purchaseReturnNumbers[$purchaseReturn->id] = (string) (
                    $purchaseReturn->getAttributes()['return_number'] ?? $purchaseReturn->id
                );
            });
    }

    public static function saleInvoiceDisplayNumber(int $saleInvoiceId): string
    {
        if (isset(self::$saleInvoiceNumbers[$saleInvoiceId])) {
            return self::$saleInvoiceNumbers[$saleInvoiceId];
        }

        $invoice = SaleInvoice::query()
            ->select('id')
            ->find($saleInvoiceId);

        $number = $invoice ? (string) $invoice->invoice_number : (string) $saleInvoiceId;

        return self::$saleInvoiceNumbers[$saleInvoiceId] = $number;
    }

    private static function isPurchaseReturnVoucherType(string $type): bool
    {
        return in_array($type, [
            'purchase return',
            'purchasereturn',
            'purchase return cancellation',
            'purchasereturncancellation',
        ], true);
    }

    private static function isPurchaseVoucherType(string $type): bool
    {
        if ($type === '' || str_contains($type, 'return')) {
            return false;
        }

        return $type === 'purchase' || $type === 'purchase cancellation';
    }

    private static function isSaleInvoiceVoucherType(string $type): bool
    {
        if ($type === '' || str_contains($type, 'return')) {
            return false;
        }

        return in_array($type, [
            'saleinvoice',
            'sale invoice',
            'sale invoice cancellation',
        ], true);
    }
}
