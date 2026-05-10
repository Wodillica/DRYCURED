(function () {
    function norm(value) {
        return (value || "")
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[—–]/g, " ")
            .replace(/[_-]/g, " ")
            .replace(/[^a-z0-9]+/g, " ")
            .trim();
    }

    function getParam(name) {
        return new URLSearchParams(window.location.search).get(name);
    }

    function getCalculatorData() {
        var el = document.getElementById("dc-json");
        if (!el) return null;

        try {
            return JSON.parse(el.textContent || el.innerText || "{}");
        } catch (e) {
            return null;
        }
    }

    function clickElement(el) {
        if (!el) return false;

        el.scrollIntoView({ behavior: "smooth", block: "center" });

        ["mousedown", "mouseup", "click"].forEach(function (type) {
            el.dispatchEvent(new MouseEvent(type, {
                bubbles: true,
                cancelable: true,
                view: window
            }));
        });

        return true;
    }

    function findClickableParent(el) {
        var current = el;

        for (var i = 0; i < 8 && current; i++) {
            if (
                current.tagName === "BUTTON" ||
                current.tagName === "A" ||
                current.getAttribute("role") === "button" ||
                current.onclick ||
                current.className.toString().match(/item|card|row|product|region|accordion|head|title/i)
            ) {
                return current;
            }

            current = current.parentElement;
        }

        return el;
    }

    function allVisibleElements() {
        return Array.from(document.querySelectorAll("button, a, div, summary, li, span, article"))
            .filter(function (el) {
                var r = el.getBoundingClientRect();
                var st = window.getComputedStyle(el);
                return r.width > 0 && r.height > 0 && st.display !== "none" && st.visibility !== "hidden";
            });
    }

    function clickByText(possibleTexts) {
        var wanted = possibleTexts.map(norm).filter(Boolean);
        var elements = allVisibleElements();

        for (var i = 0; i < elements.length; i++) {
            var el = elements[i];
            var text = norm(el.textContent);

            for (var j = 0; j < wanted.length; j++) {
                if (!wanted[j]) continue;

                if (text === wanted[j] || text.indexOf(wanted[j]) !== -1) {
                    return clickElement(findClickableParent(el));
                }
            }
        }

        return false;
    }

    function selectFromDropdown(possibleTexts) {
        var wanted = possibleTexts.map(norm).filter(Boolean);
        var selects = Array.from(document.querySelectorAll("select"));

        for (var i = 0; i < selects.length; i++) {
            var select = selects[i];
            var options = Array.from(select.options || []);

            for (var j = 0; j < options.length; j++) {
                var option = options[j];
                var text = norm(option.textContent + " " + option.value);

                for (var k = 0; k < wanted.length; k++) {
                    if (text === wanted[k] || text.indexOf(wanted[k]) !== -1) {
                        select.value = option.value;
                        select.dispatchEvent(new Event("change", { bubbles: true }));
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function findProductElementByClass(productName) {
        var wanted = norm(productName);

        var nameEls = Array.from(document.querySelectorAll(".pn, .product-name, .dc-product-name, .recipe-name"));

        for (var i = 0; i < nameEls.length; i++) {
            var el = nameEls[i];

            if (norm(el.textContent).indexOf(wanted) !== -1) {
                return clickElement(findClickableParent(el));
            }
        }

        return false;
    }

    function showNotice(message) {
        if (document.querySelector(".drycured-calculator-bridge-notice")) return;

        var notice = document.createElement("div");
        notice.className = "drycured-calculator-bridge-notice";
        notice.textContent = message;

        var target = document.querySelector("main") || document.querySelector(".entry-content") || document.body;
        target.prepend(notice);
    }

    function productKeyVariants(key) {
        if (!key) return [];

        return Array.from(new Set([
            key,
            key.replace(/-/g, "_"),
            key.replace(/_/g, "-")
        ]));
    }

    function runBridge() {
        var productParam = getParam("product");
        var recipeId = getParam("recipe_id");

        if (!productParam && !recipeId) return;

        var data = getCalculatorData();

        if (!data || !data.products) {
            showNotice("Kalkulator je učitan, ali podaci proizvoda nisu pronađeni.");
            return;
        }

        var product = null;
        var productKey = null;

        var keys = productKeyVariants(productParam);

        for (var i = 0; i < keys.length; i++) {
            if (data.products[keys[i]]) {
                product = data.products[keys[i]];
                productKey = keys[i];
                break;
            }
        }

        if (!product && recipeId) {
            // Rezervna logika: pokušaj po nazivu recepta ako postoji sličan key.
            Object.keys(data.products).some(function (key) {
                var p = data.products[key];
                if (norm(p.name).indexOf(norm(productParam || recipeId)) !== -1) {
                    product = p;
                    productKey = key;
                    return true;
                }
                return false;
            });
        }

        if (!product) {
            showNotice("Za ovaj recept još nije pronađen odgovarajući kalkulatorski proizvod.");
            return;
        }

        var productName = product.name || productKey;
        var region = product.region || "";

        var regionCandidates = [
            region,
            "hrvatska " + region,
            "hrvatska — " + region,
            "hrvatska - " + region
        ];

        var productCandidates = [
            productName,
            productKey,
            productKey.replace(/_/g, " "),
            productKey.replace(/-/g, " ")
        ];

        var attempts = 0;
        var regionOpened = false;

        var timer = setInterval(function () {
            attempts++;

            // Ako je proizvod već prikazan u aktivnom kalkulatoru, gotovo.
            var bodyText = norm(document.body.textContent);
            if (bodyText.indexOf(norm(productName)) !== -1 && bodyText.indexOf("koliko mesa") !== -1) {
                clearInterval(timer);
                return;
            }

            // 1. pokušaj direktno proizvod
            if (findProductElementByClass(productName) || selectFromDropdown(productCandidates) || clickByText(productCandidates)) {
                clearInterval(timer);
                return;
            }

            // 2. otvori regiju
            if (!regionOpened && region) {
                if (selectFromDropdown(regionCandidates) || clickByText(regionCandidates)) {
                    regionOpened = true;
                    return;
                }
            }

            // 3. nakon otvaranja regije opet pokušaj proizvod
            if (regionOpened) {
                if (findProductElementByClass(productName) || clickByText(productCandidates)) {
                    clearInterval(timer);
                    return;
                }
            }

            if (attempts >= 16) {
                clearInterval(timer);
                showNotice("Recept je otvorio kalkulator, ali automatski odabir proizvoda još nije uspio.");
            }
        }, 500);
    }

    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(runBridge, 300);
    });
})();
