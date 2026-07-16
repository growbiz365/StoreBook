@props([
    'code',
    'name',
    'price',
    'variant' => 'thermal', // thermal | a4 | preview
    'svgId' => null,
])

<div @class([
    'barcode-label',
    'barcode-label--thermal' => $variant === 'thermal',
    'barcode-label--a4' => $variant === 'a4',
    'barcode-label--preview' => $variant === 'preview',
]) data-label-layout="{{ $variant === 'preview' ? 'thermal' : $variant }}">
    <div class="barcode-label__bars">
        <svg @if($svgId) id="{{ $svgId }}" @endif data-barcode-code="{{ $code }}" data-barcode-layout="{{ $variant === 'preview' ? 'thermal' : $variant }}"></svg>
    </div>
    <div class="barcode-label__body">
        <div class="barcode-label__code">{{ $code }}</div>
        <div class="barcode-label__name" title="{{ $name }}">{{ $name }}</div>
        <div class="barcode-label__price">{{ $price }}</div>
    </div>
</div>
