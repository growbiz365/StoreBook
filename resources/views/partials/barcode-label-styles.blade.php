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
        border-radius: 0.5rem;
        background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .barcode-label__scan {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
        padding: 0 1mm;
    }

    .barcode-label__scan canvas,
    .barcode-label__scan svg {
        display: block;
        max-width: 100%;
        height: auto;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
    }

    .barcode-label__caption {
        margin-top: 2.5mm;
        max-width: 100%;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding: 0 0.5mm;
    }

    .barcode-label__code {
        font-family: Consolas, 'Courier New', ui-monospace, monospace;
        font-size: 9pt;
        font-weight: 700;
        letter-spacing: 0.06em;
        color: #111827;
    }

    .barcode-label__sep {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8pt;
        font-weight: 400;
        color: #9ca3af;
        margin: 0 0.15em;
    }

    .barcode-label__name {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8pt;
        font-weight: 500;
        color: #4b5563;
    }

    /* Thermal roll / single sticker */
    .barcode-labels-root--thermal {
        max-width: 52mm;
        margin: 0 auto;
    }

    .barcode-labels-root--thermal .barcode-label {
        width: 50mm;
        min-height: 24mm;
        padding: 2.5mm 2.5mm 2mm;
        margin: 0 auto 2mm;
    }

    .barcode-labels-root--thermal .barcode-label__scan canvas,
    .barcode-labels-root--thermal .barcode-label__scan svg {
        max-width: 46mm;
    }

    .barcode-labels-root--thermal .barcode-label--preview {
        width: 100%;
        min-height: auto;
        padding: 1.125rem 1.25rem 1rem;
        margin: 0;
    }

    .barcode-labels-root--thermal .barcode-label--preview .barcode-label__caption {
        margin-top: 0.75rem;
    }

    .barcode-labels-root--thermal .barcode-label--preview .barcode-label__code {
        font-size: 0.8125rem;
    }

    .barcode-labels-root--thermal .barcode-label--preview .barcode-label__name {
        font-size: 0.75rem;
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
        min-height: 26mm;
        padding: 3mm 3mm 2.5mm;
        border: 1px dashed #d1d5db;
        border-radius: 1mm;
    }

    .barcode-labels-root--a4 .barcode-label__code {
        font-size: 9.5pt;
    }

    @media print {
        .barcode-label__scan canvas,
        .barcode-label__scan svg {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

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
