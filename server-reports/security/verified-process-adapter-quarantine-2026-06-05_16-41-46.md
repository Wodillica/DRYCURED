# Drycured sigurnosni zapis — Verified Process Adapter karantena

- Vrijeme zatvaranja incidenta: 2026-06-05T16:41:48+00:00
- Problematična datoteka: `wp-content/mu-plugins/drycured-verified-process-adapter.php`
- SHA256 izvorne i karantenske kopije: `1d153ac65400f7404acc8a46027082132de2189e5ae7b35ef24f0a4779b961bc`
- Karantenska kopija nalazi se izvan webroota.
- Karantenski PHP izvorni kôd nije dodan u Git repozitorij.
- Adapter prije uklanjanja nije bio aktivan.
- Uzrok: UTF-8 BOM i neprepoznati obrazac otvarajućeg PHP taga uzrokovali su ispis izvornog kôda u javni HTML i WP-CLI izlaz.
- Poduzeta mjera: datoteka je uklonjena iz MU-plugin direktorija i spremljena u sigurnosnu karantenu.
- Javni prikaz recepata nije promijenjen jer adapter nije bio izvršavan.

## Završne javne provjere

```text
200 | plugin=0 | function=0 | https://drycured.com/
200 | plugin=0 | function=0 | https://drycured.com/vodici/
200 | plugin=0 | function=0 | https://drycured.com/recepti-baza/pe-ena-salamurena-slanina-s-toplim-dimom/
404 | izravni URL uklonjene datoteke | https://drycured.com/wp-content/mu-plugins/drycured-verified-process-adapter.php
```

## Status

- WP-CLI bootstrap: čist
- početna stranica: bez curenja izvornog kôda
- blog `/vodici/`: bez curenja izvornog kôda
- uzorak javnog recepta: bez curenja izvornog kôda
- izravni URL uklonjene datoteke: nije dostupan
