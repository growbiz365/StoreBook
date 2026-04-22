@php
    $b = $saleInvoice->business;
    $bizName = $b->store_name ?? $b->business_name ?? config('app.name');
    $bizAddress = $b->store_address ?? $b->address ?? '';
    $bizPhone = trim(implode(' - ', array_filter([$b->store_phone ?? null, $b->contact_no ?? null])));
    $bizEmail = $b->store_email ?? $b->email ?? '';
    $invoiceNo = $saleInvoice->invoice_number ?? ('SI-' . $saleInvoice->id);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Sale invoice') }} #{{ $invoiceNo }} — {{ $bizName }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 8px;
            font-family: ui-monospace, 'Cascadia Mono', 'Segoe UI Mono', Consolas, monospace;
            font-size: 11px;
            line-height: 1.35;
            max-width: 80mm;
            margin-left: auto;
            margin-right: auto;
            color: #000;
        }
        .align-center { text-align: center; }
        .margin-bottom-10 { margin-bottom: 10px; }
        .margin-bottom-15 { margin-bottom: 15px; }
        h1 { font-size: 14px; margin: 0 0 6px; font-weight: 700; }
        h3 { font-size: 12px; margin: 0 0 8px; font-weight: 700; }
        h4 { font-size: 10px; margin: 0 0 4px; font-weight: 400; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 4px; vertical-align: top; }
        thead th { background: #e8e8e8; font-weight: 700; }
        .group-row td { background: #d9d9d9; text-align: center; font-weight: 600; }
        .totals th { background: #f0f0f0; text-align: left; }
        .totals td, .totals th { border-color: #333; }
        img.logo { max-width: 150px; height: auto; margin-bottom: 6px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print align-center margin-bottom-10">
    <button type="button" onclick="window.print()" style="padding:6px 12px;font-size:12px;cursor:pointer;">{{ __('Print again') }}</button>
    <a href="{{ route('sale-invoices.show', $saleInvoice) }}" style="margin-left:8px;font-size:12px;">{{ __('Back to invoice') }}</a>
</div>

<div class="container">
    <div class="margin-bottom-10">
        <div class="align-center">
            @if(file_exists(public_path('img/logo.png')))
                <img src="{{ asset('img/logo.png') }}" alt="" class="logo" width="150">
            @endif
            <h1 class="margin-bottom-15">{{ $bizName }}</h1>
            @if($bizAddress !== '')
                <h4 class="margin-bottom-10">{{ __('Address') }}: {{ $bizAddress }}</h4>
            @endif
            <h4 class="margin-bottom-10">
                @if($bizPhone !== ''){{ $bizPhone }}@endif
                @if($bizPhone !== '' && $bizEmail !== '') — @endif
                @if($bizEmail !== ''){{ $bizEmail }}@endif
            </h4>
            <h3 class="margin-bottom-15">
                <strong>{{ __('SALE INVOICE') }}</strong># {{ $invoiceNo }}
                — {{ $customerLabel }}
            </h3>
        </div>
    </div>

    <table cellspacing="0" class="margin-bottom-15">
        <thead>
            <tr>
                <th style="width:10%">#</th>
                <th style="width:50%">{{ __('Items') }}</th>
                <th style="width:12%">{{ __('QTY') }}</th>
                <th style="width:13%">{{ __('Rate') }}</th>
                <th style="width:15%">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($groupedLines as $groupTitle => $rows)
                <tr class="group-row">
                    <td colspan="5">{{ $groupTitle }}</td>
                </tr>
                @foreach($rows as $value)
                    <tr>
                        <td>{{ $no }}</td>
                        <td>{{ $value['item_name'] }}</td>
                        <td>{{ number_format((float) $value['quantity'], (float) $value['quantity'] == floor((float) $value['quantity']) ? 0 : 2) }}</td>
                        <td>{{ number_format((float) $value['price'], 2) }}</td>
                        <td>{{ number_format((float) $value['total'], 2) }}</td>
                    </tr>
                    @php $no++; @endphp
                @endforeach
            @empty
                <tr><td colspan="5" class="align-center">{{ __('No line items') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <table cellspacing="0" class="margin-bottom-15 totals">
        @if((float) $saleInvoice->subtotal != (float) $saleInvoice->total_amount)
            <tr>
                <th style="width:50%">{{ __('Sub Total') }}</th>
                <th style="width:50%">{{ number_format((float) $saleInvoice->subtotal, 2) }}</th>
            </tr>
        @endif
        @if((float) $saleInvoice->shipping_charges > 0)
            <tr>
                <td>{{ __('Shipping Charges') }}</td>
                <td>{{ number_format((float) $saleInvoice->shipping_charges, 2) }}</td>
            </tr>
        @endif
        <tr>
            <th><strong>{{ __('Invoice Total') }}</strong></th>
            <th><strong>{{ number_format((float) $saleInvoice->total_amount, 2) }}</strong></th>
        </tr>
        @if($saleInvoice->sale_type === 'credit' && $oldBalance !== null && $totalBalance !== null)
            @php
                $fmtThermalLedgerBal = static function (float $raw): string {
                    if (abs($raw) < 0.005) {
                        return number_format(0.0, 2);
                    }
                    if ($raw < 0) {
                        return number_format(abs($raw), 2) . ' Dr';
                    }
                    return number_format($raw, 2) . ' Cr';
                };
            @endphp
            <tr>
                <th><strong>{{ __('Previous Balance') }}</strong><br><span style="font-weight:400;font-size:9px;">Dr=owes بنام · Cr= جمع</span></th>
                <th style="background:#f7faff;"><strong>{{ $currency }} {{ $fmtThermalLedgerBal($oldBalance) }}</strong></th>
            </tr>
            <tr>
                <th><strong>{{ __('Total Balance') }}</strong></th>
                <th style="background:#f7faff;"><strong>{{ $currency }} {{ $fmtThermalLedgerBal($totalBalance) }}</strong></th>
            </tr>
        @endif
    </table>
</div>

<p class="align-center margin-bottom-15"><strong>{{ __('Print Date/Time') }}:</strong> {{ now()->format('d-m-Y H:i:s') }}</p>
<p class="align-center margin-bottom-15"><strong>{{ __('Powered by') }}:</strong> Grow Business 365</p>

</body>
</html>
