INSERT IGNORE INTO ppe_roles (id, slug, name) VALUES
(1, 'admin', 'Pääkäyttäjä'),
(2, 'manager', 'Työmaavastaava'),
(3, 'reviewer', 'Tarkastaja'),
(4, 'worker', 'Työntekijä');

-- Seed users use plaintext password: Admin123!
INSERT IGNORE INTO ppe_users (id, email, display_name, password_hash, role_id, is_active, created_at) VALUES
(1, 'admin@suojainmatriisi.local', 'Admin User', '$2y$10$KjiZm4F8XHlkB1N8MjKe..nihtTJ8CWcDWzLyzrK9Vq.MJ/GzZeyK', 1, 1, NOW()),
(2, 'reviewer@suojainmatriisi.local', 'Reviewer User', '$2y$10$KjiZm4F8XHlkB1N8MjKe..nihtTJ8CWcDWzLyzrK9Vq.MJ/GzZeyK', 3, 1, NOW());

INSERT IGNORE INTO ppe_sites (id, code, name, is_active, created_at) VALUES
(1, 'TM-001', 'Keskustan työmaa', 1, NOW()),
(2, 'TM-002', 'Sataman työmaa', 1, NOW());

INSERT IGNORE INTO ppe_tasks (id, name, category, is_active, created_at) VALUES
(1, 'Hionta', 'Mekaaninen', 1, NOW()),
(2, 'Hitsaus', 'Tulityö', 1, NOW());

INSERT IGNORE INTO ppe_items (id, code, name, category, icon, is_active, created_at) VALUES
(1, 'PPE-HELM', 'Kypärä', 'Head', 'helmet.svg', 1, NOW()),
(2, 'PPE-GOG', 'Suojalasit', 'Eyes', 'goggles.svg', 1, NOW()),
(3, 'PPE-GLO', 'Suojakäsineet', 'Hands', 'gloves.svg', 1, NOW()),
(4, 'PPE-RES', 'Hengityssuojain', 'Respiratory', 'respirator.svg', 1, NOW());

INSERT IGNORE INTO ppe_workers (id, full_name, site_id, task_id, is_active, created_at) VALUES
(1, 'Matti Mallikas', 1, 1, 1, NOW()),
(2, 'Anna Esimerkki', 1, 2, 1, NOW()),
(3, 'Sven Test', 2, 2, 1, NOW());

INSERT IGNORE INTO ppe_requirement_rules (id, scope_type, site_id, task_id, ppe_item_id, requirement_level, status, version_no, notes, created_by, created_at, updated_at) VALUES
(1, 'global', NULL, NULL, 1, 'required', 'published', 1, 'Kaikille työmaille', 1, NOW(), NOW()),
(2, 'task', NULL, 2, 2, 'required', 'published', 1, 'Hitsauksessa aina suojalasit', 1, NOW(), NOW()),
(3, 'local', 1, 2, 4, 'required', 'published', 1, 'Keskustan hitsaus vaatii respiratorin', 1, NOW(), NOW());

INSERT IGNORE INTO ppe_rule_versions (rule_id, version_no, status, requirement_level, notes, created_by, created_at)
SELECT id, version_no, status, requirement_level, notes, created_by, NOW() FROM ppe_requirement_rules;
