-- 002_expand_model.sql
-- Laajentaa tietomallin tukemaan uuden hierarkian:
-- organisaatio → toimintaympäristö → työmaa → alue → työlaji + ehdot + liitteet

-- 1. Toimintaympäristöt (ppe_environments)
CREATE TABLE IF NOT EXISTS ppe_environments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(190) NOT NULL,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
);

-- 2. Alueet / laitokset / osastot (ppe_zones)
CREATE TABLE IF NOT EXISTS ppe_zones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  site_id INT NULL,
  parent_zone_id INT NULL,
  code VARCHAR(40) NOT NULL,
  name VARCHAR(190) NOT NULL,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (site_id) REFERENCES ppe_sites(id),
  FOREIGN KEY (parent_zone_id) REFERENCES ppe_zones(id)
);

-- 3. Ehdot / työvaiheet (ppe_conditions)
CREATE TABLE IF NOT EXISTS ppe_conditions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(190) NOT NULL,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
);

-- 4. Liitteet / dokumentit / kartat (ppe_attachments)
CREATE TABLE IF NOT EXISTS ppe_attachments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  environment_id INT NULL,
  site_id INT NULL,
  zone_id INT NULL,
  task_id INT NULL,
  rule_id INT NULL,
  title VARCHAR(255) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(512) NOT NULL,
  mime_type VARCHAR(120) NOT NULL DEFAULT 'application/octet-stream',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (environment_id) REFERENCES ppe_environments(id),
  FOREIGN KEY (site_id) REFERENCES ppe_sites(id),
  FOREIGN KEY (created_by) REFERENCES ppe_users(id)
);

-- 5. Sääntö-ehto-liitos (ppe_rule_conditions)
CREATE TABLE IF NOT EXISTS ppe_rule_conditions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rule_id INT NOT NULL,
  condition_id INT NOT NULL,
  UNIQUE KEY uq_rule_condition (rule_id, condition_id),
  FOREIGN KEY (rule_id) REFERENCES ppe_requirement_rules(id),
  FOREIGN KEY (condition_id) REFERENCES ppe_conditions(id)
);

-- 6. Lisää environment_id työmaille
ALTER TABLE ppe_sites
  ADD COLUMN environment_id INT NULL AFTER id,
  ADD COLUMN description TEXT NULL AFTER name;
ALTER TABLE ppe_sites
  ADD FOREIGN KEY fk_sites_env (environment_id) REFERENCES ppe_environments(id);

-- 7. Lisää item_class ja standard_ref suojaimille
ALTER TABLE ppe_items
  ADD COLUMN item_class ENUM('personal_protection','other_safety') NOT NULL DEFAULT 'personal_protection' AFTER category,
  ADD COLUMN standard_ref VARCHAR(120) NULL AFTER item_class;

-- 8. Lisää work_type ja description työlajille
ALTER TABLE ppe_tasks
  ADD COLUMN work_type ENUM('task','work_type','position') NOT NULL DEFAULT 'task' AFTER name,
  ADD COLUMN description TEXT NULL AFTER category;

-- 9. Laajenna scope_type ja requirement_level ENUMit vaatimussäännöissä
ALTER TABLE ppe_requirement_rules
  MODIFY COLUMN scope_type ENUM('global','environment','site','zone','task','site_task','zone_task','phase','exception','local') NOT NULL,
  MODIFY COLUMN requirement_level ENUM('required','mandatory','recommended','conditional','forbidden','prohibited','information','not_applicable') NOT NULL;
ALTER TABLE ppe_requirement_rules
  ADD COLUMN environment_id INT NULL AFTER scope_type,
  ADD COLUMN zone_id INT NULL AFTER site_id,
  ADD COLUMN condition_text TEXT NULL AFTER notes,
  ADD COLUMN reviewed_by INT NULL,
  ADD COLUMN reviewed_at DATETIME NULL,
  ADD COLUMN approved_by INT NULL,
  ADD COLUMN approved_at DATETIME NULL,
  ADD COLUMN published_by INT NULL,
  ADD COLUMN published_at DATETIME NULL,
  ADD COLUMN effective_date DATE NULL,
  ADD COLUMN replaced_rule_id INT NULL,
  ADD COLUMN change_description TEXT NULL;
ALTER TABLE ppe_requirement_rules
  ADD FOREIGN KEY fk_rr_env (environment_id) REFERENCES ppe_environments(id),
  ADD FOREIGN KEY fk_rr_reviewed_by (reviewed_by) REFERENCES ppe_users(id),
  ADD FOREIGN KEY fk_rr_approved_by (approved_by) REFERENCES ppe_users(id),
  ADD FOREIGN KEY fk_rr_published_by (published_by) REFERENCES ppe_users(id);

-- 10. Laajenna requirement_level versiohistoriassa
ALTER TABLE ppe_rule_versions
  MODIFY COLUMN requirement_level ENUM('required','mandatory','recommended','conditional','forbidden','prohibited','information','not_applicable') NOT NULL,
  ADD COLUMN reviewed_by INT NULL,
  ADD COLUMN approved_by INT NULL;

-- 11. Lisää uudet roolit
INSERT IGNORE INTO ppe_roles (slug, name) VALUES
  ('site_manager', 'Työmaan ylläpitäjä'),
  ('hseq_reviewer', 'HSEQ-tarkastaja'),
  ('hseq_approver', 'HSEQ-hyväksyjä');
