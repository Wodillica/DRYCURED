# DRYCURED_FRESH_LIVE_EXPORT_FILE_MAP_v1

Status: operativna file mapa v1  
Projekt: drycured.com  
Datum: 2026-03-31  
Jezik rada: hrvatski

---

## 1. Lokalni export artefakti

### SQL dump

- Naziv: `drycured_live_fresh_2026-03-31.sql`
- Lokalna putanja: `E:\SWAB_V2\website\drycured_local\imports\live_db\drycured_live_fresh_2026-03-31.sql`
- Veličina: `4,947,968` bytes
- Svrha: obnova live baze, uključujući sadržaj stranica, objava, opcija, Home logike i `page_on_front` reference
- Pouzdanost: visoka
- Status: spremno za import

### wp-content payload

- Naziv: `drycured_wp_content_fresh_2026-03-31.tar.gz`
- Lokalna putanja: `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\drycured_wp_content_fresh_2026-03-31.tar.gz`
- Veličina: `2,039,278,758` bytes
- Svrha: obnova `uploads`, tema, pluginova i drugih sadržajnih tragova iz `wp-content` sloja
- Pouzdanost: visoka
- Status: spremno za raspakiravanje/sync

---

## 2. Forenzički validacijski tragovi

- SQL dump je izvezen iz live baze `drycured`
- live origin koristi WordPress root `/var/www/html`
- `page_on_front` na live sustavu pokazuje na `101`
- page `101` na live sustavu je `Home`
- `tar` listing potvrđuje prisutnost `wp-content/uploads`
- `tar` listing potvrđuje stvarne medijske podmape iz više godina

---

## 3. Operativna procjena

Ovaj file set je dovoljno jak za:

- retry live-content importa u lokalni runtime
- obnovu Home/page `101` sadržaja iz baze
- obnovu medija i `wp-content` sloja

Ovaj file set sam po sebi još ne potvrđuje uspješan lokalni restore.

