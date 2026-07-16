@props([
    'itemCode',
    'itemName',
    'formattedPrice',
    'barcodeId' => null,
    'preview' => false,
])

<div @class(['barcode-label', 'barcode-label--preview' => $preview])>
    <div class="barcode-label__scan">
        <svg
            @if($barcodeId) id="{{ $barcodeId }}" @endif
            data-barcode-code="{{ $itemCode }}"
            @if($preview) data-barcode-height="38" data-barcode-width="1.75" @endif
            role="img"
            aria-label="Barcode {{ $itemCode }}"
        ></svg>
    </div>
    <div class="barcode-label__code">{{ $itemCode }}</div>
    <div class="barcode-label__name" title="{{ $itemName }}">{{ $itemName }}</div>
    <div class="barcode-label__price">{{ $formattedPrice }}</div>
</div>
