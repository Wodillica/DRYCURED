# Drycured.com — zapisnik procesnih stranica

Datum: 2026-05-26 19:05:52
Server: swab-production
Radni direktorij WordPressa: /var/www/html

## Sažetak

Dovršen je niz stranica procesa izrade suhomesnatih proizvoda na drycured.com.

Uređene i potvrđene stranice:

- /proces-izrade/mijesanje/
- /proces-izrade/odlezavanje-smjese/
- /proces-izrade/punjenje/
- /proces-izrade/dimljenje/
- /proces-izrade/susenje/
- /proces-izrade/zrenje/
- /proces-izrade/pakiranje/

## Model rada

Za svaku stranicu korišten je odvojeni MU-plugin sloj:
- moderni hero blok
- postojeća/stara slika procesa
- edukativni simulator
- kontrolna lista
- konkretna rješenja za probleme
- završni problem → rješenje blokovi

## Važna napomena

Kod stranice Dimljenje nastao je problem s pokušajem dubinskog linkanja Planera dimljenja.
Problem je riješen i postojeći alat Planer dimljenja ponovno se prikazuje i linkovi rade.

Za buduće alate:
- ne raditi globalno presretanje linkova
- ne stvarati privremene stranice alata bez prethodne provjere stvarnog shortcodea
- prvo pronaći stvarni registrirani alat/shortcode, tek zatim povezivati

## Aktivni MU-pluginovi procesa

- wp-content/mu-plugins/drycured-process-mijesanje-core.php
- wp-content/mu-plugins/drycured-process-odlezavanje-core.php
- wp-content/mu-plugins/drycured-process-odlezavanje-text-fix-v011.php
- wp-content/mu-plugins/drycured-process-punjenje-core.php
- wp-content/mu-plugins/drycured-process-dimljenje-core.php
- wp-content/mu-plugins/drycured-process-susenje-core.php
- wp-content/mu-plugins/drycured-process-zrenje-core.php
- wp-content/mu-plugins/drycured-process-pakiranje-core.php

## Slike procesa

Korištene su mape:

- wp-content/uploads/drycured/procesi/mijesanje/
- wp-content/uploads/drycured/procesi/odlezavanje/
- wp-content/uploads/drycured/procesi/punjenje/
- wp-content/uploads/drycured/procesi/dimljenje/
- wp-content/uploads/drycured/procesi/susenje/
- wp-content/uploads/drycured/procesi/zrenje/
- wp-content/uploads/drycured/procesi/pakiranje/

Posebna napomena:
- Zrenje koristi zrenje-hero-v02.jpg jer je prva verzija greškom bila ista kao slika za Sušenje.

## Status

Korisnik je potvrdio da završne stranice rade.
Preporuka za sljedeći radni dan: miran QA prolaz kroz sve procesne stranice, mobilni prikaz i CTA linkove.
