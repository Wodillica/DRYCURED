# Drycured WPCode 1454 Migration — 2026-05-28

Status: PASS

Purpose:
Migrate WPCode snippet 1454, "Limit Elementor Posts Excerpt Length", into controlled MU-plugin code and deactivate the original WPCode snippet after validation.

Original WPCode snippet:
- ID: 1454
- Title: Limit Elementor Posts Excerpt Length
- Previous status: publish
- New status: draft

Migration target:
- /var/www/html/wp-content/mu-plugins/drycured-wpcode-migrated-small-fixes.php

Behavior migrated:
- excerpt_length returns 30
- excerpt_more returns &hellip;

Validation:
- Raw WPCode snippet content was rechecked because the first TSV-style export showed malformed output.
- Raw WP-CLI post get confirmed the actual snippet content was valid.
- MU-plugin PHP syntax check passed.
- Filter output after MU-plugin creation:
  - excerpt_length=30
  - excerpt_more=&hellip;
- WPCode snippet 1454 was set to draft.
- Filter output after WPCode deactivation remained:
  - excerpt_length=30
  - excerpt_more=&hellip;
- Home returned 200 OK.
- sitemap.xml returned 200 OK.
- Recepti returned 200 OK.
- Alati returned 200 OK.
- wp-json returned 200 OK.

Rollback:
- Re-publish WPCode snippet 1454 if needed:
  wp post update 1454 --post_status=publish --allow-root
- Remove or edit the MU-plugin only after rollback validation.

Notes:
This is the first controlled WPCode-to-MU-plugin migration. No other snippets were changed.
