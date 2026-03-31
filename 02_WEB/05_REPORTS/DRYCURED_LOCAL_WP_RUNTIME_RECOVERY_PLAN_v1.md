# DRYCURED_LOCAL_WP_RUNTIME_RECOVERY_PLAN_v1

Status: izvršeni plan v1  
Projekt: drycured.com

---

## 1. Recovery strategija

Recovery je vođen principom najmanjeg stvarnog puta do bootable lokalnog WordPressa.

### Minimal path

1. potvrditi što stvarno postoji u `E:\SWAB_V2\website\drycured_local`  
2. vratiti `docker-compose.yml`  
3. vratiti `wp-config.php`  
4. koristiti Docker WordPress image kao jezgru  
5. zadržati `wp-content` i bazu na `E:`  
6. podići lokalni stack  
7. inicijalizirati lokalnu bazu ako live SQL dump ne postoji  
8. vratiti Astru i Elementor baseline  
9. validirati frontend, `wp-admin` i editor baseline

### Fallback path

Ako postojeći `wp-content` payload ne radi:

- sačuvati ga pod backup nazivom
- vratiti čisti minimalni Astra + Elementor baseline
- zadržati infrastrukturu bootable
- sadržajni live import odgoditi za zasebnu fazu

---

## 2. Korišteni lokalni izvori

- `E:\SWAB_V2\website\drycured_local\.env`
- `E:\SWAB_V2\website\drycured_local\wordpress\wp-content`
- `E:\SWAB_V2\website\drycured_local\db_data`
- postojeći Docker image cache:
  - `wordpress:6.8.3-php8.3-apache`
  - `wordpress:cli`
  - `mariadb:11.4`
  - `adminer:4.8.1`

---

## 3. Zašto nije korišten live content path

Nije pronađen lokalni SQL dump ni potpuni backup paket. Zato live-content recovery nije mogao biti minimal path.

Najmanji stvarni recovery bio je:

- vratiti runtime
- vratiti admin pristup
- vratiti Elementor baseline

umjesto da se lažno glumi puna live kopija.

---

## 4. Što je odgođeno

- pravi live SQL import
- stvarni lokalni attachment/media mapping iz baze
- povrat `elementor-pro` lokalnog stanja
- Home interaction copy build

---

## 5. Zaključak

Ovaj plan je ispravno odvojio:

- infrastrukturni recovery
od
- sadržajnog live-copy recoveryja

To je bilo nužno da `localhost:8085` ponovno postane stvarno upotrebljiv WordPress baseline.
