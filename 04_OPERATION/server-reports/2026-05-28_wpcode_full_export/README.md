# Drycured WPCode Full Export — 2026-05-28

Status: PASS

Purpose:
Create a root-only private export/backup of all WPCode snippets and metadata before cleanup or conversion work.

Private export location:
- /root/drycured_private_exports/wpcode_full_export_2026-05-28_18-32-23

Report location:
- /root/DRYCURED_GITHUB/server-reports/tech-security/wpcode_full_export_2026-05-28_18-32-23

Exported:
- Full wpcode posts TSV
- Full wpcode postmeta TSV
- WPCode inventory TSV
- One readable text file per snippet
- SHA256 checksums for exported files

Validation:
- 21 snippet text files were exported.
- SHA256 checksums were generated.
- Home returned 200 OK.
- sitemap.xml returned 200 OK.
- Recepti returned 200 OK.
- Alati returned 200 OK.
- wp-json returned 200 OK.

Security note:
The full export files contain snippet source code and are not committed to Git. Git archives only the report, file tree, checksums and validation notes.

Next recommended phase:
- Convert the smallest low-risk snippet into controlled code first:
  1454 — Limit Elementor Posts Excerpt Length.
- Validate behavior.
- Only after replacement is confirmed, deactivate the matching WPCode snippet.
