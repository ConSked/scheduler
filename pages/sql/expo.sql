-- $Id: expo.sql 2403 2012-10-22 18:38:30Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
-- http://dev.mysql.com/doc/refman/5.0/en/enum.html
--
--
DROP TABLE IF EXISTS expo CASCADE;
CREATE TABLE expo
(
	expoid						BIGINT AUTO_INCREMENT PRIMARY KEY,
	startTime					TIMESTAMP NOT NULL, -- START means this day
	stopTime					TIMESTAMP NOT NULL,
	expoHourCeiling				INT NOT NULL DEFAULT 40,
	title						VARCHAR(255) NOT NULL UNIQUE,
	description					VARCHAR(2048) NOT NULL,
	scheduleAssignAsYouGo		BOOLEAN DEFAULT TRUE,
	scheduleVisible				BOOLEAN DEFAULT TRUE,
	allowScheduleTimeConflict	BOOLEAN DEFAULT FALSE,
	newUserAddedOnRegistration	BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE expo TO phpserver@localhost;
