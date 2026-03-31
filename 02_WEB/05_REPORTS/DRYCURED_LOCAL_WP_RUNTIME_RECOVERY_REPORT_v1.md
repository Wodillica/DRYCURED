# DRYCURED_LOCAL_WP_RUNTIME_RECOVERY_REPORT_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Datum: 2026-03-31

---

## 1. Što je zatečeno

Na početku recoveryja potvrđeno je:

- `http://localhost:8085` nije radio
- `docker-compose.yml` nije postojao u `E:\SWAB_V2\website\drycured_local`
- `wordpress\wp-config.php` nije postojao
- lokalni `wordpress` root nije imao punu WP jezgru
- lokalni SQL dump nije pronađen
- `wp-content` je postojao, ali je bio djelomično polomljen

---

## 2. Što je vraćeno

Vraćeni su:

- `E:\SWAB_V2\website\drycured_local\docker-compose.yml`
- `E:\SWAB_V2\website\drycured_local\wordpress\wp-config.php`
- mape:
  - `db_data`
  - `backups`
  - `scripts`
- helper skripte:
  - `start_drycured_local.ps1`
  - `stop_drycured_local.ps1`
  - `restart_drycured_local.ps1`
  - `backup_drycured_local_db.ps1`

Recovery je izveden tako da Docker WordPress image daje jezgru, a lokalni bind mount zadržava `wp-content` i bazu na `E:`.

---

## 3. Što sada radi

### Frontend

- `http://localhost:8085` radi
- vraća `200`
- title je `DRYCURED Local`

### wp-admin

- `http://localhost:8085/wp-admin` radi
- više ne vodi na `install.php`
- vodi na login ekran:
  - `http://localhost:8085/wp-login.php?...`

### Baza

- lokalna MariaDB baza postoji i bootable je
- potvrđen lokalni DB container `drycured_local_db`
- lokalna WordPress instalacija napravljena je non-interactive preko `wp-cli`
- checkpoint dump spremljen je u:
  - `E:\SWAB_V2\website\drycured_local\backups\DRYCURED_LOCAL_RUNTIME_RECOVERY_BASELINE_2026-03-31.sql`

### Elementor

- `elementor/elementor.php` je aktivan plugin
- recovery check Elementor stranica postoji kao draft `ID 5`
- editor route postoji i uredno vodi na login redirect:
  - `/wp-admin/post.php?post=5&action=elementor`

### Adminer

- `http://localhost:8086` radi

---

## 4. Status baze i sadržaja

Lokalna baza sada postoji i radi, ali nije live-content kopija.

Trenutni lokalni sadržaj:

- `Hello world!`
- `Sample Page`
- `Privacy Policy`
- `Elementor Recovery Check`

To znači:

- runtime je vraćen
- ali sadržajni import live sajta nije vraćen

---

## 5. Status teme

Aktivni template i stylesheet:

- `astra`
- `astra`

Napomena:

- originalni lokalni `astra` payload bio je polomljen
- sačuvan je pod backup nazivom
- čista Astra tema je zatim vraćena i aktivirana

---

## 6. Warningi

- lokalna baza nije live import nego svježi lokalni baseline
- originalni lokalni `wp-content` payload bio je nepotpun za Astru i Elementor
- `elementor-pro` nije vraćen kao radni lokalni plugin
- uploads payload za radni runtime trenutno ne daje stvarne media attachmente u bazi

---

## 7. Konačni status

`PASS WITH WARNING`

Razlog:

- WordPress runtime je stvarno bootable
- frontend, `wp-admin` i Elementor baseline rade
- ali lokalna kopija još nije sadržajno jednaka live sajtu i još nije spremna za vjernu Home rekonstrukciju bez dodatnog content importa
