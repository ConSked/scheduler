-- $Id: workerrole.sql 1350 2012-08-21 18:30:12Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
-- http://dev.mysql.com/doc/refman/5.0/en/set.html
--
DROP TABLE IF EXISTS workerrole CASCADE;
CREATE TABLE workerrole
(
    workerid        BIGINT NOT NULL PRIMARY KEY,
    authrole        ENUM('ORGANIZER', 'SUPERVISOR', 'CREWMEMBER') NOT NULL DEFAULT 'CREWMEMBER',
    FOREIGN KEY (workerid) REFERENCES worker(workerid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE workerrole TO phpserver@localhost;
