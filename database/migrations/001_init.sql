CREATE TABLE IF NOT EXISTS ppe_roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL
);

CREATE TABLE IF NOT EXISTS ppe_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  display_name VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (role_id) REFERENCES ppe_roles(id)
);

CREATE TABLE IF NOT EXISTS ppe_sites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(190) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS ppe_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL UNIQUE,
  category VARCHAR(120) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS ppe_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(190) NOT NULL,
  category VARCHAR(120) NOT NULL,
  icon VARCHAR(120) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS ppe_workers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(190) NOT NULL UNIQUE,
  site_id INT NOT NULL,
  task_id INT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (site_id) REFERENCES ppe_sites(id),
  FOREIGN KEY (task_id) REFERENCES ppe_tasks(id)
);

CREATE TABLE IF NOT EXISTS ppe_requirement_rules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  scope_type ENUM('global','site','task','local') NOT NULL,
  site_id INT NULL,
  task_id INT NULL,
  ppe_item_id INT NOT NULL,
  requirement_level ENUM('required','recommended','forbidden') NOT NULL,
  status ENUM('draft','review','approved','published','archived') NOT NULL DEFAULT 'draft',
  version_no INT NOT NULL DEFAULT 1,
  notes TEXT NULL,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  FOREIGN KEY (site_id) REFERENCES ppe_sites(id),
  FOREIGN KEY (task_id) REFERENCES ppe_tasks(id),
  FOREIGN KEY (ppe_item_id) REFERENCES ppe_items(id),
  FOREIGN KEY (created_by) REFERENCES ppe_users(id)
);

CREATE TABLE IF NOT EXISTS ppe_rule_versions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rule_id INT NOT NULL,
  version_no INT NOT NULL,
  status ENUM('draft','review','approved','published','archived') NOT NULL,
  requirement_level ENUM('required','recommended','forbidden') NOT NULL,
  notes TEXT NULL,
  created_by INT NOT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (rule_id) REFERENCES ppe_requirement_rules(id),
  FOREIGN KEY (created_by) REFERENCES ppe_users(id)
);

CREATE TABLE IF NOT EXISTS ppe_audit_log (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_type VARCHAR(120) NOT NULL,
  payload_json JSON NOT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES ppe_users(id)
);

CREATE TABLE IF NOT EXISTS ppe_login_attempts (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL,
  ip_address VARCHAR(64) NOT NULL,
  success TINYINT(1) NOT NULL,
  attempted_at DATETIME NOT NULL,
  INDEX idx_login_attempts_time (attempted_at)
);
