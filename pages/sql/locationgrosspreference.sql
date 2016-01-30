-- $Id: locationgrosspreference.sql 2015 2012-09-17 22:27:37Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.
--
--
-- shiftpreference is the table that records the preference on a per-station basis
-- if detailFlag is set, then the locationgrosspreference settings are irrelevant
-- locationgrosspreference is the table that records the defaulting settings for any shiftpreference row
--
-- mysql> select CURRENT_TIMESTAMP, cast(CURRENT_TIMESTAMP AS TIME), cast(CURRENT_TIMESTAMP AS DATE);
-- +---------------------+---------------------------------+---------------------------------+
-- | CURRENT_TIMESTAMP   | cast(CURRENT_TIMESTAMP AS TIME) | cast(CURRENT_TIMESTAMP AS DATE) |
-- +---------------------+---------------------------------+---------------------------------+
-- | 2012-07-15 16:57:00 | 16:57:00                        | 2012-07-15                      |
-- +---------------------+---------------------------------+---------------------------------+--
--
--
DROP TABLE IF EXISTS locationgrosspreference CASCADE;
CREATE TABLE locationgrosspreference
(
    workerid        BIGINT NOT NULL,
    expoid          BIGINT NOT NULL,
    desirePercent	FLOAT NULL DEFAULT 0.0, -- if NULL; then is impossible for worker
    maxHours        INT NULL,
    startDate       DATE NULL,
    startTime       TIME NULL,
    stopTime        TIME NULL,
    location             VARCHAR(255) NULL,
    -- PRIMARY KEY (workerid, expoid, maxHours, startDate, startTime, stopTime, description),
    -- primary key requires not null columns
    FOREIGN KEY (expoid, workerid) REFERENCES workerexpo(expoid, workerid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE locationgrosspreference TO phpserver@localhost;


-- DELIMITER @
-- implement check constraints
-- the naming convention reflects that there may exist 1 and only 1 INSERT trigger on shiftpreference table
CREATE TRIGGER locationgrosspreference_update BEFORE UPDATE
    ON locationgrosspreference FOR EACH ROW BEGIN
        IF (NEW.description IS NOT NULL) THEN
            SET NEW.maxHours = NULL;
            SET NEW.startDate = NULL;
            SET NEW.startTime = NULL;
            SET NEW.stopTime = NULL;
        END IF;
        IF (NEW.startDate IS NOT NULL) THEN
            SET NEW.maxHours = NULL;
            SET NEW.description = NULL;
            SET NEW.startTime = NULL;
            SET NEW.stopTime = NULL;
        END IF;
        IF (NEW.maxHours IS NOT NULL) THEN
            SET NEW.location = NULL;
            SET NEW.startDate = NULL;
            SET NEW.startTime = NULL;
            SET NEW.stopTime = NULL;
            SET NEW.desirePercent = 50.0; -- irrelevant, but ensure set to avoid issues later
        END IF;
        IF ((NEW.startTime IS NOT NULL) AND (NEW.stopTime IS NOT NULL)) THEN
            SET NEW.maxHours = NULL;
            SET NEW.startDate = NULL;
            SET NEW.location = NULL;
        END IF;
        -- mysql is SOOO broken
        -- IF ((NEW.maxHours IS NULL) AND ((NEW.startDate IS NULL) OR (NEW.startTime IS NULL)) AND (NEW.stopTime IS NULL) AND (NEW.description IS NULL)) THEN
        --     ROLLBACK; -- mysql is non-standard - cannot:RAISE EXCEPTION 'check constraint violation - all are null';
        -- END IF;
    END;
-- @
-- DELIMITER ;


-- these triggers are complex enough that we'll do them in PHP code with a transaction block
-- not quite as robust, but given the likely churn in the locationgrosspreferences, better than
-- slaving to make thse work - pcu
-- ps - @see svn 971
--
