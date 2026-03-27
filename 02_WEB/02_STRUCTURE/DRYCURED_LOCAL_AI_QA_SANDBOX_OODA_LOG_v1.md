# DRYCURED_LOCAL_AI_QA_SANDBOX_OODA_LOG_v1

Status: local sandbox OODA log v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## Observe

- Retrieval i answer prototipi već daju dovoljno materijala za lokalni controlled runner.
- Najveći rizik nije generiranje teksta nego lažni osjećaj da sandbox “zna” više nego što evidence pack pokriva.
- Exact sample answers daju stabilan proof-of-work baseline.

## Orient

- Najjednostavniji održivi sandbox je local CLI/runner bez UI-ja.
- Sandbox mora biti auditabilan: mode, evidence channels i confidence razlog moraju ostati vidljivi.
- Insufficient evidence mora biti jednako valjan rezultat kao i uspješan odgovor.

## Decide

- V1 sandbox ostaje local-only helper.
- Repo prima samo dokumentaciju i IO/test artefakte.
- Fallback retrieval ostaje heuristički i ograničen na local proof-of-work.

## Act

- Napravljen je local sandbox runner.
- Generirani su IO schema i test query pack.
- Dokumentiran je flow od query inputa do controlled outputa.

## Preporučeni sljedeći korak

- drycured web integration boundary plan v1
