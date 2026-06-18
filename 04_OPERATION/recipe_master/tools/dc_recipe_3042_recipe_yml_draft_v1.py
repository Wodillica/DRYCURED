#!/usr/bin/env python3
from pathlib import Path
import json
import sys
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_3042_recipe_yml_draft_v1.py DOSSIER_DIR SOURCES_YML RECIPE_YML QA_REPORT", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
sources_path = Path(sys.argv[2])
recipe_path = Path(sys.argv[3])
qa_path = Path(sys.argv[4])

review_dirs = sorted((dossier_dir / "review").glob("recipe_yml_draft_v1_*"))
review_dir = review_dirs[-1] if review_dirs else dossier_dir / "review" / "recipe_yml_draft_v1_manual"
review_dir.mkdir(parents=True, exist_ok=True)

now = datetime.now(timezone.utc).isoformat()

recipe_yml = r'''# recipe.yml
dossier_status: "CANON_DRAFT_V1_NOT_PUBLIC"
public_update_allowed: false
post_id: 3042
title: "Jésus de Lyon – debela suha kobasica"
url: "https://drycured.com/recepti-baza/jesus-de-lyon-debela-suha-kobasica/"
recipe_type: "GROUND_MEAT_OR_CASING"
language: "hr"
batch_size_kg: 10
canonical_recipe_ready: false
public_verified: false
source_validation_status: "PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED"
protected_status_claim_allowed: false
reference_model: "HR-SL-005 Slavonska domaća kobasica — dizajnerski/sadržajni model za mljevene proizvode u omotaču; ne kopirati sastojke ni parametre."

editorial_warning:
  - "Ovo je kanonski radni nacrt, nije javni recept."
  - "Vanjski izvori potvrđuju proizvod i tehnološku obitelj, ali ne potvrđuju sve količine iz postojećeg WP zapisa."
  - "Količine su normalizirane iz postojećeg WP/MD nacrta na 10 kg i moraju proći dodatnu tehnološku provjeru."
  - "Ne tvrditi aktualni IGP/ZOI/zaštićeni status."
  - "Javni update nije dopušten u ovom koraku."

product_identity:
  canonical_name: "Jésus de Lyon"
  croatian_display_name: "Jésus de Lyon – debela suha kobasica"
  region_country: "Lyon, Francuska"
  product_family: "suhi saucisson / debela suha kobasica"
  product_type: "mljeveno/usitnjeno meso u prirodnom ovitku"
  source_confidence: "PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED"
  public_status_note: "Proizvod je potvrđen kao lyonški suhi saucisson; aktualni zaštićeni status nije potvrđen za javno navođenje."

batch:
  total_meat_mass_kg: 10.000
  yield_target:
    weight_loss_percent: "35–40"
    source_status: "WP_DRAFT_TECHNOLOGICAL_GUIDELINE"
  expected_duration:
    value: "oko 45–60 dana"
    source_status: "WP_DRAFT_TECHNOLOGICAL_GUIDELINE"

raw_materials_kg:
  - item: "svinjska lopatica"
    amount_kg: 5.833
    percent: 58.33
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "svinjska potrbušina bez kože"
    amount_kg: 2.500
    percent: 25.00
    source_status: "WP_DRAFT_SCALED_FROM_MD"
    note: "Kožu ukloniti prije mljevenja; u nadjev ulazi samo mesno-masni dio."
  - item: "tvrda leđna slanina / svinjska masnoća"
    amount_kg: 1.667
    percent: 16.67
    source_status: "WP_DRAFT_SCALED_FROM_MD"
    note: "Masnoća mora ostati hladna i čvrsta kako se ne bi razmazala u presjeku."

spices_and_additives_g:
  - item: "morska sol"
    amount_g: 200
    percent: 2.00
    g_per_kg: 20.0
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "šećer"
    amount_g: 42
    percent: 0.42
    g_per_kg: 4.2
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "mljeveni crni papar"
    amount_g: 50
    percent: 0.50
    g_per_kg: 5.0
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "mljeveni bijeli papar"
    amount_g: 25
    percent: 0.25
    g_per_kg: 2.5
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "sušeni češnjak u prahu"
    amount_g: 42
    percent: 0.42
    g_per_kg: 4.2
    source_status: "WP_DRAFT_SCALED_FROM_MD"
    garlic_mode: "direct_powder"
  - item: "mljeveni piment / allspice"
    amount_g: 25
    percent: 0.25
    g_per_kg: 2.5
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "sjemenke gorušice, mljevene"
    amount_g: 17
    percent: 0.17
    g_per_kg: 1.7
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "starter kultura T-SPX ili slična"
    amount_g: 42
    source_status: "WP_DRAFT_SCALED_FROM_MD_REQUIRES_TECHNICAL_REVIEW"
    warning: "Količina starter kulture je mehanički skalirana iz WP/MD nacrta i mora se provjeriti prema deklaraciji proizvođača starter kulture prije javne objave."

liquids:
  - item: "konjak"
    amount_l: 0.208
    amount_ml: 208
    source_status: "WP_DRAFT_SCALED_FROM_MD"
  - item: "mlaka destilirana voda za razrjeđivanje startera"
    amount_l: 0.083
    amount_ml: 83
    source_status: "WP_DRAFT_SCALED_FROM_MD_REQUIRES_TECHNICAL_REVIEW"

garlic_liquid_details:
  used: false
  mode: "sušeni češnjak u prahu dodaje se izravno"
  garlic_amount_g: 42
  liquid_type: null
  liquid_amount_l: null
  steeping_time: null
  boiled_or_cold: null
  strained_amount_added_l: null
  note: "U ovom radnom nacrtu ne koristi se procijeđena tekućina od češnjaka. Ako se u kasnijem izvoru pojavi svježi češnjak, treba izraditi varijantu s procijeđenom tekućinom i svim detaljima."

grinding:
  pre_cut: "meso narezati na kocke 2–3 cm"
  meat_plate_mm: "6–8"
  fat_cut_mm: null
  fat_handling: "meso, potrbušinu i leđnu slaninu držati vrlo hladnima; masnoća mora biti čvrsta prije mljevenja"
  temperature_control: "temperatura smjese ne smije prijeći 8 °C"
  source_status: "WP_DRAFT_TECHNOLOGICAL_GUIDELINE"
  qa_note: "Za javnu objavu dodatno odlučiti treba li tvrdu leđnu slaninu mljeti zajedno s mesom ili rezati na sitnije kockice; postojeći WP nacrt navodi mljevenje 6–8 mm."

casing:
  type: "svinjska crijeva"
  diameter_mm: "28–32"
  soaking_liquid: "mlaka voda"
  soaking_time: "30–45 minuta"
  soaking_temperature_c: "25–30"
  boiled_or_cold_liquid: "ne prokuhavati"
  source_status: "WP_DRAFT"
  note: "Službeni opis proizvoda potvrđuje prirodni ovitak, ali ne potvrđuje ovaj točan promjer za kućnu varijantu."

process:
  preparation:
    - "Meso i masnoću očistiti od žila, krvnih podljeva i labavih dijelova."
    - "Potrbušini ukloniti kožu prije mljevenja."
    - "Sirovinu ohladiti na 0–2 °C prije rezanja i mljevenja."
  starter_preparation:
    - "Starter kulturu razmutiti u mlakoj destiliranoj vodi prema uputi proizvođača."
    - "Količinu starter kulture prije javne objave provjeriti prema deklaraciji proizvođača."
  mixing:
    - "Dodati sol, šećer, začine, češnjak u prahu, konjak i pripremljenu starter kulturu."
    - "Miješati dok se nadjev ne poveže i ne postane blago ljepljiv."
    - "Tijekom miješanja održavati hladnu masu."
  resting:
    duration: "12–24 sata"
    temperature_c: "2–4"
    purpose: "ravnomjerno raspoređivanje soli i začina"
  stuffing:
    - "Puniti u pripremljena svinjska crijeva bez zračnih džepova."
    - "Puniti čvrsto, ali bez pucanja ovitka."
    - "Zračne džepove probosti sterilnom iglom."
  fermentation_or_predrying:
    duration: "12–48 sati"
    conditions: "hladan i prozračan prostor ili kontrolirana fermentacija prema starter kulturi"
    source_status: "WP_DRAFT_TECHNOLOGICAL_GUIDELINE"
  smoking:
    used: "needs_confirmation"
    method: "hladni dim ako se potvrdi da se u ovoj kućnoj varijanti dimi"
    temperature_c: "ispod 25"
    cycle_count: "više kratkih ciklusa, samo ako se dimljenje potvrdi"
    source_status: "WP_DRAFT_NOT_OFFICIAL_CONFIRMED"
    warning: "Službeni izvori potvrđuju suhi saucisson, ali ne potvrđuju obvezno dimljenje. Ne smije se javno prikazati kao obvezna faza dok se ne potvrdi izvor."
  drying_and_aging:
    temperature_c: "10–15"
    relative_humidity_percent: "70–80"
    target_weight_loss_percent: "35–40"
    source_status: "WP_DRAFT_TECHNOLOGICAL_GUIDELINE"
    critical_control: "Ako se površina prebrzo suši, smanjiti propuh, povisiti relativnu vlagu i produljiti zrenje."

nitrite_salt:
  used: false
  source_status: "WP_DRAFT_NO_NITRITE_LISTED"
  safety_note_required: false
  note: "U ovom radnom nacrtu navedena je morska sol, a nitritna sol nije navedena. Ako se doda varijanta s nitritnom soli, obavezno uključiti sigurnosnu napomenu o preciznom vaganju i zabrani prekoračenja."

sensory_profile:
  texture: "čvrsta, reziva suha kobasica, bez vlažne jezgre"
  cut_surface: "stabilan presjek; masnoća ne smije biti razmazana"
  aroma: "čist suhomesnati miris, papar, blagi češnjak, začinski tonovi i eventualno blaga nota konjaka"
  source_status: "WP_DRAFT_EDITORIAL"

done_when:
  - "proizvod je izgubio približno 35–40 % početne mase"
  - "presjek je stabilan, bez vlažne jezgre"
  - "miris je čist, bez truležnog, sluzavog, užeglog ili neugodnog tona"
  - "površina je suha, ali nije pretvrdo zapečena"
  - "može se rezati tanko bez razmazivanja masnoće"

common_errors_and_solutions:
  - problem: "površina se prebrzo stvrdnula, a sredina ostaje mekana"
    cause: "prejak propuh, preniska relativna vlaga ili prebrzo sušenje"
    solution: "smanjiti strujanje zraka, povisiti relativnu vlagu i produljiti zrenje"
    risk: "medium"
  - problem: "mast se razmazuje u presjeku"
    cause: "meso ili slanina nisu bili dovoljno hladni tijekom mljevenja i miješanja"
    solution: "prekinuti obradu, ohladiti sirovinu, nož, pužnicu i posudu te nastaviti tek kada je masa ponovno čvrsta"
    risk: "medium"
  - problem: "zračni džepovi u ovitku"
    cause: "neravnomjerno punjenje ili nedovoljno odzračivanje"
    solution: "puniti čvrsto, ali bez pucanja ovitka; zračne džepove probosti sterilnom iglom"
    risk: "medium"
  - problem: "neugodan, truo, sluzav ili sumnjiv miris"
    cause: "previsoka temperatura, kvarenje, loša higijena ili presporo sušenje"
    solution: "proizvod ne koristiti; ne prikrivati začinima; pregledati higijenu, soljenje, temperaturu i uvjete zrenja prije nove izrade"
    risk: "high"

serving_and_storage:
  serving: "rezati tanko čistim nožem nakon kratkog odmora na sobnoj temperaturi"
  pairing: "poslužiti uz kruh, sir, ukiseljeno povrće ili jednostavnu zakusku"
  storage: "čuvati na 8–12 °C u tamnom i prozračnom prostoru bez kondenzacije"
  after_cutting: "presjek zaštititi od pretjeranog isušivanja"
  safety_discard: "ne konzumirati ako se pojavi neugodan miris, sluzava površina ili sumnjiva promjena boje"

qa_blockers_before_public_update:
  - "kanonski izvor za točne količine nije potvrđen"
  - "količina starter kulture zahtijeva tehničku provjeru"
  - "dimljenje je označeno kao needs_confirmation"
  - "javni tekst još sadrži interne tragove prema intake izvještaju"
  - "potrebno je završiti qa_report.md prije bilo kakvog WordPress updatea"
'''

