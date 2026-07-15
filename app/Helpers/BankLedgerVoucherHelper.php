<?php

namespace App\Helpers;

class BankLedgerVoucherHelper
{
    public static function url(object $entry): ?string
    {
        $voucherId = $entry->voucher_id ?? null;
        if ($voucherId === null || $voucherId === '') {
            return null;
        }

        $type = strtolower(trim((string) ($entry->voucher_type ?? '')));

        if (self::isSaleInvoiceType($type)) {
            return route('sale-invoices.show', $voucherId);
        }

        if (self::isPurchaseType($type)) {
            return route('purchases.show', $voucherId);
        }

        if (self::isSaleReturnType($type)) {
            return route('sale-returns.show', $voucherId);
        }

        if (self::isPurchaseReturnType($type)) {
            return route('purchase-returns.show', $voucherId);
        }

        if ($type === 'general voucher') {
            return route('general-vouchers.show', $voucherId);
        }

        if ($type === 'bank transfer') {
            return route('bank-transfers.show', $voucherId);
        }

        if ($type === 'party transfer') {
            return route('party-transfers.show', $voucherId);
        }

        if ($type === 'owner contribution') {
            return route('owner-contributions.show', $voucherId);
        }

        if ($type === 'owner drawing') {
            return route('owner-drawings.show', $voucherId);
        }

        if ($type === 'expense') {
            return route('expenses.show', $voucherId);
        }

        if (in_array($type, ['other income', 'other_income'], true)) {
            return route('other-incomes.show', $voucherId);
        }

        if (in_array($type, ['quotation', 'quotations'], true)) {
            return route('quotations.show', $voucherId);
        }

        return null;
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
}
