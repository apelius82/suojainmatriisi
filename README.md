# Suojainmatriisi

Suojainmatriisi on SafetyFlashista inspiroitu, mutta täysin erillinen PHP 8+ -sovellus työmaa- ja työtehtäväkohtaisten PPE-vaatimusten hallintaan.

Sovellus tukee laajaa hierarkiaa:
`organisaatio → toimintaympäristö → työmaa → alue → työlaji → vaatimukset`

## Sisäänkirjautuminen (seed)
- Käyttäjä: `admin@suojainmatriisi.local`
- Salasana: `Admin123!`

## Käynnistys
1. Kopioi `.env.example` -> `.env` ja aseta MySQL/MariaDB-DSN.
2. Avaa sovellus (`index.php`).
3. Migraatiot ja seedit ajetaan bootstrapissa automaattisesti.

## Ominaisuudet
- Kirjautuminen, roolit ja palvelinpuolen käyttöoikeustarkistukset
- **Uusi hierarkia:** toimintaympäristöt → työmaat → alueet → työlajit
- Vaatimustasot: `mandatory`, `conditional`, `recommended`, `information`, `not_applicable`, `prohibited`
- **RequirementResolver** — yhdistää vaatimukset 9 prioriteettitasolta
  - Tiukempi vaatimus voittaa lievemmän
  - Lähdetaso näkyy tuloskortissa
- Tulossivun osiot A–E (aina vaadittavat / tilanteen mukaan / muut / huomiot / liitteet)
- Työnkulku: `draft → review → approved → published → archived`
- Versiohistoria ja audit-loki
- Vaiheistettu haku: toimintaympäristö → työmaa → alue → työlaji
- Mobiilinäkymä korttipohjaisena (ei leveä taulukko)
- PWA-perusta (manifest, service worker, offline)
- Kielet: fi, sv, en, it, el (kaikki uudet termit viidellä kielellä)
- Avolouhos-siemendata esimerkkinä

## Käyttäjäroolit
| Rooli | Slug | Oikeudet |
|-------|------|----------|
| Pääkäyttäjä | `admin` | Kaikki |
| Työmaan ylläpitäjä | `site_manager` | Työmaat, alueet, säännöt |
| HSEQ-tarkastaja | `hseq_reviewer` | Tarkistaa säännöt |
| HSEQ-hyväksyjä | `hseq_approver` | Hyväksyy ja julkaisee |
| Työmaavastaava | `manager` | Työmaat, tehtävät, suojaimet |
| Tarkastaja | `reviewer` | Säännöt |
| Työntekijä | `worker` | Vain haku (julkaistut versiot) |

## Testit
```bash
php tests/run.php
```
14 testiä kattaa: periytyminen, työnkulku, CSRF, i18n, RequirementResolver.

Lisätiedot: `DEVELOPER_GUIDE.md`, `DATABASE.md`, `CHANGELOG.md`.
