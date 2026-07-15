<style>
    @page { margin: 10mm; }
    * { box-sizing: border-box; }
    html, body { width: 100%; }
    body {
        font-family: 'Inter', sans-serif;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        color: #1a1a1a;
        background: #fff;
    }
    .page-container {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 16px 20px;
        background: white;
    }
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
        border-bottom: 2px solid #333;
        padding-bottom: 12px;
        gap: 30px;
    }
    .report-header-left { flex: 1; text-align: left; }
    .report-header-right { flex: 1; text-align: right; }
    .business-logo { height: 55px; margin-bottom: 6px; display: block; }
    .business-info h2 { margin: 4px 0 6px 0; font-size: 18px; font-weight: 700; color: #1a1a1a; }
    .business-info-details { font-size: 11px; color: #555; line-height: 1.6; }
    .report-title h2 { margin: 0 0 8px 0; font-size: 18px; font-weight: 700; color: #1a1a1a; }
    .report-title .meta { font-size: 12px; color: #555; line-height: 1.7; }
    .report-title .meta strong { color: #1a1a1a; }
    .meta-pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        background: #eef2ff;
        color: #3730a3;
        font-size: 11px;
        font-weight: 600;
    }
    .filters {
        margin: 15px 0;
        padding: 16px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .filter-form .form-group { flex: 1; min-width: 140px; }
    .filter-form .form-group.wide { flex: 2; min-width: 220px; }
    .filter-form label {
        display: block;
        margin-bottom: 5px;
        font-size: 12px;
        font-weight: 500;
        color: #444;
    }
    .filter-form input[type="date"],
    .filter-form input[type="text"],
    .filter-form select {
        width: 100%;
        padding: 8px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 13px;
        font-family: 'Inter', sans-serif;
        background: #fff;
    }
    .button-group {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
        flex-wrap: nowrap;
        align-items: center;
    }
    button, .btn-link {
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
        height: 36px;
        line-height: 1;
    }
    .btn-primary { background: #0d6efd; color: white; }
    .btn-primary:hover { background: #0b5ed7; }
    .btn-print { background: #1f2937; color: white; }
    .btn-print:hover { background: #111827; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn-secondary:hover { background: #5c636a; }
    .voucher-link { color: #0d6efd; font-weight: 600; text-decoration: none; }
    .voucher-link:hover { text-decoration: underline; }
    .table-container {
        overflow-x: auto;
        margin-top: 4px;
        border: 1px solid #333;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { border: 1px solid #333; padding: 7px 8px; vertical-align: top; }
    th { background: #f1f5f9; font-weight: 600; text-align: left; white-space: nowrap; }
    .urdu { font-size: 10px; color: #666; display: block; font-weight: 400; }
    .amount { text-align: right; white-space: nowrap; }
    .report-table tbody td, .report-table tfoot td { font-weight: 600; }
    .credit-val { color: #15803d; font-weight: 600; }
    .debit-val { color: #b91c1c; font-weight: 600; }
    .expense-val { color: #b91c1c; font-weight: 600; }
    .section-title-row td {
        background: #f1f5f9;
        font-weight: 700;
        font-size: 13px;
    }
    .category-row td {
        background: #e2e8f0;
        font-weight: 700;
        font-size: 12px;
    }
    .sub-row td:first-child { padding-left: 24px; }
    .total-row td { font-weight: 700; border-top: 2px solid #333; background: #f9fafb; }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin: 12px 0 16px;
    }
    .summary-card {
        border: 1px solid #333;
        padding: 12px 14px;
        border-radius: 4px;
        background: #fafafa;
    }
    .summary-card h4 {
        margin: 0 0 6px 0;
        font-size: 11px;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
    }
    .summary-value { font-size: 16px; font-weight: 700; }
    .notice-box {
        margin: 12px 0;
        padding: 12px 14px;
        border: 1px solid #fde68a;
        border-radius: 6px;
        background: #fffbeb;
        font-size: 12px;
        color: #854d0e;
    }
    .notice-box h4 { margin: 0 0 8px 0; font-size: 13px; font-weight: 600; }
    .notice-box ul { margin: 0; padding-left: 18px; }
    .notice-box.warn { border-color: #fecaca; background: #fef2f2; color: #991b1b; }
    .notice-box.info { border-color: #bae6fd; background: #f0f9ff; color: #0c4a6e; }
    .record-count { margin-top: 10px; font-size: 12px; color: #6b7280; }
    .report-footer {
        margin-top: 16px;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #6b7280;
        border-top: 1px solid #d1d5db;
        padding-top: 8px;
    }
    .empty-state { text-align: center; color: #6b7280; padding: 40px 20px; }
    .account-link { color: #0d6efd; text-decoration: none; font-weight: 500; }
    .account-link:hover { text-decoration: underline; }
    .account-block {
        margin-bottom: 16px;
        border: 1px solid #333;
        page-break-inside: avoid;
    }
    .account-block-header {
        padding: 10px 12px;
        background: #f1f5f9;
        border-bottom: 1px solid #333;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    .account-block-meta { font-size: 11px; font-weight: 500; color: #555; }
    @media screen and (max-width: 768px) {
        .page-container { padding: 12px 10px; }
        .filter-form { flex-direction: column; align-items: stretch; }
        .filter-form .form-group { min-width: 100%; width: 100%; }
        .button-group { width: 100%; flex-wrap: wrap; }
        .button-group button, .button-group .btn-link {
            flex: 1; justify-content: center; min-width: min(100%, 120px);
        }
        table { font-size: 10px; }
        th, td { padding: 5px 4px; }
    }
    @media print {
        html, body { background: white !important; margin: 0 !important; padding: 0 !important; color: #000 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .page-container { padding: 0 !important; margin: 0 !important; max-width: none !important; width: 100% !important; background: white !important; }
        .no-print { display: none !important; }
        .report-header { display: flex !important; flex-direction: row !important; align-items: flex-start !important; justify-content: space-between !important; gap: 24px !important; margin-bottom: 14px; padding-bottom: 10px; page-break-inside: avoid; break-inside: avoid; }
        .report-header-left { flex: 1 !important; text-align: left !important; }
        .report-header-right { flex: 1 !important; text-align: right !important; }
        .business-info { text-align: left !important; }
        .report-title { text-align: right !important; }
        .business-info h2, .report-title h2 { font-size: 16px; }
        .business-info-details, .report-title .meta { font-size: 11px; }
        .meta-pill { background: #eef2ff !important; color: #3730a3 !important; }
        .table-container, .account-block { overflow: visible !important; border: 1px solid #000 !important; width: 100% !important; }
        table { font-size: 10.5px; width: 100% !important; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        th { background: #f1f5f9 !important; border: 1px solid #000 !important; padding: 5px 6px; }
        td { border: 1px solid #000 !important; padding: 4px 6px; font-weight: 600 !important; }
        .section-title-row td, .category-row td, .account-block-header { background: #f1f5f9 !important; }
        .total-row td { background: #f3f4f6 !important; border-top: 2px solid #000 !important; }
        .credit-val { color: #15803d !important; }
        .debit-val, .expense-val { color: #b91c1c !important; }
        .voucher-link, .account-link { color: inherit !important; text-decoration: none !important; font-weight: 600 !important; }
        .report-footer { margin-top: 12px; font-size: 9px; page-break-inside: avoid; }
        .summary-grid { display: none !important; }
    }
</style>
