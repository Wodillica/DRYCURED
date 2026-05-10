(function () {
    function norm(value) {
        return (value || "")
            .toString()
            .trim()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[—–]/g, "-")
            .replace(/\s+/g, " ");
    }

    var replacements = {
        "otok krk": "Kvarner i otoci / Krk",
        "krk": "Kvarner i otoci / Krk",
        "dalmacija / istra": "Više hrvatskih regija / Dalmacija i Istra",
        "dalmacija istra": "Više hrvatskih regija / Dalmacija i Istra",
        "lika": "Lika i Gorski kotar",
        "hrvatsko zagorje": "Sjeverna Hrvatska / Hrvatsko zagorje",
        "sjeverna hrvatska zagorje medimurje okolica zagreba": "Sjeverna Hrvatska",
        "kvarner i otoci / hrvatsko primorje": "Hrvatsko primorje",
        "kvarner i otoci hrvatsko primorje": "Hrvatsko primorje",
        "hrvatsko primorje / primorje": "Hrvatsko primorje",
        "sira regija hrvatska bih srbija": "Više država / Hrvatska, BiH, Srbija",
        "šira regija hrvatska bih srbija": "Više država / Hrvatska, BiH, Srbija"
    };

    function cleanText(text) {
        var key = norm(text);
        return replacements[key] || null;
    }

    function normalizeRegionLabels() {
        var candidates = Array.from(document.querySelectorAll(
            ".dc-h span:first-child, .dc-h, summary, .dc-subregion-head span:first-child, .drycured-region-summary span:first-child, .pr"
        ));

        candidates.forEach(function (el) {
            var original = (el.textContent || "").trim();

            if (!original) return;

            var countText = "";
            var match = original.match(/\s+(\d+\s+proizvoda?.*)$/i);
            if (match) {
                countText = " " + match[1];
                original = original.replace(match[1], "").trim();
            }

            var replacement = cleanText(original);
            if (!replacement) return;

            el.textContent = replacement + countText;
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(normalizeRegionLabels, 200);
        setTimeout(normalizeRegionLabels, 800);
        setTimeout(normalizeRegionLabels, 1600);
        setTimeout(normalizeRegionLabels, 2600);
    });

    document.addEventListener("click", function () {
        setTimeout(normalizeRegionLabels, 150);
        setTimeout(normalizeRegionLabels, 600);
    });

    document.addEventListener("change", function () {
        setTimeout(normalizeRegionLabels, 150);
        setTimeout(normalizeRegionLabels, 600);
    });
})();
