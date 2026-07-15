#!/usr/bin/env python3
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

FILES = [
    'resources/views/sale_returns/edit.blade.php',
    'resources/views/quotations/create.blade.php',
    'resources/views/quotations/edit.blade.php',
    'resources/views/purchases/create.blade.php',
    'resources/views/purchases/edit.blade.php',
    'resources/views/purchase_returns/create.blade.php',
    'resources/views/purchase_returns/edit.blade.php',
]

REPLACEMENTS = [
    (
        """                    // Create display text with name and additional info
                    let displayText = party.name;
                    if (party.cnic) {
                        displayText += ` (CNIC: ${party.cnic})`;
                    }
                    
                    partySearchInput.value = displayText;""",
        """                    partySearchInput.value = typeof window.formatPartyDisplayText === 'function'
                        ? window.formatPartyDisplayText(party)
                        : party.name;""",
    ),
    (
        """                        // Create display text with name and additional info
                        let displayText = party.name;
                        if (party.cnic) {
                            displayText += ` (CNIC: ${party.cnic})`;
                        }
                        
                        partySearchInput.value = displayText;""",
        """                        partySearchInput.value = typeof window.formatPartyDisplayText === 'function'
                            ? window.formatPartyDisplayText(party)
                            : party.name;""",
    ),
    (
        """        let displayText = party.name;
        if (party.cnic) {
            displayText += ` (CNIC: ${party.cnic})`;
        }
        
        this.input.value = displayText;""",
        """        this.input.value = typeof window.formatPartyDisplayText === 'function'
            ? window.formatPartyDisplayText(party)
            : party.name;""",
    ),
    (
        """                let displayText = party.name;
                if (party.cnic) {
                    displayText += ` (CNIC: ${party.cnic})`;
                }
                
                this.input.value = displayText;""",
        """                this.input.value = typeof window.formatPartyDisplayText === 'function'
                    ? window.formatPartyDisplayText(party)
                    : party.name;""",
    ),
    (
        """                // Create display text with name and additional info
                let displayText = party.name;
                if (party.cnic) {
                    displayText += ` (CNIC: ${party.cnic})`;
                }
                
                this.input.value = displayText;""",
        """                this.input.value = typeof window.formatPartyDisplayText === 'function'
                    ? window.formatPartyDisplayText(party)
                    : party.name;""",
    ),
]

PARTY_FOREACH = re.compile(
    r"(\s*)parties\.forEach\(\(party, index\) => \{\s*"
    r"(?:// Validate party data\s*)?"
    r"if \(!party \|\| !party\.id \|\| !party\.name\) \{\s*"
    r"return;(?: // Skip invalid parties)?\s*\}\s*"
    r"const resultItem = document\.createElement\('div'\);\s*"
    r"resultItem\.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';\s*"
    r"resultItem\.dataset\.partyId = party\.id;\s*"
    r"resultItem\.dataset\.partyName = party\.name;\s*"
    r"resultItem\.dataset\.partyCnic = party\.cnic \|\| '';\s*"
    r"resultItem\.innerHTML = `\s*"
    r"<div class=\"font-medium text-gray-900\">\$\{party\.name\}</div>\s*"
    r"<div class=\"text-sm text-gray-500\">\s*"
    r"\$\{party\.cnic \? `CNIC: \$\{party\.cnic\}` : ''\}\s*"
    r"</div>\s*`;\s*"
    r"resultItem\.addEventListener\('click', \(\) => \{\s*"
    r"this\.(selectParty|selectItem)\(party\);\s*"
    r"\}\);",
    re.MULTILINE,
)


