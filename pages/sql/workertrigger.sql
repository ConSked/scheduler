-- $Id: workertrigger.sql 2103 2012-09-19 15:42:25Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
-- worker trigger exists as a separate file because so many tables trigger off a isDisabled update, etc.
--

DROP TRIGGER IF EXISTS workerexpo_delete;
-- DELIMITER @
CREATE TRIGGER workerexpo_delete BEFORE DELETE
    ON workerexpo FOR EACH ROW BEGIN
        DELETE FROM shiftassignment
            WHERE expoid = OLD.expoid AND workerid = OLD.workerid;
        DELETE FROM shiftpreference
            WHERE expoid = OLD.expoid AND workerid = OLD.workerid;
        DELETE FROM shiftstatus
            WHERE expoid = OLD.expoid AND workerid = OLD.workerid;
        DELETE FROM jobpreference
            WHERE workerid = OLD.workerid;
        DELETE FROM timepreference
            WHERE workerid = OLD.workerid;
        DELETE FROM invitation
            WHERE expirationDate < CURRENT_DATE
            OR (expoid = OLD.expoid AND workerid = OLD.workerid);
    END;
-- @
-- DELIMITER ;

DROP TRIGGER IF EXISTS worker_insert;
-- DELIMITER @
CREATE TRIGGER worker_insert AFTER INSERT
    ON worker FOR EACH ROW BEGIN
        INSERT INTO workerrole (workerid) VALUES (NEW.workerid);
        DELETE FROM invitation
            WHERE expirationDate < CURRENT_DATE
            OR (expoid IS NULL
            AND email = NEW.email);
        UPDATE invitation
            SET workerid = NEW.workerid, email = NULL
            WHERE email = NEW.email;
    END;
--    ON worker FOR EACH ROW BEGIN
-- use authrole DEFAULT on workerrole
-- INSERT INTO workerrole (workerid) VALUES (NEW.workerid);
-- invitation has been accepted
-- @
-- DELIMITER ;


DROP TRIGGER IF EXISTS worker_update;
-- DELIMITER @
CREATE TRIGGER worker_update AFTER UPDATE
    ON worker FOR EACH ROW BEGIN
        IF (NEW.isDisabled IS TRUE) THEN
            DELETE FROM workerexpo
                WHERE workerid = NEW.workerid
                AND expoid IN (SELECT expoid FROM expo WHERE stopTime > CURRENT_TIMESTAMP);
            DELETE FROM invitation
                WHERE expirationDate < CURRENT_DATE
                OR workerid = NEW.workerid;
        END IF;
    END;
-- @
-- DELIMITER ;


DROP TRIGGER IF EXISTS workerexpo_insert;
-- DELIMITER @
-- This trigger ensures that shiftpreferences always exist
-- the naming convention reflects that there may exist 1 and only 1 INSERT trigger on workerexpo table
-- it also removes 'old' invitations
-- DELIMITER @
CREATE TRIGGER workerexpo_insert AFTER INSERT
    ON workerexpo FOR EACH ROW BEGIN
        INSERT INTO shiftpreference (workerid, jobid, stationid, expoid)
            SELECT NEW.workerid, jobid, stationid, NEW.expoid
            FROM job WHERE expoid = NEW.expoid;
        DELETE FROM invitation
            WHERE expirationDate < CURRENT_DATE
            OR (expoid = NEW.expoid
            AND workerid = NEW.workerid);
    END;
-- FROM station WHERE expoid = NEW.expoid;
-- always ensure is a maxHours
-- FROM expo WHERE expoid = NEW.expoid;
-- delete 'accepted' invitations
-- @
-- DELIMITER ;
