# 3042 private WordPress preview — execution checklist

Status: **PLAN_ONLY — NO_WORDPRESS_WRITE**

## Zabranjeno

- [x] Ne mijenjati javni post 3042.
- [x] Ne mijenjati javni title.
- [x] Ne mijenjati javni slug.
- [x] Ne mijenjati javni status.
- [x] Ne mijenjati javni URL.
- [x] Ne mijenjati renderer.

## Dopušteno tek u idućem koraku uz posebno odobrenje

- [ ] Stvoriti privatni clone s post_status=private.
- [ ] Meta vrijednosti iz `3042_private_wp_preview_meta_map.json` upisati samo na privatni clone.
- [ ] Provjeriti privatni URL/preview.
- [ ] Ako preview nije dobar, obrisati ili arhivirati privatni clone, ne dirati javni post.

## Blokade prije javne objave

- [ ] kanonski izvor za točne količine nije potvrđen
- [ ] količina starter kulture zahtijeva tehničku provjeru
- [ ] dimljenje je označeno kao needs_confirmation
- [ ] javni tekst još sadrži interne tragove prema intake izvještaju
- [ ] potrebno je završiti qa_report.md prije bilo kakvog WordPress updatea
