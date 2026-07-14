# Database Documentation

## Tietokannan rakenne (v2 — laajennettu)

### Migraatiot
Kaikki migraatiot ja seedit ajetaan automaattisesti bootstrapissa.

| Tiedosto | Kuvaus |
|----------|--------|
| `001_init.sql` | Perustaulut: roolit, käyttäjät, työmaat, tehtävät, suojaimet, säännöt, versiohistoria, audit |
| `002_expand_model.sql` | Laajennus: ympäristöt, alueet, ehdot, liitteet, uudet ENUM-arvot, roolilisäykset |
| `001_seed.sql` | Testidata: admin-käyttäjä, esimerkkityömaat, suojaimet, säännöt |
| `002_seed_avolouhos.sql` | Avolouhos-toimintaympäristö, 10 työlajia, PPE-kirjasto, vaatimussäännöt |

### Taulut

#### ppe_environments — Toimintaympäristöt
| Sarake | Tyyppi | Kuvaus |
|--------|--------|--------|
| id | INT PK | |
| code | VARCHAR(40) UNIQUE | Lyhytkoodi (ENV-AVOL) |
| name | VARCHAR(190) | Nimi |
| description | TEXT | Kuvaus |
| is_active | TINYINT(1) | |
| created_at | DATETIME | |

#### ppe_sites — Työmaat / toimipaikat
| Sarake | Tyyppi | Kuvaus |
|--------|--------|--------|
| id | INT PK | |
| environment_id | INT FK → ppe_environments | |
| code | VARCHAR(40) UNIQUE | |
| name | VARCHAR(190) | |
| description | TEXT | |
| is_active | TINYINT(1) | |
| created_at | DATETIME | |

#### ppe_zones — Alueet / laitokset / osastot
| Sarake | Tyyppi | Kuvaus |
|--------|--------|--------|
| id | INT PK | |
| site_id | INT FK → ppe_sites | |
| parent_zone_id | INT FK → ppe_zones | Hierarkkisuus |
| code | VARCHAR(40) | |
| name | VARCHAR(190) | |
| description | TEXT | |
| is_active | TINYINT(1) | |
| created_at | DATETIME | |

#### ppe_tasks — Työlajit / tehtävät / vakanssit
| Sarake | Tyyppi | Kuvaus |
|--------|--------|--------|
| id | INT PK | |
| name | VARCHAR(190) UNIQUE | |
| work_type | ENUM(task/work_type/position) | |
| category | VARCHAR(120) | |
| description | TEXT | |
| is_active | TINYINT(1) | |
| created_at | DATETIME | |

#### ppe_items — Suojainkirjasto
| Sarake | Tyyppi | Kuvaus |
|--------|--------|--------|
| id | INT PK | |
| code | VARCHAR(40) UNIQUE | |
| name | VARCHAR(190) | |
| category | VARCHAR(120) | |
| item_class | ENUM(personal_protection/other_safety) | |
| standard_ref | VARCHAR(120) | Standardi (EN 397 jne.) |
| icon | VARCHAR(120) | SVG-tiedostonimi |
| is_active | TINYINT(1) | |
| created_at | DATETIME | |

#### ppe_requirement_rules — Vaatimussäännöt
| Sarake | Tyyppi | Kuvaus |
|--------|--------|--------|
| id | INT PK | |
| scope_type | ENUM(...) | global/environment/site/zone/task/site_task/zone_task/phase/exception/local |
| environment_id | INT FK | |
| site_id | INT FK | |
| zone_id | INT FK | |
| task_id | INT FK | |
| ppe_item_id | INT FK | |
| requirement_level | ENUM(...) | mandatory/conditional/recommended/information/not_applicable/prohibited (+ legacy: required/forbidden) |
| status | ENUM | draft/review/approved/published/archived |
| version_no | INT | |
| notes | TEXT | |
| condition_text | TEXT | Ehdollisen vaatimuksen ehto |
| reviewed_by | INT FK | |
| reviewed_at | DATETIME | |
| approved_by | INT FK | |
| approved_at | DATETIME | |
| published_by | INT FK | |
| published_at | DATETIME | |
| effective_date | DATE | Voimaantulopäivä |
| replaced_rule_id | INT | Korvattu sääntö |
| change_description | TEXT | Muutoskuvaus |
| created_by | INT FK | |
| created_at | DATETIME | |
| updated_at | DATETIME | |

#### ppe_conditions — Ehdot / työvaiheet
Rakenteinen ehtomalli (liitetään sääntöihin ppe_rule_conditions-taulussa).

#### ppe_attachments — Liitteet / dokumentit / kartat
Tiedostoliitteet ympäristölle, työmaille, alueille, työlajille tai säännölle.

#### ppe_rule_versions — Versiohistoria
Kaikki tilamuutokset tallennetaan.

#### ppe_audit_log — Audit-loki
Kaikki käyttäjätoimenpiteet kirjataan.

## RequirementResolver — prioriteettijärjestys

1. `global` — organisaation yleinen (perustaso)
2. `environment` — toimintaympäristön vaatimus
3. `site` — työmaakohtainen
4. `zone` — aluekohtainen
5. `task` — työlajin yleinen
6. `site_task` / `local` — työmaa + työlaji
7. `zone_task` — alue + työlaji
8. `phase` — työvaihe
9. `exception` — hyväksytty poikkeus (korkein prioriteetti)

**Tiukempi vaatimus voittaa lievemmän.** Strictness: mandatory > conditional > recommended > information > not_applicable > prohibited.
