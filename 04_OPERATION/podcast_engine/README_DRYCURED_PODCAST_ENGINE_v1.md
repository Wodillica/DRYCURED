# Drycured Podcast Engine v1

Automatski TTS paket za izradu prvih 5 podcast epizoda iz 20 članaka rubrike **Enciklopedija znanja**.

## Što ti radiš

1. U ElevenLabsu napravi API key.
2. Pokreni `01_POSTAVI_OKRUZENJE.bat`.
3. U `.env` zalijepi API key.
4. Pokreni `02_PROVJERI_GLASOVE.bat`.
5. Pokreni `03_GENERIRAJ_TEST_EP01.bat`.
6. Ako test zvuči dobro, pokreni `04_GENERIRAJ_SVIH_5_EPIZODA.bat`.

## Što sustav radi automatski

- čita scenarije,
- bira glasove ako nisu ručno postavljeni,
- šalje segmente u ElevenLabs TTS,
- sprema MP3 segmente,
- spaja segmente u finalne epizode preko FFmpeg-a,
- priprema CSV za WordPress opis epizoda.

## Važno

API key ne lijepi u chat. Čuva se samo u lokalnoj `.env` datoteci.

## Glasovi

Ako želiš zaključati konkretne glasove:

1. Pokreni `02_PROVJERI_GLASOVE.bat`.
2. Otvori `output/voices_list.json`.
3. Kopiraj `voice_id` za dva odabrana glasa.
4. Zalijepi ih u `config/voices_config.json`:
   - `voice_host.voice_id`
   - `voice_master.voice_id`

Ako ih ne upišeš, sustav automatski uzima prva dva dostupna glasa.

## Finalni MP3

Nakon generiranja, finalni MP3 nalazi se u:

```text
output/final/
```

## Ako FFmpeg nije instaliran

Segmenti će se generirati, ali spajanje finalnog MP3-a neće proći. Tada treba instalirati FFmpeg i dodati ga u PATH, pa ponovno pokrenuti generiranje. Skripta neće ponovno naplaćivati već postojeće segmente jer ih preskače ako već postoje.
