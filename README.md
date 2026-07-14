# Suojainmatriisi

Suojainmatriisi on SafetyFlashista inspiroitu, mutta täysin erillinen PHP 8+ -sovellus työmaa- ja työtehtäväkohtaisten PPE-vaatimusten hallintaan.

## Sisäänkirjautuminen (seed)
- Käyttäjä: `admin@suojainmatriisi.local`
- Salasana: `Admin123!`

## Käynnistys
1. Kopioi `.env.example` -> `.env` ja aseta MySQL/MariaDB-DSN.
2. Avaa sovellus (`index.php`).
3. Migraatiot ja seedit ajetaan bootstrapissa automaattisesti.

## Ominaisuudet
- kirjautuminen, roolit ja palvelinpuolen käyttöoikeustarkistukset
- työmaat, työtehtävät, suojainkirjasto
- yleiset/paikalliset vaatimukset ja periytymislogiikka
- työnkulku: `draft -> review -> approved -> published -> archived`
- versiohistoria ja audit-loki
- työntekijähaku (site + task + autocomplete)
- tulossivu suojainkorteilla + tulostusnäkymä
- PWA-perusta (manifest, service worker, offline)
- kielet: fi, sv, en, it, el

Lisätiedot: `DEVELOPER_GUIDE.md`, `DATABASE.md`, `CHANGELOG.md`.
