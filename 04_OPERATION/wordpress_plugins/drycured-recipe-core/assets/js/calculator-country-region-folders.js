/* calculator-country-region-folders.js  v2
 * Hrvatska -> Regija -> Mikroregija
 * Ostale drzave -> Regija (jednorazinska)
 */
(function () {
  "use strict";

  /* ---------- utils ---------- */
  function norm(v) {
    return (v || "").toString().trim().toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")   /* ukloni dijakritike */
      .replace(/[\u2013\u2014\/\-]/g, " ") /* -, –, —, / => razmak */
      .replace(/[()]/g, " ")              /* zagrade => razmak */
      .replace(/\s+/g, " ")
      .trim();
  }

  function getName(prod) {
    var el = prod.querySelector(".pn");
    return el ? el.textContent.trim() : "";
  }
  function getRegion(prod) {
    var el = prod.querySelector(".pr");
    return el ? el.textContent.trim() : "";
  }

  /* ---------- HRVATSKA: 2-razinska klasifikacija ----------
   *
   *  Pravilo prioriteta:
   *    1. ime proizvoda (jak signal — koristi se za korekciju krivo dodijeljene regije)
   *    2. tekst regije iz .pr
   *
   *  Vraca { main: "RegijaNaziv", sub: "MikroregijaNaziv" | null }
   */
  function hrHierarchy(rawRegion, productName) {
    var rn  = norm(rawRegion || "");
    var pn  = norm(productName || "");
    /* kombinirani niz za generalne provjere */
    var all = norm((rawRegion || "") + " " + (productName || ""));

    /* ── 1. KOREKTURA: ime otkriva pravu regiju (bez obzira na .pr) ── */

    /* Slavonija: Slavonsk*, Vinkovac*, Miholjac*, Dakovo/Đakovo, Osijek, Slavonije */
    if (pn.indexOf("slavonsk")  !== -1 || pn.indexOf("slavonije") !== -1 ||
        pn.indexOf("vinkovac")  !== -1 || pn.indexOf("miholjac")  !== -1 ||
        pn.indexOf("djakovo")   !== -1 || pn.indexOf("dakovo")    !== -1) {
      if (all.indexOf("baranja") !== -1) return { main: "Slavonija i Baranja", sub: "Baranja" };
      if (all.indexOf("srijem")  !== -1) return { main: "Slavonija i Baranja", sub: "Srijem" };
      return { main: "Slavonija i Baranja", sub: null };
    }

    /* Podravina: Podravsk*, Koprivnic* */
    if (pn.indexOf("podravsk")  !== -1 || pn.indexOf("koprivnic") !== -1 ||
        pn.indexOf("virovitic") !== -1) {
      return { main: "Sjeverna Hrvatska", sub: "Podravina" };
    }

    /* Lika: Lick*, Gracac (Gračac), Plitvice, Gospic */
    if (pn.indexOf("licka")   !== -1 || pn.indexOf("licki")   !== -1 ||
        pn.indexOf("gracac")  !== -1 || pn.indexOf("plitvice") !== -1 ||
        pn.indexOf("gospic")  !== -1) {
      return { main: "Lika i Gorski kotar", sub: "Lika" };
    }

    /* Primorje: Crikvenick* */
    if (pn.indexOf("crikvenicka") !== -1 || pn.indexOf("crikvenick") !== -1) {
      return { main: "Hrvatsko primorje", sub: null };
    }

    /* Kvarner: Krck*, Krcki */
    if (pn.indexOf("krcki") !== -1 || pn.indexOf("krck") !== -1) {
      return { main: "Kvarner i otoci", sub: "Krk" };
    }

    /* Kvarner: Creski*, Cresk* */
    if (pn.indexOf("cresk")  !== -1 || pn.indexOf("creski") !== -1) {
      return { main: "Kvarner i otoci", sub: "Cres" };
    }

    /* Zagorje: Zagorsk* */
    if (pn.indexOf("zagorsk") !== -1) {
      return { main: "Sjeverna Hrvatska", sub: "Zagorje" };
    }

    /* Dalmatinski iz imena */
    if (pn.indexOf("dalmatinsk") !== -1 || pn.indexOf("drniski") !== -1) {
      return { main: "Dalmacija", sub: null };
    }

    /* Istarski iz imena */
    if (pn.indexOf("istarski") !== -1 || pn.indexOf("istarska") !== -1) {
      return { main: "Istra", sub: null };
    }

    /* ── 2. REGIONALNI TEKST iz .pr ── */

    /* Više regija: Dalmacija + Istra istovremeno */
    if (rn.indexOf("dalmacija") !== -1 && rn.indexOf("istra") !== -1) {
      return { main: "Više regija", sub: "Dalmacija i Istra" };
    }
    if (rn.indexOf("vise")      !== -1 && rn.indexOf("regija") !== -1) {
      return { main: "Više regija", sub: null };
    }

    /* Slavonija i Baranja */
    if (rn.indexOf("slavonija") !== -1 || rn.indexOf("baranja") !== -1 ||
        all.indexOf("slavonija") !== -1) {
      if (rn.indexOf("baranja") !== -1) return { main: "Slavonija i Baranja", sub: "Baranja" };
      if (rn.indexOf("srijem")  !== -1) return { main: "Slavonija i Baranja", sub: "Srijem" };
      return { main: "Slavonija i Baranja", sub: null };
    }

    /* Dalmacija — provjeri Dalmatinsku zagoru */
    if (rn.indexOf("dalmacija")     !== -1 || rn.indexOf("dalmatinsk")   !== -1 ||
        rn.indexOf("split")         !== -1 || rn.indexOf("sibenik")      !== -1 ||
        rn.indexOf("zadar")         !== -1 || rn.indexOf("kistanje")     !== -1 ||
        rn.indexOf("petrovo polje") !== -1 || rn.indexOf("drnis")        !== -1 ||
        rn.indexOf("dalmatinska")   !== -1) {
      if (rn.indexOf("zagora") !== -1) return { main: "Dalmacija", sub: "Dalmatinska zagora" };
      return { main: "Dalmacija", sub: null };
    }

    /* Kvarner i otoci */
    if (rn.indexOf("krk") !== -1 || all.indexOf("otok krk") !== -1)
      return { main: "Kvarner i otoci", sub: "Krk" };
    if (rn.indexOf("cres") !== -1)
      return { main: "Kvarner i otoci", sub: "Cres" };
    if (rn.indexOf("losinj") !== -1)
      return { main: "Kvarner i otoci", sub: "Losinj" };
    if (rn.indexOf("kvarner") !== -1)
      return { main: "Kvarner i otoci", sub: null };

    /* Sjeverna Hrvatska */
    if (rn.indexOf("zagorje")        !== -1)
      return { main: "Sjeverna Hrvatska", sub: "Zagorje" };
    if (rn.indexOf("medimurje")      !== -1)
      return { main: "Sjeverna Hrvatska", sub: "Medimurje" };
    if (rn.indexOf("podravina")      !== -1 || rn.indexOf("podravsk") !== -1 ||
        rn.indexOf("koprivnic")      !== -1)
      return { main: "Sjeverna Hrvatska", sub: "Podravina" };
    if (rn.indexOf("moslavina")      !== -1 || rn.indexOf("bjelovar") !== -1)
      return { main: "Sjeverna Hrvatska", sub: "Moslavina i Bilogora" };
    if (rn.indexOf("zagreb")         !== -1)
      return { main: "Sjeverna Hrvatska", sub: "Okolica Zagreba" };
    if (rn.indexOf("sjeverna hrvatska") !== -1)
      return { main: "Sjeverna Hrvatska", sub: null };

    /* Lika i Gorski kotar */
    if (rn.indexOf("lika")           !== -1 || rn.indexOf("gorski kotar") !== -1 ||
        rn.indexOf("plitvice")       !== -1 || rn.indexOf("gracac")       !== -1 ||
        rn.indexOf("gospic")         !== -1) {
      var hasLika    = rn.indexOf("lika")         !== -1;
      var hasGorski  = rn.indexOf("gorski kotar") !== -1;
      if (hasLika && !hasGorski)  return { main: "Lika i Gorski kotar", sub: "Lika" };
      if (!hasLika && hasGorski)  return { main: "Lika i Gorski kotar", sub: "Gorski kotar" };
      return { main: "Lika i Gorski kotar", sub: null };
    }

    /* Hrvatsko primorje — ODVOJENO od Kvarnera */
    if (rn.indexOf("primorje") !== -1 || rn.indexOf("crikvenica") !== -1 ||
        rn.indexOf("rijeka")   !== -1)
      return { main: "Hrvatsko primorje", sub: null };

    /* Istra */
    if (rn.indexOf("istra") !== -1 || rn.indexOf("istarska") !== -1 ||
        rn.indexOf("istarski") !== -1)
      return { main: "Istra", sub: null };

    /* Banovina / Kordun */
    if (rn.indexOf("banovina") !== -1 || rn.indexOf("kordun")   !== -1 ||
        rn.indexOf("sisak")    !== -1 || rn.indexOf("petrinja") !== -1)
      return { main: "Banovina i Kordun", sub: null };

    return { main: "Ostale regije", sub: null };
  }

  /* ---------- DOM helpers ---------- */
  function makeFolder(label, count, cls) {
    var d = document.createElement("details");
    d.className = cls || "dc-rf";
    var s = document.createElement("summary");
    s.className = "dc-rfh";
    s.innerHTML =
      "<span class=\"dc-rfn\">" + label + "</span>" +
      "<span class=\"dc-rfc\">" + count + "\u00a0" +
      (count === 1 ? "proizvod" : "proizvoda") + "\u00a0\u25be</span>";
    d.appendChild(s);
    return d;
  }

  function sortByName(arr) {
    return arr.slice().sort(function (a, b) {
      return getName(a).localeCompare(getName(b), "hr");
    });
  }

  /* ---------- HRVATSKA: 2-razinska organizacija ---------- */
  function enhanceCroatia(block) {
    if (block.dataset.dcRe === "hr2") return;
    var rb = block.querySelector(".dc-rb");
    if (!rb) return;
    var prods = Array.from(rb.querySelectorAll(".dc-prod"));
    if (prods.length < 1) return;
    block.dataset.dcRe = "hr2";

    /* grupiranje: mainMap[main][sub] = [prods] */
    var mainMap = {};
    prods.forEach(function (p) {
      var h = hrHierarchy(getRegion(p), getName(p));
      if (!mainMap[h.main]) mainMap[h.main] = {};
      var sk = h.sub || "_";
      if (!mainMap[h.main][sk]) mainMap[h.main][sk] = [];
      mainMap[h.main][sk].push(p);
    });

    /* makni originalne iz DOM-a */
    prods.forEach(function (p) { if (p.parentNode) p.parentNode.removeChild(p); });

    /* sortiraj i dodaj main foldere */
    Object.keys(mainMap)
      .sort(function (a, b) { return a.localeCompare(b, "hr"); })
      .forEach(function (main) {
        var subMap = mainMap[main];
        var subKeys = Object.keys(subMap);
        var total = subKeys.reduce(function (s, k) { return s + subMap[k].length; }, 0);

        var mf = makeFolder(main, total, "dc-rf dc-rf-main");
        var mb = document.createElement("div");
        mb.className = "dc-rfb";

        /* direktni proizvodi (bez sub-regije) */
        if (subMap["_"]) {
          sortByName(subMap["_"]).forEach(function (p) { mb.appendChild(p); });
        }

        /* sub-regije */
        subKeys
          .filter(function (k) { return k !== "_"; })
          .sort(function (a, b) { return a.localeCompare(b, "hr"); })
          .forEach(function (sub) {
            var sp = sortByName(subMap[sub]);
            var sf = makeFolder(sub, sp.length, "dc-rf dc-rf-sub");
            var sb = document.createElement("div");
            sb.className = "dc-rfb";
            sp.forEach(function (p) { sb.appendChild(p); });
            sf.appendChild(sb);
            mb.appendChild(sf);
          });

        mf.appendChild(mb);
        rb.appendChild(mf);
      });
  }

  /* ---------- Ostale drzave: jednorazinska organizacija ---------- */
  function enhanceOther(block, countryNorm) {
    if (block.dataset.dcRe === "1" || block.dataset.dcRe === "hr2") return;
    var rb = block.querySelector(".dc-rb");
    if (!rb) return;
    var prods = Array.from(rb.querySelectorAll(".dc-prod"));
    if (prods.length < 2) return;

    var groups = {};
    prods.forEach(function (p) {
      var r = getRegion(p);
      /* koristi sirovi tekst regije ali ocisti ga od naziva drzave */
      var lbl = (r && norm(r) !== countryNorm) ? r : "Opci recepti";
      if (!groups[lbl]) groups[lbl] = [];
      groups[lbl].push(p);
    });

    var names = Object.keys(groups);
    if (names.length < 2) return; /* sve iste — nema smisla grupirati */

    block.dataset.dcRe = "1";
    prods.forEach(function (p) { if (p.parentNode) p.parentNode.removeChild(p); });

    names
      .sort(function (a, b) {
        if (a === "Opci recepti") return -1;
        if (b === "Opci recepti") return 1;
        return a.localeCompare(b, "hr");
      })
      .forEach(function (name) {
        var gp = sortByName(groups[name]);
        var f = makeFolder(name, gp.length, "dc-rf");
        var bd = document.createElement("div");
        bd.className = "dc-rfb";
        gp.forEach(function (p) { bd.appendChild(p); });
        f.appendChild(bd);
        rb.appendChild(f);
      });
  }

  /* ---------- Runner: samo otvorene mape ---------- */
  function run() {
    document.querySelectorAll(".dc-rg").forEach(function (block) {
      var rb = block.querySelector(".dc-rb");
      if (!rb || rb.offsetHeight === 0) return;   /* preskoči zatvorene */

      var hb = block.querySelector(".dc-rh");
      if (!hb) return;

      /* KRITICAN FIX: \s* umjesto \s+ — "Hrvatska31 proizvoda" nema razmak */
      var countryRaw = hb.textContent.replace(/\s*\d[\d\s]*proizvod[ae]?\s*.*/i, "").trim();
      var countryNorm = norm(countryRaw);

      if (countryNorm === "hrvatska") {
        enhanceCroatia(block);
      } else {
        enhanceOther(block, countryNorm);
      }
    });
  }

  function defer() {
    setTimeout(run, 100);
    setTimeout(run, 400);
    setTimeout(run, 900);
  }

  document.addEventListener("DOMContentLoaded", defer);
  document.addEventListener("click",  defer);
  document.addEventListener("change", defer);
  document.addEventListener("DOMContentLoaded", function () {
    var app = document.querySelector("#dc-app") || document.body;
    new MutationObserver(defer).observe(app, { childList: true, subtree: true });
  });

})();
