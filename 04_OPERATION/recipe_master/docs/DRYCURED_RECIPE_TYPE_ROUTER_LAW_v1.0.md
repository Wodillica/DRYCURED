# DRYCURED_RECIPE_TYPE_ROUTER_LAW_v1.0

Projekt: drycured.com / suhosuseno.com  
Sustav: web recepti za suhomesnate proizvode  
Jezik javnog sadržaja: hrvatski  
Status: kanonski operativni dokument  
Datum: 2026-06-18  

---

## 1. Svrha dokumenta

Ovaj dokument zaključava obvezni podatkovni i QA sloj koji se mora primijeniti prije uređivanja, dopune ili javnog ažuriranja bilo kojeg recepta u bazi drycured.com.

Glavni cilj je spriječiti pogrešno punjenje postojećeg prikaza recepta. Recept se više ne smije obrađivati samo po nazivu ili po općem kobasičnom modelu. Svaki recept prvo mora dobiti tehnološki tip, zatim obvezna polja za taj tip, a tek nakon toga smije ići u privatni preview ili javnu doradu.

Ovaj dokument ne mijenja dizajn, renderer, raspored kartica, tipografiju, boje, javne URL-ove ni postojeći dogovoreni prikaz recepta.

---

## 2. Referentni model

Potvrđeni referentni javni recept:

- Naziv: Slavonska domaća kobasica
- URL: https://drycured.com/recepti-baza/hr-sl-005-slavonska-domaca-kobasica/
- Post ID: 2976
- Recipe code: HR-SL-005

Ovaj recept je referentni model samo za tehnološki tip:

GROUND_MEAT_OR_CASING

Odnosno: mljeveno ili usitnjeno meso u omotaču.

Ne smije se automatski primjenjivati na pršute, šunke, pancete, slanine, rebra, barene proizvode, krvavice, tlačenice, ribu i morske proizvode.

---

## 3. Strogo pravilo prikaza

Zabranjeno je bez posebnog dogovora:

- mijenjati javni dizajn recepta
- mijenjati redoslijed blokova
- mijenjati kartični sustav
- mijenjati tipografiju, boje i layout
- uvoditi novi renderer
- masovno zamijeniti javne recepte
- mijenjati javne URL-ove
- brisati javne recepte bez pojedinačne odluke
- prikazivati interne oznake javnim korisnicima

Dopušteno je:

- puniti postojeći prikaz točnim podacima
- uklanjati fallback sadržaj
- dopunjavati postojeća polja
- razvijati adaptere, normalizere i QA-gate kao podatkovni sloj
- raditi read-only audit
- raditi privatni preview prije javne objave

---

## 4. Zabranjene javne oznake

U javnom prikazu recepta ne smiju se pojaviti riječi ili oznake internog rada, uključujući:

- preview
- fallback
- source-lock
- radni recept
- audit
- adapter
- clone
- private
- working
- debug
- meta
- internal
- testni prikaz
- privremeni tekst
- fotografija će biti dodana
- sadržaj će biti dopunjen
- čeka provjeru

Ako se bilo koja od ovih oznaka pojavi u javnom HTML-u, recept pada QA.

---

## 5. Obvezni tehnološki tipovi

Svaki recept mora prije uređivanja dobiti jedan od ovih tipova:

1. GROUND_MEAT_OR_CASING
2. WHOLE_CUT
3. THERMAL_PROCESSED
4. FISH_OR_SEAFOOD
5. NEEDS_CLASSIFICATION

Recept sa statusom NEEDS_CLASSIFICATION ne smije se javno ažurirati.

---

## 6. Tip A — GROUND_MEAT_OR_CASING

Ovaj tip obuhvaća proizvode od mljevenog ili usitnjenog mesa u omotaču.

Primjeri:

- kobasice
- salame
- kulen
- kulenova seka
- sudžuk
- debele suhe kobasice
- fermentirane salame
- mazive salame ako su u omotaču

Obvezna polja:

- meso i masnoća u kg
- udio sirovina u postotku
- rešetka za mljevenje u mm
- način obrade masnoće
- rezanje slanine ili masnoće u mm ako se reže nožem
- temperatura ili hladna obrada mase
- miješanje i cilj miješanja
- omotač ili crijevo
- kalibar omotača gdje je poznat
- namakanje crijeva
- tekućina za namakanje crijeva
- punjenje
- bušenje zračnih džepova gdje je primjenjivo
- češnjak: direktno, macerat, procijeđena tekućina, nema češnjaka ili needs_review
- fermentacija gdje postoji
- predsušenje
- dimljenje gdje postoji
- sušenje
- zrenje
- znakovi gotovosti
- greške i konkretna rješenja

QA blokade za ovaj tip:

- nema granulacije
- nema obrade masnoće
- nema omotača kod punjenog proizvoda
- češnjak nije klasificiran
- dimljenje/sušenje/zrenje postoje u procesu, ali nemaju trajanje
- problemi nemaju rješenja

---

## 7. Tip B — WHOLE_CUT

Ovaj tip obuhvaća meso ili masno tkivo u komadu.

Primjeri:

