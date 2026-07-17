@props([
    'itemCode',
    'itemName',
    'barcodeId' => null,
    'preview' => false,
])

@php
    $barcodeValue = \App\Support\GeneralItemBarcode::codeForBarcode($itemCode);
@endphp

<div @class(['barcode-label', 'barcode-label--preview' => $preview])>
    <div class="barcode-label__scan">
        <canvas
            @if($barcodeId) id="{{ $barcodeId }}" @endif
            data-barcode-code="{{ $barcodeValue }}"
            @if($preview) data-barcode-height="44" data-barcode-width="2" data-barcode-margin="10" @endif
            role="img"
            aria-label="Barcode {{ $itemCode }}"
        ></canvas>
    </div>
    <div class="barcode-label__caption" title="{{ $itemCode }} — {{ $itemName }}">
        <span class="barcode-label__code">{{ $itemCode }}</span>
        <span class="barcode-label__sep" aria-hidden="true">·</span>
        <span class="barcode-label__name">{{ $itemName }}</span>
    </div>
</div>
