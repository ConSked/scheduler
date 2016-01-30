-- $Id: invitation.sql 1891 2012-09-11 14:49:41Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.
--
--
-- expoid - will set expoid upon invitation; if NULL, no expo defaulted
-- workerid - if NULL, see email
--          - if NOT NULL, use worker.email WHERE workerid matche
-- email    - NULL if workerid NOT NULL -- i.e. ensure email is from worker table to avoid data-slippage
--          - if NOT NULL, new worker
-- code     - if NULL, anyone can register
--          - if NOT NULL, code must be present
--
-- reap me: | x    | x    | x     | yesterday | x    |
-- NEVER!   | NULL | w    | email | tomorrow  | NULL |
-- | NULL | NULL   | email | tomorrow | NULL | - send email; do not assign expo, no code required
-- | expo | NULL   | email | tomorrow | NULL | - send email; assign expo, no code required
-- | expo | worker | NULL  | tomorrow | NULL | - send email; assign expo, no code required
-- | expo | worker | NULL  | tomorrow | code | - send email; assign expo, (maybe unique) code required
-- | expo | NULL   | NULL  | tomorrow | ""   | - if email and workerid NULL, then accept anyone; code must be ""
--
--
DROP TABLE IF EXISTS invitation CASCADE;
CREATE TABLE invitation
(
    expoid          BIGINT NULL,
    workerid        BIGINT NULL,
    email           VARCHAR(255) NULL,
    expirationDate  DATE NOT NULL,
    code            VARBINARY(255) NULL,
    phone           CHAR(10) NULL,
    firstName       VARCHAR(255) NULL,
    middleName      VARCHAR(255) NULL,
    lastName        VARCHAR(255) NULL,
    UNIQUE  KEY (expoid, workerid, email, expirationDate),
    FOREIGN KEY (expoid) REFERENCES expo(expoid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (workerid) REFERENCES worker(workerid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


-- invitation_private SHOULD NOT be used by anything other than invitationview
DROP VIEW IF EXISTS invitation_private CASCADE;
CREATE VIEW invitation_private AS
    SELECT * FROM invitation
    WHERE workerid IS NULL
    UNION
    SELECT r.expoid, r.workerid, w.email, r.expirationDate, r.code, w.phone, w.firstName, w.middleName, w.lastName FROM invitation r
    INNER JOIN worker w USING (workerid)
    WHERE w.isDisabled = FALSE;


DROP VIEW IF EXISTS invitationview CASCADE;
CREATE VIEW invitationview AS
    SELECT DISTINCT * FROM invitation_private;


-- DELIMITER @
-- this trigger changes default desirePercent for organizers; must be BEFORE
-- the naming convention reflects that there may exist 1 and only 1 INSERT trigger on shiftpreference table
CREATE TRIGGER invitation_insert BEFORE INSERT
    ON invitation FOR EACH ROW BEGIN
        DECLARE wid BIGINT;
        DECLARE exd DATE;
        IF (NEW.email IS NOT NULL) THEN
            SET NEW.email = LOWER(NEW.email);
        END IF;
        SELECT workerid INTO wid FROM worker WHERE email = NEW.email;
        IF (wid IS NOT NULL) THEN
            SET NEW.email = NULL;
            SET NEW.phone = NULL;
            SET NEW.firstName = NULL;
            SET NEW.middleName = NULL;
            SET NEW.lastName = NULL;
            SET NEW.workerid = wid;
        END IF;
        IF (NEW.expoid IS NOT NULL) THEN
            SELECT DATE(stopTime) INTO exd FROM expo WHERE expoid = NEW.expoid;
            IF (NEW.expirationDate IS NULL OR NEW.expirationDate > exd) THEN
                SET NEW.expirationDate = exd;
            END IF;
        END IF;
        IF (NEW.workerid IS NULL AND NEW.email IS NULL) THEN
            SET NEW.code = NULL;
        END IF;
        IF (NEW.expoid IS NULL AND NEW.workerid IS NOT NULL) THEN
            SET NEW.expirationDate = NULL; -- force an exception; an existing worker can only be invited to an expo
        END IF;
    END;
-- @
-- DELIMITER ;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE invitation TO phpserver@localhost;
GRANT SELECT ON TABLE invitation_private TO phpserver@localhost;
GRANT SELECT ON TABLE invitationview TO phpserver@localhost;
