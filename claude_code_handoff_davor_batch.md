# Zadatak za Claude Code: Obrada velikog batcha novih recepata (Davorov istraženi materijal)

## Kontekst
Davor je dostavio detaljan, citiran materijal za ~35-40 hrvatskih regionalnih
suhomesnatih proizvoda, organiziran po mikroregijama. Materijal sadrži konkretne
sastojke (kg/g/postoci), začine, postupke i izvore. Treba ga obraditi i, gdje je
osnovan, objaviti na drycured.com u istoj shemi kao dosadašnji recepti
(_dry_recipe_overrides + _dry_verified_process, registracija u
aaa-drycured-registry-01B.php po uzoru na žlomprt/tiblicu/pazinsku/rovinjsku).

Puni tekst materijala je u prilogu ovog zadatka (vidi
`davor_batch_recipes_2026-06-28.txt` u istom direktoriju) — sadrži recepte
organizirane u nekoliko skupina po mikroregiji (Banija, Dalmacija/Zagora, Istra,
Slavonija/Baranja/Srijem, Cernik, Virovitičko-podravski kraj, Vukovarsko-srijemski
kraj/Cvelferija, Hrvatsko primorje, Samobor).

## ZADATAK A — Provjera postojanja u bazi (prije ičega drugog)
Za SVAKI recept iz materijala, provjeriti postoji li već post u bazi s istim ili
sličnim nazivom (pretraga po post_title i _dry_recipe_id). Izlistati CSV:
naziv iz materijala | postoji li već (Y/N) | post_id ako postoji | trenutni status.

## ZADATAK B — Klasifikacija po jačini izvora
Razdvojiti recepte u dvije skupine:

**Skupina 1 — JAKA podloga (može se objaviti bez dodatne provjere):**
Recepti s pozivom na službeni ZOI/ZOZP status, stručne poljoprivredne/prehrambene
izvore s konkretnim brojkama, ili izvore koje Davor osobno poznaje/potvrđuje.
Primjeri iz materijala: Slavonska kobasica (ZOZP, "Gastro Lega" izvor s točnim %),
Baranjski kulen (ZOZP, "The Story of Baranja Kulen"), Istarska kosnica (24sata.hr
+ konkretne brojke), Krvavice, Slavonski švargl, Lička kastradina (Agroklub izvor).

**Skupina 2 — TANJA podloga (NE objavljivati automatski, izlistati za Davorovu
potvrdu prije unosa):**
Recepti čiji jedini citat je generički blog, lokalni news članak o posjeti
gradonačelnika, ili formulacija poput "specifičnost ovog mikrolokalnog recepta"
bez jasnog dokaza da je naziv stvarno u tradicionalnoj upotrebi (isti obrazac kao
prethodno obrisani izmišljeni nazivi ovu sesiju — npr. Rovinjska/Pazinska kobasica
PRIJE Davorove osobne potvrde). Iz materijala, kandidati za ovu skupinu (provjeriti
i preostale): "Cernički ćupteti" (izvor: gastro-shopping blog), "Babogredski kulin"
(izvor: lokalna manifestacija/slogan, provjeriti postoji li stvarna receptura iza
toga ili je to samo naziv festivala), "Orahovački kulin", "Vrgorvačka sušena
panceta s medom", "Konavoska domaća kobasica s ljutikom i rakijom", "Žrnovska
dimljena pečenica s ružmarinom", "Turopoljska presana slanina", "Goranska
dimljena šunka", "Lička sušena govedina", "Ninski šokol" (TasteAtlas izvor — provjeriti
jačinu), "Slatinska kobasica" (izvor: Scribd katalog tvrtke — komercijalni katalog
nije isto što i tradicionalna receptura, provjeriti).

Pravilo: kad je nesigurno u koju skupinu ide, staviti u Skupinu 2 (draft, čeka
potvrdu) — isto pravilo kao cijeli dan: bolje manje objavljenih ali sigurnih.

## ZADATAK C — Za Skupinu 1: kreirati postove i podatke
Za svaki recept iz Skupine 1 koji NE postoji još u bazi (iz Zadatka A):
1. Kreirati `dry_recipe` post (status publish), s ispravnim slug-om
2. Postaviti `_dry_recipe_id` (novu šifru — koristiti uzorak HR-XX-0NN po regiji,
   npr. HR-SL-0xx za slavonske, HR-IS-0xx za istarske, HR-DA-0xx za dalmatinske,
   HR-BA-001 za banijske, HR-ME-0xx za međimurske/centralne, itd. — provjeriti
   koje prefikse već postoje u registry-u da se ne kolidira)
3. Popuniti `_dry_recipe_overrides` (materials/spices/liquids/casing_note/
   grinding_note/timeline) iz TOČNIH podataka koje je Davor dao — NE
   parafrazirati brojeve, prenijeti ih egzaktno
4. Popuniti `_dry_recipe_full_profile_json` NE koristiti — koristiti isti
   _dry_verified_process pristup kao za žlomprt/tiblicu/pazinsku/rovinjsku
   (phases s T/RH/day gdje je poznato, mljevenje podobjekt s točnom rešetkom u mm)
5. Dodati `profile` i `climate` override gdje generički kobasica/kulen default
   ne odgovara (npr. recepti BEZ paprike trebaju profile bez "Paprika"/"Ljutina"
   stavki — isti popravak kao za Pazinsku/Rovinjsku kobasicu ovu sesiju)
6. Registrirati u `aaa-drycured-registry-01B.php` (ili prikladnijem registry
   fileu ako se radi o drugoj regiji/klasteru — provjeriti postoji li npr.
   aaa-drycured-registry-01C.php za kontinentalnu Hrvatsku, ako ne postoji,
   razmotriti kreiranje analognog fajla za novi klaster)

## ZADATAK D — Za Skupinu 2: pripremiti, ali NE objavljivati
Za recepte iz Skupine 2, pripremiti isti JSON sadržaj (overrides + verified_process)
ali OSTAVITI POST KAO DRAFT. U izvještaju Davoru jasno naznačiti koji recepti
čekaju njegovu potvrdu i zašto (koji izvor je nedovoljan).

## Nakon dovršetka
1. Izvještaj: tablica po recept — Skupina (1/2), akcija (objavljeno/draft/već
   postojalo), post_id, šifra
2. Vizualna provjera 3-4 nasumična novo-objavljena recepta
3. Git commit+push
4. NE brisati niti mijenjati postojeće recepte koji nisu spomenuti u ovom batchu
