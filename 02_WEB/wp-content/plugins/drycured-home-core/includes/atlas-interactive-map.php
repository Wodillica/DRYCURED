<?php
defined('ABSPATH') || exit;

/**
 * Drycured Atlas Interactive Map v0.1.73
 * Klik na područje Europe otvara detaljnu kartu, proizvode i stil proizvodnje.
 */

function drycured_atlas_v073_base_url() {
    return home_url('/wp-content/uploads/drycured/atlas/');
}

function drycured_atlas_v073_items() {
    return [
        'hr' => ['x'=>55,'y'=>63,'title'=>'Hrvatska','web'=>'atlas_hr_hrvatska.webp','type'=>'Državna karta','style'=>'Jadranski, gorski i panonski stilovi: od pršuta i pancete do kulena, čvaraka i suhih kobasica.','products'=>['dalmatinski pršut','istarski pršut','slavonski kulen','panceta','suhe kobasice']],
        'ba' => ['x'=>58,'y'=>66,'title'=>'Bosna i Hercegovina','web'=>'atlas_ba_bosna_i_hercegovina.webp','type'=>'Državna karta','style'=>'Planinsko-kontinentalna tradicija dimljenja i sušenja, s jakim regionalnim razlikama između Posavine, Krajine, Bosne i Hercegovine.','products'=>['suho meso','sudžuk','pečenica','suha vratina','slanina']],
        'si' => ['x'=>52,'y'=>59,'title'=>'Slovenija','web'=>'atlas_si_slovenija.webp','type'=>'Državna karta','style'=>'Spoj alpske, panonske i primorske logike: kontrolirano sušenje, dimljenje, pršut i regionalne kobasice.','products'=>['kranjska kobasica','kraški pršut','zaseka','dolenjska kobasica','prekmurska šunka']],
        'rs' => ['x'=>62,'y'=>66,'title'=>'Srbija','web'=>'atlas_rs_srbija.webp','type'=>'Državna karta','style'=>'Ravničarski i brdsko-planinski stilovi s naglaskom na dimljene kobasice, suho meso, pečenicu i papriku.','products'=>['kulen','pečenica','sudžuk','suvi vrat','pirotska kobasica']],
        'it' => ['x'=>51,'y'=>68,'title'=>'Italija','web'=>'atlas_it_italija.webp','type'=>'Državna karta','style'=>'Izrazito regionalna kultura sušenja i zrenja: od alpskog specka do pršuta, salama, pancete i larda.','products'=>['prosciutto','salame','pancetta','speck','lardo']],
        'fr' => ['x'=>41,'y'=>55,'title'=>'Francuska','web'=>'atlas_fr_francuska.webp','type'=>'Državna karta','style'=>'Zapadnoeuropski stil dugog zrenja, regionalnih šunki, suhih kobasica i proizvoda oblikovanih klimom, vinom i lokalnom tradicijom.','products'=>['saucisson sec','jambon sec','coppa corse','andouille','jambon de Bayonne']],
        'de' => ['x'=>49,'y'=>46,'title'=>'Njemačka','web'=>'atlas_de_njemacka.webp','type'=>'Državna karta','style'=>'Tehnički uredan srednjoeuropski stil: kobasice, dimljenje, soljenje i stabilna kontrola procesa.','products'=>['mettwurst','landjäger','schinken','blutwurst','speck']],
        'pl' => ['x'=>58,'y'=>43,'title'=>'Poljska','web'=>'atlas_pl_poljska.webp','type'=>'Državna karta','style'=>'Istočno-srednjoeuropski stil s naglaskom na dimljene kobasice, hladniju klimu, soljenje i dugotrajno čuvanje.','products'=>['kiełbasa','kabanosy','szynka','boczek','krakowska']],
        'by' => ['x'=>60.5,'y'=>49.5,'title'=>'Bjelorusija','web'=>'atlas_by_bjelorusija.webp','type'=>'Državna karta','style'=>'Istočnoeuropski stil hladnijeg podneblja: sušenje na zraku, blago dimljenje, zrenje, češnjak, papar, kim i domaća tradicija očuvanja mesa.','products'=>['polendvica','kabanosy','suha svinina','salo','sudžuk gomeljski']],
        'hu' => ['x'=>59,'y'=>57,'title'=>'Mađarska','web'=>'atlas_hu_madjarska.webp','type'=>'Državna karta','style'=>'Panonski stil paprike, dimljenja, sušenja i masnijih proizvoda snažnog aromatskog potpisa.','products'=>['csabai salama','gyulai kolbász','mangalica šunka','slanina','papricirana kobasica']],
        'es' => ['x'=>35,'y'=>69,'title'=>'Španjolska','web'=>'atlas_es_spanjolska.webp','type'=>'Državna karta','style'=>'Mediteransko-atlantski stil dugog zrenja, svinjske masti, paprike, planinskih šunki i regionalnih kobasica.','products'=>['jamón ibérico','chorizo','lomo embuchado','cecina','fuet']],
        'pt' => ['x'=>30,'y'=>69,'title'=>'Portugal','web'=>'atlas_pt_portugal.webp','type'=>'Državna karta','style'=>'Atlantsko-mediteranski stil sa suhim kobasicama, dimljenjem, solju i snažnim regionalnim razlikama.','products'=>['chouriço','presunto','alheira','paio','morcela']],
        'mt' => ['x'=>55,'y'=>80,'title'=>'Malta','web'=>'atlas_mt_malta.webp','type'=>'Državna karta','style'=>'Mali otočni mediteranski stil s jakim utjecajem soli, suhog zraka, maslinovog ulja i lokalnih kućnih proizvoda.','products'=>['zalzett','majjal imqadded','ġbejna','pastizzi tal-lardo','slanina']],
        'gb' => ['x'=>29,'y'=>35,'title'=>'Velika Britanija, Irska i Island','web'=>'atlas_gb_ie_is_velika_britanija_irska_island.webp','type'=>'Regionalna karta','style'=>'Atlantski i sjeverni stilovi: soljenje, dimljenje, hladna klima, sušeni komadi i proizvodi za dugotrajno čuvanje.','products'=>['bacon','ham','black pudding','air-dried beef','hangikjöt']],
        'au_ch' => ['x'=>49,'y'=>56,'title'=>'Austrija i Švicarska','web'=>'atlas_at_ch_austrija_svicarska.webp','type'=>'Regionalna karta','style'=>'Alpski stil hladnog zraka, sporog zrenja, dimljenja i precizne kontrole teksture.','products'=>['speck','bündnerfleisch','selchfleisch','suhe kobasice','šunka']],
        'cz_sk' => ['x'=>54,'y'=>52,'title'=>'Češka i Slovačka','web'=>'atlas_cz_sk_ceska_slovacka.webp','type'=>'Regionalna karta','style'=>'Srednjoeuropski stil dimljenih kobasica, šunki i proizvoda za kuhanje, pečenje i sušenje.','products'=>['klobása','šunka','slanina','uzené maso','suhe kobasice']],
        'ro_bg' => ['x'=>67,'y'=>65,'title'=>'Rumunjska i Bugarska','web'=>'atlas_ro_bg_rumunjska_bugarska.webp','type'=>'Regionalna karta','style'=>'Karpatsko-balkanski stil dimljenja, paprike, češnjaka i suhih proizvoda za duže čuvanje.','products'=>['salam de Sibiu','lukanka','sudžuk','slanina','dimljena šunka']],
        'ua_md' => ['x'=>73,'y'=>55,'title'=>'Ukrajina i Moldavija','web'=>'atlas_ua_md_ukrajina_moldavija.webp','type'=>'Regionalna karta','style'=>'Istočnoeuropski stil hladnije klime, dimljenja, slanine, kobasica i fermentiranih tradicija.','products'=>['kovbasa','salo','dimljena slanina','sušena šunka','kobasice']],
        'ru' => ['x'=>82,'y'=>40,'title'=>'Europski dio Rusije','web'=>'atlas_ru_europski_dio_rusije.webp','type'=>'Regionalna karta','style'=>'Hladni kontinentalni stil očuvanja mesa soljenjem, dimljenjem i dugim čuvanjem u stabilnim uvjetima.','products'=>['kolbasa','slanina','dimljeno meso','sušeni komadi','divljač']],
        'me_al_xk' => ['x'=>61,'y'=>72,'title'=>'Crna Gora, Kosovo i Albanija','web'=>'atlas_me_al_xk_crna_gora_albanija_kosovo.webp','type'=>'Regionalna karta','style'=>'Planinsko-mediteranski prijelaz: suho meso, dim, sol, ovčetina, govedina i lokalne kućne metode.','products'=>['pršut','suho meso','sudžuk','dimljena slanina','suhe kobasice']],
        'mk_gr_tr' => ['x'=>66,'y'=>76,'title'=>'Makedonija, Grčka i Turska','web'=>'atlas_mk_gr_tr_makedonija_grcka_turska.webp','type'=>'Regionalna karta','style'=>'Jugoistočni mediteranski stil s toplijom klimom, začinima, fermentacijom i jakim lokalnim aromama.','products'=>['sujuk','pastirma','loukaniko','dimljene kobasice','suho meso']],
        'lt_lv_ee' => ['x'=>63,'y'=>35,'title'=>'Litva, Latvija i Estonija','web'=>'atlas_lt_lv_ee_litva_latvija_estonija.webp','type'=>'Regionalna karta','style'=>'Baltički stil hladne klime, dimljenja, soljenja i dugog čuvanja proizvoda.','products'=>['dimljene kobasice','slanina','šunka','sušeno meso','divljač']],
        'se_no_fi' => ['x'=>58,'y'=>23,'title'=>'Švedska, Norveška i Finska','web'=>'atlas_se_no_fi_svedska_norveska_finska.webp','type'=>'Regionalna karta','style'=>'Nordijski stil hladnoće, sušenja, dimljenja, ribe, divljači i stabilnog dugog čuvanja.','products'=>['dimljeno meso','sušena divljač','soljeni komadi','kobasice','dimljena riba']],
        'ben_dan' => ['x'=>45,'y'=>39,'title'=>'Benelux i Danska','web'=>'atlas_be_nl_lu_dk_benelux_danska.webp','type'=>'Regionalna karta','style'=>'Sjeverno-zapadni stil s naglaskom na šunke, kobasice, slaninu, dim i vlažniju morsku klimu.','products'=>['ham','bacon','rookworst','spegepølse','dimljene kobasice']],
        'med' => ['x'=>47,'y'=>77,'title'=>'Mediteranski pojas','web'=>'atlas_eu_mediteran.webp','type'=>'Pregledna karta','style'=>'Širi proizvodni pojas mora, soli, suhog zraka, maslinovog ulja i dugog zrenja.','products'=>['pršut','panceta','salame','suhe kobasice','suha vratina']],
        'pan' => ['x'=>60,'y'=>58,'title'=>'Panonska Europa','web'=>'atlas_eu_panonska.webp','type'=>'Pregledna karta','style'=>'Kontinentalni stil dimljenja, paprike, češnjaka, fermentacije i stabilnog hladnijeg sušenja.','products'=>['kulen','papricirane kobasice','dimljena šunka','slanina','čvarci']],
        'bal' => ['x'=>61,'y'=>68,'title'=>'Balkan','web'=>'atlas_eu_balkan.webp','type'=>'Pregledna karta','style'=>'Dinarsko-balkanski proizvodni prostor: dim, planina, soljenje, suho meso i jaka kućna tradicija.','products'=>['suho meso','sudžuk','pečenica','pršut','dimljena slanina']],
        'istok' => ['x'=>72,'y'=>48,'title'=>'Istočna Europa','web'=>'atlas_eu_istocna_evropa.webp','type'=>'Pregledna karta','style'=>'Hladniji kontinentalni prostor dimljenja, slanine, kobasica i proizvoda za dugotrajno čuvanje.','products'=>['kobasice','slanina','dimljeno meso','sušena šunka','divljač']],
        'zapad' => ['x'=>36,'y'=>53,'title'=>'Zapadna Europa','web'=>'atlas_eu_zapadna_evropa.webp','type'=>'Pregledna karta','style'=>'Atlantsko-zapadni stil vlage, soli, sporog sazrijevanja, šunki i suhih kobasica.','products'=>['saucisson sec','jamón','chouriço','jambon sec','bacon']],
        'sredina' => ['x'=>50,'y'=>50,'title'=>'Središnja Europa','web'=>'atlas_eu_sredisnja_evropa.webp','type'=>'Pregledna karta','style'=>'Srednjoeuropska logika urednog procesa, dimljenja, kobasica, šunki i precizne kontrole.','products'=>['speck','kobasice','šunka','slanina','sušeni komadi']],
        'baltik' => ['x'=>64,'y'=>36,'title'=>'Baltik','web'=>'atlas_eu_baltik.webp','type'=>'Pregledna karta','style'=>'Hladna klima, soljenje, dimljenje i proizvodi za duže čuvanje u sjevernoistočnom prostoru.','products'=>['dimljene kobasice','slanina','šunka','sušeno meso']],
        'nord' => ['x'=>57,'y'=>24,'title'=>'Nordijske zemlje','web'=>'atlas_eu_nordijske.webp','type'=>'Pregledna karta','style'=>'Sjeverni stil hladnoće, sporog sušenja, dimljenja, divljači i ribe.','products'=>['dimljeno meso','sušena divljač','soljeni komadi','kobasice']],
    ];
}

