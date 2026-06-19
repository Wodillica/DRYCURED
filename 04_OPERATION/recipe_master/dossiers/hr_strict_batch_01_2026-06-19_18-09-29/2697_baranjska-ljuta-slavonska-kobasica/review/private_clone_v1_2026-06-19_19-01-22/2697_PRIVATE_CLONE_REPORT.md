# 2697 Baranjska kobasica – ljuta varijanta private clone v1

Status: **PRIVATE_CLONE_CREATED_QA_PASS**

Ovaj korak stvara samo privatni preview clone. Javni recept `2697` nije mijenjan.

## Sažetak

- Source post ID: `2697`
- Clone ID: `3537`
- Clone status: `private`
- Clone type: `dry_recipe`
- Admin preview URL: `https://drycured.com/?post_type=dry_recipe&p=3537`
- Admin edit URL: `https://drycured.com/wp-admin/post.php?post=3537&action=edit`
- Source unchanged: `true`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Publicly exposed: `false`
- Blocker fail total: `0`

## QA provjere

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| clone_created | PASS | BLOCKER | Private clone mora biti kreiran. |
| clone_private | PASS | BLOCKER | Clone mora biti private. |
| clone_type | PASS | BLOCKER | Clone mora biti dry_recipe. |
| source_unchanged | PASS | BLOCKER | Source post 2697 ne smije biti promijenjen. |
| preview_mode | PASS | BLOCKER | Preview mode mora biti PRIVATE_CLONE_ONLY. |
| source_link | PASS | BLOCKER | Clone mora biti vezan na source 2697. |
| public_update_zero | PASS | BLOCKER | Public update mora biti 0. |
| public_verified_zero | PASS | MAJOR | Public verified mora biti 0. |
| recipe_id_present | PASS | BLOCKER | Recipe ID mora biti upisan. |
| sections_present | PASS | BLOCKER | Sections meta mora biti prisutan. |
| verified_process_present | PASS | BLOCKER | Verified process meta mora biti prisutan. |
| full_markdown_present | PASS | MAJOR | Full markdown meta mora biti prisutan. |
| not_publicly_exposed | PASS | BLOCKER | Privatni clone ne smije biti javno izložen neprijavljenom korisniku. |

## Ručni pregled

Korisnik treba otvoriti admin preview URL dok je prijavljen kao administrator:

`https://drycured.com/?post_type=dry_recipe&p=3537`

Očekuje se strukturirani Drycured kartični prikaz. Ako se vidi samo sirovi Markdown ili interni blokovi na javno neprikladan način, treba napraviti preview repair.
