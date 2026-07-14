-- 002_seed_avolouhos.sql
-- Siemendata: Avolouhos-toimintaympäristö, työlajit ja suojainsäännöt
-- Lähde: SUOJAINVAATIMUKSET3.md (avolouhos)

-- Toimintaympäristö
INSERT IGNORE INTO ppe_environments (id, code, name, description, is_active, created_at) VALUES
(1, 'ENV-AVOL', 'Avolouhos', 'Avolouhostoiminta: räjäytykset, lastaus, ajo ja ylläpito', 1, NOW());

-- Työmaa
INSERT IGNORE INTO ppe_sites (id, environment_id, code, name, description, is_active, created_at) VALUES
(3, 1, 'AVOL-001', 'Avolouhos-työmaa', 'Esimerkkiohje avolouhostoimintaan', 1, NOW());

-- Alueet / laitokset
INSERT IGNORE INTO ppe_zones (id, site_id, parent_zone_id, code, name, description, is_active, created_at) VALUES
(1, 3, NULL, 'Z-KENTTA', 'Porauskenttä', 'Poraus- ja panostusalue', 1, NOW()),
(2, 3, NULL, 'Z-LASTAUS', 'Lastauskenttä', 'Louheenlastaus- ja rikotusalue', 1, NOW()),
(3, 3, NULL, 'Z-AJOREITTI', 'Louheenajoreitti', 'Louheen kuljetusreitti', 1, NOW()),
(4, 3, NULL, 'Z-TANKK', 'Tankkausasema', 'Polttoaineen tankkaus ja käsittely', 1, NOW()),
(5, 3, NULL, 'Z-PESU', 'Pesuasema', 'Ajoneuvojen ja koneiden pesu', 1, NOW());

-- Työlajit (lisätään olemassaolevien lisäksi, id:t 3-12)
INSERT IGNORE INTO ppe_tasks (id, name, work_type, category, description, is_active, created_at) VALUES
(3,  'Poraus',                         'work_type', 'Louhintatyöt', 'Poraustyöt avolouhoksella', 1, NOW()),
(4,  'Panostus',                        'work_type', 'Räjäytystyöt', 'Panos- ja sytytystyöt', 1, NOW()),
(5,  'Porakentän puhdistus',            'work_type', 'Louhintatyöt', 'Porakentän siivous ja tarkistus', 1, NOW()),
(6,  'Louheenlastaus',                  'work_type', 'Lastaustyöt', 'Louheen lastaus ajoneuvoon', 1, NOW()),
(7,  'Louheenajo',                      'work_type', 'Kuljetustyöt', 'Louheen kuljetus murskaukseen', 1, NOW()),
(8,  'Rusnaus',                         'work_type', 'Louhintatyöt', 'Löystyneen kiven poisto (rusnaus)', 1, NOW()),
(9,  'Rikotus',                         'work_type', 'Louhintatyöt', 'Suurten lohkareiden rikotus', 1, NOW()),
(10, 'Teiden ylläpito',                 'work_type', 'Kunnossapito', 'Ajoväylien kunnossapito', 1, NOW()),
(11, 'Polttoainekuljetukset / tankkaus','work_type', 'Logistiikka',  'Polttoaineen kuljetus ja tankkaus', 1, NOW()),
(12, 'Työkoneiden / laitteiden pesu',   'work_type', 'Kunnossapito', 'Koneiden ja laitteiden pesutoimet', 1, NOW());

-- Ehdot / työvaiheet
INSERT IGNORE INTO ppe_conditions (id, code, name, description, is_active, created_at) VALUES
(1, 'COND-KONE-NOUSU',  'Koneeseen nousu / poistuminen', 'Poistettava liukuesteet ennen nousuaskelmalle astumista', 1, NOW()),
(2, 'COND-KEMIK',       'Kemikaalialtistus',             'Käytettävä kemikaalisuojakäsineitä ja tiiviitä suojalaseja', 1, NOW()),
(3, 'COND-KORKEUS',     'Korkeustyö',                   'Putoamissuojain pakollinen yli 2 m korkeudella', 1, NOW()),
(4, 'COND-PÖLY',        'Korkea pölypitoisuus',         'FFP3-hengityssuojain, kun pöly >4 mg/m³', 1, NOW()),
(5, 'COND-TANKKAUS',    'Tankkauksen aikana',            'Kemikaalisuojaus aktiiivisena tankkauksen ajan', 1, NOW());