function drycured_atlas_v073_shortcode() {
    $items = drycured_atlas_v073_items();
    $base = drycured_atlas_v073_base_url();
    $main_map = $base . 'web/atlas_eu_cijela.webp';
    $json = wp_json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    ob_start();
    ?>
    <section class="dc-atlas-map-v073" id="interaktivna-karta-europe">
        <header class="dc-atlas-map-head-v073">
            <div class="dc-atlas-kicker-v073">Interaktivna karta</div>
            <h2>Odaberite područje na karti Europe</h2>
            <p>Klikom na državu, regiju ili proizvodni pojas otvara se detaljna karta, najvažniji proizvodi i stil proizvodnje. Galerija svih karata ostaje ispod kao arhiva.</p>
        </header>

        <div class="dc-atlas-shell-v073">
            <div class="dc-atlas-main-map-v073">
                <img src="<?php echo esc_url($main_map); ?>" alt="Interaktivna karta Europe" loading="lazy">
                <?php foreach ($items as $key => $item) : ?>
                    <button type="button"
                        class="dc-atlas-pin-v073"
                        style="left:<?php echo esc_attr($item['x']); ?>%;top:<?php echo esc_attr($item['y']); ?>%;"
                        data-atlas-key="<?php echo esc_attr($key); ?>">
                        <?php echo esc_html($item['title']); ?>
                    </button>
                <?php endforeach; ?>

                <button type="button"
                    class="dc-atlas-pin-v073 dc-atlas-pin-v073--iceland"
                    data-atlas-key="gb"
                    aria-label="Island — otvori kartu Velike Britanije, Irske i Islanda">
                    Island
                </button>
            </div>

            <aside class="dc-atlas-detail-v073">
                <div class="dc-atlas-kicker-v073" data-atlas-type>Državna karta</div>
                <h3 data-atlas-title>Hrvatska</h3>

                <a data-atlas-map-link href="<?php echo esc_url($base . 'web/atlas_hr_hrvatska.webp'); ?>" target="_blank" rel="noopener" class="dc-atlas-preview-link-v073">
                    <img data-atlas-preview src="<?php echo esc_url($base . 'web/atlas_hr_hrvatska.webp'); ?>" alt="Detaljna karta" loading="lazy">
                </a>

                <div class="dc-atlas-block-v073">
                    <strong>Stil proizvodnje</strong>
                    <p data-atlas-style></p>
                </div>

                <div class="dc-atlas-block-v073">
                    <strong>Najvažniji proizvodi</strong>
                    <div class="dc-atlas-tags-v073" data-atlas-products></div>
                </div>

                <div class="dc-atlas-actions-v073">
                    <a data-atlas-open href="<?php echo esc_url($base . 'web/atlas_hr_hrvatska.webp'); ?>" target="_blank" rel="noopener">Otvori detaljnu kartu</a>
                    <a href="<?php echo esc_url(home_url('/kalkulator/')); ?>">Kalkulator receptura</a>
                </div>
            </aside>
        </div>
    </section>

    <script id="dc-atlas-map-data-v073" type="application/json"><?php echo $json; ?></script>
    <?php
    return trim(ob_get_clean());
}
add_shortcode('drycured_atlas_interactive_map', 'drycured_atlas_v073_shortcode');