def party_foreach_repl(match: re.Match) -> str:
    indent = match.group(1)
    method = match.group(2)
    i = indent
    return (
        f"{i}const sortedParties = typeof window.sortPartiesForSearch === 'function'\n"
        f"{i}    ? window.sortPartiesForSearch(parties, this.searchTerm)\n"
        f"{i}    : parties;\n\n"
        f"{i}sortedParties.forEach((party, index) => {{\n"
        f"{i}    if (!party || !party.id || !party.name) {{\n"
        f"{i}        return;\n"
        f"{i}    }}\n"
        f"{i}    \n"
        f"{i}    const resultItem = document.createElement('div');\n"
        f"{i}    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';\n"
        f"{i}    resultItem.dataset.partyId = party.id;\n"
        f"{i}    resultItem.dataset.partyName = party.name;\n"
        f"{i}    resultItem.dataset.partyPcode = party.pcode || '';\n"
        f"{i}    resultItem.dataset.partyCnic = party.cnic || '';\n"
        f"{i}    \n"
        f"{i}    resultItem.innerHTML = `\n"
        f"{i}        <div class=\"font-medium text-gray-900\">${{typeof window.formatPartyDisplayText === 'function' ? window.formatPartyDisplayText(party) : party.name}}</div>\n"
        f"{i}        <div class=\"text-sm text-gray-500\">\n"
        f"{i}            ${{party.phone_no ? `Phone: ${{party.phone_no}}` : (party.cnic ? `CNIC: ${{party.cnic}}` : '')}}\n"
        f"{i}        </div>\n"
        f"{i}    `;\n"
        f"{i}    \n"
        f"{i}    resultItem.addEventListener('click', () => {{\n"
        f"{i}        this.{method}(party);\n"
        f"{i}    }});"
    )


def add_helpers_include(text: str) -> str:
    if 'party-search-helpers' in text:
        return text
    patterns = [
        (
            '\n<script>\n// Clear data immediately',
            "\n@include('partials.party-search-helpers')\n\n<script>\n// Clear data immediately",
        ),
        (
            '\n    <script>\n    // Clear data immediately',
            "\n    @include('partials.party-search-helpers')\n\n    <script>\n    // Clear data immediately",
        ),
        (
            '\n    </template>\n\n    <script>\n    document.addEventListener',
            "\n    </template>\n\n    @include('partials.party-search-helpers')\n\n    <script>\n    document.addEventListener",
        ),
    ]
    for old, new in patterns:
        if old in text:
            return text.replace(old, new, 1)
    return text


def add_pcode_to_select_first(text: str) -> str:
    pairs = [
        (
            "                name: firstResult.dataset.partyName,\n                cnic: firstResult.dataset.partyCnic",
            "                name: firstResult.dataset.partyName,\n                pcode: firstResult.dataset.partyPcode || '',\n                cnic: firstResult.dataset.partyCnic",
        ),
        (
            "                name: highlightedResult.dataset.partyName,\n                cnic: highlightedResult.dataset.partyCnic",
            "                name: highlightedResult.dataset.partyName,\n                pcode: highlightedResult.dataset.partyPcode || '',\n                cnic: highlightedResult.dataset.partyCnic",
        ),
        (
            "                        name: firstResult.dataset.partyName,\n                        cnic: firstResult.dataset.partyCnic",
            "                        name: firstResult.dataset.partyName,\n                        pcode: firstResult.dataset.partyPcode || '',\n                        cnic: firstResult.dataset.partyCnic",
        ),
        (
            "                        name: highlightedResult.dataset.partyName,\n                        cnic: highlightedResult.dataset.partyCnic",
            "                        name: highlightedResult.dataset.partyName,\n                        pcode: highlightedResult.dataset.partyPcode || '',\n                        cnic: highlightedResult.dataset.partyCnic",
        ),
    ]
    for old, new in pairs:
        if old in text and new not in text:
            text = text.replace(old, new)
    return text


def main() -> None:
    for rel in FILES:
        path = ROOT / rel
        text = path.read_text(encoding='utf-8')
        original = text
        for old, new in REPLACEMENTS:
            text = text.replace(old, new)
        text = PARTY_FOREACH.sub(party_foreach_repl, text)
        text = add_pcode_to_select_first(text)
        text = add_helpers_include(text)
        if text != original:
            path.write_text(text, encoding='utf-8')
            print(f'updated {rel}')
        else:
            print(f'no change {rel}')


if __name__ == '__main__':
    main()
