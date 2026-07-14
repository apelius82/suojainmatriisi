# DEVELOPER GUIDE

## SafetyFlash-analyysi ja soveltaminen
Hyödynnetyt periaatteet SafetyFlashista (SF-344):
1. Kevyt PHP-rakenne (index-entrypoint + include-pohjainen renderöinti).
2. Turvallisuusrunko: CSRF, auth, security headers, login rate limit.
3. Design-linja: tumma sticky-header, keltainen painoväri, korttipohjainen UI, mobiilin bottom-nav.
4. Modulaarinen i18n-rakenne (`app/config/terms/_index.php`, kielet fi/sv/en/it/el).
5. PWA-ajattelu (`manifest.php`, `service-worker.js`, `offline.html`).

Ei kopioitu SafetyFlashista (rajattu pois v1:stä):
- tiedotetyönkulku, kuvankäsittely, tutkinta
- Xibo/infonäytöt
- push/email-ilmoitusputket
- SafetyFlash-kortti- ja julkaisuominaisuudet
- perintöinen sekava resurssirakenne (esim. `assets/css/js/...`)

## Arkkitehtuuri
- `/app/includes`: bootstrap, auth, csrf, security, rate limit
- `/app/repositories`: data access
- `/app/services`: domainlogiikka (auth, workflow, inheritance)
- `/app/controllers`: sivujen orchestration
- `/app/actions`: mutaatiot (POST)
- `/app/api`: autocomplete-haku ja AJAX-endpointit
- `/assets`: CSS/JS/ikonit/PPE-SVG:t/liput
- `/database`: SQL-migraatiot + seedit
- `/tests`: yksikkötestit palveluille

## Testit
Aja:
```bash
php tests/run.php
```

Testit kattavat:
- `RequirementResolverTest` — vaatimusresoluutiologiikka (10 testiä)
- `InheritanceServiceTest` — periytymislogiikka
- `WorkflowAuthCsrfTest` — työnkulku, autentikaatio, CSRF
- `I18nSearchTest` — i18n-termit kaikille kielille

---

## v2.0 — Hierarkialaajennus

### Uusi tietohierarkia
```
organisaatio
└── toimintaympäristö (ppe_environments)
    └── työmaa/toimipaikka (ppe_sites)
        └── alue/laitos/osasto (ppe_zones)
            └── työlaji/tehtävä/vakanssi (ppe_tasks)
                └── vaatimukset (ppe_requirement_rules)
```

### RequirementResolver
Tiedosto: `app/services/RequirementResolver.php`

Yhdistää vaatimukset 9-tasoisen prioriteettijärjestyksen mukaan:
1. global — organisaation yleinen vaatimus
2. environment — toimintaympäristön vaatimus
3. site — työmaan vaatimus
4. zone — alueen vaatimus
5. task — työlajin yleinen vaatimus
6. site_task — työmaa + työlaji -yhdistelmä
7. zone_task — alue + työlaji -yhdistelmä
8. phase — työvaihekohtainen
9. exception — nimenomainen hyväksytty poikkeus

Tiukempi vaatimus (mandatory > recommended > conditional) voittaa lievemmän.
Jokainen resoluutiossa valittu sääntö saa `_source_scope`-merkinnän (mistä tasosta vaatimus tuli).

Käyttö:
```php
$resolver = new RequirementResolver();
$result   = $resolver->resolve($rules, $envId, $siteId, $zoneId, $taskId);
$sections = $resolver->groupSections($result['resolved']);
// $sections: [A => [...], B => [...], C => [...], D => [...]]
```

Osiot tulossivulla:
- **A** — aina vaadittavat henkilönsuojaimet
- **B** — tilanteen mukaan (ehdolliset/suositeltavat)
- **C** — muut turvallisuusvarusteet
- **D** — kriittiset huomiot
- **E** — ohjeet ja liitteet (placeholder, vaatii liitetiedostojen toteutuksen)

### Uudet repositoriot
- `app/repositories/EnvironmentRepository.php` — toimintaympäristöjen CRUD
- `app/repositories/ZoneRepository.php` — alueiden CRUD (site_id-kohtaiset)

### Uudet migraatiot ja seedit
- `database/migrations/002_expand_model.sql` — lisää 5 taulua, laajentaa 5 olemassa olevaa
- `database/seeds/002_seed_avolouhos.sql` — Avolouhos avolouhoksen esimerkkirakenne (domain-validaatio)

### Käyttäjäroolit (v2)
| Rooli | Kuvaus |
|---|---|
| `worker` | Hakee vaatimukset, ei hallintaa |
| `manager` | Hallitsee työmaan tietoja |
| `site_manager` | Uusi: työmaakohtainen ylläpito |
| `reviewer` | Tarkastaa vaatimuksia |
| `hseq_reviewer` | Uusi: HSEQ-tarkastaja |
| `hseq_approver` | Uusi: HSEQ-hyväksyjä |
| `admin` | Pääkäyttäjä, kaikki oikeudet |

### AJAX API
- `app/api/zones_by_site.php?site_id=X` — palauttaa JSON-listan alueen vyöhykkeistä
- `app/api/search_workers.php?site_id=X&task_id=Y&q=Z` — hakee työtekijöitä

### Julkaisuvirta
```
luonnos → tarkastettavana → hyväksytty → julkaistu → arkistoitu
```
Toteutettu `WorkflowService`-luokassa. Työntekijälle näkyy vain `published`-tila.
Versionumero, hyväksyjä, päivämäärä ja muutoskuvaus tallennetaan `ppe_rule_versions`-tauluun.

### i18n
Kaikki uudet UI-tekstit kaikille 5 kielelle: fi / sv / en / it / el.
Uudet termit moduulissa `app/config/terms/admin.php`.
Käytä `sm_t($key, $lang)` kaikkialla; tyhjä arvo palauttaa suomenkielisen vastineen.

### Tunnetut puutteet / jatkokehitys
- Liitetiedostojen lataus ja osion E täydellinen toteutus
- Matriisinäkymä hallinnolle (rivit=työlajit, sarakkeet=varusteryhmät)
- Ylläpitäjän esikatselutila (worker-näkymä suoraan dashboardista)
- QR-linkin generointi tulossivulle
- Massatuonti Word/Excel-pohjaisesta matriisidokumentista
- PWA offline-synkronointi uusille hierarkiatasoille

