# DATABASE

## Nimeäminen
Kaikissa tauluissa käytetään `ppe_`-prefiksiä.

## Keskeiset taulut
- `ppe_users`, `ppe_roles`
- `ppe_sites`, `ppe_tasks`, `ppe_items`, `ppe_workers`
- `ppe_requirement_rules` (scope: global/site/task/local)
- `ppe_rule_versions` (versiohistoria)
- `ppe_audit_log` (audit)
- `ppe_login_attempts` (rate limit)
- `ppe_migrations`

## Periytymisprioriteetti
1. `local` (site + task)
2. `task`
3. `site`
4. `global`

Ristiriitatapaukset raportoidaan tulossivulle.
