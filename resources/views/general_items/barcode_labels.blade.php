@php
    $layout = in_array($layout ?? 'thermal', ['thermal', 'a4'], true) ? ($layout ?? 'thermal') : 'thermal';
    $autoPrint = (bool) ($autoPrint ?? false);
    $labelCount = count($labels ?? []);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barcode Labels ({{ $labelCount }})</title>
    @include('partials.barcode-label-styles')
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 10px;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #f3f4f6;
        }
        .print-toolbar {
            max-width: 210mm;
            margin: 0 auto 12px;
            padding: 10px 14px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .print-toolbar__title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }
        .print-toolbar__meta {
            font-size: 12px;
            color: #6b7280;
        }
        .print-toolbar__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .print-toolbar__actions button,
        .print-toolbar__actions a {
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }
        .print-toolbar__actions button {
            border: none;
            background: #059669;
            color: #fff;
            font-weight: 600;
        }
        .print-toolbar__actions button:hover {
            background: #047857;
        }
        .print-toolbar__actions a {
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
        }
        .print-toolbar__actions a:hover {
            background: #f9fafb;
        }
        .labels-sheet {
            background: #fff;
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .barcode-labels-root--a4.labels-sheet {
            padding: 6mm;
        }
        @media print {
            body {
                padding: 0;
                background: #fff;
            }
            .print-toolbar { display: none !important; }
            .labels-sheet {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }
        }
        @page {
            margin: {{ $layout === 'a4' ? '8mm' : '4mm' }};
        }
    </style>
</head>
<body>

<div class="print-toolbar no-print">
    <div>
        <div class="print-toolbar__title">Barcode labels ready</div>
        <div class="print-toolbar__meta">{{ $labelCount }} label{{ $labelCount === 1 ? '' : 's' }} · {{ $layout === 'a4' ? 'A4 sheet' : 'Thermal 50×30 mm' }}</div>
    </div>
    <div class="print-toolbar__actions">
        <button type="button" onclick="window.print()">Print</button>
        <a href="{{ route('general-items.index') }}">Back to items</a>
    </div>
</div>

<div @class(['labels-sheet', 'barcode-labels-root--' . $layout])>
    @foreach($labels as $label)
        @include('partials.barcode-label-card', [
            'itemCode' => $label['item']->item_code,
            'itemName' => $label['item']->item_name,
        ])
    @endforeach
</div>

@include('partials.jsbarcode-assets')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isThermal = @json($layout === 'thermal');
        const barcodeOptions = {
            width: isThermal ? 2.2 : 2,
            height: isThermal ? 42 : 46,
            margin: 10,
        };

        window.renderBarcodesIn(document, barcodeOptions);

        @if($autoPrint)
        setTimeout(function () { window.print(); }, 900);
        @endif
    });
</script>

</body>
</html>
