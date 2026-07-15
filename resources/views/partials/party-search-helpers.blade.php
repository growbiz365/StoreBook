<script>
(function () {
    window.formatPartyDisplayText = window.formatPartyDisplayText || function (party) {
        if (!party) {
            return '';
        }
        const name = (party.name || '').trim();
        const pcode = (party.pcode || '').trim();
        if (pcode && name) {
            return `${pcode} - ${name}`;
        }
        return pcode || name;
    };

    window.sortPartiesForSearch = window.sortPartiesForSearch || function (parties, term) {
        const upper = (term || '').trim().toUpperCase();

        const score = (party) => {
            const name = (party?.name || '').trim().toUpperCase();
            const pcode = (party?.pcode || '').trim().toUpperCase();
            if (upper === '') return 6;
            if (pcode === upper) return 0;
            if (name === upper) return 1;
            if (pcode.startsWith(upper)) return 2;
            if (name.startsWith(upper)) return 3;
            if (pcode.includes(upper)) return 4;
            if (name.includes(upper)) return 5;
            return 6;
        };

        return [...(parties || [])].sort((a, b) => {
            const diff = score(a) - score(b);
            return diff !== 0 ? diff : (a.name || '').localeCompare(b.name || '');
        });
    };
})();
</script>
