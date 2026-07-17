<?php

namespace App\Support;

use App\Models\GeneralItem;
use App\Models\GeneralItemStockLedger;

class GeneralItemBarcode
{
    /**
     * Normalize a scanned or typed code for lookup.
     */
    public static function normalizeCode(?string $code): string
    {
        $code = (string) $code;
        $code = preg_replace('/[\x00-\x1F\x7F]/u', '', $code) ?? $code;

        return trim($code);
    }

    /**
     * Value encoded into printed/scanned barcodes.
     */
    public static function codeForBarcode(?string $code): string
    {
        return self::normalizeCode($code);
    }

    /**
     * Shape used by POS / barcode scan handlers.
     */
    public static function toScanPayload(GeneralItem $item): array
    {
        $item->loadMissing('itemType');

        $payload = [
            'id' => $item->id,
            'item_name' => $item->item_name,
            'item_code' => $item->item_code,
            'sale_price' => (float) $item->sale_price,
            'item_kind' => $item->item_kind ?? GeneralItem::KIND_GOODS,
            'tracks_inventory' => $item->tracksInventory(),
            'item_type_id' => $item->item_type_id,
            'item_type' => $item->itemType ? ['item_type' => $item->itemType->item_type] : null,
            'available_stock' => null,
        ];

        if ($item->tracksInventory()) {
            $balance = GeneralItemStockLedger::getStockBalance($item->id, $item->business_id);
            $payload['available_stock'] = StockQuantity::normalize($balance['balance']);
        }

        return $payload;
    }
}
