# 3535 manual admin preview QA v1

Status: **PASS_ADMIN_PRIVATE_DIRECT_URL**

## Što je provjereno

Ručno je provjeren privatni clone `3535` u pregledniku dok je korisnik prijavljen u WordPress kao administrator.

## Rezultat

- `preview=true` URL prikazuje 404 i nije koristan za pregled.
- Admin edit ekran prikazuje sirovi markdown u editoru, što je očekivano.
- Direktni privatni URL dok je administrator prijavljen prikazuje strukturirani Drycured/kartični prikaz.
- Privatni clone ostaje neprikladan za javnu objavu jer recept još ima aktivne blokade izvora, starter kulture i dimljenja.
- Nije potreban dodatni admin-only preview bridge plugin u ovoj fazi.

## Ispravni URL za admin pregled

`https://drycured.com/?post_type=dry_recipe&p=3535`

## URL koji nije koristan

`https://drycured.com/?post_type=dry_recipe&p=3535&preview=true`

## Sigurnosna odluka

- Ne raditi javni update.
- Ne mijenjati javni post `3042`.
- Ne mijenjati renderer.
- Ne dodavati novi bridge plugin dok direktni privatni URL radi za administratorski pregled.
- Sljedeći korak je sadržajna/tehnološka odluka o receptu: zatvaranje blokada ili ostavljanje u internom dosje-statusu.

## Zaključak

Opcija A iz admin-only preview bridge plana je prošla. Privatni clone `3535` može služiti kao interni vizualni preview model za daljnju provjeru, ali ne smije u javnu objavu.
