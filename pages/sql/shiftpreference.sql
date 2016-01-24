-- $Id: shiftpreference.sql 2127 2012-09-21 19:23:40Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
-- desirePercent is above 0
-- note that this database-centric utility MAY be converted by some function
-- i.e. the database-storage and the ui-presentation do not have to be equal, only equivalent
--
--
DROP TABLE IF EXISTS shiftpreference CASCADE;
CREATE TABLE shiftpreference
(
    workerid        BIGINT NOT NULL,
    jobid           BIGINT NOT NULL,
    stationid       BIGINT NOT NULL,
    expoid          BIGINT NOT NULL,
    desirePercent	FLOAT NULL DEFAULT NULL, -- if NULL; then is impossible for worker
    PRIMARY KEY (workerid, jobid),
    FOREIGN KEY (expoid, workerid) REFERENCES workerexpo(expoid, workerid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (jobid, stationid, expoid) REFERENCES job(jobid, stationid, expoid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK desirePercent [0, 100]


-- DELIMITER @
-- This trigger changes default desirePercent for organizers; must be BEFORE
-- the naming convention reflects that there may exist 1 and only 1 INSERT trigger on shiftpreference table
CREATE TRIGGER shiftpreference_insert BEFORE INSERT
    ON shiftpreference FOR EACH ROW BEGIN
        DECLARE organizerFlag BOOLEAN DEFAULT FALSE;
        SELECT TRUE INTO organizerFlag FROM workerrole WHERE workerid = NEW.workerid AND authrole = 'ORGANIZER';
        IF (organizerFlag) THEN
            SET NEW.desirePercent = NULL;
        END IF;
    END;
-- @
-- DELIMITER ;


-- DELIMITER @
-- This trigger ensures that shiftpreferences always exist
-- the naming convention reflects that there may exist 1 and only 1 INSERT trigger on station table
CREATE TRIGGER job_insert AFTER INSERT
    ON job FOR EACH ROW BEGIN
        INSERT INTO shiftpreference (workerid, jobid, stationid, expoid)
            SELECT workerid, NEW.jobid, NEW.stationid, NEW.expoid
            FROM workerexpo WHERE expoid = NEW.expoid;
    END;
-- @
-- DELIMITER ;

GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE shiftpreference TO phpserver@localhost;