-- Suojainkirjasto: henkilönsuojaimet (personal_protection)
INSERT IGNORE INTO ppe_items (id, code, name, category, item_class, standard_ref, icon, is_active, created_at) VALUES
(5,  'PPE-HELM-397',  'Kypärä EN 397',             'Pään suojaus',           'personal_protection', 'EN 397',          'helmet.svg',      1, NOW()),
(6,  'PPE-RESP-FFP3', 'Hengityssuojain FFP3',       'Hengityssuojaus',        'personal_protection', 'EN 149:2001+A1',  'respirator.svg',  1, NOW()),
(7,  'PPE-GLO-CHEM',  'Kemikaalisuojakäsineet',     'Käsiensuojaus',          'personal_protection', 'EN 374',          'gloves.svg',      1, NOW()),
(8,  'PPE-GOG-TIGHT', 'Tiiviit suojalasit',          'Silmien- ja kasvojensuojaus', 'personal_protection', 'EN 166', 'goggles.svg',     1, NOW()),
(9,  'PPE-VISOR',     'Visiiri',                     'Kasvojensuojaus',        'personal_protection', 'EN 166',          'goggles.svg',     1, NOW()),
(10, 'PPE-BOOT',      'Turvajalkineet',              'Jalkasuojaus',           'personal_protection', 'EN ISO 20345',    'boots.svg',       1, NOW()),
(11, 'PPE-HEAR',      'Kuulonsuojaimet',             'Kuulonsuojaus',          'personal_protection', 'EN 352',          'earmuff.svg',     1, NOW()),
(12, 'PPE-VIS',       'Näkyvä suojavaatetus',        'Vartalonsuojaus',        'personal_protection', 'EN ISO 20471',    'vest.svg',        1, NOW()),
(13, 'PPE-FALL-361',  'Putoamissuojain EN 361',      'Putoamissuojaus',        'personal_protection', 'EN 361',          'harness.svg',     1, NOW()),
(14, 'PPE-ANTI-SLIP', 'Liukuesteet',                 'Liukastumisenesto',      'personal_protection', NULL,              'boots.svg',       1, NOW());

-- Muut turvallisuusvarusteet (other_safety)
INSERT IGNORE INTO ppe_items (id, code, name, category, item_class, standard_ref, icon, is_active, created_at) VALUES
(15, 'OSE-LAMP',      'Henkilökohtainen valaisin',   'Valaisus',               'other_safety', NULL, 'lamp.svg',  1, NOW()),
(16, 'OSE-RADIO',     'Radio / viestintälaite',      'Viestintä',              'other_safety', NULL, 'radio.svg', 1, NOW());

-- ===========================================================================
-- VAATIMUSSÄÄNNÖT: Avolouhos
-- Scope-tasot: environment (yleinen), task (työlaji), site_task (työmaa+laji)
-- ===========================================================================

-- A) Toimintaympäristön yleiset vaatimukset (scope = environment)
INSERT IGNORE INTO ppe_requirement_rules
  (id, scope_type, environment_id, site_id, zone_id, task_id, ppe_item_id, requirement_level, status, version_no, notes, created_by, created_at, updated_at)
VALUES
(10, 'environment', 1, NULL, NULL, NULL,  5, 'mandatory', 'published', 1, 'EN 397 kypärä kaikilla avolouhoksella',               1, NOW(), NOW()),
(11, 'environment', 1, NULL, NULL, NULL,  6, 'mandatory', 'published', 1, 'Hengityssuojain FFP3 avolouhoksella',                  1, NOW(), NOW()),
(12, 'environment', 1, NULL, NULL, NULL,  3, 'mandatory', 'published', 1, 'Yleiset suojakäsineet kaikille',                       1, NOW(), NOW()),
(13, 'environment', 1, NULL, NULL, NULL,  2, 'mandatory', 'published', 1, 'Silmiensuojaimet kaikille avolouhoksella',             1, NOW(), NOW()),
(14, 'environment', 1, NULL, NULL, NULL, 10, 'mandatory', 'published', 1, 'Turvajalkineet EN ISO 20345',                          1, NOW(), NOW()),
(15, 'environment', 1, NULL, NULL, NULL, 11, 'mandatory', 'published', 1, 'Kuulonsuojaimet avolouhoksella',                       1, NOW(), NOW()),
(16, 'environment', 1, NULL, NULL, NULL, 12, 'mandatory', 'published', 1, 'Näkyvä suojavaatetus EN ISO 20471',                    1, NOW(), NOW()),
(17, 'environment', 1, NULL, NULL, NULL, 13, 'mandatory', 'published', 1, 'Putoamissuojain EN 361 kaikille',                      1, NOW(), NOW()),
(18, 'environment', 1, NULL, NULL, NULL, 15, 'mandatory', 'published', 1, 'Henkilökohtainen valaisin avolouhoksella',             1, NOW(), NOW()),
(19, 'environment', 1, NULL, NULL, NULL, 16, 'mandatory', 'published', 1, 'Radio / viestintälaite avolouhoksella',                1, NOW(), NOW()),
(20, 'environment', 1, NULL, NULL, NULL, 14, 'mandatory', 'published', 1, 'Liukuesteet (poistetaan koneeseen noustessa)',          1, NOW(), NOW());

