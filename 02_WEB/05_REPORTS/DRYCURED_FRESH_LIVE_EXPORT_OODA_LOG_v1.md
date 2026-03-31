# DRYCURED_FRESH_LIVE_EXPORT_OODA_LOG_v1

Status: OODA log v1  
Projekt: drycured.com  
Datum: 2026-03-31  
Jezik rada: hrvatski

---

## Observe

Prethodni content import failao je jer lokalno nije postojao stvarni live SQL dump niti stvarni `wp-content` payload. Potvrđeno je i da lokalno ne postoji `page 101` ni njegov stvarni sadržajni ekvivalent.

---

## Orient

Jedini operativno ispravan sljedeći korak bio je novi read-only export sa stvarnog live origina. Za vjernu lokalnu obnovu nisu dovoljni parcijalni snapshoti ili samo HTML izlaz; potreban je kompletan SQL sloj i `wp-content`.

---

## Decide

Odabran je minimalni, ali stvarni export set:

- puni SQL dump baze `drycured`
- puni `wp-content` archive

Artefakti su spremljeni direktno u `E:\SWAB_V2\website\drycured_local\imports\...` kako bi sljedeći import korak mogao raditi bez dodatnog premještanja payload-a.

---

## Act

Iz live origina su izvezeni i lokalno spremljeni:

- `E:\SWAB_V2\website\drycured_local\imports\live_db\drycured_live_fresh_2026-03-31.sql`
- `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\drycured_wp_content_fresh_2026-03-31.tar.gz`

Nakon toga su validirani:

- postojanje
- veličina
- čitljivost arhive
- prisutnost `uploads`
- veza s live Home logikom kroz `page_on_front = 101`

---

## Preporučeni sljedeći korak

- `live-content import retry u lokalni runtime`

