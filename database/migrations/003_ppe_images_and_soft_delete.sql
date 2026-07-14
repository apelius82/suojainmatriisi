-- 003_ppe_images_and_soft_delete.sql
-- Lisää varustekuva ja pehmeä poisto / arkistointi entiteeteille

-- 1. Varustekuvat: image_path ppe_items-tauluun
ALTER TABLE ppe_items
  ADD COLUMN IF NOT EXISTS image_path VARCHAR(512) NULL;

-- 2. Luodaan PPE-kuvien tallennushakemisto -merkintä (ei SQL-toimenpidettä, tehdään storage-tasolla)

-- Kaikilla entiteeteillä on jo is_active=1/0 pehmeää poistoa varten,
-- joten uusi sarake ei ole tarpeen. Varmistetaan vain indeksit.
CREATE INDEX IF NOT EXISTS idx_ppe_items_active ON ppe_items (is_active);
CREATE INDEX IF NOT EXISTS idx_ppe_tasks_active ON ppe_tasks (is_active);
CREATE INDEX IF NOT EXISTS idx_ppe_sites_active ON ppe_sites (is_active);
CREATE INDEX IF NOT EXISTS idx_ppe_zones_active ON ppe_zones (is_active);
CREATE INDEX IF NOT EXISTS idx_ppe_environments_active ON ppe_environments (is_active);