recipe_path.write_text(recipe_yml, encoding="utf-8")

draft_payload = {
    "generated_at": now,
    "post_id": 3042,
    "title": "Jésus de Lyon – debela suha kobasica",
    "status": "CANON_DRAFT_V1_NOT_PUBLIC",
    "public_update_allowed": False,
    "source_validation_status": "PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED",
    "important_blockers": [
        "recipe quantities not fully source-confirmed",
        "starter culture amount requires technical review",
        "smoking requires confirmation",
        "public internal terms detected in intake",
        "qa_report not complete"
    ],
    "draft_recipe_yml": str(recipe_path)
}
(review_dir / "3042_recipe_yml_draft_v1.json").write_text(
    json.dumps(draft_payload, ensure_ascii=False, indent=2),
    encoding="utf-8"
)

md = []
md.append("# 3042 Jésus de Lyon — recipe.yml draft v1")
md.append("")
md.append("Status: **CANON_DRAFT_V1_NOT_PUBLIC**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Popunjen je samo radni `recipe.yml` u dosjeu.")
md.append("")
md.append("## Što je napravljeno")
md.append("")
md.append("- Recept je strukturiran kao `GROUND_MEAT_OR_CASING`.")
md.append("- Šarža je postavljena na 10 kg.")
md.append("- Sirovine su navedene u kg.")
md.append("- Začini su navedeni u g, postotku i g/kg.")
md.append("- Crijeva imaju tip, promjer, namakanje, tekućinu, temperaturu i napomenu da se ne prokuhavaju.")
md.append("- Češnjak je označen kao izravni sušeni češnjak u prahu, bez procijeđene tekućine.")
md.append("- Mljevenje je označeno kao 6–8 mm, uz obveznu hladnu obradu.")
md.append("- Problemi imaju konkretna rješenja.")
md.append("")
md.append("## Blokade prije javnog updatea")
md.append("")
md.append("- Količine iz WP/MD nacrta nisu potpuno potvrđene vanjskim izvorom.")
md.append("- Količina starter kulture je mehanički skalirana i mora se provjeriti prema deklaraciji proizvođača.")
md.append("- Dimljenje je označeno kao `needs_confirmation`; ne smije se prikazati kao obvezna faza bez dodatnog izvora.")
md.append("- Intake je detektirao javne interne tragove; to se mora očistiti prije javnog updatea.")
md.append("- `qa_report.md` još nije završno zatvoren.")
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Dosje je spreman za QA reviziju radnog nacrta. Javni update i dalje nije dopušten.")
md.append("")
md.append("Datoteka: `recipe.yml`")
md.append("")