function drycured_atlas_v073_styles() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-map-style-v073">
    .dc-atlas-map-v073{max-width:1320px;margin:28px auto 42px;padding:0 24px;color:#102033}
    .dc-atlas-map-head-v073{max-width:980px;margin-bottom:22px}
    .dc-atlas-kicker-v073{color:#9a7838;font-size:12px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;margin-bottom:9px}
    .dc-atlas-map-head-v073 h2{margin:0 0 10px;font-size:clamp(27px,2.6vw,40px);line-height:1.14;color:#102033}
    .dc-atlas-map-head-v073 p{margin:0;color:#374151;font-size:16px;line-height:1.7}
    .dc-atlas-shell-v073{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(360px,.75fr);gap:18px;align-items:start}
    .dc-atlas-main-map-v073,.dc-atlas-detail-v073{background:#fffaf0;border:1px solid rgba(184,135,53,.24);border-radius:18px;box-shadow:0 12px 30px rgba(31,41,51,.07)}
    .dc-atlas-main-map-v073{position:relative;padding:14px;overflow:hidden}
    .dc-atlas-main-map-v073 img{display:block;width:100%;height:auto;border-radius:14px}
    .dc-atlas-pin-v073{position:absolute;transform:translate(-50%,-50%);z-index:2;border:1px solid rgba(255,255,255,.75);background:rgba(16,32,51,.82);color:#fff;border-radius:999px;padding:7px 9px;font-size:11px;font-weight:800;line-height:1;cursor:pointer;white-space:nowrap;box-shadow:0 8px 18px rgba(16,32,51,.22);transition:.18s ease}
    .dc-atlas-pin-v073:hover,.dc-atlas-pin-v073.is-active{background:#9a7838;transform:translate(-50%,-50%) scale(1.07)}
    .dc-atlas-detail-v073{padding:22px;position:sticky;top:100px}
    .dc-atlas-detail-v073 h3{margin:0 0 14px;font-size:28px;line-height:1.16;color:#102033}
    .dc-atlas-preview-link-v073{display:block;text-decoration:none!important;background:#fffdf8;border-radius:14px;overflow:hidden;border:1px solid rgba(154,120,56,.15)}
    .dc-atlas-preview-link-v073 img{display:block;width:100%;height:auto}
    .dc-atlas-block-v073{margin-top:16px;padding-top:16px;border-top:1px solid rgba(154,120,56,.16)}
    .dc-atlas-block-v073 strong{display:block;margin-bottom:7px;color:#102033;font-size:13px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
    .dc-atlas-block-v073 p{margin:0;color:#374151;font-size:15.5px;line-height:1.65}
    .dc-atlas-tags-v073{display:flex;flex-wrap:wrap;gap:8px}
    .dc-atlas-tags-v073 span{display:inline-flex;padding:7px 9px;border-radius:999px;background:rgba(154,120,56,.11);color:#8b6f47;font-size:13px;font-weight:700}
    .dc-atlas-actions-v073{display:flex;flex-wrap:wrap;gap:9px;margin-top:20px}
    .dc-atlas-actions-v073 a{display:inline-flex;align-items:center;justify-content:center;background:#9a7838;color:#fff!important;border-radius:8px;padding:10px 12px;text-decoration:none!important;font-size:13.5px;font-weight:700}
    .dc-atlas-actions-v073 a:nth-child(2){background:#102033}
    @media(max-width:1100px){.dc-atlas-shell-v073{grid-template-columns:1fr}.dc-atlas-detail-v073{position:static}}
    @media(max-width:760px){.dc-atlas-map-v073{padding:0 16px}.dc-atlas-pin-v073{position:static;transform:none;margin:7px 5px 0 0;display:inline-flex}.dc-atlas-pin-v073:hover,.dc-atlas-pin-v073.is-active{transform:none}.dc-atlas-main-map-v073{display:block}}
    </style>
    <?php
}
add_action('wp_head','drycured_atlas_v073_styles',70);

function drycured_atlas_v073_script() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <script id="drycured-atlas-map-script-v073">
    (function(){
        function data(){
            const n=document.getElementById('dc-atlas-map-data-v073');
            try{return JSON.parse(n.textContent||'{}')}catch(e){return{}}
        }
        function setZone(root,items,key){
            const item=items[key]||items.hr;
            const base='<?php echo esc_js(drycured_atlas_v073_base_url()); ?>';
            const url=base+'web/'+item.web;

            root.querySelector('[data-atlas-type]').textContent=item.type||'Karta';
            root.querySelector('[data-atlas-title]').textContent=item.title||'';
            root.querySelector('[data-atlas-style]').textContent=item.style||'';
            root.querySelector('[data-atlas-preview]').src=url;
            root.querySelector('[data-atlas-preview]').alt=item.title||'Detaljna karta';
            root.querySelector('[data-atlas-map-link]').href=url;
            root.querySelector('[data-atlas-open]').href=url;

            const tags=root.querySelector('[data-atlas-products]');
            tags.innerHTML='';
            (item.products||[]).forEach(function(p){
                const s=document.createElement('span');
                s.textContent=p;
                tags.appendChild(s);
            });

            root.querySelectorAll('.dc-atlas-pin-v073').forEach(function(btn){
                btn.classList.toggle('is-active',btn.getAttribute('data-atlas-key')===key);
            });
        }
        function init(){
            const root=document.querySelector('.dc-atlas-map-v073');
            if(!root)return;
            const items=data();
            root.querySelectorAll('.dc-atlas-pin-v073').forEach(function(btn){
                btn.addEventListener('click',function(){
                    setZone(root,items,btn.getAttribute('data-atlas-key'));
                });
            });
            setZone(root,items,'hr');
        }
        if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init)}else{init()}
    })();
    </script>
    <?php
}
add_action('wp_footer','drycured_atlas_v073_script',70);

function drycured_atlas_v073_insert($content) {
    if (is_admin() || wp_doing_ajax()) return $content;
    if (!is_page('atlas-stilova-europe')) return $content;
    if (!in_the_loop() || !is_main_query()) return $content;
    if (get_option('drycured_atlas_interactive_enabled_v073','1') !== '1') return $content;
    if (strpos($content,'dc-atlas-map-v073') !== false) return $content;

    return do_shortcode('[drycured_atlas_interactive_map]') . $content;
}
add_filter('the_content','drycured_atlas_v073_insert',20);


/* === DRYCURED ATLAS HOTSPOT OVERRIDE v0.1.74 START === */
function drycured_atlas_v074_invisible_hotspots() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-override-v074">
    /*
      v0.1.74:
      Vidljive crne oznake pretvaramo u nevidljive klik-zone.
      Karta ostaje čista, a klik na područje otvara desni panel.
    */

    .dc-atlas-main-map-v073 {
        cursor: default;
    }

    .dc-atlas-pin-v073 {
        position: absolute !important;
        display: block !important;
        padding: 0 !important;
        margin: 0 !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 0 !important;
        background: rgba(154, 120, 56, 0) !important;
        border: 0 !important;
        box-shadow: none !important;
        border-radius: 22px !important;
        overflow: hidden !important;
        transform: translate(-50%, -50%) !important;
        cursor: pointer !important;
        z-index: 6 !important;
    }

    .dc-atlas-pin-v073::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: rgba(154, 120, 56, 0);
        outline: 0 solid rgba(154, 120, 56, 0);
        transition: background .18s ease, outline .18s ease;
    }

    .dc-atlas-pin-v073:hover::after,
    .dc-atlas-pin-v073:focus-visible::after,
    .dc-atlas-pin-v073.is-active::after {
        background: rgba(154, 120, 56, .13);
        outline: 2px solid rgba(154, 120, 56, .45);
    }

    .dc-atlas-pin-v073:focus-visible {
        outline: none !important;
    }

    /*
      Šire pregledne zone stavljamo ispod državnih/regionalnih klik-zona.
      Tako klik na Hrvatsku otvara Hrvatsku, a ne npr. Balkan ili Mediteran.
    */
    .dc-atlas-pin-v073[data-atlas-key="med"],
    .dc-atlas-pin-v073[data-atlas-key="pan"],
    .dc-atlas-pin-v073[data-atlas-key="bal"],
    .dc-atlas-pin-v073[data-atlas-key="istok"],
    .dc-atlas-pin-v073[data-atlas-key="zapad"],
    .dc-atlas-pin-v073[data-atlas-key="sredina"],
    .dc-atlas-pin-v073[data-atlas-key="baltik"],
    .dc-atlas-pin-v073[data-atlas-key="nord"] {
        z-index: 3 !important;
    }

    /* Države i regionalne karte — približne klik-zone */
    .dc-atlas-pin-v073[data-atlas-key="hr"] { width: 7%; height: 8%; }
    .dc-atlas-pin-v073[data-atlas-key="ba"] { width: 7%; height: 7%; }
    .dc-atlas-pin-v073[data-atlas-key="si"] { width: 5%; height: 5%; }
    .dc-atlas-pin-v073[data-atlas-key="rs"] { width: 7%; height: 8%; }
    .dc-atlas-pin-v073[data-atlas-key="it"] { width: 10%; height: 16%; }
    .dc-atlas-pin-v073[data-atlas-key="fr"] { width: 14%; height: 14%; }
    .dc-atlas-pin-v073[data-atlas-key="de"] { width: 9%; height: 12%; }
    .dc-atlas-pin-v073[data-atlas-key="pl"] { width: 10%; height: 9%; }
    .dc-atlas-pin-v073[data-atlas-key="hu"] { width: 7%; height: 6%; }
    .dc-atlas-pin-v073[data-atlas-key="es"] { width: 13%; height: 11%; }
    .dc-atlas-pin-v073[data-atlas-key="pt"] { width: 5%; height: 10%; }
    .dc-atlas-pin-v073[data-atlas-key="mt"] { width: 3%; height: 3%; }

    .dc-atlas-pin-v073[data-atlas-key="gb"] { width: 10%; height: 14%; }
    .dc-atlas-pin-v073[data-atlas-key="au_ch"] { width: 8%; height: 6%; }
    .dc-atlas-pin-v073[data-atlas-key="cz_sk"] { width: 8%; height: 5%; }
    .dc-atlas-pin-v073[data-atlas-key="ro_bg"] { width: 10%; height: 8%; }
    .dc-atlas-pin-v073[data-atlas-key="ua_md"] { width: 12%; height: 8%; }
    .dc-atlas-pin-v073[data-atlas-key="ru"] { width: 17%; height: 15%; }
    .dc-atlas-pin-v073[data-atlas-key="me_al_xk"] { width: 6%; height: 7%; }
    .dc-atlas-pin-v073[data-atlas-key="mk_gr_tr"] { width: 12%; height: 8%; }
    .dc-atlas-pin-v073[data-atlas-key="lt_lv_ee"] { width: 8%; height: 7%; }
    .dc-atlas-pin-v073[data-atlas-key="se_no_fi"] { width: 15%; height: 18%; }
    .dc-atlas-pin-v073[data-atlas-key="ben_dan"] { width: 8%; height: 7%; }

    /* Pregledne proizvodne zone */
    .dc-atlas-pin-v073[data-atlas-key="med"] { width: 34%; height: 12%; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="pan"] { width: 19%; height: 10%; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="bal"] { width: 18%; height: 12%; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="istok"] { width: 25%; height: 22%; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="zapad"] { width: 25%; height: 18%; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="sredina"] { width: 20%; height: 15%; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="baltik"] { width: 12%; height: 9%; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="nord"] { width: 18%; height: 20%; border-radius: 999px !important; }

    /*
      Na mobitelu također zadržavamo klik-zone preko karte,
      ne vraćamo ih u obične gumbe.
    */
    @media(max-width:760px) {
        .dc-atlas-pin-v073 {
            position: absolute !important;
            display: block !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v074_invisible_hotspots', 99);
/* === DRYCURED ATLAS HOTSPOT OVERRIDE v0.1.74 END === */


/* === DRYCURED ATLAS HOTSPOT PRECISION v0.1.75 START === */
function drycured_atlas_v075_precise_hotspots() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-precision-v075">
    /*
      v0.1.75:
      Nevidljive klik-zone raspoređene po stvarnim područjima Europe.
      Nema vidljivih gumba. Karta ostaje čista.
    */

    .dc-atlas-main-map-v073 {
        position: relative !important;
        cursor: default;
    }

    .dc-atlas-pin-v073 {
        position: absolute !important;
        display: block !important;
        padding: 0 !important;
        margin: 0 !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        border-radius: 22px !important;
        overflow: hidden !important;
        transform: translate(-50%, -50%) !important;
        cursor: pointer !important;
        z-index: 12 !important;
    }

    .dc-atlas-pin-v073::after {
        content: "" !important;
        position: absolute !important;
        inset: 0 !important;
        background: transparent !important;
        outline: none !important;
        border-radius: inherit !important;
    }

    .dc-atlas-pin-v073:hover::after,
    .dc-atlas-pin-v073:focus-visible::after,
    .dc-atlas-pin-v073.is-active::after {
        background: transparent !important;
        outline: none !important;
    }

    /*
      PREGLEDNE ZONE — niži z-index.
      One hvataju šira područja samo tamo gdje nema preciznije državne/regionalne zone.
    */
    .dc-atlas-pin-v073[data-atlas-key="med"],
    .dc-atlas-pin-v073[data-atlas-key="pan"],
    .dc-atlas-pin-v073[data-atlas-key="bal"],
    .dc-atlas-pin-v073[data-atlas-key="istok"],
    .dc-atlas-pin-v073[data-atlas-key="zapad"],
    .dc-atlas-pin-v073[data-atlas-key="sredina"],
    .dc-atlas-pin-v073[data-atlas-key="baltik"],
    .dc-atlas-pin-v073[data-atlas-key="nord"] {
        z-index: 3 !important;
    }

    /* DRŽAVE I REGIONALNE KARTE — precizniji sloj */
    .dc-atlas-pin-v073[data-atlas-key="pt"]       { left: 23.5% !important; top: 72.0% !important; width: 5.0% !important; height: 13.5% !important; }
    .dc-atlas-pin-v073[data-atlas-key="es"]       { left: 31.0% !important; top: 72.0% !important; width: 14.0% !important; height: 13.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="fr"]       { left: 39.5% !important; top: 56.5% !important; width: 13.0% !important; height: 13.5% !important; }
    .dc-atlas-pin-v073[data-atlas-key="gb"]       { left: 31.0% !important; top: 38.0% !important; width: 11.0% !important; height: 15.0% !important; }

    .dc-atlas-pin-v073[data-atlas-key="ben_dan"]  { left: 45.0% !important; top: 42.0% !important; width: 8.0% !important; height: 9.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="de"]       { left: 49.5% !important; top: 49.0% !important; width: 8.5% !important; height: 11.5% !important; }
    .dc-atlas-pin-v073[data-atlas-key="au_ch"]    { left: 48.0% !important; top: 58.5% !important; width: 10.0% !important; height: 6.5% !important; }
    .dc-atlas-pin-v073[data-atlas-key="cz_sk"]    { left: 55.0% !important; top: 53.0% !important; width: 8.5% !important; height: 5.5% !important; }
    .dc-atlas-pin-v073[data-atlas-key="hu"]       { left: 59.0% !important; top: 58.0% !important; width: 7.0% !important; height: 5.5% !important; }
    .dc-atlas-pin-v073[data-atlas-key="si"]       { left: 53.0% !important; top: 61.5% !important; width: 4.0% !important; height: 4.0% !important; }

    .dc-atlas-pin-v073[data-atlas-key="it"]       { left: 50.5% !important; top: 70.0% !important; width: 10.5% !important; height: 18.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="mt"]       { left: 54.7% !important; top: 86.0% !important; width: 3.5% !important; height: 3.0% !important; }

    .dc-atlas-pin-v073[data-atlas-key="hr"]       { left: 56.0% !important; top: 64.5% !important; width: 7.0% !important; height: 6.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="ba"]       { left: 58.5% !important; top: 67.5% !important; width: 6.5% !important; height: 5.8% !important; }
    .dc-atlas-pin-v073[data-atlas-key="rs"]       { left: 63.0% !important; top: 67.5% !important; width: 6.5% !important; height: 7.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="me_al_xk"] { left: 61.5% !important; top: 73.5% !important; width: 6.0% !important; height: 6.5% !important; }
    .dc-atlas-pin-v073[data-atlas-key="mk_gr_tr"] { left: 66.5% !important; top: 78.0% !important; width: 13.5% !important; height: 8.5% !important; }

    .dc-atlas-pin-v073[data-atlas-key="pl"]       { left: 60.5% !important; top: 46.5% !important; width: 10.0% !important; height: 9.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="lt_lv_ee"] { left: 64.5% !important; top: 37.5% !important; width: 8.0% !important; height: 8.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="se_no_fi"] { left: 57.0% !important; top: 24.5% !important; width: 20.0% !important; height: 25.0% !important; }

    .dc-atlas-pin-v073[data-atlas-key="ro_bg"]    { left: 67.0% !important; top: 66.0% !important; width: 10.0% !important; height: 8.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="ua_md"]    { left: 73.0% !important; top: 56.0% !important; width: 12.0% !important; height: 10.0% !important; }
    .dc-atlas-pin-v073[data-atlas-key="ru"]       { left: 83.5% !important; top: 40.0% !important; width: 22.0% !important; height: 23.0% !important; }

    /* ŠIRE PREGLEDNE ZONE — postavljene tako da ne kradu klik od država */
    .dc-atlas-pin-v073[data-atlas-key="med"]      { left: 47.0% !important; top: 83.0% !important; width: 35.0% !important; height: 8.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="pan"]      { left: 61.0% !important; top: 61.0% !important; width: 18.0% !important; height: 8.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="bal"]      { left: 62.0% !important; top: 71.0% !important; width: 18.0% !important; height: 9.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="istok"]    { left: 75.5% !important; top: 50.0% !important; width: 25.0% !important; height: 20.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="zapad"]    { left: 36.0% !important; top: 53.0% !important; width: 23.0% !important; height: 17.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="sredina"]  { left: 51.5% !important; top: 52.0% !important; width: 18.0% !important; height: 14.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="baltik"]   { left: 65.5% !important; top: 38.0% !important; width: 11.0% !important; height: 8.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="nord"]     { left: 57.0% !important; top: 24.0% !important; width: 20.0% !important; height: 20.0% !important; border-radius: 999px !important; }

    /*
      Debug način za fino štelanje:
      Otvori /atlas-stilova-europe/?v=075&atlas_debug=1
      i zone će se prikazati kao zlatni prozirni oblici.
    */
    .dc-atlas-map-v073.dc-atlas-debug-v075 .dc-atlas-pin-v073 {
        background: rgba(154,120,56,.20) !important;
        border: 1px solid rgba(154,120,56,.70) !important;
        color: #102033 !important;
        font-size: 10px !important;
        line-height: 1.05 !important;
        font-weight: 800 !important;
        padding: 4px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        white-space: normal !important;
    }

    @media(max-width:760px) {
        .dc-atlas-pin-v073 {
            position: absolute !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
        }
    }
    </style>

    <script id="drycured-atlas-hotspot-debug-v075">
    (function(){
        function initDebug(){
            var root = document.querySelector('.dc-atlas-map-v073');
            if(!root) return;
            var params = new URLSearchParams(window.location.search);
            if(params.get('atlas_debug') === '1'){
                root.classList.add('dc-atlas-debug-v075');
            }
        }
        if(document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', initDebug);
        } else {
            initDebug();
        }
    })();
    </script>
    <?php
}
add_action('wp_head', 'drycured_atlas_v075_precise_hotspots', 100);
/* === DRYCURED ATLAS HOTSPOT PRECISION v0.1.75 END === */


/* === DRYCURED ATLAS HOTSPOT REALIGN v0.1.76 START === */
function drycured_atlas_v076_realign_hotspots() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-realign-v076">
    /*
      v0.1.76:
      Korekcija koordinata prema stvarnoj karti.
      Portugal je referentno pomaknut s pogrešnog područja na stvarni zapad Iberije.
    */

    .dc-atlas-pin-v073 {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        position: absolute !important;
        transform: translate(-50%, -50%) !important;
        cursor: pointer !important;
        z-index: 20 !important;
    }

    .dc-atlas-pin-v073::after {
        content: "" !important;
        position: absolute !important;
        inset: 0 !important;
        border-radius: inherit !important;
        background: transparent !important;
        outline: none !important;
    }

    .dc-atlas-pin-v073:hover::after,
    .dc-atlas-pin-v073:focus-visible::after,
    .dc-atlas-pin-v073.is-active::after {
        background: rgba(154,120,56,.10) !important;
        outline: 2px solid rgba(154,120,56,.35) !important;
    }

    /* Šire zone idu ispod preciznijih državnih/regionalnih zona */
    .dc-atlas-pin-v073[data-atlas-key="med"],
    .dc-atlas-pin-v073[data-atlas-key="pan"],
    .dc-atlas-pin-v073[data-atlas-key="bal"],
    .dc-atlas-pin-v073[data-atlas-key="istok"],
    .dc-atlas-pin-v073[data-atlas-key="zapad"],
    .dc-atlas-pin-v073[data-atlas-key="sredina"],
    .dc-atlas-pin-v073[data-atlas-key="baltik"],
    .dc-atlas-pin-v073[data-atlas-key="nord"] {
        z-index: 4 !important;
    }

    /* ZAPAD I ATLANTIK */
    .dc-atlas-pin-v073[data-atlas-key="pt"]       { left: 10.5% !important; top: 73.0% !important; width: 5.2% !important; height: 13.5% !important; border-radius: 16px !important; }
    .dc-atlas-pin-v073[data-atlas-key="es"]       { left: 18.8% !important; top: 72.5% !important; width: 15.5% !important; height: 13.5% !important; border-radius: 22px !important; }
    .dc-atlas-pin-v073[data-atlas-key="fr"]       { left: 31.0% !important; top: 59.0% !important; width: 14.5% !important; height: 14.5% !important; border-radius: 22px !important; }
    .dc-atlas-pin-v073[data-atlas-key="gb"]       { left: 23.5% !important; top: 39.5% !important; width: 12.5% !important; height: 16.0% !important; border-radius: 22px !important; }
    .dc-atlas-pin-v073[data-atlas-key="ben_dan"]  { left: 36.5% !important; top: 45.5% !important; width: 8.5% !important; height: 8.5% !important; border-radius: 20px !important; }

    /* SREDIŠNJA EUROPA */
    .dc-atlas-pin-v073[data-atlas-key="de"]       { left: 40.5% !important; top: 52.0% !important; width: 9.5% !important; height: 12.0% !important; border-radius: 22px !important; }
    .dc-atlas-pin-v073[data-atlas-key="au_ch"]    { left: 41.5% !important; top: 63.0% !important; width: 10.5% !important; height: 6.5% !important; border-radius: 18px !important; }
    .dc-atlas-pin-v073[data-atlas-key="cz_sk"]    { left: 47.0% !important; top: 56.0% !important; width: 9.0% !important; height: 5.8% !important; border-radius: 18px !important; }
    .dc-atlas-pin-v073[data-atlas-key="hu"]       { left: 51.5% !important; top: 61.8% !important; width: 7.5% !important; height: 5.5% !important; border-radius: 18px !important; }
    .dc-atlas-pin-v073[data-atlas-key="si"]       { left: 44.5% !important; top: 65.4% !important; width: 4.8% !important; height: 4.5% !important; border-radius: 14px !important; }

    /* MEDITERAN I JADRAN */
    .dc-atlas-pin-v073[data-atlas-key="it"]       { left: 42.5% !important; top: 73.5% !important; width: 11.0% !important; height: 18.5% !important; border-radius: 28px !important; }
    .dc-atlas-pin-v073[data-atlas-key="mt"]       { left: 49.8% !important; top: 88.0% !important; width: 3.0% !important; height: 3.0% !important; border-radius: 999px !important; }

    /* BALKAN */
    .dc-atlas-pin-v073[data-atlas-key="hr"]       { left: 49.0% !important; top: 68.0% !important; width: 8.0% !important; height: 6.2% !important; border-radius: 18px !important; }
    .dc-atlas-pin-v073[data-atlas-key="ba"]       { left: 51.5% !important; top: 71.0% !important; width: 7.0% !important; height: 5.8% !important; border-radius: 18px !important; }
    .dc-atlas-pin-v073[data-atlas-key="rs"]       { left: 56.5% !important; top: 71.0% !important; width: 7.0% !important; height: 7.0% !important; border-radius: 18px !important; }
    .dc-atlas-pin-v073[data-atlas-key="me_al_xk"] { left: 55.0% !important; top: 77.0% !important; width: 6.5% !important; height: 6.6% !important; border-radius: 18px !important; }
    .dc-atlas-pin-v073[data-atlas-key="mk_gr_tr"] { left: 61.0% !important; top: 81.5% !important; width: 14.0% !important; height: 8.5% !important; border-radius: 22px !important; }

    /* SJEVER, BALTIK, ISTOK */
    .dc-atlas-pin-v073[data-atlas-key="pl"]       { left: 51.5% !important; top: 50.0% !important; width: 11.0% !important; height: 9.5% !important; border-radius: 20px !important; }
    .dc-atlas-pin-v073[data-atlas-key="lt_lv_ee"] { left: 56.5% !important; top: 40.0% !important; width: 8.5% !important; height: 8.5% !important; border-radius: 20px !important; }
    .dc-atlas-pin-v073[data-atlas-key="se_no_fi"] { left: 50.5% !important; top: 26.5% !important; width: 22.0% !important; height: 25.5% !important; border-radius: 30px !important; }
    .dc-atlas-pin-v073[data-atlas-key="ro_bg"]    { left: 60.5% !important; top: 69.5% !important; width: 10.5% !important; height: 8.2% !important; border-radius: 20px !important; }
    .dc-atlas-pin-v073[data-atlas-key="ua_md"]    { left: 66.5% !important; top: 59.5% !important; width: 13.0% !important; height: 10.0% !important; border-radius: 22px !important; }
    .dc-atlas-pin-v073[data-atlas-key="ru"]       { left: 79.5% !important; top: 42.5% !important; width: 24.0% !important; height: 24.0% !important; border-radius: 30px !important; }

    /* PREGLEDNI POJASEVI — namjerno niži sloj */
    .dc-atlas-pin-v073[data-atlas-key="zapad"]    { left: 29.5% !important; top: 59.5% !important; width: 28.0% !important; height: 19.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="sredina"]  { left: 43.5% !important; top: 56.0% !important; width: 22.0% !important; height: 15.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="pan"]      { left: 54.5% !important; top: 64.0% !important; width: 19.0% !important; height: 8.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="bal"]      { left: 56.0% !important; top: 74.0% !important; width: 19.0% !important; height: 9.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="med"]      { left: 41.5% !important; top: 84.5% !important; width: 36.0% !important; height: 8.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="istok"]    { left: 69.5% !important; top: 53.0% !important; width: 27.0% !important; height: 21.0% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="baltik"]   { left: 57.5% !important; top: 40.5% !important; width: 12.0% !important; height: 8.5% !important; border-radius: 999px !important; }
    .dc-atlas-pin-v073[data-atlas-key="nord"]     { left: 50.5% !important; top: 26.0% !important; width: 22.0% !important; height: 21.0% !important; border-radius: 999px !important; }

    /* Debug prikaz: /atlas-stilova-europe/?v=076&atlas_debug=1 */
    .dc-atlas-map-v073.dc-atlas-debug-v075 .dc-atlas-pin-v073,
    .dc-atlas-map-v073.dc-atlas-debug-v076 .dc-atlas-pin-v073 {
        background: rgba(154,120,56,.20) !important;
        border: 1px solid rgba(154,120,56,.70) !important;
        color: #102033 !important;
        font-size: 10px !important;
        line-height: 1.05 !important;
        font-weight: 800 !important;
        padding: 4px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        white-space: normal !important;
    }
    </style>

    <script id="drycured-atlas-hotspot-debug-v076">
    (function(){
        function initDebug(){
            var root = document.querySelector('.dc-atlas-map-v073');
            if(!root) return;
            var params = new URLSearchParams(window.location.search);
            if(params.get('atlas_debug') === '1'){
                root.classList.add('dc-atlas-debug-v076');
            }
        }
        if(document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', initDebug);
        } else {
            initDebug();
        }
    })();
    </script>
    <?php
}
add_action('wp_head', 'drycured_atlas_v076_realign_hotspots', 110);
/* === DRYCURED ATLAS HOTSPOT REALIGN v0.1.76 END === */


/* === DRYCURED ATLAS HOTSPOT IBERIA v0.1.77 START === */
function drycured_atlas_v077_iberia_hotspots() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iberia-v077">
    /*
      v0.1.77:
      Korekcija samo za Portugal i Španjolsku.
      Ostale regije se ne diraju.
    */

    .dc-atlas-pin-v073[data-atlas-key="pt"] {
        left: 8.3% !important;
        top: 73.2% !important;
        width: 4.7% !important;
        height: 13.8% !important;
        border-radius: 15px !important;
    }

    .dc-atlas-pin-v073[data-atlas-key="es"] {
        left: 16.3% !important;
        top: 72.7% !important;
        width: 15.2% !important;
        height: 13.8% !important;
        border-radius: 22px !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v077_iberia_hotspots', 130);
/* === DRYCURED ATLAS HOTSPOT IBERIA v0.1.77 END === */


/* === DRYCURED ATLAS HOTSPOT ITALY v0.1.78 START === */
function drycured_atlas_v078_italy_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-italy-v078">
    /*
      v0.1.78:
      Korekcija samo za Italiju.
      Hotspot je uži i duži kako bi bolje pratio oblik Italije.
    */

    .dc-atlas-pin-v073[data-atlas-key="it"] {
        left: 42.8% !important;
        top: 74.5% !important;
        width: 7.4% !important;
        height: 21.5% !important;
        border-radius: 28px !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v078_italy_hotspot', 131);
/* === DRYCURED ATLAS HOTSPOT ITALY v0.1.78 END === */


/* === DRYCURED ATLAS HOTSPOT AUSTRIA SWITZERLAND v0.1.79 START === */
function drycured_atlas_v079_austria_switzerland_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-au-ch-v079">
    /*
      v0.1.79:
      Korekcija samo za Austriju i Švicarsku.
      Hotspot je niži i pomaknut prema gore na alpski pojas.
    */

    .dc-atlas-pin-v073[data-atlas-key="au_ch"] {
        left: 41.3% !important;
        top: 60.3% !important;
        width: 10.2% !important;
        height: 4.2% !important;
        border-radius: 16px !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v079_austria_switzerland_hotspot', 132);
/* === DRYCURED ATLAS HOTSPOT AUSTRIA SWITZERLAND v0.1.79 END === */


/* === DRYCURED ATLAS PRODUCT ONLY HOTSPOTS v0.1.80 START === */
function drycured_atlas_v080_product_only_hotspots() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-product-only-hotspots-v080">
    /*
      v0.1.80:
      Iz interaktivne karte uklanjamo pregledne zone bez proizvoda.
      Ostaju samo karte država/regija koje imaju proizvodne minijature i konkretan proizvodni sadržaj.
    */

    .dc-atlas-pin-v073[data-atlas-key="med"],
    .dc-atlas-pin-v073[data-atlas-key="pan"],
    .dc-atlas-pin-v073[data-atlas-key="bal"],
    .dc-atlas-pin-v073[data-atlas-key="istok"],
    .dc-atlas-pin-v073[data-atlas-key="zapad"],
    .dc-atlas-pin-v073[data-atlas-key="sredina"],
    .dc-atlas-pin-v073[data-atlas-key="baltik"],
    .dc-atlas-pin-v073[data-atlas-key="nord"] {
        display: none !important;
        pointer-events: none !important;
    }
    </style>

    <script id="drycured-atlas-product-only-hotspots-script-v080">
    (function(){
        function removeOverviewHotspots(){
            var removeKeys = ['med','pan','bal','istok','zapad','sredina','baltik','nord'];
            removeKeys.forEach(function(key){
                document.querySelectorAll('.dc-atlas-pin-v073[data-atlas-key="' + key + '"]').forEach(function(el){
                    el.remove();
                });
            });
        }

        if(document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', removeOverviewHotspots);
        } else {
            removeOverviewHotspots();
        }
    })();
    </script>
    <?php
}
add_action('wp_head', 'drycured_atlas_v080_product_only_hotspots', 150);
/* === DRYCURED ATLAS PRODUCT ONLY HOTSPOTS v0.1.80 END === */


/* === DRYCURED ATLAS HOTSPOT NORDIC v0.1.81 START === */
function drycured_atlas_v081_nordic_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-nordic-v081">
    /*
      v0.1.81:
      Korekcija samo za Švedsku, Norvešku i Finsku.
      Hotspot je niži i podignut prema sjeveru da ne prelazi preko Baltičkih zemalja.
    */

    .dc-atlas-pin-v073[data-atlas-key="se_no_fi"] {
        left: 50.8% !important;
        top: 23.7% !important;
        width: 19.0% !important;
        height: 18.2% !important;
        border-radius: 28px !important;
        z-index: 20 !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v081_nordic_hotspot', 160);
/* === DRYCURED ATLAS HOTSPOT NORDIC v0.1.81 END === */


/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.82 START === */
function drycured_atlas_v082_malta_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-malta-v082">
    /*
      v0.1.82:
      Korekcija samo za Maltu.
      Budući da je Malta na glavnoj karti vrlo mala, klik-zona je namjerno veća od samog otoka.
    */

    .dc-atlas-pin-v073[data-atlas-key="mt"] {
        display: block !important;
        pointer-events: auto !important;
        left: 48.9% !important;
        top: 86.2% !important;
        width: 5.6% !important;
        height: 5.8% !important;
        border-radius: 999px !important;
        z-index: 30 !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v082_malta_hotspot', 170);
/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.82 END === */


/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.83 START === */
function drycured_atlas_v083_malta_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-malta-v083">
    /*
      v0.1.83:
      Korekcija samo za Maltu.
      Hotspot pomaknut dolje gotovo do donjeg ruba karte i malo ulijevo.
    */

    .dc-atlas-pin-v073[data-atlas-key="mt"] {
        display: block !important;
        pointer-events: auto !important;
        left: 46.8% !important;
        top: 93.2% !important;
        width: 4.8% !important;
        height: 4.8% !important;
        border-radius: 999px !important;
        z-index: 30 !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v083_malta_hotspot', 171);
/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.83 END === */


/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.84 START === */
function drycured_atlas_v084_malta_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-malta-v084">
    /*
      v0.1.84:
      Malta pomaknuta još niže i ulijevo.
    */

    .dc-atlas-pin-v073[data-atlas-key="mt"] {
        display: block !important;
        pointer-events: auto !important;
        left: 44.8% !important;
        top: 96.4% !important;
        width: 4.8% !important;
        height: 4.8% !important;
        border-radius: 999px !important;
        z-index: 30 !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v084_malta_hotspot', 172);
/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.84 END === */


/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.85 START === */
function drycured_atlas_v085_malta_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-malta-v085">
    /*
      v0.1.85:
      Malta podignuta mrvicu gore.
      Lijevo/desno i veličina ostaju isti.
    */

    .dc-atlas-pin-v073[data-atlas-key="mt"] {
        display: block !important;
        pointer-events: auto !important;
        left: 44.8% !important;
        top: 95.4% !important;
        width: 4.8% !important;
        height: 4.8% !important;
        border-radius: 999px !important;
        z-index: 30 !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v085_malta_hotspot', 173);
/* === DRYCURED ATLAS HOTSPOT MALTA v0.1.85 END === */


/* === DRYCURED ATLAS HOTSPOT ICELAND v0.1.86 START === */
function drycured_atlas_v086_iceland_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iceland-v086">
    /*
      v0.1.86:
      Dodan drugi nevidljivi hotspot za Island.
      Otvara istu kartu kao Velika Britanija i Irska: atlas_gb_ie_is_velika_britanija_irska_island.webp
    */

    .dc-atlas-pin-v073--iceland {
        display: block !important;
        pointer-events: auto !important;
        left: 15.2% !important;
        top: 22.5% !important;
        width: 7.4% !important;
        height: 5.8% !important;
        border-radius: 999px !important;
        z-index: 31 !important;
        position: absolute !important;
        transform: translate(-50%, -50%) !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        cursor: pointer !important;
    }

    .dc-atlas-pin-v073--iceland::after {
        content: "" !important;
        position: absolute !important;
        inset: 0 !important;
        border-radius: inherit !important;
        background: transparent !important;
        outline: none !important;
    }

    .dc-atlas-pin-v073--iceland:hover::after,
    .dc-atlas-pin-v073--iceland:focus-visible::after,
    .dc-atlas-pin-v073--iceland.is-active::after {
        background: rgba(154,120,56,.10) !important;
        outline: 2px solid rgba(154,120,56,.35) !important;
    }

    .dc-atlas-map-v073.dc-atlas-debug-v075 .dc-atlas-pin-v073--iceland,
    .dc-atlas-map-v073.dc-atlas-debug-v076 .dc-atlas-pin-v073--iceland {
        background: rgba(154,120,56,.20) !important;
        border: 1px solid rgba(154,120,56,.70) !important;
        color: #102033 !important;
        font-size: 10px !important;
        line-height: 1.05 !important;
        font-weight: 800 !important;
        padding: 4px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        white-space: normal !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v086_iceland_hotspot', 174);
/* === DRYCURED ATLAS HOTSPOT ICELAND v0.1.86 END === */


/* === DRYCURED ATLAS HOTSPOT ICELAND FORCE v0.1.87 START === */
function drycured_atlas_v087_force_iceland_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iceland-force-v087">
    /*
      v0.1.87:
      Forsirano dodavanje Island hotspota preko JS-a.
      Klik na Island pokreće postojeću kartu VB + Irska + Island.
    */

    .dc-atlas-pin-v073--iceland-force {
        position: absolute !important;
        display: block !important;
        pointer-events: auto !important;
        left: 15.2% !important;
        top: 22.5% !important;
        width: 8.2% !important;
        height: 6.4% !important;
        transform: translate(-50%, -50%) !important;
        border-radius: 999px !important;
        z-index: 80 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        cursor: pointer !important;
    }

    .dc-atlas-pin-v073--iceland-force::after {
        content: "" !important;
        position: absolute !important;
        inset: 0 !important;
        border-radius: inherit !important;
        background: transparent !important;
        outline: none !important;
    }

    .dc-atlas-pin-v073--iceland-force:hover::after,
    .dc-atlas-pin-v073--iceland-force:focus-visible::after,
    .dc-atlas-pin-v073--iceland-force.is-active::after {
        background: rgba(154,120,56,.10) !important;
        outline: 2px solid rgba(154,120,56,.35) !important;
    }

    .dc-atlas-map-v073.dc-atlas-debug-v075 .dc-atlas-pin-v073--iceland-force,
    .dc-atlas-map-v073.dc-atlas-debug-v076 .dc-atlas-pin-v073--iceland-force {
        background: rgba(154,120,56,.24) !important;
        border: 1px solid rgba(154,120,56,.80) !important;
        color: #102033 !important;
        font-size: 10px !important;
        line-height: 1.05 !important;
        font-weight: 800 !important;
        padding: 4px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        white-space: normal !important;
    }
    </style>

    <script id="drycured-atlas-hotspot-iceland-force-script-v087">
    (function(){
        function addIcelandHotspot(){
            var map = document.querySelector('.dc-atlas-main-map-v073');
            if (!map) return;

            if (map.querySelector('.dc-atlas-pin-v073--iceland-force')) return;

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dc-atlas-pin-v073 dc-atlas-pin-v073--iceland-force';
            btn.setAttribute('data-atlas-key', 'gb');
            btn.setAttribute('aria-label', 'Island — otvori kartu Velike Britanije, Irske i Islanda');
            btn.textContent = 'Island';

            btn.addEventListener('click', function(){
                var gb = document.querySelector('.dc-atlas-pin-v073[data-atlas-key="gb"]:not(.dc-atlas-pin-v073--iceland-force)');
                if (gb) {
                    gb.click();
                }
                document.querySelectorAll('.dc-atlas-pin-v073').forEach(function(el){
                    el.classList.remove('is-active');
                });
                btn.classList.add('is-active');
            });

            map.appendChild(btn);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', addIcelandHotspot);
        } else {
            addIcelandHotspot();
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'drycured_atlas_v087_force_iceland_hotspot', 200);
/* === DRYCURED ATLAS HOTSPOT ICELAND FORCE v0.1.87 END === */


/* === DRYCURED ATLAS HOTSPOT ICELAND OVERLAY v0.1.88 START === */
function drycured_atlas_v088_iceland_overlay_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iceland-overlay-v088">
    /*
      v0.1.88:
      Island hotspot se dodaje kao zaseban overlay sloj iznad karte.
      Ne može biti ispod slike jer overlay ima vlastiti z-index i pointer-events.
    */

    .dc-atlas-main-map-v073 {
        position: relative !important;
        isolation: isolate !important;
    }

    .dc-atlas-main-map-v073 > img {
        position: relative !important;
        z-index: 1 !important;
    }

    .dc-atlas-iceland-overlay-v088 {
        position: absolute !important;
        inset: 14px !important; /* prati unutarnji padding karte */
        z-index: 9999 !important;
        pointer-events: none !important;
    }

    .dc-atlas-iceland-hotspot-v088 {
        position: absolute !important;
        left: 15.2% !important;
        top: 22.5% !important;
        width: 8.2% !important;
        height: 6.4% !important;
        transform: translate(-50%, -50%) !important;
        display: block !important;
        pointer-events: auto !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: transparent !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        cursor: pointer !important;
        box-shadow: none !important;
    }

    .dc-atlas-iceland-hotspot-v088::after {
        content: "" !important;
        position: absolute !important;
        inset: 0 !important;
        border-radius: inherit !important;
        background: transparent !important;
        outline: none !important;
    }

    .dc-atlas-iceland-hotspot-v088:hover::after,
    .dc-atlas-iceland-hotspot-v088:focus-visible::after,
    .dc-atlas-iceland-hotspot-v088.is-active::after {
        background: rgba(154,120,56,.10) !important;
        outline: 2px solid rgba(154,120,56,.38) !important;
    }

    .dc-atlas-map-v073.dc-atlas-debug-v075 .dc-atlas-iceland-hotspot-v088,
    .dc-atlas-map-v073.dc-atlas-debug-v076 .dc-atlas-iceland-hotspot-v088 {
        background: rgba(154,120,56,.28) !important;
        border: 1px solid rgba(154,120,56,.85) !important;
        color: #102033 !important;
        font-size: 10px !important;
        line-height: 1.05 !important;
        font-weight: 800 !important;
        padding: 4px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        white-space: normal !important;
    }
    </style>

    <script id="drycured-atlas-hotspot-iceland-overlay-script-v088">
    (function(){
        function addIcelandOverlay(){
            var map = document.querySelector('.dc-atlas-main-map-v073');
            if (!map) return;

            if (map.querySelector('.dc-atlas-iceland-overlay-v088')) return;

            var overlay = document.createElement('div');
            overlay.className = 'dc-atlas-iceland-overlay-v088';

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dc-atlas-iceland-hotspot-v088';
            btn.setAttribute('aria-label', 'Island — otvori kartu Velike Britanije, Irske i Islanda');
            btn.textContent = 'Island';

            btn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();

                var gb = document.querySelector('.dc-atlas-pin-v073[data-atlas-key="gb"]');
                if (gb) {
                    gb.click();
                }

                document.querySelectorAll('.dc-atlas-pin-v073, .dc-atlas-iceland-hotspot-v088').forEach(function(el){
                    el.classList.remove('is-active');
                });
                btn.classList.add('is-active');
            });

            overlay.appendChild(btn);
            map.appendChild(overlay);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', addIcelandOverlay);
        } else {
            addIcelandOverlay();
        }

        window.addEventListener('load', addIcelandOverlay);
    })();
    </script>
    <?php
}
add_action('wp_footer', 'drycured_atlas_v088_iceland_overlay_hotspot', 300);
/* === DRYCURED ATLAS HOTSPOT ICELAND OVERLAY v0.1.88 END === */


/* === DRYCURED ATLAS HOTSPOT ICELAND POSITION v0.1.89 START === */
function drycured_atlas_v089_iceland_position() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iceland-position-v089">
    /*
      v0.1.89:
      Island hotspot podignut prema stvarnom položaju Islanda.
      Ostale zone se ne diraju.
    */

    .dc-atlas-iceland-hotspot-v088 {
        left: 15.2% !important;
        top: 11.8% !important;
        width: 8.2% !important;
        height: 6.4% !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v089_iceland_position', 999);
/* === DRYCURED ATLAS HOTSPOT ICELAND POSITION v0.1.89 END === */


/* === DRYCURED ATLAS HOTSPOT ICELAND POSITION v0.1.90 START === */
function drycured_atlas_v090_iceland_position() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iceland-position-v090">
    /*
      v0.1.90:
      Island hotspot pomaknut gore za približno tri visine gumba.
      Ostale zone se ne diraju.
    */

    .dc-atlas-iceland-hotspot-v088 {
        left: 15.2% !important;
        top: 3.2% !important;
        width: 8.2% !important;
        height: 6.4% !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v090_iceland_position', 1000);
/* === DRYCURED ATLAS HOTSPOT ICELAND POSITION v0.1.90 END === */


/* === DRYCURED ATLAS HOTSPOT ICELAND HARD POSITION v0.1.91 START === */
function drycured_atlas_v091_iceland_hard_position() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iceland-hard-position-v091">
    /*
      v0.1.91:
      Tvrdi override za Island.
      Učitava se u footeru poslije starog v088 CSS-a, zato sada stvarno pobjeđuje.
    */

    .dc-atlas-iceland-hotspot-v088 {
        left: 15.2% !important;
        top: 3.2% !important;
        width: 8.2% !important;
        height: 6.4% !important;
    }
    </style>

    <script id="drycured-atlas-hotspot-iceland-hard-position-script-v091">
    (function(){
        function moveIceland(){
            var btn = document.querySelector('.dc-atlas-iceland-hotspot-v088');
            if (!btn) return;

            btn.style.setProperty('left', '15.2%', 'important');
            btn.style.setProperty('top', '3.2%', 'important');
            btn.style.setProperty('width', '8.2%', 'important');
            btn.style.setProperty('height', '6.4%', 'important');
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', moveIceland);
        } else {
            moveIceland();
        }

        window.addEventListener('load', moveIceland);
        setTimeout(moveIceland, 300);
        setTimeout(moveIceland, 900);
    })();
    </script>
    <?php
}
add_action('wp_footer', 'drycured_atlas_v091_iceland_hard_position', 9999);
/* === DRYCURED ATLAS HOTSPOT ICELAND HARD POSITION v0.1.91 END === */


/* === DRYCURED ATLAS HOTSPOT ICELAND FINAL POSITION v0.1.92 START === */
function drycured_atlas_v092_iceland_final_position() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-iceland-final-position-v092">
    /*
      v0.1.92:
      Island spušten prema dolje za približno polovinu visine gumba.
      Ostale zone se ne diraju.
    */

    .dc-atlas-iceland-hotspot-v088 {
        left: 15.2% !important;
        top: 6.4% !important;
        width: 8.2% !important;
        height: 6.4% !important;
    }
    </style>

    <script id="drycured-atlas-hotspot-iceland-final-position-script-v092">
    (function(){
        function moveIcelandFinal(){
            var btn = document.querySelector('.dc-atlas-iceland-hotspot-v088');
            if (!btn) return;

            btn.style.setProperty('left', '15.2%', 'important');
            btn.style.setProperty('top', '6.4%', 'important');
            btn.style.setProperty('width', '8.2%', 'important');
            btn.style.setProperty('height', '6.4%', 'important');
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', moveIcelandFinal);
        } else {
            moveIcelandFinal();
        }

        window.addEventListener('load', moveIcelandFinal);
        setTimeout(moveIcelandFinal, 300);
        setTimeout(moveIcelandFinal, 900);
        setTimeout(moveIcelandFinal, 1500);
    })();
    </script>
    <?php
}
add_action('wp_footer', 'drycured_atlas_v092_iceland_final_position', 10000);
/* === DRYCURED ATLAS HOTSPOT ICELAND FINAL POSITION v0.1.92 END === */


/* === DRYCURED ATLAS HOTSPOT BELARUS v0.1.93 START === */
function drycured_atlas_v093_belarus_hotspot() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-belarus-v093">
    /*
      v0.1.93:
      Dodan hotspot za Bjelorusiju.
      Pozicija je između Poljske, Litve/Latvije, Ukrajine i europskog dijela Rusije.
    */

    .dc-atlas-pin-v073[data-atlas-key="by"] {
        left: 60.5% !important;
        top: 49.5% !important;
        width: 8.2% !important;
        height: 6.4% !important;
        border-radius: 18px !important;
        z-index: 35 !important;
        display: block !important;
        pointer-events: auto !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v093_belarus_hotspot', 180);
/* === DRYCURED ATLAS HOTSPOT BELARUS v0.1.93 END === */


/* === DRYCURED ATLAS HOTSPOT BALTIC POSITION v0.1.95 START === */
function drycured_atlas_v095_baltic_position() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-baltic-position-v095">
    /*
      v0.1.95:
      Baltičke zemlje pomaknute malo prema gore.
      Ostale zone se ne diraju.
    */

    .dc-atlas-pin-v073[data-atlas-key="lt_lv_ee"] {
        left: 56.5% !important;
        top: 37.6% !important;
        width: 8.5% !important;
        height: 8.5% !important;
        border-radius: 20px !important;
        z-index: 35 !important;
        display: block !important;
        pointer-events: auto !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v095_baltic_position', 182);
/* === DRYCURED ATLAS HOTSPOT BALTIC POSITION v0.1.95 END === */


/* === DRYCURED ATLAS HOTSPOT BELARUS POSITION v0.1.96 START === */
function drycured_atlas_v096_belarus_position() {
    if (!is_page('atlas-stilova-europe')) return;
    ?>
    <style id="drycured-atlas-hotspot-belarus-position-v096">
    /*
      v0.1.96:
      Bjelorusija pomaknuta malo prema gore.
      Ostale zone se ne diraju.
    */

    .dc-atlas-pin-v073[data-atlas-key="by"] {
        left: 60.5% !important;
        top: 44.8% !important;
        width: 8.2% !important;
        height: 6.4% !important;
        border-radius: 18px !important;
        z-index: 35 !important;
        display: block !important;
        pointer-events: auto !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_atlas_v096_belarus_position', 183);
/* === DRYCURED ATLAS HOTSPOT BELARUS POSITION v0.1.96 END === */

