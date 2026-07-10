<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<style>
    .chosen-container { width: 100% !important; }
    .chosen-container-single .chosen-single {
        height: auto;
        min-height: 31px;
        line-height: 1.5;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 2rem 0.25rem 0.5rem;
        background: #fff;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        font-size: 0.875rem;
        color: #111827;
    }
    .chosen-container-single .chosen-single span { margin-right: 0.5rem; }
    .chosen-container-single .chosen-single div { right: 0.5rem; }
    .chosen-container-active .chosen-single,
    .chosen-container .chosen-single:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 1px #6366f1 inset, 0 0 0 1px rgba(99, 102, 241, 0.2);
    }
    .chosen-container .chosen-search input {
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .chosen-container .chosen-results { max-height: 200px; }
    .chosen-container .chosen-results li.highlighted {
        background-color: #e0e7ff;
        color: #312e81;
    }
</style>
@include('partials.chosen-party-pcode-search')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.jQuery || !jQuery.fn.chosen) {
            return;
        }

        $('.chosen-select-party-filter').each(function () {
            const $select = $(this);
            if ($select.data('chosen')) {
                return;
            }

            if (typeof window.initPartyChosen === 'function') {
                window.initPartyChosen($select, {
                    placeholder_text_single: $select.data('placeholder') || 'All Parties',
                });
                return;
            }

            $select.chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: $select.data('placeholder') || 'All Parties'
            });

            if (typeof window.attachPartyChosenPcodeSort === 'function') {
                window.attachPartyChosenPcodeSort($select);
            }
        });
    });
</script>
