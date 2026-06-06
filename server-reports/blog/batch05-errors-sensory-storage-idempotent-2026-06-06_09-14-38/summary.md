# Drycured blog — Batch 05 idempotent finish

Vrijeme: 2026-06-06_09-14-38

## Obrađeni članci

- ID 2133 — Case-hardening
- ID 2138 — Plemenita plijesan
- ID 2141 — Najčešće greške kod kobasica
- ID 2854 — Senzorska kontrola
- ID 2842 — Čuvanje nakon zrenja

## Korekcija

Preflight je promijenjen u idempotentan način: dopušta originalni naslov ili ciljni novi naslov.
Time se sprječava pad ako je članak već djelomično dobio novi naslov prije završnog upisa.

## QA

Globalni DRY QA mora proći prije WordPress upisa.

## Backup

`/root/drycured-blog-backups/blog-batch05-idempotent-finish-2026-06-06_09-14-38`
