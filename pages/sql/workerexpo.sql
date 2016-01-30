-- $Id: workerexpo.sql 1896 2012-09-11 21:24:30Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.
--
--
DROP TABLE IF EXISTS workerexpo CASCADE;
CREATE TABLE workerexpo
(
    expoid      BIGINT NOT NULL,
    workerid    BIGINT NOT NULL,
    maxHours    INT NOT NULL DEFAULT 20, -- todo - default to Expo value for ceiling hours
    PRIMARY KEY (expoid, workerid),
    FOREIGN KEY (expoid) REFERENCES expo(expoid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (workerid) REFERENCES worker(workerid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL

GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE workerexpo TO phpserver@localhost;