- šunka
- pršut
- vrat
- plećka
- panceta
- slanina
- rebra
- lonza
- bresaola
- pečenica u komadu
- kare
- pastirma
- pastrma
- lardo

Obvezna polja:

- anatomski komad
- masa komada ili raspon mase
- oblikovanje i obrezivanje
- suhi pac, mokri pac ili salamura
- količina soli i začina
- utrljavanje ili potapanje
- trajanje soljenja
- temperatura soljenja
- okretanje ili preslagivanje
- ispiranje ili bez ispiranja
- cijeđenje i predsušenje
- prešanje gdje postoji
- omatanje, mrežica, crijevo ili kalup gdje postoji
- dimljenje gdje postoji
- sušenje
- zrenje
- očekivani gubitak mase gdje je dostupan
- znakovi gotovosti
- greške i konkretna rješenja

QA blokade za ovaj tip:

- prikazan je generički kobasični blok
- prikazana je automatska češnjakova tekućina bez izvora
- nema paca, salamure ili metode soljenja
- nema trajanja soljenja
- nema uvjeta sušenja ili zrenja gdje su potrebni
- nema kontrole gubitka mase gdje je proizvod zreli komad
- problemi nemaju rješenja

---

## 8. Tip C — THERMAL_PROCESSED

Ovaj tip obuhvaća proizvode kod kojih je toplinska obrada ključna faza.

Primjeri:

- barene kobasice
- parene kobasice
- kuhane kobasice
- pečene kobasice
- polutrajne kobasice
- krvavice
- jetrenjače
- tlačenice
- švargle
- hrenovke
- mortadella
- zampone
- cotechino
- toplo ili vruće dimljeni proizvodi
- barena slanina
- kuhana šunka
- pasterizirani proizvodi

Obvezna polja:

- vrsta toplinske obrade
- temperatura medija
- trajanje obrade
- ciljna temperatura jezgre gdje je primjenjivo
- način hlađenja
- cijeđenje
- oblikovanje ili prešanje gdje postoji
- dimljenje prije ili poslije toplinske obrade gdje postoji
- sigurnosne kontrole
- rok i način čuvanja
- način posluživanja
- greške i konkretna rješenja

QA blokade za ovaj tip:

- nema temperature medija
- nema trajanja toplinske obrade
- nema hlađenja
- nema sigurnosne kontrole
- toplinski proizvod pogrešno prikazan kao trajna suha kobasica
- problemi nemaju rješenja

---

## 9. Tip D — FISH_OR_SEAFOOD

Ovaj tip obuhvaća riblje i morske sušene, soljene ili dimljene proizvode.

Primjeri:

- dimljena riba
- sušena riba
- soljena riba
- dimljeni fileti
- morski suhomesnati proizvodi

Obvezna polja:

- vrsta ribe ili morskog organizma
- oblik obrade
- svježina sirovine
- hladni lanac
- soljenje ili salamura
- trajanje soljenja
- ispiranje
- cijeđenje
- hladno ili nježno dimljenje gdje postoji
- temperatura dimljenja gdje je dostupna
- sušenje
- posebne sigurnosne napomene
- rok čuvanja
- greške i konkretna rješenja

QA blokade za ovaj tip:

- nema hladnog lanca
- nema soljenja ili salamure
- nema posebne sigurnosne napomene
- riblji proizvod prikazan kao mesni proizvod
- problemi nemaju rješenja

---

## 10. Nitritna sol

Svaki recept koji koristi nitritnu sol mora imati javnu sigurnosnu napomenu.

Kratka javna napomena:

Oprez: nitritnu sol vagati precizno i ne prekoračiti navedenu količinu. Ne dodavati je od oka i ne kombinirati s drugim nitritnim mješavinama ako recept to izričito ne traži. Koristi se za stabilniju boju, prepoznatljiv suhomesnati profil i dodatnu sigurnost proizvoda. U kućnoj izradi može se izostaviti samo ako je recept vođen kao varijanta bez nitrita.

QA blokade:

- recept koristi nitritnu sol, ali nema napomenu
- recept kombinira nitritnu sol s drugim nitritnim pripravcima bez jasne receptne upute
- količina nitritne soli nije precizno izražena

---

## 11. Dimljenje, sušenje i zrenje

Svaki recept koji ima dimljenje, sušenje ili zrenje mora imati:

- trajanje faze
- temperaturu ili temperaturu dima
- relativnu vlagu gdje je primjenjivo
- broj ciklusa kod dimljenja gdje je primjenjivo
- trajanje jednog ciklusa kod dimljenja gdje je moguće
- pauze između ciklusa
- cilj faze
- kritičnu kontrolu
- konkretno rješenje ako faza pođe krivo

Ako izvor ne daje preciznu vrijednost, ne smije se prikazati kao izvorni podatak. Smije se dodati oprezna radna smjernica, jasno označena kao radna smjernica.

---

## 12. Read-only audit prije javnih izmjena

Prvi audit mora biti isključivo read-only.

Audit smije čitati:

- WordPress postove
- meta podatke
- postojeći javni HTML
- postojeći recipe JSON/meta zapis
- postojeće kanonske dosjee u repozitoriju

