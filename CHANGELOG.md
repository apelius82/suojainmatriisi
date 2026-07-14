# Changelog

## 2.0.0 - 2026-07-14
### Tietomallin laajennus (migraatio 002)
- Lisätty `ppe_environments` (toimintaympäristöt)
- Lisätty `ppe_zones` (alueet / laitokset / osastot, hierarkkinen)
- Lisätty `ppe_conditions` (ehdot / työvaiheet)
- Lisätty `ppe_attachments` (liitteet / dokumentit)
- Lisätty `ppe_rule_conditions` (sääntö–ehto-liitos)
- `ppe_sites`: lisätty `environment_id`, `description`
- `ppe_tasks`: lisätty `work_type` (task|work_type|position), `description`
- `ppe_items`: lisätty `item_class` (personal_protection|other_safety), `standard_ref`
- `ppe_requirement_rules`: laajennettu `scope_type` ja `requirement_level` ENUM, lisätty hyväksyntäkentät
- Uudet roolit: `site_manager`, `hseq_reviewer`, `hseq_approver`

### RequirementResolver
- Korvannut yksinkertaisen RequirementInheritanceService-logiikan
- 9-tasoinen prioriteettijärjestys (global → exception)
- "Tiukempi vaatimus voittaa lievemmän" -logiikka
- Lähdetason seuranta (`_source_scope`)
- Tulossivun osiointi A–E (always/conditional/other_safety/information)

### Käyttöliittymä
- Hakusivu: vaiheistettu haku (ympäristö → työmaa → alue → työlaji)
- Tulossivun osiot A–E, "Vain sähköinen ohje on virallinen" -huomio
- Hallintapaneeli: uudet välilehdet Toimintaympäristöt ja Alueet
- Vaatimussäännöt-lomake: scope-taso, ehto, muutoskuvaus, ympäristö/alue-kentät
- Poistettu emojit navigaatiosta ja välilehdistä, korvattu SVG-ikoneilla
- Uudet CSS-tyylit: result-header-card, ppe-card, search-steps, section-badges

### i18n
- Lisätty 40+ uutta termiä kaikille 5 kielelle (fi, sv, en, it, el)
- Uusi terms-moduuli: `admin.php`

### Siemendata
- `002_seed_avolouhos.sql`: Avolouhos-toimintaympäristö, 10 työlajia, 12 PPE-tuotetta, 30+ vaatimussääntöä

### Testit
- Uusi `RequirementResolverTest.php`: 10 testiä
- Päivitetty `I18nSearchTest.php`: tarkistaa uudet termit

## 1.0.0 - 2026-07-14
- Rakennettu uusi itsenäinen Suojainmatriisi-sovellus SafetyFlash-referenssin pohjalta
- Toteutettu autentikointi, roolit, CSRF, security headers, rate limit
- Toteutettu työmaat/tehtävät/suojainkirjasto
- Yleiset/paikalliset vaatimukset ja periytymislogiikka
- Työnkulku: draft -> review -> approved -> published -> archived
- Versiohistoria ja audit-loki
- Työntekijähaku (site + task + autocomplete)
- Tulossivu suojainkorteilla + tulostusnäkymä
- PWA-perusta (manifest, service worker, offline)
- Kielet: fi, sv, en, it, el
- SQL-migraatiot, seedit ja yksikkötestit