(review_dir / "3042_RECIPE_YML_DRAFT_V1_REPORT.md").write_text("\n".join(md), encoding="utf-8")

qa_text = qa_path.read_text(encoding="utf-8")
marker = "<!-- DC_3042_RECIPE_YML_DRAFT_V1 -->"
append = f"""
{marker}

## Recipe.yml draft v1

Status: **CANON_DRAFT_V1_NOT_PUBLIC**

- [x] `recipe.yml` je popunjen kao radni nacrt.
- [x] Šarža je 10 kg.
- [x] Sirovine su u kg.
- [x] Začini su u g.
- [x] Crijeva imaju namakanje.
- [x] Češnjak je označen kao sušeni češnjak u prahu, bez procijeđene tekućine.
- [x] Problemi imaju rješenja.
- [ ] Količina starter kulture nije tehnički potvrđena.
- [ ] Dimljenje nije potvrđeno kao obvezna faza.
- [ ] Javni update nije dopušten.
- [ ] Završni QA nije zatvoren.

Report: `review/{review_dir.name}/3042_RECIPE_YML_DRAFT_V1_REPORT.md`
"""
if marker not in qa_text:
    qa_path.write_text(qa_text.rstrip() + "\n\n" + append.strip() + "\n", encoding="utf-8")

print("=== 3042 RECIPE.YML DRAFT COMPLETE ===")
print("STATUS=CANON_DRAFT_V1_NOT_PUBLIC")
print("PUBLIC_UPDATE_ALLOWED=false")
print("RECIPE_YML_UPDATED=true")
print("STARTER_CULTURE_REQUIRES_REVIEW=true")
print("SMOKING_REQUIRES_CONFIRMATION=true")
print(f"RECIPE={recipe_path}")
print(f"REPORT={review_dir / '3042_RECIPE_YML_DRAFT_V1_REPORT.md'}")
