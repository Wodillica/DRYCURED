# Drycured blog duplicate cleanup finish v1.1

Vrijeme: 2026-06-07_17-00-01

## Što se dogodilo

Prethodni duplicate cleanup je sadržajno prošao, ali je javni HTTP QA pao na ID 1465 jer je skripta provjeravala i ne-javne postove kao da moraju vratiti HTTP 200.

## Ispravak u ovom prolazu

- Ponovno je provjeren duplicate audit.
- HTTP QA sada provjerava samo javno objavljene `publish` članke.
- Ne-javni postovi se evidentiraju, ali ne blokiraju javni QA.
- ID 1465 je posebno evidentiran.

## QA

- Duplicate HIGH: 0
- Duplicate REVIEW: 0
- Javni publish URL-ovi: 200
- Sample članak `Miris, boja i presjek`: bez `Dodatna radna napomena`

## Prethodni cleanup

`/root/drycured-blog-backups/duplicate-content-cleanup-2026-06-07_16-50-07`

## Finish backup

`/root/drycured-blog-backups/duplicate-content-cleanup-finish-v1-1-2026-06-07_17-00-01`
