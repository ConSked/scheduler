-- $Id: expoworkerview.sql 1896 2012-09-11 21:24:30Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
DROP VIEW IF EXISTS expoworkerview CASCADE;
CREATE VIEW expoworkerview AS
    SELECT e.*, w.workerid
    FROM expo e
    LEFT JOIN workerexpo w USING (expoid)
    ORDER BY startTime DESC, stopTime DESC, title ASC;

GRANT SELECT ON TABLE expoworkerview TO phpserver@localhost;
