-- 004_task_cover_image.sql
-- Lisää tehtävälle kansikuva ja huomiolaatikko

ALTER TABLE ppe_tasks
  ADD COLUMN IF NOT EXISTS cover_image_path VARCHAR(512) NULL,
  ADD COLUMN IF NOT EXISTS cover_note TEXT NULL;

CREATE INDEX IF NOT EXISTS idx_ppe_tasks_cover ON ppe_tasks (cover_image_path(64));
