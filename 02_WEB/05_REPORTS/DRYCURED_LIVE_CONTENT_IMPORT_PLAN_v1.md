# DRYCURED_LIVE_CONTENT_IMPORT_PLAN_v1

Status: plan zaključan nakon lokalne provjere  
Projekt: drycured.com

---

## 1. Strategija importa

Cilj stvarnog content importa ostaje:

1. import baze  
2. import `wp-content`  
3. provjera Home/page `101`  
4. provjera medija i Elementor sadržaja  
5. tek onda Home interaction copy build

---

## 2. Minimal path

Najmanji ispravan put do stvarne lokalne live-content kopije je:

1. osigurati valjani live SQL dump  
2. potvrditi ili ponovno izvesti stvarni `wp-content/uploads` payload  
3. importati SQL u oporavljeni lokalni runtime  
4. uskladiti URL-ove na `http://localhost:8085`  
5. provjeriti postoji li lokalno page `101` ili njegov sadržajni ekvivalent  
6. provjeriti Elementor sadržaj i Astru  
7. tek tada krenuti na Home interaction copy build

---

## 3. Fallback path

Ako SQL dump nije odmah dostupan, fallback nije “graditi iz glave”.

Fallback je:

- zadržati bootable runtime
- koristiti HTML snapshot samo kao referentnu analitičku bazu
- ne tvrditi da postoji live-content kopija
- pribaviti novi export pa tek tada raditi stvarni import

---

## 4. Korišteni lokalni izvori

Pregledani lokalni izvori:

- `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\wp-content`
- `E:\SWAB_V2\website\drycured_local\imports\server_home\drycured_home_server_snapshot_2026-03-31.html`
- `E:\SWAB_V2\website\drycured_local\backups\DRYCURED_LOCAL_RUNTIME_RECOVERY_BASELINE_2026-03-31.sql`

Pouzdanost:

- `server_home` snapshot je dobar za analizu, ali nije DB import artefakt
- recovery baseline SQL nije live SQL dump
- `live_wp_content` je djelomičan i bez stvarnog uploads payloada

---

## 5. Zaključak

Minimal path do live-content kopije trenutno je blokiran nedostatkom source-of-truth sadržajnih artefakata. Zato se sadržajni import ne smije lažno označiti kao izveden.
