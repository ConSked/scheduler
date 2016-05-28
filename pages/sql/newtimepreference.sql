-- $Id: timepreference.sql 2114 2012-09-21 03:12:19Z cross $
--
DROP TABLE IF EXISTS newtimepreference CASCADE;
CREATE TABLE newtimepreference
(
    workerid        BIGINT NOT NULL,
    expoid          BIGINT NOT NULL,
    day             DATE NOT NULL,
    hour1           INT NOT NULL,
    hour2           INT NOT NULL,
    hour3           INT NOT NULL,
    hour4           INT NOT NULL,
    hour5           INT NOT NULL,
    hour6           INT NOT NULL,
    hour7           INT NOT NULL,
    hour8           INT NOT NULL,
    hour9           INT NOT NULL,
    hour10          INT NOT NULL,
    hour11          INT NOT NULL,
    hour12          INT NOT NULL,
    hour13          INT NOT NULL,
    hour14          INT NOT NULL,
    hour15          INT NOT NULL,
    hour16          INT NOT NULL,
    hour17          INT NOT NULL,
    hour18          INT NOT NULL,
    hour19          INT NOT NULL,
    hour20          INT NOT NULL,
    hour21          INT NOT NULL,
    hour22          INT NOT NULL,
    hour23          INT NOT NULL,
    hour24          INT NOT NULL,
    PRIMARY KEY (workerid, expoid, day)); 
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE newtimepreference TO phpserver@localhost;
