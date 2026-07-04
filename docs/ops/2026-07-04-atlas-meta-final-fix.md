# Atlas Meta Fix — Završni ispravci (2026-07-04)

## Kontekst

Nakon što su taxonomy termini ispravno postavljeni za sve 199 objavljenih dry_recipe postova,
otkriveno je da REST API (koji JS Atlas čita) koristi `_dry_recipe_data` JSON meta **prije** taxonomy.
Ova faza ispravlja preostalih 7 postova čiji je meta bio netočan ili garbled.

## Ispravci (post meta `_dry_recipe_data`)

| Post ID | Naziv | Problem | Ispravak |
|---------|-------|---------|----------|
| 2564 | Maltežanski pršut | region = garbled tekst (sastojci) | region → "Malta" |
| 2725 | FLÄSKLÄGG | region = garbled tekst | region → "Švedska" |
| 2575 | PASTRAMĂ DE OAIE | country = "Moldavija" (krivo), region = garbled | country → "Rumunjska", region → "Rumunjska" |
| 2637 | Thüringer Schinken | country = "Nepoznato" | country → "Njemačka" |
| 2638 | Bavarski Bierschinken | country = "Nepoznato" | country → "Njemačka" |
| 2703 | HR post | region = "Slavonija i Baranja" (duplikat) | region → "Baranja" |
| 2705 | HR post | region = "Slavonija i Baranja" (duplikat) | region → "Srijem" |

## Finalni Atlas stanje (meta-source of truth)

```
Hrvatska          (38) [10 regija] Baranja, Dalmacija, Istra, Kvarner, Lika, Međimurje, Posavina, Slavonija, Središnja Hrvatska, Srijem
Grčka             (12) [7 regija]  Egejski otoci, Epir, Grčka, Jonski otoci, Kreta, Makedonia, Peloponez
Norveška          (10) [3 regija]  Nord-Norge, Vestlandet, Østlandet
Cipar              (9) [1 regija]  Cipar
Island             (9) [1 regija]  Island
Italija            (8) [4 regija]  Alto Adige, Emilia-Romagna, Kalabrija, Marche
Ujedinjeno Kraljevstvo (7) [4 regija] Arbroath, Ayrshire, Engleska, Škotska
Finska             (7) [1 regija]  Finska
Litva              (6) [2 regija]  Suvalkija, Žemaitija
Irska              (6) [1 regija]  Irska
Bugarska           (6) [2 regija]  Stara Planina, Trakija
Švicarska          (5) [4 regija]  Appenzell, Graubünden, Valais, Zürich
Turska             (5) [1 regija]  Turska
Danska             (5) [1 regija]  Danska
Crna Gora          (5) [1 regija]  Crna Gora
Slovenija          (4) [3 regija]  Gornja Savinjska dolina, Prekmurje, Prlekija
Rumunjska          (4) [1 regija]  Rumunjska
Njemačka           (4) [4 regija]  Bavarska, Schleswig-Holstein, Turingija, Vestfalija
Albanija           (4) [1 regija]  Albanija
Belgija            (4) [4 regija]  Antwerpen, Ardeni, Flandrija, Valonija
Austrija           (4) [4 regija]  Austrija, Koruška, Tirol, Štajerska
Estonija           (4) [1 regija]  Estonija
Rusija             (3) [1 regija]  Rusija
Ukrajna            (3) [1 regija]  Ukrajna
Mađarska           (3) [1 regija]  Mađarska
Francuska          (3) [3 regija]  Bayonne, Bourgogne-Franche-Comté, Korzika
Malta              (3) [1 regija]  Malta
Engleska           (3) [1 regija]  Engleska
Poljska            (3) [1 regija]  Poljska
Švedska            (3) [1 regija]  Švedska
Španjolska         (2) [2 regija]  Castilla y León, Extremadura
Portugal           (2) [2 regija]  Alentejo, Trás-os-Montes
Bosna i Hercegovina(1) [1 regija]  Hercegovina
Kosovo             (1) [1 regija]  Kosovo
Nizozemska         (1) [1 regija]  Nizozemska
Moldavija          (1) [1 regija]  Moldavija
Slovačka           (1) [1 regija]  Slovačka
```

Ukupno: 37 zemalja, 199 recepata

## Napomene

- HR ima 10 regija s postovima + 4 kanonske prazne (Banija, Gorski kotar, Zagorje, Podravina) — prikazuju se u Atlas UI putem REST API canonical seeding
- "Kalabrija" = taxonomy term name za talijansku regiju (canonical je "Calabria") — nebitno za prikaz
- "Engleska" kao zasebna država: 3 posta specifično engleska, nije UK generički
- "Moldavija" (1 post): zasebna moldavska receptura, nije greška