Audit ne smije:

- mijenjati WordPress bazu
- mijenjati post_content
- mijenjati meta podatke
- mijenjati statuse objava
- mijenjati slugove
- brisati recepte
- raditi redirecte
- kopirati izmjene u live plugin

Audit mora izvući:

- post ID
- title
- URL
- status
- prepoznati tehnološki tip
- confidence
- ima li mljevenje/granulaciju
- ima li obradu masnoće
- ima li omotač/crijevo
- ima li pac/salamuru
- ima li barenje/parenje/kuhanje
- ima li ribu/morski proizvod
- ima li dimljenje
- ima li sušenje
- ima li zrenje
- ima li trajanja i parametre
- ima li nitritnu sol
- ima li nitritnu napomenu
- ima li fallback/interne tekstove
- javna blokada: PASS/FAIL
- razlog blokade

---

## 13. Klasifikacijska logika

Router smije koristiti samo signalne pojmove kao pomoćnu klasifikaciju. Ne smije donositi konačne stručne odluke za nejasne recepte.

Primjeri signala za GROUND_MEAT_OR_CASING:

- kobasica
- salama
- kulen
- sudžuk
- mljeveno
- samljeti
- rešetka
- šajba
- puniti u crijeva
- omotač
- nadjev

Primjeri signala za WHOLE_CUT:

- pršut
- šunka
- vrat
- plećka
- panceta
- slanina
- rebra
- kare
- lonza
- bresaola
- komad
- suhi pac
- salamura
- utrljati
- preslagivati
- okretati

Primjeri signala za THERMAL_PROCESSED:

- bariti
- pariti
- kuhati
- peći
- pasterizirati
- temperatura jezgre
- 72 °C
- 80 °C
- krvavica
- jetrenjača
- tlačenica
- švargla
- hrenovka
- mortadella

Primjeri signala za FISH_OR_SEAFOOD:

- riba
- file
- pastrva
- losos
- bakalar
- tuna
- morski
- hladni lanac
- soljena riba
- dimljena riba

Ako su signali miješani ili nedovoljni, rezultat je NEEDS_CLASSIFICATION.

---

## 14. QA-gate prije javnog ažuriranja

Recept ne smije ići u javni prikaz ako nije prošao:

- tipološku klasifikaciju
- provjeru obveznih polja za svoj tehnološki tip
- provjeru da nema fallback tekstova
- provjeru da nema internih oznaka u javnom prikazu
- provjeru da problemi imaju konkretna rješenja
- provjeru dimljenja/sušenja/zrenja gdje postoje
- provjeru nitritne napomene ako koristi nitritnu sol
- provjeru granulacije ako je mljeveni/usitnjeni proizvod
- provjeru paca/salamure ako je meso u komadu
- provjeru toplinske obrade ako je termički obrađen proizvod
- provjeru hladnog lanca ako je riblji proizvod
- provjeru očuvanja postojećeg URL-a

---

## 15. Statusi audita

Audit može dodijeliti ove interne statuse:

- TYPE_PASS_READY_FOR_SOURCE_DOSSIER
- TYPE_PASS_MISSING_FIELDS
- TYPE_PASS_PUBLIC_TEXT_BLOCKED
- TYPE_CONFLICT_NEEDS_REVIEW
- NEEDS_CLASSIFICATION
- DO_NOT_PUBLIC_UPDATE

Ovi statusi su interni i ne smiju biti javno prikazani.

---

## 16. Workflow nakon audita

Redoslijed rada:

1. read-only audit
2. izvještaj po tehnološkim tipovima
3. popis blokiranih recepata
4. odabir malog batcha po jednom tehnološkom tipu
5. izvorni dosje za svaki recept
6. recipe.yml
7. QA izvještaj
8. privatni preview
9. javni QA
10. tek zatim javno ažuriranje postojećeg recepta

---

## 17. Rollback pravilo

Svaka promjena mora imati rollback.

Za ovaj LAW dokument rollback je:

git revert COMMIT_HASH

Za svaku buduću WordPress izmjenu minimalni rollback uključuje:

- backup posta
- backup meta podataka
- zapis korištene skripte
- zapis commit hasha
- javni QA prije i poslije

---

## 18. Adobe alati

Adobe Acrobat može pomoći kod pregleda PDF izvora i ručne provjere skeniranih dokumenata.

Photoshop se koristi samo za vizualni sloj i slike proizvoda, ne za receptne podatke.

InDesign može biti koristan za buduću tiskanu verziju, ali kanonski receptni podaci ostaju u recipe.yml i QA dokumentima.

---

## 19. Završna naredba

Ne mijenjati nijedan javni recept dok Recipe Type Router i read-only audit ne pokažu:

- tehnološki tip
- obvezna polja
- blokade
- fallback/interne tekstove
- sigurnosne rupe
- recepte spremne za pojedinačnu obradu

Bolje je imati manji broj ispravnih, provjerenih i vjerodostojnih recepata nego veliku bazu koja izgleda kao da ju je punio pijani mesorezač u ponoć.
