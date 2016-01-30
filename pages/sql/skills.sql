-- $Id: skills.sql 1890 2012-09-11 14:37:08Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.
--
--
-- @see doc/skillexample.html
--
-- skill     - this is the list of skills
--           - 'registered nurse', 'CPR', 'public notary', "driver's license"
-- stationskill - this is the list of skills needed per station; note the crewnumber
-- workerskill - this is the list of skills a worker provides
--
--
DROP TABLE IF EXISTS skill CASCADE;
CREATE TABLE skill
(
    skillid     BIGINT AUTO_INCREMENT NOT NULL,
    description VARCHAR(255) NOT NULL UNIQUE, -- this is the skill! "Registered Nurse", "Able to lift 50lbs"
    PRIMARY KEY (skillid)
) ENGINE=InnoDB;
--


DROP TABLE IF EXISTS stationskill CASCADE;
CREATE TABLE stationskill
(
    skillid     BIGINT NOT NULL,        -- the skill
    stationid   BIGINT NOT NULL,        -- the connect to station
    crewnumber  INT NOT NULL DEFAULT 0, -- number of crew needed with skill
    PRIMARY KEY (skillid, stationid),
    FOREIGN KEY (skillid) REFERENCES skill(skillid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (stationid) REFERENCES station(stationid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
--


-- this is used by the registration page for an expo
-- SELECT s.*, t.expoid FROM skill s, stationskill k, station t
--     WHERE s.skillid = k.skillid
--     AND k.stationid = t.stationid
-- note DISTINCT takes care of multiple stations
DROP VIEW IF EXISTS exposkill CASCADE;
CREATE VIEW exposkill AS
    SELECT DISTINCT s.skillid, s.description, t.expoid
    FROM skill s
    INNER JOIN stationskill USING (skillid)
    INNER JOIN station t USING (stationid);

DROP TABLE IF EXISTS workerskill CASCADE;
CREATE TABLE workerskill
(
    skillid     BIGINT NOT NULL, -- the skill
    workerid    BIGINT NOT NULL, -- the connect to worker; i.e. this worker has this skill
    PRIMARY KEY (skillid, workerid),
    FOREIGN KEY (skillid) REFERENCES skill(skillid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (workerid) REFERENCES worker(workerid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
--

GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE skill TO phpserver@localhost;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE stationskill TO phpserver@localhost;
GRANT SELECT ON TABLE exposkill TO phpserver@localhost;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE workerskill TO phpserver@localhost;
