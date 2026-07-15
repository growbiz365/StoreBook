<?php

namespace App\Helpers;

use App\Models\Approval;
use App\Models\GeneralVoucher;
use App\Models\Party;
use App\Models\PartyLedger;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Quotation;
use App\Models\SaleInvoice;
use App\Models\SaleReturn;
use Illuminate\Support\Collection;

class PartyLedgerVoucherHelper
{
    /** @var array<string, string|null> */
    protected static array $transactionParties = [];

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
        $quotationIds = [];
        $approvalIds = [];
        $partyObIds = [];

        foreach ($entries as $entry) {
            $type = self::normalizeType($entry->voucher_type);
            $id = (int) $entry->voucher_id;

            if ($id <= 0) {
                continue;
            }

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
            } elseif (in_array($type, ['quotation', 'quotations'], true)) {
                $quotationIds[$id] = $id;
            } elseif (in_array($type, ['approval', 'approvals'], true)) {
                $approvalIds[$id] = $id;
            } elseif ($type === 'party ob') {
                $partyObIds[$id] = $id;
            }
        }

        self::loadPartiesForVouchers('sale invoice', $saleInvoiceIds, SaleInvoice::class);
        self::loadPartiesForVouchers('purchase', $purchaseIds, Purchase::class);
        self::loadPartiesForVouchers('sale return', $saleReturnIds, SaleReturn::class);
        self::loadPartiesForVouchers('purchase return', $purchaseReturnIds, PurchaseReturn::class);
        self::loadPartiesForVouchers('general voucher', $generalVoucherIds, GeneralVoucher::class);
        self::loadPartiesForVouchers('quotation', $quotationIds, Quotation::class);
        self::loadPartiesForVouchers('approval', $approvalIds, Approval::class);
        self::loadPartyObParties($partyObIds);
    }

    public static function url(PartyLedger $entry): ?string
    {
        $voucherId = $entry->voucher_id;
        if ($voucherId === null || $voucherId === '') {
            return null;
        }

        $type = self::normalizeType($entry->voucher_type);

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

        if ($type === 'party transfer') {
            return route('party-transfers.show', $voucherId);
        }

        if (in_array($type, ['quotation', 'quotations'], true)) {
            return route('quotations.show', $voucherId);
        }

        if (in_array($type, ['approval', 'approvals'], true)) {
            return route('approvals.show', $voucherId);
        }

        return null;
    }

    public static function transactionParty(PartyLedger $entry): ?string
    {
        $type = self::normalizeType($entry->voucher_type);
        $partyName = null;

        if ($type === 'party transfer') {
            $partyName = self::partyTransferCounterpartyName($entry);
        } else {
            $cached = self::lookupCachedParty($type, (int) $entry->voucher_id);
            if ($cached !== null && $cached !== '') {
                $partyName = $cached;
            } elseif ($entry->relationLoaded('party') && $entry->party) {
                $partyName = self::formatPartyName($entry->party);
            }
        }

        return self::formatTransactionPartyLabel($entry, $partyName);
    }

    protected static function formatTransactionPartyLabel(PartyLedger $entry, ?string $partyName): ?string
    {
        $partyName = trim((string) $partyName);
        if ($partyName === '') {
            return null;
        }

        if ((float) $entry->debit_amount > 0) {
            return 'Debit Party: ' . $partyName;
        }

        if ((float) $entry->credit_amount > 0) {
            return 'Credit Party: ' . $partyName;
        }

        return $partyName;
    }

    protected static function partyTransferCounterpartyName(PartyLedger $entry): ?string
    {
        if (! $entry->partyTransfer) {
            return null;
        }

        if ((float) $entry->debit_amount > 0) {
            return self::formatPartyName($entry->partyTransfer->creditParty);
        }

        if ((float) $entry->credit_amount > 0) {
            return self::formatPartyName($entry->partyTransfer->debitParty);
        }

        return null;
    }

    /**
     * @param  array<int, int>  $ids
     */
    protected static function loadPartiesForVouchers(string $typeKey, array $ids, string $modelClass): void
    {
        if ($ids === []) {
            return;
        }

        $modelClass::query()
            ->select('id', 'party_id')
            ->with(['party:id,name,pcode'])
            ->whereIn('id', array_values($ids))
            ->get()
            ->each(function ($model) use ($typeKey) {
                self::$transactionParties[self::cacheKey($typeKey, (int) $model->id)] = self::formatPartyName($model->party);
            });
    }

    /**
     * @param  array<int, int>  $ids
     */
    protected static function loadPartyObParties(array $ids): void
    {
        if ($ids === []) {
            return;
        }

        Party::query()
            ->select('id', 'name', 'pcode')
            ->whereIn('id', array_values($ids))
            ->get()
            ->each(function (Party $party) {
                self::$transactionParties[self::cacheKey('party ob', $party->id)] = self::formatPartyName($party);
            });
    }

    protected static function formatPartyName(?Party $party): ?string
    {
        if (! $party) {
            return null;
        }

        $name = trim((string) $party->name);

        return $name !== '' ? $name : null;
    }

    protected static function cacheKey(string $type, int $id): string
    {
        return $type . '|' . $id;
    }

    protected static function lookupCachedParty(string $type, int $id): ?string
    {
        if ($id <= 0) {
            return null;
        }

        $bucket = self::partyBucketType($type);

        return self::$transactionParties[self::cacheKey($bucket, $id)] ?? null;
    }

    protected static function partyBucketType(string $type): string
    {
        if (self::isSaleInvoiceType($type)) {
            return 'sale invoice';
        }

        if (self::isPurchaseType($type)) {
            return 'purchase';
        }

        if (self::isSaleReturnType($type)) {
            return 'sale return';
        }

        if (self::isPurchaseReturnType($type)) {
            return 'purchase return';
        }

        if ($type === 'general voucher') {
            return 'general voucher';
        }

        if (in_array($type, ['quotation', 'quotations'], true)) {
            return 'quotation';
        }

        if (in_array($type, ['approval', 'approvals'], true)) {
            return 'approval';
        }

        if ($type === 'party ob') {
            return 'party ob';
        }

        return $type;
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
}
