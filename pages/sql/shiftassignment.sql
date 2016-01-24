-- $Id: shiftassignment.sql 2106 2003-01-01 06:10:02Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
DROP TABLE IF EXISTS shiftassignment CASCADE;
CREATE TABLE shiftassignment
(
    workerid    BIGINT NOT NULL, -- link to workerexpo, NOT worker
    jobid       BIGINT NOT NULL, -- link to job
    stationid   BIGINT NOT NULL, -- force UNIQUE
    expoid      BIGINT NOT NULL, -- link to workerexpo, NOT worker
    UNIQUE      (workerid, stationid, expoid), -- 1 worker can only have 1 job at a station
    PRIMARY KEY (workerid, jobid),
    FOREIGN KEY (expoid, workerid) REFERENCES workerexpo(expoid, workerid), --  ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (jobid, stationid, expoid) REFERENCES job(jobid, stationid, expoid) --  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL


DROP VIEW IF EXISTS shiftassignmentview CASCADE;
CREATE VIEW shiftassignmentview AS
    SELECT a.workerid, a.jobid, a.stationid, a.expoid, s.stationTitle, s.location, s.startTime, s.stopTime, e.title as expoTitle,
           j.minCrew, j.maxCrew, j.assignedCrew, j.minSupervisor, j.maxSupervisor, j.assignedSupervisor, j.jobTitle
    FROM  job j -- because job has these keys, and sa doesn't
    INNER JOIN shiftassignment a USING (jobid, stationid, expoid)
    INNER JOIN station s USING (stationid, expoid)
    INNER JOIN expo e USING (expoid)
    ORDER BY startTime DESC, stopTime DESC, jobTitle ASC, stationTitle ASC, expoTitle ASC;


DROP TRIGGER IF EXISTS shiftassignment_insert;
-- DELIMITER @
CREATE TRIGGER shiftassignment_insert AFTER INSERT
    ON shiftassignment FOR EACH ROW BEGIN
        DECLARE crewFlag BOOLEAN DEFAULT FALSE;
        SELECT TRUE INTO crewFlag FROM workerrole WHERE workerid = NEW.workerid AND authrole = 'CREWMEMBER';
        IF (crewFlag) THEN
            UPDATE job SET assignedCrew = assignedCrew + 1 WHERE jobid = NEW.jobid and expoid = NEW.expoid;
        ELSE -- super or organizer
            UPDATE job SET assignedSupervisor = assignedSupervisor + 1 WHERE jobid = NEW.jobid and expoid = NEW.expoid;
        END IF;
    END;
-- @
-- DELIMITER ;


DROP TRIGGER IF EXISTS shiftassignment_delete;
-- DELIMITER @
CREATE TRIGGER shiftassignment_delete AFTER DELETE
    ON shiftassignment FOR EACH ROW BEGIN
        DECLARE crewFlag BOOLEAN DEFAULT FALSE;
        SELECT TRUE INTO crewFlag FROM workerrole WHERE workerid = OLD.workerid AND authrole = 'CREWMEMBER';
        IF (crewFlag) THEN
            UPDATE job SET assignedCrew = assignedCrew - 1 WHERE jobid = OLD.jobid and expoid = OLD.expoid;
        ELSE -- super or organizer
            UPDATE job SET assignedSupervisor = assignedSupervisor - 1 WHERE jobid = OLD.jobid and expoid = OLD.expoid;
        END IF;
    END;
-- @
-- DELIMITER ;


DROP TRIGGER IF EXISTS workerrole_update;
-- DELIMITER @
CREATE TRIGGER workerrole_update AFTER UPDATE
    ON workerrole FOR EACH ROW BEGIN
        IF (NEW.authrole != OLD.authrole) THEN
            -- note that older expos have incorrect workers
            -- note that older stations in current expos are incorrect
            IF ('CREWMEMBER' = NEW.authrole) THEN
                UPDATE job j, station s, shiftassignment a
                SET assignedCrew = assignedCrew + 1, assignedSupervisor = assignedSupervisor - 1
                WHERE j.expoid = s.expoid AND j.stationid = s.stationid AND s.stopTime > CURRENT_TIMESTAMP
                AND j.expoid = a.expoid AND j.stationid = a.stationid AND j.jobid = a.jobid
                AND a.workerid = NEW.workerid;
            ELSEIF ('CREWMEMBER' = OLD.authrole) THEN
                UPDATE job j, station s, shiftassignment a
                SET assignedCrew = assignedCrew - 1, assignedSupervisor = assignedSupervisor + 1
                WHERE j.expoid = s.expoid AND j.stationid = s.stationid AND s.stopTime > CURRENT_TIMESTAMP
                AND j.expoid = a.expoid AND j.stationid = a.stationid AND j.jobid = a.jobid
                AND a.workerid = NEW.workerid;
            END IF;
        END IF;
    END;
-- @
-- DELIMITER ;


-- to be used ONLY by the shiftcount view
-- union avoids sub-selects (i.e. we normally join, which means a non-inner join won't have a value)
DROP VIEW IF EXISTS shiftcount_private CASCADE;
CREATE VIEW shiftcount_private AS
    SELECT DISTINCT 0 AS num, expoid, jobid, 'CREWMEMBER' AS authrole FROM job
    UNION
    SELECT DISTINCT 0 AS num, expoid, jobid, 'SUPERVISOR' AS authrole FROM job
    UNION
    SELECT count(*) AS num, a.expoid, a.jobid, r.authrole
    FROM shiftassignment a
    INNER JOIN workerrole r USING (workerid)
    WHERE r.authrole != 'ORGANIZER'
    GROUP BY 2, 3, 4
    UNION
    SELECT count(*) AS num, a.expoid, a.jobid, 'SUPERVISOR' AS authrole
    FROM shiftassignment a
    INNER JOIN workerrole r USING (workerid)
    WHERE r.authrole = 'ORGANIZER'
    GROUP BY 2, 3, 4;


-- to be used by ShiftAssignment for updating station assignedCrew, assignedSupervisor
DROP VIEW IF EXISTS shiftcount CASCADE;
CREATE VIEW shiftcount AS
    SELECT sum(num) AS num, expoid, jobid, authrole
    FROM shiftcount_private
    GROUP BY 2, 3, 4;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE shiftassignment TO phpserver@localhost;
GRANT SELECT ON TABLE shiftassignmentview TO phpserver@localhost;
GRANT SELECT ON TABLE shiftcount_private TO phpserver@localhost;
GRANT SELECT ON TABLE shiftcount TO phpserver@localhost;
