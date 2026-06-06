# Drycured legal footer links v1.1

Vrijeme: 2026-06-06_18-23-58

## Status

Ažuriran MU-plugin `drycured-legal-footer-links-v1.php`.

## Promjena

Iz footera je uklonjen srednji link/gumb:

- Postavke kolačića

U footeru ostaju samo:

- Politika privatnosti
- Politika kolačića

Lijevi plutajući gumb `Postavke kolačića` ostaje kroz cookie privacy plugin.

## QA

- Home: 200
- /politika-kolacica/: 200
- /politika-privatnosti/: 200
- Footer marker: PASS
- Srednji footer settings gumb uklonjen: PASS
- Lijevi settings gumb postoji: PASS