-- B) Työlajitarkennukset (scope = task)
-- Poraus: FFP3, EN 397, tiiviit suojalasit
INSERT IGNORE INTO ppe_requirement_rules
  (id, scope_type, environment_id, site_id, zone_id, task_id, ppe_item_id, requirement_level, status, version_no, notes, created_by, created_at, updated_at)
VALUES
(21, 'task', 1, NULL, NULL, 3,  5, 'mandatory', 'published', 1, 'Poraus vaatii EN 397 kypärän',                       1, NOW(), NOW()),
(22, 'task', 1, NULL, NULL, 3,  6, 'mandatory', 'published', 1, 'Poraus: FFP3 kivipölyltä suojaamiseen',              1, NOW(), NOW()),
(23, 'task', 1, NULL, NULL, 3,  8, 'mandatory', 'published', 1, 'Poraus: tiiviit suojalasit pölyltä',                 1, NOW(), NOW()),
-- Panostus: kemikaalisuojakäsineet, visiiri, FFP3
(24, 'task', 1, NULL, NULL, 4,  7, 'mandatory', 'published', 1, 'Panostus: kemikaalisuojakäsineet räjähteille',       1, NOW(), NOW()),
(25, 'task', 1, NULL, NULL, 4,  9, 'mandatory', 'published', 1, 'Panostus: visiiri panostajan kasvojensuojaukseen',   1, NOW(), NOW()),
(26, 'task', 1, NULL, NULL, 4,  6, 'mandatory', 'published', 1, 'Panostus: FFP3 hengityssuojain',                    1, NOW(), NOW()),
-- Louheenlastaus: tiiviit suojalasit, kuulonsuojain
(27, 'task', 1, NULL, NULL, 6,  8, 'mandatory', 'published', 1, 'Lastaus: tiiviit suojalasit kiviroiskeilta',         1, NOW(), NOW()),
-- Rusnaus: putoamissuojain, kypärä EN 397
(28, 'task', 1, NULL, NULL, 8, 13, 'mandatory', 'published', 1, 'Rusnaus: putoamissuojain, yli 2 m korkeus',          1, NOW(), NOW()),
(29, 'task', 1, NULL, NULL, 8,  5, 'mandatory', 'published', 1, 'Rusnaus: EN 397 kypärä kiviiskulta',                 1, NOW(), NOW()),
-- Tankkaus / polttoainekuljetukset
(30, 'task', 1, NULL, NULL, 11,  7, 'mandatory', 'published', 1, 'Tankkaus: kemikaalisuojakäsineet polttoaineelle',   1, NOW(), NOW()),
(31, 'task', 1, NULL, NULL, 11,  9, 'mandatory', 'published', 1, 'Tankkaus: visiiri roiskeilta',                      1, NOW(), NOW()),
(32, 'task', 1, NULL, NULL, 11,  8, 'mandatory', 'published', 1, 'Tankkaus: tiiviit suojalasit',                      1, NOW(), NOW()),
-- Koneen pesu
(33, 'task', 1, NULL, NULL, 12,  7, 'mandatory', 'published', 1, 'Pesu: kemikaalisuojakäsineet pesukemikaalille',     1, NOW(), NOW()),
(34, 'task', 1, NULL, NULL, 12,  9, 'mandatory', 'published', 1, 'Pesu: visiiri roiskeilta',                          1, NOW(), NOW()),
(35, 'task', 1, NULL, NULL, 12,  8, 'mandatory', 'published', 1, 'Pesu: tiiviit suojalasit',                          1, NOW(), NOW()),
-- Teiden ylläpito: näkyvä vaatetus (lisävaatimus)
(36, 'task', 1, NULL, NULL, 10, 12, 'mandatory', 'published', 1, 'Teiden ylläpito: korkean näkyvyyden vaatetus',      1, NOW(), NOW());

-- C) Ehdolliset vaatimukset (scope = task, requirement_level = conditional)
INSERT IGNORE INTO ppe_requirement_rules
  (id, scope_type, environment_id, site_id, zone_id, task_id, ppe_item_id, requirement_level, status, version_no, notes, condition_text, created_by, created_at, updated_at)
VALUES
(37, 'task', 1, NULL, NULL, 3, 14, 'conditional', 'published', 1, 'Poraus: liukuesteet — poistetaan koneeseen noustessa',
     'Liukuesteet poistetaan ennen nousuaskelmalle astumista koneeseen', 1, NOW(), NOW()),
(38, 'task', 1, NULL, NULL, 11, 14, 'conditional', 'published', 1, 'Tankkaus: liukuesteet — poistetaan koneeseen noustessa',
     'Liukuesteet poistetaan ennen nousuaskelmalle astumista', 1, NOW(), NOW());

-- Versiohistoriarivit uusille säännöille
INSERT IGNORE INTO ppe_rule_versions (rule_id, version_no, status, requirement_level, notes, created_by, created_at)
SELECT id, version_no, status, requirement_level, notes, created_by, NOW()
FROM ppe_requirement_rules
WHERE id >= 10;
