-- $Id: workerview.sql 1892 2012-09-11 14:55:24Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
DROP VIEW IF EXISTS workerview CASCADE;
CREATE VIEW workerview AS
    SELECT w.*, r.authrole, e.expoid
    FROM worker w
    LEFT JOIN workerrole r USING (workerid)
    LEFT JOIN workerexpo e USING (workerid)
    ORDER BY isDisabled, lastName, firstName, middleName;


DROP VIEW IF EXISTS workerscheduleview CASCADE;
CREATE VIEW workerscheduleview AS
    SELECT w.workerid, r.authrole, (w.maxHours * 60) AS maxMinutes, w.expoid
    FROM workerexpo w
    LEFT JOIN workerrole r USING (workerid);
-- isDisabled - removed from workerexpo by trigger when set
-- ORGANIZER - left in for future company that might schedule organizers

GRANT SELECT ON TABLE workerview TO phpserver@localhost;
GRANT SELECT ON TABLE workerscheduleview TO phpserver@localhost;
