# DRYCURED_LATEX_AUDIT_OODA_LOG_v1

Status: audit log v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## Observe

Potvrđeno je da je knjiga stvarni LaTeX corpus s `main.tex` entrypointom, chapter splitom, `style.tex` semantic slojem, tablicama, slikama, infografikama i label/reference mehanizmom.

Istodobno je potvrđeno da filesystem sadrži duplicate/nested stare `.tex` varijante koje ne smiju automatski ući u kanonski parse.

## Orient

To znači da je ispravan parser model:
- entrypoint-driven
- include-aware
- semantic-block-aware
- visual-aware

A ne:
- raw filesystem crawl
- plain-text dump
- PDF-like flattening

## Decide

Zaključena minimalna extraction granularity v1:
- section-aware semantički blok
- warning/savjet blok kao zaseban object
- tablica + caption + label + context zajedno
- infografika + caption + label + context zajedno

Knowledge objects prve klase u v1 trebaju biti:
- theory block
- warning
- practical tip / correction
- problem/cause/correction trio gdje postoji
- process phase
- table
- infographic

## Act

Napravljeno je:
- corpus inventory
- structural audit
- custom environments map
- references and visuals map
- preporuka za extraction granularity v1

## Preporučeni sljedeći korak

- `LATEX knowledge extraction prototype v1`

Taj prototip treba validirati:
- entrypoint-based parsing
- object extraction na 2-3 prioritetna chaptera
- table/infografika linking bez gubitka contexta
