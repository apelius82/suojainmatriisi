# Suojainmatriisi – analyysi ja arkkitehtuurisuunnitelma

## SafetyFlashista hyödynnetyt osat (viite, ei kopio)
- Kevyt PHP-sovellusmalli (`index.php` + page/action-reititys).
- Turvakerrokset: bootstrap, auth, csrf, security headers, rate limit.
- UI-tyyli: tumma sticky-header, keltainen painotus, korttipohjat, mobiilin bottom-nav.
- Modulaarinen termistömalli ja 5 kieltä (`fi`, `sv`, `en`, `it`, `el`).
- PWA-perusta: manifest, service worker, offline-sivu.

## SafetyFlashista tietoisesti pois jätetyt osat v1
- tiedotetyönkulku- ja kuvankäsittelytoiminnot
- tutkinta, Xibo/infonäytöt, push/email-jakelut
- SafetyFlash-spesifinen korttimalli
- sekavat perintöresurssipolut

## Uuden sovelluksen itsenäisyys
- oma tauluprefiksi: `ppe_`
- oma session/cookie-avain: `sm_session`, `sm_identity`
- oma loki: `storage/logs/suojainmatriisi.log`
- oma PWA-identiteetti: Suojainmatriisi
- oma rakenne: `/app`, `/assets`, `/database`, `/storage`, `/tests`

## Pääkäsitteet
- kirjautuminen + roolit + palvelinpuolen oikeustarkastus
- työmaakirjasto, työtehtäväkirjasto, suojainkirjasto
- vaatimussäännöt (`global/site/task/local`)
- keskitetty periytymisratkaisin (local > task > site > global)
- workflow: draft/review/approved/published/archived
- versiohistoria + audit-loki
- työntekijähaku autocomplete + tuloskortit + print

## Testauspainopisteet
- periytymislogiikka + ristiriidat
- workflow-siirtymät
- CSRF + auth
- i18n-kielikonfiguraatio
