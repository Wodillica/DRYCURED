# DRYCURED_LIVE_CONTENT_FILE_MAP_v1

Status: file map v1  
Projekt: drycured.com

---

## 1. Source-of-truth kandidati

| Putanja | Stanje | Pouzdanost | Napomena |
|---|---|---|---|
| `E:\SWAB_V2\website\drycured_local\imports\server_home\drycured_home_server_snapshot_2026-03-31.html` | exists | medium | stvarni live HTML snapshot, ali nije DB import artefakt |
| `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\wp-content` | exists | low to medium | djelomičan content filesystem sloj bez baze |

---

## 2. Sumnjivi / djelomični artefakti

| Putanja | Stanje | Pouzdanost | Napomena |
|---|---|---|---|
| `imports/live_wp_content/wp-content/themes/astra` | partial | low | nije dovoljan za čistu aktivaciju bez popravka |
| `imports/live_wp_content/wp-content/plugins/elementor` | partial | low | postoji direktorij, ali ne kao kompletan radni plugin baseline |
| `imports/live_wp_content/wp-content/plugins/elementor-pro` | partial | low | nema potvrđen radni plugin entry |
| `imports/live_wp_content/wp-content/uploads` | exists but empty | low | nema stvarnih media datoteka |
| `wordpress/wp-content/*_2026-03-31_recovery_backup` | exists | low | sačuvani polomljeni payloadi, ne source-of-truth |

---

## 3. Nedostajući kritični dijelovi

| Artefakt | Status | Zašto je kritičan |
|---|---|---|
| live SQL dump (`drycured_live.sql` ili ekvivalent) | missing | bez njega nema stvarne baze stranica, objava, menija ni page `101` |
| WXR/XML export | missing | nema content fallback izvoza |
| All-in-One / Duplicator paket | missing | nema gotovog backup restore artefakta |
| stvarni media/uploads payload | missing | bez njega nema vjerne lokalne media kopije |
| lokalni Elementor page `101` data artefakt | missing | Home se ne može vjerno vratiti samo iz filesystema |

---

## 4. Zaključak

Trenutno lokalno postoji:

- runtime
- djelomičan `wp-content`
- read-only live HTML snapshot

Trenutno lokalno ne postoji:

- stvarni source-of-truth content import set potreban za live-content obnovu
