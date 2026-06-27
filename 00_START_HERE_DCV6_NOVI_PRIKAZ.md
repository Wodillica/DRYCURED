# SIDRO — Novi DCV6 prikaz recepata (drycured.com)

## Status: PLANIRANJE ZAVRŠENO, IMPLEMENTACIJA NIJE POČELA

## Ključna odluka (potvrđena s Davorom)
Gradimo NOVI, jedinstveni sustav prikaza recepata. Stari sustav
(drycured-recipe-view-v1.php, ~7000 redaka) se NE dira i NE briše —
ostaje kao fallback za nemigrirane recepte.

## Arhitektura novog sustava
1. JEDNO meta polje po receptu: `_dry_recipe_profile` (JSON).
   Sadrži: identitet, sastojke (materials/spices/liquids), granulaciju,
   crijeva (s promjerom), kompletan timeline (faza/dan/temperatura/
   vlaga/kritična_točka), greške, sigurnost, posluživanje, izvore.
   Format dokazan na postu 3315 (Vrgorački kulen) — RADI.
2. JEDNA PHP funkcija (novi mu-plugin, JOŠ NAPISAN) koja čita to polje
   i renderira HTML. Bez markdown parsiranja, bez family-defaults,
   bez hardkodiranih recepata, bez sekundarnih dodataka.
3. Migracija je postupna, recept po recept.

## Poznati problem koji novi sustav rješava
HR-SL-001 (Slavonski kulen) ima HARDKODIRANU PHP funkciju
`dcv5_slavonski_kulen_profile($post_id)` u drycured-recipe-view-v1.php
koja IGNORIRA `_dry_verified_process` i sve ostale meta izmjene.
Zato izmjene dimljenja (trajanje ciklusa) nisu vidljive na webu
za taj recept. PRVI KANDIDAT za migraciju na novi sustav.

## Što već radi (ne dirati, samo nadograditi)
- `_dry_recipe_full_profile_json` meta polje + patch na početku
  `dcv5_get_recipe_profile()` u drycured-recipe-view-v1.php
  (linija ~1636) — ako ovo polje postoji s `title`, vraća se
  DIREKTNO, bez ostatka starog sustava. Ovo JE zametak novog sustava.
- `aaa-drycured-registry-01B.php` (preimenovan s prefiksom aaa- da
  se učita PRIJE drycured-recipe-view-v1.php po abecedi) — registrira
  Klaster 01-B kodove (HR-IS-001, HR-LI-001, HR-DA-001 do 004 itd.)
- `zzz-drycured-recipe-overrides.php` — čita `_dry_recipe_overrides`
  JSON po `_dry_recipe_id`, override-a materials/spices/timeline.
  Hook ubačen direktno u `dcv12_apply_final_profile_overrides()`.
- `drycured-granulation-display-core.php` — čita
  `_dry_verified_process.mljevenje` i ubacuje "Obavezna granulacija"
  box gdje pronađe <h*>Mljevenje</h*> u HTML-u. NEOVISAN sustav,
  mora se posebno popuniti za svaki recept.

## Izvori podataka (project knowledge, već indeksirano)
- main_under_75mb.pdf = Enciklopedija suhomesnatih proizvoda Tom 2
  (autor Davor Savicki). Tablice 17.1-17.4 = standardizirane recepture
  (trajna kobasica, kulen, panceta, pršut). PAŽNJA: Enciklopedija ima
  unutarnju nekonzistentnost (13.3 vs 17.2 o mljevenju vs rezanju
  kulena) — uvijek provjeriti protiv vanjskih izvora prije primjene.
- Pavičić, stocarstvo.com (vojvođanski/srpski recepti — drugi
  proizvodi pod istim imenom, ne brkati s hrvatskim verzijama),
  TOM2_*.md dokumenti u /var/www/html/wp-content/uploads/drycured-import/,
  Moja Kuhinja recepti, etnografski docx/odt/txt dokumenti.
- WP alati tool_03_dnevnik_serije.jsx, tool_04_planer_dimljenja.jsx
  sadrže STVARNE proizvođačke vrijednosti (npr. dimljenje 3h ciklus,
  12h odmor) — koristiti kao izvor za vremenske parametre.

## Ispravljeno u ovoj sesiji
- post 2972 (Slavonski kulen master): mljevenje ispravljeno s
  "rešetka 8mm" na "ručno rezanje nožem na kockice 8-12mm" (potvrđeno
  s više izvora — Wikipedia, baranjskikulen.hr, vjesnik.com.au)
- post 2972: dodano dimljenje trajanje_ciklusa_h=3, odmor=12h u
  `_dry_verified_process` (NE utječe na web prikaz zbog hardkodirane
  funkcije — vidi gore)
- postovi 3308/3311/3315 (Istarska/Lička/Vrgorački): kompletan
  `_dry_recipe_full_profile_json` upisan i RADI na webu

## Sljedeći korak (gdje nastaviti)
1. Napisati novi mu-plugin (čist renderer za `_dry_recipe_profile`)
2. Migrirati HR-SL-001 prvi (zamjena za hardkodiranu funkciju)
3. Git commit + push na svaki uspješan korak
4. Nastaviti kroz Klaster 01-A, 01-B, dalje — recept po recept,
   prema dokumentima u project knowledge

## Pravilo komunikacije (Davor + Claude)
- project_knowledge_search PRIJE pisanja JSON-a za bilo koji recept
- Veći koraci, manje round-trips
- Jasno reći "ne znam, treba provjeriti" umjesto pretpostavki
- Git commit+push na kraju svakog bloka rada

## Naredba za početak novog razgovora
```bash
cat /root/DRYCURED_GITHUB/00_START_HERE_DCV6_NOVI_PRIKAZ.md
```
