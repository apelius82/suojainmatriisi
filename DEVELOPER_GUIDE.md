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
- `/app/api`: autocomplete-haku
- `/assets`: CSS/JS/ikonit/PPE-SVG:t/liput
- `/database`: SQL-migraatiot + seedit
- `/tests`: yksikkötestit palveluille

## Testit
Aja:
```bash
php /home/runner/work/suojainmatriisi/suojainmatriisi/tests/run.php
```
