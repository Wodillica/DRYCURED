# Drycured WPCode Cleanup Plan v0.0.1 — 2026-05-28

Status: PLAN ONLY / NO CHANGES

Purpose:
Classify current WPCode / Insert Headers and Footers snippets and define a safe cleanup direction without modifying production.

Principle:
WPCode should not remain the long-term place for production PHP logic, server operations, upload tooling or infrastructure fixes. Stable functionality should be moved into controlled plugin or MU-plugin code.

Classification summary:
- Keep temporarily:
  - 1619 Savjeti — Rotacijski Widget
  - 1506 Savjeti — Custom Post Type
  - 1454 Limit Elementor Posts Excerpt Length
  - 1418 Veličina naslova — početna stranica
  - 2815 Kalkulator Soli — Fix gumbi kopiraj/print

- Convert to plugin or MU-plugin:
  - 1424 PDF Chunked Upload Admin Page
  - 1429 Flip Viewer — Knjiga stranica
  - 1619 Savjeti — Rotacijski Widget
  - 1506 Savjeti — Custom Post Type
  - 2815 Kalkulator Soli — Fix gumbi kopiraj/print

- Deactivate/archive after export and validation:
  - 1426 Nginx fix via shell
  - 1422 Povećaj upload limit na 256MB
  - 1417 Flush Elementor Cache post 101
  - 1412 Obriši TranslatePress
  - 1423 Fix nginx upload limit + PDF uploader
  - 1425 Fix nginx client_max_body_size

- Review/archive if obsolete:
  - GTranslate draft snippets 1411, 1413, 1419, 1420, 1421, 1505

No production changes were made.

Next recommended phase:
- Export all WPCode snippets and meta to root-only backup/report.
- Then start conversion with the least risky functional snippet, preferably:
  1454 Limit Elementor Posts Excerpt Length or
  2815 Kalkulator Soli — Fix gumbi kopiraj/print.
