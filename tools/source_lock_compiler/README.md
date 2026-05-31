# Drycured Source Lock Compiler v1.1

Pilot scope: `HR-SL-001` / `Slavonski kulen`.

This tool builds source-locked recipe JSON from the strict primary source
document declared in `source_recipes/hr/source_priority_manifest.yml`. It does
not publish recipes, change WordPress post status, edit WordPress HTML, or write
to `/var/www/html`.

## Run

From the project root:

```bash
python3 tools/source_lock_compiler/drycured_source_lock_compiler_v1.py --recipe HR-SL-001 --dry-run
```

If running from another directory, pass the root explicitly:

```bash
python3 tools/source_lock_compiler/drycured_source_lock_compiler_v1.py --root /root/DRYCURED_GITHUB --recipe HR-SL-001 --dry-run
```

## Inputs

The compiler first reads:

- `source_recipes/hr/source_priority_manifest.yml`

The manifest currently requires:

- `primary_source: TOM2_HR_SOURCE_LOCK_MASTER.md`
- `authority_mode: strict`

In strict mode the compiler uses only:

- `source_recipes/hr/TOM2_HR_SOURCE_LOCK_MASTER.md`

If that file is missing, the run fails with:

```text
Authoritative source file TOM2_HR_SOURCE_LOCK_MASTER.md is missing.
```

In this local sandbox, if `/root/DRYCURED_GITHUB` does not exist, the same
layout is created under `./DRYCURED_GITHUB`.

Recipe-to-section mapping is stored in:

- `tools/source_lock_compiler/recipe_id_map.yml`

For `HR-SL-001`, the only authority block is:

```text
### 1. Slavonski kulen (OZP EU)
```

The later legacy heading `## 1. Slavonski kulen (PDO EU)` is not used as
authority. If legacy values are present outside the authority block, audit logs:

```text
LEGACY_CONFLICT_BLOCK_FOUND_OUTSIDE_AUTHORITY=YES
```

## Outputs

The compiler writes:

- `build/source_locked_json/HR-SL-001.source_locked.json`
- `build/source_lock_audit/HR-SL-001.source_lock_audit.txt`
- `build/source_lock_audit/source_lock_manifest.csv`

## Audit Status

`PASS` means the source-locked JSON contains every required pilot value and no
forbidden old values.

`REVIEW` means no forbidden old values were found, but one or more source values
or process fields are missing or unclear. A human must review the source.

`FAIL` means the strict primary source is missing, the authority block is
missing, or a forbidden old value was found in the source-locked JSON.

WordPress must not be updated unless the audit status is `PASS`.
