# Data adapter patch point inspection

Ovaj korak ne mijenja WordPress i ne mijenja dogovoreni prikaz recepata.

Cilj je pronaći točnu točku u postojećem rendereru gdje se može dodati podatkovni adapter koji puni već dogovoreni prikaz.

## Zaključano pravilo

- Ne mijenja se dizajn.
- Ne mijenja se redoslijed blokova.
- Ne uvodi se novi renderer.
- Ne radi se ručno HTML krpanje pojedinačnih recepata.
- Data adapter smije samo puniti postojeći prikaz točnim podacima.

## Sljedeći korak

Dodati pilot data adapter samo na postojeći profilni/data sloj za post 2976 i zatim provesti HTML QA.
