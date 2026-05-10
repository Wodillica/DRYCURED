# DRYCURED — home hero fix

## Sažetak

Ispravljen je prikaz hero bloka "Digitalna pušnica" na početnoj stranici drycured.com.

## Problem

- digitalna mreža iznad naslova bježala je ulijevo pri zoomu 90 %, 80 % i 70 %
- opisni tekst ispod naslova nije ostajao centriran
- raniji CSS/JS pokušaji stvarali su dodatne slojeve i svijetlu traku

## Uzrok

U Elementor podacima pronađeno je:

- container `3h24rr6`
- negativni `background_overlay_xpos` pomaci za digitalnu mrežu
- text widget `aaf40c4` s ručno zadanom širinom od 1177 px

## Rješenje

- ispravljena je horizontalna pozicija digitalne mreže na izvoru u Elementor podacima
- uklonjena je prevelika ručna širina opisnog teksta
- tekst hero bloka vraćen je u centrirani tok
- uklonjeni su prethodni nestabilni CSS/JS overlay pokušaji

## Status

Hero blok "Digitalna pušnica" sada je stabilan i centriran pri 100 %, 90 %, 80 % i 70 % prikaza.
