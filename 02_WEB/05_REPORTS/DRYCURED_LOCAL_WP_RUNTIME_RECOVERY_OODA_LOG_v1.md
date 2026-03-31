# DRYCURED_LOCAL_WP_RUNTIME_RECOVERY_OODA_LOG_v1

Status: OODA log v1  
Projekt: drycured.com

---

## Observe

Lokalni WordPress runtime bio je slomljen: nije bilo `docker-compose.yml`, `wp-config.php`, pune WP jezgre ni SQL dumpa, a `localhost:8085` nije radio.

## Orient

Ispostavilo se da ipak postoji korisna baza:

- Docker Desktop je bio instaliran
- image cache za WordPress, WP-CLI, MariaDB i Adminer već je postojao
- `db_data` je postojao i stari DB container mogao se ponovno upotrijebiti
- `wp-content` je postojao, ali s polomljenim Astra/Elementor payloadom

## Decide

Odabran je minimalni stvarni recovery path:

- Docker image daje core
- lokalni `wp-content` i baza ostaju na `E:`
- runtime se diže kao svježi lokalni baseline
- polomljeni payload se čuva pod backup nazivima, ne briše se

## Act

Vraćeni su Compose, `wp-config.php`, helper skripte, baza, frontend, `wp-admin`, Elementor plugin baseline, Astra tema i SQL checkpoint dump.

## Preporučeni sljedeći korak

Actual local home interaction copy build v1, ali na ovom oporavljenom baselineu i uz jasnu svijest da sadržaj još nije live-copy import.
