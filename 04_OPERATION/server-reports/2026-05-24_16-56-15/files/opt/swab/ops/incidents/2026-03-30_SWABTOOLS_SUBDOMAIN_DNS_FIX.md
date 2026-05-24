# SWABTOOLS INCIDENT NOTE — SUBDOMAIN DNS FIX

**Datum:** 2026-03-30

## Problem
Poddomene na swabtools.com nisu se prikazivale.

## Uzrok
Nedostajao je wildcard DNS zapis za poddomene u Dynadot DNS postavkama.

## Rješenje
Dodan DNS zapis:

- Type: A
- Host: *
- Value: 178.104.92.245
- TTL: 5 min

## Rezultat
Poddomene sada pravilno resolveaju na VPS.

Potvrđeno:
- `test.swabtools.com` resolvea na `178.104.92.245`
- `demo.swabtools.com` resolvea na `178.104.92.245`
- HTTPS radi
- stvarna poddomena `kecap.swabtools.com` vraća `200 OK`

## Napomena
Nginx i wildcard SSL certifikat već su bili ispravno postavljeni.
Primarni problem bio je DNS wildcard zapis.
