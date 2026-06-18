@php
    $voucher = $voucher ?? null;
@endphp

<div class="flex flex-col h-full">
    <div class="flex items-center justify-between gap-2">
        <span class="text-xs font-medium text-gray-700">Attachments</span>
        <span class="text-[10px] text-gray-500">PDF, Word, Images</span>
    </div>

    <div class="mt-0.5 flex-1 flex flex-col rounded-md border border-gray-200 bg-gray-50 p-2 min-h-[9rem]">
        @if($voucher && $voucher->attachments->isNotEmpty())
            <div class="mb-1.5 space-y-0.5 max-h-14 overflow-y-auto pr-1 shrink-0">
                @foreach($voucher->attachments as $attachment)
                    <div class="flex items-center justify-between gap-1 bg-white rounded border px-1.5 py-0.5 text-[10px]" data-attachment-id="{{ $attachment->id }}">
                        <span class="truncate" title="{{ $attachment->original_name }}">{{ $attachment->original_name }}</span>
                        <div class="shrink-0 flex gap-1.5">
                            <a href="{{ route('files.general-voucher-attachments.download', $attachment) }}" target="_blank" class="text-indigo-600">View</a>
                            <button type="button" onclick="deleteAttachment({{ $attachment->id }})" class="text-red-600">Del</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div id="attachments-container" class="flex-1 min-h-0 max-h-32 overflow-y-auto space-y-2 pr-1">
            <div class="attachment-group">
                @include('general_vouchers._attachment_fields')
            </div>
        </div>

        <button type="button" onclick="addAttachmentField()"
            class="mt-1.5 shrink-0 self-start inline-flex items-center px-1.5 py-0.5 border border-gray-300 text-[10px] font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
            <svg class="h-3 w-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add More
        </button>
    </div>
</div>
