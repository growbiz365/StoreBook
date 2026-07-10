#!/usr/bin/env python3
"""Extract pcode, name, and phone (Contact) from Customer/Supplier BioReport xlsx files."""

from __future__ import annotations

import json
import sys
from pathlib import Path

import openpyxl

EMPTY_CONTACT_VALUES = {"", "null", "none", "n/a", "-", "//"}


def norm(value) -> str:
    if value is None:
        return ""
    return str(value).strip()


def norm_contact(value) -> str:
    contact = norm(value)
    if contact.lower() in EMPTY_CONTACT_VALUES:
        return ""
    return contact


def find_columns(rows: list[tuple]) -> tuple[int, int, int, int | None]:
    for index, row in enumerate(rows):
        cells = [norm(c) for c in (row or ())]
        pcode_col = None
        name_col = None
        contact_col = None
        for col, cell in enumerate(cells):
            lowered = cell.lower()
            if lowered in {"pcode", "personnal code", "code"}:
                pcode_col = col
            elif lowered == "name":
                name_col = col
            elif lowered == "contact":
                contact_col = col
        if pcode_col is not None and name_col is not None:
            return index, pcode_col, name_col, contact_col
    raise RuntimeError("Could not locate pcode/name header row")


def extract_file(path: Path, source: str, parties: dict, duplicates: list) -> dict:
    workbook = openpyxl.load_workbook(path, read_only=True, data_only=True)
    worksheet = workbook.active
    rows = list(worksheet.iter_rows(values_only=True))
    workbook.close()

    header_idx, pcode_col, name_col, contact_col = find_columns(rows)
    imported = 0
    skipped = 0
    with_phone = 0

    for row in rows[header_idx + 1 :]:
        cells = list(row or ())
        pcode = norm(cells[pcode_col] if pcode_col < len(cells) else "")
        name = norm(cells[name_col] if name_col < len(cells) else "")
        phone_no = norm_contact(
            cells[contact_col] if contact_col is not None and contact_col < len(cells) else ""
        )
        if not pcode or not name:
            skipped += 1
            continue

        key = pcode.upper()
        if key in parties:
            existing = parties[key]
            if existing["name"] != name:
                duplicates.append(
                    {
                        "pcode": pcode,
                        "existing": existing,
                        "new": {
                            "pcode": pcode,
                            "name": name,
                            "phone_no": phone_no,
                            "source": source,
                        },
                    }
                )
            if phone_no and not existing.get("phone_no"):
                existing["phone_no"] = phone_no
        else:
            parties[key] = {
                "pcode": pcode,
                "name": name,
                "phone_no": phone_no,
                "source": source,
            }
            imported += 1
            if phone_no:
                with_phone += 1

    return {
        "imported": imported,
        "skipped_empty": skipped,
        "with_phone": with_phone,
        "header_row": header_idx + 1,
    }


def main() -> int:
    base = Path(sys.argv[1]) if len(sys.argv) > 1 else Path(__file__).resolve().parents[1]
    output = (
        Path(sys.argv[2])
        if len(sys.argv) > 2
        else base / "storage" / "app" / "parties_bio_import_b5.json"
    )

    files = [
        ("customer_bio", base / "Customer_BioReport.xlsx"),
        ("customer_balance", base / "CustomerBioDataReport.xlsx"),
        ("supplier_bio", base / "Supplier_BioReport.xlsx"),
    ]

    parties: dict[str, dict] = {}
    duplicates: list[dict] = []
    per_file: dict[str, dict] = {}

    for source, path in files:
        if not path.is_file():
            print(f"Missing file: {path}", file=sys.stderr)
            return 1
        per_file[source] = extract_file(path, source, parties, duplicates)

    payload = {
        "parties": sorted(parties.values(), key=lambda item: item["pcode"].upper()),
        "stats": {
            "total_unique": len(parties),
            "with_phone": sum(1 for item in parties.values() if item.get("phone_no")),
            "per_file": per_file,
            "name_conflicts": len(duplicates),
        },
    }

    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")

    print(f"Wrote {len(parties)} unique parties to {output}")
    for source, stats in per_file.items():
        print(f"  {source}: {stats}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
