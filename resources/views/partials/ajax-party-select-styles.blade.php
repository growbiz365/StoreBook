@once
<style>
    .ajax-party-select {
        position: relative;
        width: 100%;
    }

    .ajax-party-select .hidden {
        display: none !important;
    }

    .ajax-party-select .searchable-input {
        width: 100%;
        box-sizing: border-box;
    }

    .ajax-party-select .searchable-dropdown {
        position: absolute;
        left: 0;
        right: 0;
        z-index: 50;
        margin-top: 4px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        max-height: 220px;
        overflow: hidden;
    }

    .ajax-party-select .search-results-container {
        max-height: 176px;
        overflow-y: auto;
    }

    .ajax-party-select .result-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
    }

    .ajax-party-select .result-item:last-child {
        border-bottom: none;
    }

    .ajax-party-select .result-item:hover,
    .ajax-party-select .result-item.bg-indigo-50 {
        background: #eef2ff;
    }

    .ajax-party-select .result-item .font-medium {
        font-weight: 600;
        color: #111827;
        font-size: 13px;
    }

    .ajax-party-select .result-item .text-sm {
        font-size: 12px;
        color: #6b7280;
    }

    .ajax-party-select .px-4 {
        padding-left: 12px;
        padding-right: 12px;
    }

    .ajax-party-select .py-2 {
        padding-top: 8px;
        padding-bottom: 8px;
    }

    .ajax-party-select .py-3 {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .ajax-party-select .text-sm {
        font-size: 12px;
    }

    .ajax-party-select .text-xs {
        font-size: 11px;
    }

    .ajax-party-select .text-center {
        text-align: center;
    }

    .ajax-party-select .text-gray-500 {
        color: #6b7280;
    }

    .ajax-party-select .text-red-600 {
        color: #dc2626;
    }

    .ajax-party-select .pagination-container {
        border-top: 1px solid #f1f5f9;
        padding: 8px;
        background: #f8fafc;
    }

    .ajax-party-select .pagination-container .flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .ajax-party-select .prev-page,
    .ajax-party-select .next-page {
        border: none;
        background: transparent;
        color: #4f46e5;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
        cursor: pointer;
    }

    .ajax-party-select .prev-page:disabled,
    .ajax-party-select .next-page:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .ajax-party-select .page-info {
        color: #6b7280;
        font-size: 11px;
    }

    .ajax-party-select .loading-indicator {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .ajax-party-select .loading-indicator svg {
        display: block;
        width: 16px;
        height: 16px;
        color: #9ca3af;
        animation: aps-spin 1s linear infinite;
    }

    @keyframes aps-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endonce
