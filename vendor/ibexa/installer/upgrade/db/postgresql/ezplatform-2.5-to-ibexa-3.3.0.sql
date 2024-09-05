-- Product name migration
START TRANSACTION;
DELETE FROM "ezsite_data" WHERE "name" IN ('ezpublish-version', 'ezplatform-release');
INSERT INTO "ezsite_data" ("name", "value") VALUES ('ezplatform-release', '3.0.0');
COMMIT;

--
ALTER TABLE "ezcontentclass_attribute" ALTER COLUMN "data_text1" TYPE varchar(255);
--

--
ALTER TABLE "ezcontentclass_attribute" ADD "is_thumbnail" boolean DEFAULT false NOT NULL;
--

-- EZP-31471: Keywords versioning
ALTER TABLE "ezkeyword_attribute_link" ADD COLUMN "version" INT;

UPDATE "ezkeyword_attribute_link"
SET "version" = COALESCE(
        (
            SELECT "current_version"
            FROM "ezcontentobject_attribute" AS cattr
                     JOIN "ezcontentobject" AS contentobj
                          ON cattr.contentobject_id = contentobj.id
                              AND cattr.version = contentobj.current_version
            WHERE cattr.id = ezkeyword_attribute_link.objectattribute_id
        ), 0)
;

ALTER TABLE "ezkeyword_attribute_link" ALTER COLUMN "version" SET NOT NULL;

CREATE INDEX "ezkeyword_attr_link_oaid_ver" ON "ezkeyword_attribute_link" ("objectattribute_id", "version");
--

-- EZP-31079: Provided default value for ezuser login pattern --
UPDATE "ezcontentclass_attribute" SET "data_text2" = '^[^@]+$'
WHERE "data_type_string" = 'ezuser'
  AND "data_text2" IS NULL;
--

-- EZEE-2880: Added support for stage and transition actions --
ALTER TABLE "ezeditorialworkflow_markings"
    ADD COLUMN "message" TEXT NOT NULL DEFAULT '',
    ADD COLUMN "reviewer_id" INTEGER,
    ADD COLUMN "result" TEXT;
--

ALTER TABLE "ezeditorialworkflow_markings" ALTER COLUMN "message" DROP DEFAULT;

-- EZEE-2988: Added availability for schedule hide --
START TRANSACTION;
ALTER TABLE "ezdatebasedpublisher_scheduled_version" RENAME COLUMN "publication_date" TO action_timestamp;
ALTER TABLE "ezdatebasedpublisher_scheduled_version" ADD COLUMN "action" VARCHAR(32);
UPDATE "ezdatebasedpublisher_scheduled_version" SET "action" = 'publish';
ALTER TABLE "ezdatebasedpublisher_scheduled_version" ALTER COLUMN "action" SET NOT NULL ;
COMMIT;
--
START TRANSACTION;
ALTER TABLE "ezdatebasedpublisher_scheduled_version"
    RENAME TO "ezdatebasedpublisher_scheduled_entries";
ALTER TABLE "ezdatebasedpublisher_scheduled_entries"
    ALTER COLUMN "version_id" DROP NOT NULL;
ALTER TABLE "ezdatebasedpublisher_scheduled_entries"
    ALTER COLUMN "version_number" DROP NOT NULL;
COMMIT;

--
START TRANSACTION;
DROP TABLE IF EXISTS "ezsite";
CREATE TABLE "ezsite"
(
    "id"      SERIAL NOT NULL,
    "name"    varchar(255) NOT NULL DEFAULT '',
    "created" int          NOT NULL,
    PRIMARY KEY ("id")
);
COMMIT;

--
START TRANSACTION;
DROP TABLE IF EXISTS "ezsite_public_access";
CREATE TABLE "ezsite_public_access" (
                                        "public_access_identifier" varchar(255) NOT NULL,
                                        "site_id" int NOT NULL,
                                        "site_access_group" varchar(255) NOT NULL DEFAULT '',
                                        "status" int NOT NULL,
                                        "config" text NOT NULL,
                                        "site_matcher_host" varchar(255) DEFAULT NULL,
                                        PRIMARY KEY ("public_access_identifier"),
                                        CONSTRAINT "fk_ezsite_public_access_site_id" FOREIGN KEY ("site_id") REFERENCES "ezsite" ("id")
) ;
CREATE INDEX "ezsite_public_access_site_id" ON "ezsite_public_access" ("site_id");
COMMIT;

-- EZEE-3244: Added path to site configuration --
ALTER TABLE "ezsite_public_access" ADD COLUMN "site_matcher_path" VARCHAR(255) DEFAULT NULL;
--

DROP TABLE IF EXISTS "ibexa_segment_group_map";
CREATE TABLE "ibexa_segment_group_map" (
                                           "segment_id" int NOT NULL,
                                           "group_id" int NOT NULL,
                                           PRIMARY KEY ("segment_id", "group_id")
);

DROP TABLE IF EXISTS "ibexa_segment_groups";
CREATE TABLE "ibexa_segment_groups" (
                                        "id" SERIAL NOT NULL,
                                        "identifier" varchar(255) NOT NULL,
                                        "name" varchar(255) NOT NULL DEFAULT '',
                                        PRIMARY KEY ("id", "identifier"),
                                        CONSTRAINT "ibexa_segment_groups_identifier" UNIQUE ("identifier")
);

DROP TABLE IF EXISTS "ibexa_segment_user_map";
CREATE TABLE "ibexa_segment_user_map" (
                                          "segment_id" int NOT NULL,
                                          "user_id" int NOT NULL,
                                          PRIMARY KEY ("segment_id", "user_id")
);

DROP TABLE IF EXISTS "ibexa_segments";
CREATE TABLE "ibexa_segments" (
                                  "id" SERIAL NOT NULL,
                                  "identifier" varchar(255) NOT NULL,
                                  "name" varchar(255) NOT NULL DEFAULT '',
                                  PRIMARY KEY ("id", "identifier"),
                                  CONSTRAINT "ibexa_segments_identifier" UNIQUE ("identifier")
);

DROP TABLE IF EXISTS "ibexa_setting";
CREATE TABLE "ibexa_setting" (
                                 "id" SERIAL NOT NULL,
                                 "group" varchar(128) NOT NULL,
                                 "identifier" varchar(128) NOT NULL,
                                 "value" json NOT NULL,
                                 PRIMARY KEY ("id"),
                                 CONSTRAINT "ibexa_setting_group_identifier" UNIQUE ("group", identifier)
);

INSERT INTO "ibexa_setting" ("group", "identifier", "value")
VALUES ('personalization', 'installation_key', '""');
