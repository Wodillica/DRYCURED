# Drycured Podcast Engine — patch za audiciju glasova

## Instalacija

Raspakiraj sadržaj ovog ZIP-a u postojeću mapu:

```text
D:\drycured_podcast_engine_v1\drycured_podcast_engine_v1
```

Ako Windows pita za spajanje mapa, odaberi **Replace / Overwrite** samo za dodane datoteke.

## Redoslijed rada

1. Pokreni:

```text
06_AUDICIJA_GLASOVA.bat
```

2. Preslušaj MP3 uzorke u:

```text
output\audicija_glasova
```

3. Otvori indeks:

```text
output\audicija_glasova\AUDICIJA_GLASOVA_INDEX.csv
```

4. Pokreni:

```text
07_ZAKLJUCAJ_GLASOVE.bat
```

Zalijepi `voice_id` za:
- Voditelja
- Majstora

5. Pokreni:

```text
08_GENERIRAJ_TEST_EP01_NOVI_GLASOVI.bat
```

To briše samo stare testne EP01 segmente i generira novu testnu epizodu s odabranim glasovima.
