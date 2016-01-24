-- $Id: job.sql 2105 2012-09-20 00:07:43Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
DROP TABLE IF EXISTS jobtitle CASCADE;
CREATE TABLE jobtitle
(
    expoid              BIGINT NOT NULL,
    jobTitle            VARCHAR(255) NOT NULL,  -- ex: "Florist"
    UNIQUE              (expoid, jobTitle), -- unique titles to expo
    FOREIGN KEY (expoid) REFERENCES expo(expoid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL
--
--
DROP TABLE IF EXISTS job CASCADE;
CREATE TABLE job
(
    jobid               BIGINT AUTO_INCREMENT PRIMARY KEY,
    stationid           BIGINT NOT NULL, -- link to station
    expoid              BIGINT NOT NULL, -- needed for link to shiftassignment/shiftpreference
    jobTitle            VARCHAR(255) NOT NULL,  -- ex: "Florist"
    maxCrew             INT NOT NULL DEFAULT 1,
    minCrew             INT NOT NULL DEFAULT 1,
    assignedCrew        INT NOT NULL DEFAULT 0,
    maxSupervisor       INT NOT NULL DEFAULT 1,
    minSupervisor       INT NOT NULL DEFAULT 1,
    assignedSupervisor  INT NOT NULL DEFAULT 0,
    UNIQUE              (jobid, stationid, expoid), -- create a key for shiftassignment/shiftpreference
    UNIQUE              (stationid, jobTitle), -- no more than 1 florist per station
    FOREIGN KEY (stationid, expoid) REFERENCES station(stationid, expoid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (expoid, jobTitle) REFERENCES jobtitle(expoid, jobTitle) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL


DROP VIEW IF EXISTS jobview CASCADE;
CREATE VIEW jobview AS
    SELECT DISTINCT job.*, station.stationTitle, station.location, station.startTime, station.stopTime
    FROM job INNER JOIN station USING (stationid, expoid)
    ORDER BY startTime DESC, stopTime DESC, jobTitle ASC, stationTitle ASC;
-- All grants go at the end of the file.
-- Helps with hosted site compatability.
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE jobtitle TO phpserver@localhost;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE job TO phpserver@localhost;
GRANT SELECT ON TABLE jobview TO phpserver@localhost;
