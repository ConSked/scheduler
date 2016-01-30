-- $Id: station.sql 1853 2012-09-08 01:24:14Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.
--
--
DROP TABLE IF EXISTS station CASCADE;
CREATE TABLE station
(
    stationid           BIGINT AUTO_INCREMENT PRIMARY KEY,
    expoid              BIGINT NOT NULL,
    startTime           DATETIME NOT NULL,
    stopTime            DATETIME NOT NULL,
    stationTitle        VARCHAR(255) NOT NULL,  -- ex: "Flower Arranging"
    description         VARCHAR(2048), -- ex: "Discussion of Flower Arranging in both the English and Japanese traditions."
    location            VARCHAR(2048), -- ex: "East Lobby"
    URL                 VARCHAR(2048), -- ex: "http://....."
    instruction         VARCHAR(2048), -- ex: "serve crumpets"
    UNIQUE              (stationid, expoid),
    -- UNIQUE              (expoid, startTime, stationTitle), -- unique title at time; TODO - implement post CIW
    FOREIGN KEY (expoid) REFERENCES expo(expoid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE station TO phpserver@localhost;

