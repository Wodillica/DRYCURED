# 3535 admin-only preview bridge plan v1

Status: **PLAN_READY_ADMIN_ONLY_PREVIEW_BRIDGE**

Ovaj korak ne mijenja WordPress. Planira kako pregledati privatni clone `3535` bez javnog izlaganja i bez promjene postojećeg Drycured prikaza.

## Zaključak prethodnog QA

- `3535` je private i nije javno izložen.
- `_dry_recipe_id` je upisan.
- `_dry_recipe_id` sam nije aktivirao kartični renderer.
- Sadržaj je prisutan, ali ostaje raw markdown u internom snapshotu.
- Javni update ostaje zabranjen.

## Sigurnosne granice

- WordPress write allowed now: `false`
- Public update allowed: `false`
- Source post write allowed: `false`
- Renderer change allowed: `false`
- Allowed target for future bridge: private clone `3535` only
- Forbidden target: public source `3042`

## Manualni admin preview linkovi

- Admin edit URL: `https://drycured.com/wp-admin/post.php?post=3535&action=edit`
- Logged-in front preview URL: `https://drycured.com/?post_type=dry_recipe&p=3535&preview=true`
- Public unauth URL expected 404: `https://drycured.com/?post_type=dry_recipe&p=3535`

## Preporučeni smjer

1. Prvo otvoriti `logged_in_front_preview_url` kao prijavljeni administrator i vizualno provjeriti aktivira li stvarni front-end kontekst bolji prikaz.
2. Ako i dalje prikazuje raw markdown, napraviti mali MU-plugin **admin-only preview bridge**.
3. Bridge mora raditi samo u admin/logged-in kontekstu, samo za `post_status=private`, samo uz `PRIVATE_CLONE_ONLY`, samo za post `3535`, i mora slati `noindex` / bez javnog SEO izlaganja.
4. Bridge ne smije mijenjati javni renderer ni javni recept `3042`.

## Moguće implementacije

### Opcija A — Use existing logged-in WP preview first

- Rizik: `lowest`
- Opis: Prvo ručno otvoriti logged-in front preview URL kao administrator. Ako se u stvarnom browser kontekstu aktivira prikaz, bridge nije potreban.

### Opcija B — Admin-only preview page in wp-admin

- Rizik: `low`
- Opis: Dodati wp-admin stranicu koja za post 3535 prikazuje strukturirani preview iz postojećih meta podataka. Nije javni renderer i nije indeksabilno.

### Opcija C — Temporary private preview front route with nonce

- Rizik: `medium`
- Opis: Front route dostupan samo logged-in adminu, s nonceom i noindex headerima. Koristiti samo ako admin page nije dovoljan.

## QA provjere plana

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| Clone 3535 je private | PASS | BLOCKER | Admin-only preview bridge smije raditi samo na privatnom cloneu. |
| Clone 3535 je dry_recipe | PASS | BLOCKER | Bridge ne smije raditi na drugim tipovima zapisa. |
| Source 3042 je publish | PASS | BLOCKER | Source se ne smije dirati. |
| Clone ima PRIVATE_CLONE_ONLY | PASS | BLOCKER | Bez ove oznake bridge ne smije raditi. |
| Clone je vezan na source 3042 | PASS | BLOCKER | Mora biti jasno vezan na source. |
| Public update je 0 | PASS | BLOCKER | Bridge ne smije otvarati javni update tok. |
| Public verified je 0 | PASS | BLOCKER | Recept nije verificiran za javnu objavu. |
| Clone ima _dry_recipe_id | PASS | MAJOR | Meta normalizer je uspješno odradio minimalni ID. |
| Clone ima _dry_recipe_full_markdown | PASS | MAJOR | Bridge mora imati izvor sadržaja. |
| Clone ima _dry_recipe_sections | PASS | MAJOR | Bridge može koristiti strukturirane sekcije. |
| Clone ima _dry_verified_process | PASS | MAJOR | Bridge može koristiti procesne podatke. |
| Post-patch renderer nije aktiviran | PASS | MAJOR | Potvrđuje potrebu za bridge planom. |
| Repo renderer plugin postoji | PASS | MAJOR | Ne smijemo raditi novi dizajn bez pregleda postojećeg plugin sloja. |
| Live renderer plugin postoji | PASS | MAJOR | Live i repo renderer moraju biti dostupni. |

## Odluka

Ne dodavati više meta ključeva naslijepo. Sljedeći praktični korak je ručna admin preview provjera; ako ne uspije, onda planirano izraditi admin-only preview bridge kao odvojeni MU-plugin s vrlo uskim guardovima.

Javni WordPress update i dalje nije dopušten.
