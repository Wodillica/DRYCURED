# CODEX TASK — ONEDRIVE SCAN MODULE v1

## Cilj

Pripremiti plan za pregled OneDrive sadržaja koji može služiti kao zaseban modul uz lokalni scan i web scraping.

## Dva scenarija

### Scenarij A — lokalno sinkronizirani OneDrive
Ako je OneDrive vidljiv kao lokalna mapa na računalu:
- tretirati ga kao ciljanu lokalnu lokaciju za read-only scan
- koristiti ista pravila kao za local scan, uz zasebni manifest i izvještaj

### Scenarij B — pravi cloud inventory
Ako OneDrive nije sinkroniziran lokalno:
- pripremiti plan za inventory preko Microsoft Graph pristupa
- fokus je na listanju, pretrazi i manifestu, ne na agresivnom preuzimanju sadržaja

## Što napraviti

1. provjeriti postoji li lokalni OneDrive folder
2. ako postoji, predložiti `ONEDRIVE_LOCAL_SCAN_v1`
3. ako ne postoji, sastaviti tehnički plan za `ONEDRIVE_GRAPH_INVENTORY_v1`
4. odvojiti OneDrive scan od klasičnog web scrapinga
5. predložiti koje vrste datoteka i metapodataka treba bilježiti

## Obvezna pravila
- read-only gdje god je moguće
- bez masovnog preuzimanja bez naloga
- bez miješanja privatnog oblaka i javnog web crawl sloja
- svi nalazi idu u zaseban manifest i zaseban izvještaj

## Izlaz

Izraditi izvještaj pod nazivom:
`ONEDRIVE_SCAN_STRATEGY_REPORT_v1.md`

Izvještaj treba sadržavati:
- je li na računalu pronađen lokalni OneDrive
- preporuku između lokalnog scana i Graph pristupa
- opis minimalnog manifesta za OneDrive datoteke
- upozorenja, ograničenja i preporučeni sljedeći korak
