# ONEDRIVE LOCAL SCAN MANIFEST SPEC v1

## Svrha

Ovaj dokument definira minimalni manifest za pregled lokalno sinkroniziranog OneDrive sadržaja koji je relevantan za receptni sustav.

## Način rada

Ovaj modul nije javni web scraping.
Ako je OneDrive sinkroniziran lokalno na računalu, tretira se kao read-only lokalna lokacija.

## Minimalna polja manifesta

Za svaku datoteku zabilježiti:
- full_path
- root_location
- file_name
- extension
- size_bytes
- modified_time
- hash_sha256
- text_extractable
- language_guess
- keyword_hits
- relevance_score
- classification_status
- preview_text
- notes

## Preporučeni statusi
- candidate_high
- candidate_medium
- candidate_low
- duplicate_exact
- duplicate_fuzzy
- suspect_incomplete
- archive_candidate
- irrelevant

## Preporučeni tipovi datoteka
- pdf
- docx
- doc
- txt
- md
- html
- json
- csv
- xlsx
- xls
- ods
- epub
- zip
- 7z
- rar

## Ograničenja prve faze
- read-only
- bez masovnog raspakiravanja arhiva
- bez OCR-a kao zadanog pristupa
- bez miješanja OneDrive manifesta i javnog web crawl manifesta

## Izlazi
- ONEDRIVE_LOCAL_SCAN_REPORT_v1.md
- onedrive_local_manifest_v1.csv
- onedrive_local_manifest_v1.jsonl

## Napomena

Ako lokalni OneDrive nije pronađen, umjesto ovog modula prelazi se na planiranje Graph inventory pristupa.