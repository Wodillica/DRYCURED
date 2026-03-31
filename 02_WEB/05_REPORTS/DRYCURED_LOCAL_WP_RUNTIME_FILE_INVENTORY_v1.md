# DRYCURED_LOCAL_WP_RUNTIME_FILE_INVENTORY_v1

Status: inventory v1  
Projekt: drycured.com

---

## 1. Kritične mape i datoteke

| Putanja | Status | Napomena |
|---|---|---|
| `E:\SWAB_V2\website\drycured_local` | exists | glavni local runtime root |
| `E:\SWAB_V2\website\drycured_local\.env` | exists | valjan lokalni DB i port config |
| `E:\SWAB_V2\website\drycured_local\docker-compose.yml` | restored | vraćen za recovery |
| `E:\SWAB_V2\website\drycured_local\wordpress` | partial | host root nije puna WP jezgra i ne koristi se kao source-of-truth za core |
| `E:\SWAB_V2\website\drycured_local\wordpress\wp-config.php` | restored | generiran za lokalni runtime |
| `E:\SWAB_V2\website\drycured_local\wordpress\wp-content` | exists | glavni bind mount za sadržajni sloj |
| `E:\SWAB_V2\website\drycured_local\wordpress\wp-includes` | partial/stale | nije korišten kao source-of-truth za core |
| `E:\SWAB_V2\website\drycured_local\db_data` | exists | lokalna MariaDB data mapa |
| `E:\SWAB_V2\website\drycured_local\backups` | restored | sadrži recovery baseline SQL dump |
| `E:\SWAB_V2\website\drycured_local\scripts` | restored | start/stop/restart/backup helperi |
| `E:\SWAB_V2\website\drycured_local\imports` | exists | sadrži live wp-content copy i server snapshot |

---

## 2. Sadržajni sloj

| Putanja | Status | Napomena |
|---|---|---|
| `wordpress\wp-content\themes\astra` | restored | čista Astra tema instalirana za runtime baseline |
| `wordpress\wp-content\themes\astra_2026-03-31_recovery_backup` | preserved | polomljeni stari payload |
| `wordpress\wp-content\plugins\elementor` | restored | čista Elementor instalacija |
| `wordpress\wp-content\plugins\elementor_2026-03-31_recovery_backup` | preserved | polomljeni stari payload |
| `wordpress\wp-content\plugins\elementor-pro_2026-03-31_recovery_backup` | preserved | stari nepotpuni Pro payload |
| `wordpress\wp-content\uploads` | exists but empty for active runtime baseline | nema attachment zapisa iz live baze |

---

## 3. Baza i backup artefakti

| Putanja | Status | Napomena |
|---|---|---|
| `db_data` | source-of-truth za lokalnu bazu | sad sadrži bootable lokalni baseline |
| `backups\DRYCURED_LOCAL_RUNTIME_RECOVERY_BASELINE_2026-03-31.sql` | exists | checkpoint dump nakon recoveryja |
| lokalni live SQL dump | missing | nije pronađen |
| All-in-One / Duplicator backup paket | missing | backup mape sadrže samo `robots.txt` |

---

## 4. Zaključak inventure

Najvažnija razlika je:

- `wp-content` i `db_data` su stvarni source-of-truth za lokalni sadržajni sloj
- host `wordpress` root nije valjan source-of-truth za WordPress core
- WordPress core sada daje Docker image, ne djelomični host folder
