@once
<style>
    .barcode-label {
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: #fff;
        color: #111;
        page-break-inside: avoid;
        overflow: hidden;
    }

    .barcode-label--preview {
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .barcode-label__scan {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
    }

    .barcode-label__scan svg {
        display: block;
        max-width: 100%;
        height: auto;
    }

    .barcode-label__code {
        font-family: Consolas, 'Courier New', ui-monospace, monospace;
        font-size: 9pt;
        font-weight: 600;
        letter-spacing: 0.06em;
        color: #1f2937;
        margin-top: 2mm;
        line-height: 1.15;
        max-width: 100%;
        word-break: break-all;
    }

    .barcode-label__name {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 7.5pt;
        font-weight: 400;
        color: #6b7280;
        margin-top: 1mm;
        line-height: 1.25;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .barcode-labels-root--a4 .barcode-label__name {
        white-space: normal;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .barcode-label__price {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12pt;
        font-weight: 700;
        color: #111827;
        margin-top: 1.5mm;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    /* Thermal roll / single sticker */
    .barcode-labels-root--thermal {
        max-width: 52mm;
        margin: 0 auto;
    }

    .barcode-labels-root--thermal .barcode-label {
        width: 50mm;
        min-height: 30mm;
        padding: 2.5mm 3mm 2mm;
        margin: 0 auto 2mm;
    }

    .barcode-labels-root--thermal .barcode-label__scan svg {
        max-width: 44mm;
    }

    .barcode-labels-root--thermal .barcode-label--preview {
        width: 100%;
        min-height: auto;
        padding: 1rem 1.25rem 0.875rem;
        margin: 0;
    }

    .barcode-labels-root--thermal .barcode-label--preview .barcode-label__code {
        font-size: 0.8125rem;
        margin-top: 0.625rem;
    }

    .barcode-labels-root--thermal .barcode-label--preview .barcode-label__name {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .barcode-labels-root--thermal .barcode-label--preview .barcode-label__price {
        font-size: 1.125rem;
        margin-top: 0.5rem;
    }

    /* A4 sticker sheet */
    .barcode-labels-root--a4 {
        max-width: 210mm;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 4mm 3mm;
    }

    .barcode-labels-root--a4 .barcode-label {
        width: 100%;
        min-height: 34mm;
        padding: 3mm 3mm 2.5mm;
        border: 1px dashed #d1d5db;
        border-radius: 1mm;
    }

    .barcode-labels-root--a4 .barcode-label__price {
        font-size: 13pt;
    }

    @media print {
        .barcode-labels-root--a4 .barcode-label {
            border-color: transparent;
            border-radius: 0;
        }

        .barcode-label--preview {
            border: none !important;
            box-shadow: none !important;
            background: #fff !important;
        }
    }
</style>
@endonce
