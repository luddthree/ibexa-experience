CREATE TABLE IF NOT EXISTS ibexa_workflow_version_lock
(
    "id" SERIAL,
    "content_id" INTEGER,
    "version" INTEGER,
    "user_id" INTEGER,
    "created" INTEGER DEFAULT 0 NOT NULL,
    "modified" INTEGER DEFAULT 0 NOT NULL,
    "is_locked" boolean DEFAULT TRUE NOT NULL,
    CONSTRAINT "ibexa_workflow_version_lock_pk" PRIMARY KEY ("id")
);

CREATE INDEX IF NOT EXISTS "ibexa_workflow_version_lock_content_id_index"
    ON "ibexa_workflow_version_lock" ("content_id");

CREATE INDEX IF NOT EXISTS "ibexa_workflow_version_lock_user_id_index"
    ON "ibexa_workflow_version_lock" ("user_id");

CREATE UNIQUE INDEX IF NOT EXISTS "ibexa_workflow_version_lock_content_id_version_uindex"
    ON "ibexa_workflow_version_lock" ("content_id", "version");
