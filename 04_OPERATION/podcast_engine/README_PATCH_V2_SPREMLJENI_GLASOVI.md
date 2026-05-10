# Patch v2 — audicija spremljenih ElevenLabs glasova

Ovaj patch koristi ElevenLabs `/v2/voices` i filtrira glasove kao `saved` i `non-default`.
To je potrebno kada glasovi iz Voice Libraryja nisu vidljivi kroz stari `/v1/voices` popis.

## Instalacija

Raspakiraj sadržaj ZIP-a u postojeću mapu:

```text
D:\drycured_podcast_engine_v1\drycured_podcast_engine_v1
```

## Pokretanje

Pokreni:

```text
09_AUDICIJA_SPREMLJENIH_GLASOVA.bat
```

Rezultat:

```text
output\audicija_spremljenih_glasova
```

U toj mapi bit će:
- MP3 uzorci
- AUDICIJA_SPREMLJENIH_GLASOVA_INDEX.csv
- RAW_V2_VOICES.json

Ako i dalje ne vidiš glasove koje želiš, u ElevenLabsu ih treba dodati u **My Voices / Voice Collection**, a ne samo označiti kao favorite.
