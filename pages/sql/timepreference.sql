-- $Id: timepreference.sql 2114 2012-09-21 03:12:19Z cross $
--
DROP TABLE IF EXISTS timepreference CASCADE;
CREATE TABLE timepreference
(
    workerid        BIGINT NOT NULL PRIMARY KEY,
    shift1          INT NOT NULL,
    shift2          INT NOT NULL,
    shift3          INT NOT NULL,
    shift4          INT NOT NULL,
    shift5          INT NOT NULL,
    shift6          INT NOT NULL,
    shift7          INT NOT NULL,
    shift8          INT NOT NULL,
    shift9          INT NOT NULL,
    shift10          INT NOT NULL,
    shift11          INT NOT NULL,
    shift12          INT NOT NULL,
    shift13          INT NOT NULL,
    shift14          INT NOT NULL,
    shift15          INT NOT NULL,
    shift16          INT NOT NULL,
    shift17          INT NOT NULL,
    shift18          INT NOT NULL,
    shift19          INT NOT NULL,
    shift20          INT NOT NULL,
    shift21          INT NOT NULL,
    shift22          INT NOT NULL,
    shift23          INT NOT NULL,
    shift24          INT NOT NULL,
    shift25          INT NOT NULL,
    shift26          INT NOT NULL,
    shift27          INT NOT NULL,
    shift28          INT NOT NULL,
    shift29          INT NOT NULL,    
    shift30          INT NOT NULL,
    shift31          INT NOT NULL,
    shift32          INT NOT NULL,
    shift33          INT NOT NULL,
    shift34          INT NOT NULL,
    shift35          INT NOT NULL,
    shift36          INT NOT NULL,
    shift37          INT NOT NULL,
    shift38          INT NOT NULL,
    shift39          INT NOT NULL,    
    shift40          INT NOT NULL,
    shift41          INT NOT NULL,
    shift42          INT NOT NULL,
    shift43          INT NOT NULL,
    shift44          INT NOT NULL,
    shift45          INT NOT NULL,
    shift46          INT NOT NULL,
    shift47          INT NOT NULL,
    shift48          INT NOT NULL,
    shift49          INT NOT NULL,
    shift50          INT NOT NULL); 
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE timepreference TO phpserver@localhost;
