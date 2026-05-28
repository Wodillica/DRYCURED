# Drycured WPCode Cleanup Plan v0.0.1 — NO CHANGES

Status: PLAN ONLY

Purpose:
Classify current WPCode / Insert Headers and Footers snippets and define safe cleanup direction without modifying production.

Current principle:
WPCode should not remain the long-term place for production PHP logic, server operations, upload tooling or infrastructure fixes. Useful frontend/CSS snippets may temporarily remain, but stable production functionality should be moved into controlled plugin or MU-plugin code.

## A. Keep temporarily

- 1619 — Savjeti — Rotacijski Widget — publish
  - Classification: keep temporarily / later convert to plugin or MU-plugin
  - Reason: public feature logic for daily tips.

- 1506 — Savjeti — Custom Post Type — publish
  - Classification: keep temporarily / later convert to plugin or MU-plugin
  - Reason: content structure logic should eventually live in versioned code.

- 1454 — Limit Elementor Posts Excerpt Length — publish
  - Classification: keep temporarily
  - Reason: minor display behavior; low risk.

- 1418 — Veličina naslova — početna stranica — publish
  - Classification: keep temporarily / later migrate to CSS
  - Reason: styling should eventually live in theme/plugin CSS, not WPCode.

- 2815 — Kalkulator Soli — Fix gumbi kopiraj/print — publish
  - Classification: keep temporarily / later migrate to calculator plugin
  - Reason: feature-specific behavior belongs in the calculator plugin.

## B. Convert to plugin or MU-plugin

- 1424 — PDF Chunked Upload Admin Page — publish
  - Classification: convert to controlled plugin/admin tool or remove if obsolete.
  - Reason: admin upload functionality should be versioned, reviewed and permission-scoped.

- 1429 — Flip Viewer — Knjiga stranica — publish
  - Classification: convert to plugin or shortcode module if still used.
  - Reason: public/book viewer functionality should be maintained in proper project code.

- 1619 — Savjeti — Rotacijski Widget — publish
  - Classification: convert to drycured content/plugin module.

- 1506 — Savjeti — Custom Post Type — publish
  - Classification: convert to drycured content/plugin module.

- 2815 — Kalkulator Soli — Fix gumbi kopiraj/print — publish
  - Classification: migrate into drycured calculator plugin.

## C. Deactivate/archive after export and validation

- 1426 — Nginx fix via shell — publish
  - Classification: high priority cleanup candidate.
  - Reason: server operations must not be executed from WordPress snippets.

- 1422 — Povećaj upload limit na 256MB — publish
  - Classification: cleanup candidate.
  - Reason: upload/server limits should be handled in PHP/Nginx config, not WPCode.

- 1417 — Flush Elementor Cache post 101 — publish
  - Classification: cleanup candidate.
  - Reason: one-time maintenance action; should not remain as active production snippet.

- 1412 — Obriši TranslatePress — draft
  - Classification: archive/remove after export.
  - Reason: attempted plugin deletion and filesystem operations; already draft with error history.

- 1423 — Fix nginx upload limit + PDF uploader — draft
  - Classification: archive/remove after export.
  - Reason: attempted Nginx/config writes from WPCode; draft with error history.

- 1425 — Fix nginx client_max_body_size — draft
  - Classification: archive/remove after export.
  - Reason: attempted Nginx/config writes from WPCode; draft with error history.

## D. GTranslate draft snippets

Draft snippets:
- 1411 — GTranslate — crni tekst u izborniku — draft
- 1413 — GTranslate - pozicija desno — draft
- 1419 — GTranslate — mobilni floating widget — draft
- 1420 — Mobilni header — redoslijed i GTranslate — draft
- 1421 — GTranslate u mobilni meni — draft
- 1505 — GTranslate Navigation Styling — draft

Classification:
- Review and archive if obsolete.
- Keep only if a current visual issue requires them.
- Prefer moving final styling to controlled CSS/MU-plugin code.

## E. Safe cleanup order

1. Export all WPCode snippets and meta to a root-only backup/report.
2. Create a list of active snippets with current status and risk level.
3. Convert needed published functionality into controlled drycured plugin/MU-plugin files.
4. Validate public pages after each conversion.
5. Only then deactivate obsolete snippets.
6. Do not delete snippets immediately; first move to draft/archive state and keep report.
7. Re-run health report and file integrity audit.

## F. Do not do yet

- Do not bulk delete snippets.
- Do not deactivate all WPCode snippets at once.
- Do not uninstall WPCode before replacement code exists.
- Do not touch database rows directly except for read-only export.
