<?php
return [
    // Toimintaympäristö
    'environment'       => ['fi' => 'Toimintaympäristö', 'sv' => 'Driftsmiljö',      'en' => 'Operating environment', 'it' => 'Ambiente operativo', 'el' => 'Λειτουργικό περιβάλλον'],
    'environments'      => ['fi' => 'Toimintaympäristöt','sv' => 'Driftsmiljöer',    'en' => 'Operating environments','it' => 'Ambienti operativi', 'el' => 'Λειτουργικά περιβάλλοντα'],
    // Alue / laitos / osasto
    'zone'              => ['fi' => 'Alue / laitos',     'sv' => 'Område / anläggning','en' => 'Zone / area',          'it' => 'Zona / impianto',    'el' => 'Ζώνη / περιοχή'],
    'zones'             => ['fi' => 'Alueet / laitokset','sv' => 'Områden',           'en' => 'Zones',                'it' => 'Zone',               'el' => 'Ζώνες'],
    // Vaatimustasot
    'mandatory'         => ['fi' => 'Pakollinen',        'sv' => 'Obligatorisk',      'en' => 'Mandatory',            'it' => 'Obbligatorio',       'el' => 'Υποχρεωτικό'],
    'conditional'       => ['fi' => 'Ehdollinen',        'sv' => 'Villkorlig',        'en' => 'Conditional',          'it' => 'Condizionale',       'el' => 'Υπό όρους'],
    'prohibited'        => ['fi' => 'Kielletty',         'sv' => 'Förbjuden',         'en' => 'Prohibited',           'it' => 'Vietato',            'el' => 'Απαγορευμένο'],
    'information'       => ['fi' => 'Ohje / tiedoksi',   'sv' => 'Information',       'en' => 'Information',          'it' => 'Informazione',       'el' => 'Πληροφορία'],
    'not_applicable'    => ['fi' => 'Ei sovelleta',      'sv' => 'Ej tillämplig',     'en' => 'Not applicable',       'it' => 'Non applicabile',    'el' => 'Δεν εφαρμόζεται'],
    // Suojaimen luokat
    'personal_protection' => ['fi' => 'Henkilönsuojain','sv' => 'Personlig skyddsutrustning','en' => 'Personal protective equipment','it' => 'Dispositivo di protezione individuale','el' => 'Μέσο ατομικής προστασίας'],
    'other_safety'      => ['fi' => 'Muu turvallisuusvaruste','sv' => 'Övrig säkerhetsutrustning','en' => 'Other safety equipment','it' => 'Altro dispositivo di sicurezza','el' => 'Άλλος εξοπλισμός ασφαλείας'],
    // Hakuvaiheen otsikot
    'select_environment'=> ['fi' => 'Valitse toimintaympäristö', 'sv' => 'Välj driftsmiljö',    'en' => 'Select environment',     'it' => 'Seleziona ambiente',         'el' => 'Επιλέξτε περιβάλλον'],
    'select_site'       => ['fi' => 'Valitse työmaa / toimipaikka','sv' => 'Välj arbetsplats',  'en' => 'Select site / workplace','it' => 'Seleziona cantiere',         'el' => 'Επιλέξτε εργοτάξιο'],
    'select_zone'       => ['fi' => 'Valitse alue / laitos',      'sv' => 'Välj område',        'en' => 'Select zone / area',     'it' => 'Seleziona zona',             'el' => 'Επιλέξτε ζώνη'],
    'select_task'       => ['fi' => 'Valitse työlaji / tehtävä',  'sv' => 'Välj arbetstyp',     'en' => 'Select job type / task', 'it' => 'Seleziona mansione',         'el' => 'Επιλέξτε είδος εργασίας'],
    'show_requirements' => ['fi' => 'Näytä vaatimukset',          'sv' => 'Visa krav',          'en' => 'Show requirements',      'it' => 'Mostra requisiti',           'el' => 'Εμφάνιση απαιτήσεων'],
    // Tulossivun osiot
    'section_always'    => ['fi' => 'A) Aina vaadittavat',        'sv' => 'A) Alltid obligatoriska','en' => 'A) Always required', 'it' => 'A) Sempre obbligatori',       'el' => 'Α) Πάντα απαιτούμενα'],
    'section_conditional'=> ['fi'=> 'B) Tilanteen mukaan',        'sv' => 'B) Situationsbetingat', 'en' => 'B) Situational',    'it' => 'B) In base alla situazione',  'el' => 'Β) Ανά περίσταση'],
    'section_other'     => ['fi' => 'C) Muut turvallisuusvarusteet','sv'=>'C) Övrig säkerhetsutrustning','en'=>'C) Other safety equipment','it'=>'C) Altri dispositivi di sicurezza','el'=>'Γ) Άλλος εξοπλισμός'],
    'section_critical'  => ['fi' => 'D) Kriittiset huomiot',      'sv' => 'D) Kritiska observationer','en'=> 'D) Critical notes','it'=> 'D) Note critiche',             'el' => 'Δ) Κρίσιμες παρατηρήσεις'],
    'section_attachments'=> ['fi'=> 'E) Ohjeet ja liitteet',      'sv' => 'E) Instruktioner och bilagor','en'=> 'E) Guides & attachments','it'=> 'E) Guide e allegati',  'el' => 'Ε) Οδηγίες και συνημμένα'],
    // Virallinen huomio
    'official_only'     => ['fi' => 'Vain sähköinen ohje on virallinen','sv'=>'Endast elektronisk instruktion är officiell','en'=>'Only the electronic guide is official','it'=>'Solo la guida elettronica è ufficiale','el'=>'Μόνο ο ηλεκτρονικός οδηγός είναι επίσημος'],
    'approved_version'  => ['fi' => 'Hyväksytyn version päiväys', 'sv' => 'Godkänd versionsdatum','en' => 'Approved version date','it' => 'Data versione approvata',   'el' => 'Ημερομηνία εγκεκριμένης έκδοσης'],
    // Lähde / periytyminen
    'source'            => ['fi' => 'Lähde',                      'sv' => 'Källa',               'en' => 'Source',                'it' => 'Fonte',                      'el' => 'Πηγή'],
    'condition'         => ['fi' => 'Ehto',                       'sv' => 'Villkor',             'en' => 'Condition',             'it' => 'Condizione',                 'el' => 'Προϋπόθεση'],
    'standard'          => ['fi' => 'Standardi',                  'sv' => 'Standard',            'en' => 'Standard',              'it' => 'Standard',                   'el' => 'Πρότυπο'],
    // Scope-tasot
    'scope_global'      => ['fi' => 'Yleinen vaatimus',           'sv' => 'Allmänt krav',        'en' => 'General requirement',   'it' => 'Requisito generale',         'el' => 'Γενική απαίτηση'],
    'scope_environment' => ['fi' => 'Toimintaympäristövaatimus',  'sv' => 'Krav för driftsmiljö','en' => 'Environment requirement','it' => 'Requisito ambiente',         'el' => 'Απαίτηση περιβάλλοντος'],
    'scope_site'        => ['fi' => 'Työmaakohtainen',            'sv' => 'Arbetsplatsspecifikt','en' => 'Site-specific',          'it' => 'Specifico del cantiere',     'el' => 'Ειδικό εργοταξίου'],
    'scope_zone'        => ['fi' => 'Aluekohtainen',              'sv' => 'Områdesspecifikt',    'en' => 'Zone-specific',          'it' => 'Specifico della zona',       'el' => 'Ειδικό ζώνης'],
    'scope_task'        => ['fi' => 'Tehtäväkohtainen',           'sv' => 'Arbetsuppgiftsspecifikt','en'=>'Task-specific',         'it' => 'Specifico del compito',      'el' => 'Ειδικό εργασίας'],
    'scope_site_task'   => ['fi' => 'Työmaa + tehtävä',           'sv' => 'Arbetsplats + uppgift','en' => 'Site + task',           'it' => 'Cantiere + compito',         'el' => 'Εργοτάξιο + εργασία'],
    'scope_zone_task'   => ['fi' => 'Alue + tehtävä',             'sv' => 'Område + uppgift',    'en' => 'Zone + task',           'it' => 'Zona + compito',             'el' => 'Ζώνη + εργασία'],
    'scope_phase'       => ['fi' => 'Työvaihe',                   'sv' => 'Arbetsmoment',        'en' => 'Work phase',            'it' => 'Fase di lavoro',             'el' => 'Φάση εργασίας'],
    'scope_exception'   => ['fi' => 'Hyväksytty poikkeus',        'sv' => 'Godkänt undantag',    'en' => 'Approved exception',    'it' => 'Eccezione approvata',        'el' => 'Εγκεκριμένη εξαίρεση'],
    // Työnkulku
    'draft'             => ['fi' => 'Luonnos',                    'sv' => 'Utkast',              'en' => 'Draft',                 'it' => 'Bozza',                      'el' => 'Πρόχειρο'],
    'review'            => ['fi' => 'Tarkastettavana',            'sv' => 'Under granskning',    'en' => 'Under review',          'it' => 'In revisione',               'el' => 'Υπό αναθεώρηση'],
    'approved'          => ['fi' => 'Hyväksytty',                 'sv' => 'Godkänd',             'en' => 'Approved',              'it' => 'Approvato',                  'el' => 'Εγκεκριμένο'],
    'published'         => ['fi' => 'Julkaistu',                  'sv' => 'Publicerad',          'en' => 'Published',             'it' => 'Pubblicato',                 'el' => 'Δημοσιευμένο'],
    'archived'          => ['fi' => 'Arkistoitu',                 'sv' => 'Arkiverad',           'en' => 'Archived',              'it' => 'Archiviato',                 'el' => 'Αρχειοθετημένο'],
    // Hallinnan välilehdet
    'tab_environments'  => ['fi' => 'Toimintaympäristöt',         'sv' => 'Driftsmiljöer',       'en' => 'Environments',          'it' => 'Ambienti',                   'el' => 'Περιβάλλοντα'],
    'tab_sites'         => ['fi' => 'Työmaat',                    'sv' => 'Arbetsplatser',       'en' => 'Sites',                 'it' => 'Cantieri',                   'el' => 'Εργοτάξια'],
    'tab_zones'         => ['fi' => 'Alueet',                     'sv' => 'Områden',             'en' => 'Zones',                 'it' => 'Zone',                       'el' => 'Ζώνες'],
    'tab_tasks'         => ['fi' => 'Työlajit',                   'sv' => 'Arbetstyper',         'en' => 'Job types',             'it' => 'Mansioni',                   'el' => 'Είδη εργασίας'],
    'tab_ppe'           => ['fi' => 'Suojainkirjasto',            'sv' => 'Skyddsutrustningsbibliotek','en' => 'PPE library',     'it' => 'Biblioteca DPI',             'el' => 'Βιβλιοθήκη ΜΑΠ'],
    'tab_rules'         => ['fi' => 'Vaatimussäännöt',            'sv' => 'Kravregler',          'en' => 'Requirement rules',     'it' => 'Regole di requisito',        'el' => 'Κανόνες απαιτήσεων'],
    'tab_audit'         => ['fi' => 'Audit-loki',                 'sv' => 'Granskningslogg',     'en' => 'Audit log',             'it' => 'Registro audit',             'el' => 'Αρχείο ελέγχου'],
    // Yleisiä
    'all_environments'  => ['fi' => 'Kaikki ympäristöt',          'sv' => 'Alla miljöer',        'en' => 'All environments',      'it' => 'Tutti gli ambienti',         'el' => 'Όλα τα περιβάλλοντα'],
    'all_sites'         => ['fi' => 'Kaikki työmaat',             'sv' => 'Alla arbetsplatser',  'en' => 'All sites',             'it' => 'Tutti i cantieri',           'el' => 'Όλα τα εργοτάξια'],
    'all_zones'         => ['fi' => 'Kaikki alueet',              'sv' => 'Alla områden',        'en' => 'All zones',             'it' => 'Tutte le zone',              'el' => 'Όλες οι ζώνες'],
    'all_tasks'         => ['fi' => 'Kaikki työlajit',            'sv' => 'Alla arbetstyper',    'en' => 'All job types',         'it' => 'Tutte le mansioni',          'el' => 'Όλα τα είδη εργασίας'],
    'no_zone'           => ['fi' => 'Ei aluerajausta',            'sv' => 'Inget område',        'en' => 'No zone filter',        'it' => 'Nessuna zona',               'el' => 'Χωρίς ζώνη'],
    // Vaatimussäännön hallinta
    'add_single_rule'   => ['fi' => '+ Lisää yksittäinen vaatimussääntö','sv' => '+ Lägg till enskild kravregeln','en' => '+ Add single requirement rule','it' => '+ Aggiungi regola requisito','el' => '+ Προσθήκη κανόνα'],
    'bulk_add_title'    => ['fi' => 'Massalisäys – lisää varuste usealle tehtävälle kerralla','sv' => 'Masslägga – lägg till utrustning för flera uppgifter','en' => 'Bulk add – add equipment to multiple tasks at once','it' => 'Aggiunta multipla','el' => 'Μαζική προσθήκη'],
    'bulk_add_desc'     => ['fi' => 'Valitse suojain, vaatimustaso ja kohde (työmaa/ympäristö). Sääntö lisätään kaikkiin kyseisen kohteen tehtäviin luonnoksena – tarkista ja julkaise sen jälkeen.','sv' => 'Välj skyddsutrustning, kravnivå och mål. Regel läggs till alla uppgifter som utkast.','en' => 'Select PPE, requirement level and target (site/environment). Rules are added to all tasks as drafts – review and publish afterwards.','it' => 'Seleziona DPI, livello di requisito e destinazione. Le regole vengono aggiunte come bozze.','el' => 'Επιλέξτε ΜΑΠ, επίπεδο απαίτησης και στόχο. Οι κανόνες προστίθενται ως πρόχειρα.'],
    'bulk_add_confirm'  => ['fi' => 'Lisätään varuste kaikille valitun kohteen tehtäville luonnoksena. Jatketaanko?','sv' => 'Utrustning läggs till alla uppgifter som utkast. Fortsätta?','en' => 'Equipment will be added to all tasks as drafts. Continue?','it' => 'Aggiungere come bozze a tutte le attività?','el' => 'Προσθήκη ως πρόχειρα σε όλες τις εργασίες;'],
    'confirm_archive_rule' => ['fi' => 'Arkistoi tämä sääntö?','sv' => 'Arkivera denna regel?','en' => 'Archive this rule?','it' => 'Archiviare questa regola?','el' => 'Αρχειοθέτηση αυτού του κανόνα;'],
    'add_equipment_to_task' => ['fi' => 'Lisää varuste tehtävälle','sv' => 'Lägg till utrustning för uppgiften','en' => 'Add equipment to task','it' => 'Aggiungi attrezzatura al compito','el' => 'Προσθήκη εξοπλισμού στην εργασία'],
    // Virheviestit
    'err_invalid_ppe'       => ['fi' => 'Valitse ensin suojain.','sv' => 'Välj en skyddsutrustning först.','en' => 'Please select a PPE item first.','it' => 'Seleziona prima un DPI.','el' => 'Επιλέξτε πρώτα ΜΑΠ.'],
    'err_no_tasks'          => ['fi' => 'Ei tehtäviä valitulle alueelle.','sv' => 'Inga uppgifter för det valda området.','en' => 'No tasks found for the selected area.','it' => 'Nessuna attività trovata.','el' => 'Δεν βρέθηκαν εργασίες.'],
    'err_upload_failed'     => ['fi' => 'Tiedoston lataus epäonnistui.','sv' => 'Filuppladdningen misslyckades.','en' => 'File upload failed.','it' => 'Caricamento file fallito.','el' => 'Αποτυχία μεταφόρτωσης αρχείου.'],
    'err_too_large'         => ['fi' => 'Tiedosto on liian suuri (max 2 MB).','sv' => 'Filen är för stor (max 2 MB).','en' => 'File is too large (max 2 MB).','it' => 'File troppo grande (max 2 MB).','el' => 'Το αρχείο είναι πολύ μεγάλο (max 2 MB).'],
    'err_invalid_type'      => ['fi' => 'Kuvaa ei hyväksytty – sallitut: SVG, JPG, PNG, WEBP.','sv' => 'Filen accepterades inte – tillåtna: SVG, JPG, PNG, WEBP.','en' => 'File not accepted – allowed: SVG, JPG, PNG, WEBP.','it' => 'File non accettato – permessi: SVG, JPG, PNG, WEBP.','el' => 'Μη αποδεκτό αρχείο – επιτρεπτά: SVG, JPG, PNG, WEBP.'],
    'err_move_failed'       => ['fi' => 'Tiedoston tallennus epäonnistui.','sv' => 'Det gick inte att spara filen.','en' => 'Failed to save the file.','it' => 'Impossibile salvare il file.','el' => 'Αποτυχία αποθήκευσης αρχείου.'],
    'bulk_add_success'      => ['fi' => 'Massalisäys onnistui: {count} sääntöä lisätty luonnoksina.','sv' => 'Masslägga lyckades: {count} regler tillagda som utkast.','en' => 'Bulk add succeeded: {count} rules added as drafts.','it' => 'Aggiunta multipla riuscita: {count} regole aggiunte come bozze.','el' => 'Μαζική προσθήκη επιτυχής: {count} κανόνες ως πρόχειρα.'],
];
