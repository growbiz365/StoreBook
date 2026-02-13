<div class="report-header">
    <div class="report-header-left">
        <div class="business-info">
            @php
                $logo = $business->logo ?? $business->business_logo ?? false;
                $businessName = $business->business_name ?? $business->name ?? '';
                $address = $business->address ?? '';
                $phone = $business->contact_no ?? $business->phone ?? '';
                $email = $business->email ?? '';
            @endphp
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="Business Logo" class="business-logo">
            @endif
            <h2>{{ $businessName }}</h2>
            @if(!empty($address) || !empty($phone) || !empty($email))
                <div class="business-info-details">
                    @if(!empty($address))
                        {{ $address }}<br>
                    @endif
                    @if(!empty($phone) || !empty($email))
                        <span style="white-space: nowrap;">
                            @if(!empty($phone))
                                Phone: {{ $phone }}
                            @endif
                            @if(!empty($phone) && !empty($email))
                                &nbsp;|&nbsp;
                            @endif
                            @if(!empty($email))
                                Email: {{ $email }}
                            @endif
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="report-header-right">
        <div class="report-title">
            <h2>{{ $title ?? '' }}</h2>
            {{ $slot }}
        </div>
    </div>
</div>


