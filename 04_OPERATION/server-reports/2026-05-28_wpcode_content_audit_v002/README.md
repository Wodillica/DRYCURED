# Drycured WPCode Content Audit v0.0.2 — 2026-05-28

Status: PASS as read-only audit / FOLLOW-UP REQUIRED

Purpose:
Review WPCode / Insert Headers and Footers snippets without changing production.

Validated:
- WPCode Lite / Insert Headers and Footers is active.
- wpcode post type exists.
- Multiple published and draft snippets exist.
- Live health remained OK after audit:
  - Home returned 200 OK
  - sitemap.xml returned 200 OK
  - Recepti returned 200 OK
  - Alati returned 200 OK
  - wp-json returned 200 OK
- No production changes were made.

Important findings:
- Risk pattern search did not show base64_decode, gzinflate, passthru, system, assert, curl_exec, document.write, atob or fromCharCode.
- Risk-related patterns appeared around older technical snippets and WPCode error records:
  - shell_exec
  - file_put_contents
  - exec
  - unlink
  - rmdir
- Several snippets attempted or referenced server/config/filesystem changes, including Nginx upload limit and TranslatePress removal attempts.
- WPCode execution itself uses eval internally, visible in _wpcode_last_error records. This is expected for WPCode execution, but reinforces the need to reduce active PHP snippets in production.

High-priority follow-up candidates:
- 1426 Nginx fix via shell — publish
- 1422 Povećaj upload limit na 256MB — publish
- 1417 Flush Elementor Cache post 101 — publish
- 1424 PDF Chunked Upload Admin Page — publish
- 1423 Fix nginx upload limit + PDF uploader — draft
- 1425 Fix nginx client_max_body_size — draft
- 1412 Obriši TranslatePress — draft

Recommended next phase:
- Create WPCode snippet classification report.
- Do not delete immediately.
- Export/backup all snippets first.
- Convert useful production functionality into controlled plugin/MU-plugin code.
- Deactivate/archive obsolete technical snippets only after validation.
