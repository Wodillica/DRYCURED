# Drycured Short Video Factory

Lokalni alat za izradu vertikalnih 9:16 promotivnih videozapisa za drycured.com. Alat koristi Python i FFmpeg, radi lokalno i ne koristi cloud video generatore, plaćene API-jeve ni `.env` datoteke.

## Radna mapa

```text
D:\drycured_promotion_2026_06
```

Ako neka mapa nedostaje, `tools\generate_short_video.py` će je pokušati ponovno napraviti pri pokretanju.

## Gdje se spremaju ulazi

Slike:

```text
01_VIDEO_SHORTS\input\images
```

Podcast audio:

```text
01_VIDEO_SHORTS\input\audio
```

Glazbena podloga:

```text
01_VIDEO_SHORTS\input\music
```

JSON scenariji:

```text
01_VIDEO_SHORTS\input\scripts
```

Pilot scenarij je:

```text
01_VIDEO_SHORTS\input\scripts\VID-001.json
```

## Kako pokrenuti jedan video

U PowerShellu ili Command Promptu:

```bat
cd /d D:\drycured_promotion_2026_06
GENERIRAJ_VIDEO_VID001.bat
```

Skripta očekuje sliku:

```text
01_VIDEO_SHORTS\input\images\VID-001.png
```

Ako postoje, koristi i ove audio datoteke:

```text
01_VIDEO_SHORTS\input\audio\EP04_najcesce-greske-i-kako-ih-izbjeci.mp3
01_VIDEO_SHORTS\input\music\drycured_background_01.mp3
```

Ako podcast audio ili glazba ne postoje, alat nastavlja bez njih. Slika je obavezna.

## Kako pokrenuti paket

```bat
cd /d D:\drycured_promotion_2026_06
GENERIRAJ_SVE_VIDEO_PILOTE.bat
```

Paket prolazi kroz sve `VID-*.json` scenarije u:

```text
01_VIDEO_SHORTS\input\scripts
```

## Output

Videozapisi se spremaju u:

```text
01_VIDEO_SHORTS\output\videos
```

Za `VID-001` očekivani output je:

```text
01_VIDEO_SHORTS\output\videos\VID-001_zasto-se-kobasica-ne-smije-susiti-prebrzo.mp4
```

Logovi se spremaju u:

```text
01_VIDEO_SHORTS\logs
```

## FFmpeg

Alat prije rendera provjerava postoji li `ffmpeg` u PATH-u.

Provjera:

```powershell
ffmpeg -version
```

Ako dobiješ poruku:

```text
FFmpeg nije pronađen. Instaliraj FFmpeg ili dodaj ffmpeg.exe u PATH.
```

instaliraj FFmpeg za Windows i dodaj mapu koja sadrži `ffmpeg.exe` u PATH. Nakon toga zatvori i ponovno otvori terminal.

## JSON format

Svaki video ima jedan JSON scenarij s poljima kao što su:

```json
{
  "id": "VID-001",
  "title": "Zašto se kobasica ne smije sušiti prebrzo?",
  "slug": "zasto-se-kobasica-ne-smije-susiti-prebrzo",
  "duration_seconds": 35,
  "image": "VID-001.png",
  "audio": "EP04_najcesce-greske-i-kako-ih-izbjeci.mp3",
  "music": "drycured_background_01.mp3",
  "audio_start": "00:00:20",
  "audio_duration": 35,
  "screens": []
}
```

Vrijednosti `image`, `audio` i `music` su nazivi datoteka, ne apsolutne putanje.

## Standard izvoza

```text
Format: MP4
Omjer: 9:16
Rezolucija: 1080 x 1920
FPS: 30
Video kodek: H.264
Audio kodek: AAC
```
