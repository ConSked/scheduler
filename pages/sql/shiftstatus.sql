-- $Id: shiftstatus.sql 2379 2012-10-14 20:44:04Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.
--
--
DROP TABLE IF EXISTS shiftstatus CASCADE;
CREATE TABLE shiftstatus
(
	shiftstatusid BIGINT AUTO_INCREMENT PRIMARY KEY,
    workerid      BIGINT NOT NULL,
    stationid     BIGINT NOT NULL,
    expoid        BIGINT NOT NULL,
    statusType    ENUM('CHECK_IN', 'CHECK_OUT') NOT NULL DEFAULT 'CHECK_IN',
    statusTime    TIMESTAMP NOT NULL,
    FOREIGN KEY (expoid, workerid) REFERENCES workerexpo(expoid, workerid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (stationid, expoid) REFERENCES station(stationid, expoid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE shiftstatus TO phpserver@localhost;
