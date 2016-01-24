-- $Id: worker.sql 2109 2012-09-20 13:50:29Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
--
--
DROP TABLE IF EXISTS worker CASCADE;
CREATE TABLE worker
(
    workerid        BIGINT AUTO_INCREMENT PRIMARY KEY,
    isDisabled      BOOLEAN DEFAULT FALSE,
    lastLoginTime   TIMESTAMP NULL DEFAULT NULL,
    phone           CHAR(10) NULL,
    email           VARCHAR(255) NULL UNIQUE,
    smsemail        VARCHAR(255) NULL UNIQUE,
    passwordHash    VARBINARY(255) NULL,
    resetCodeHash   VARBINARY(255) NULL,
    firstName       VARCHAR(255) NOT NULL,
    middleName      VARCHAR(255) NULL,
    lastName        VARCHAR(255) NOT NULL,
    externalAuthentication  VARCHAR(255) NULL,
    UNIQUE          (firstName, middleName, lastName)
) ENGINE=InnoDB;
    -- should CHECK 1-of phone/email not null, but CHECK is ignored by MySQL

GRANT DELETE, SELECT, UPDATE ON TABLE worker TO phpserver@localhost;
-- NOT workerid, isDisabled, lastLoginTime, passwordHash, resetCodeHash; must be updates
GRANT INSERT(phone, email, smsemail, firstName, middleName, lastName, externalAuthentication) ON TABLE worker TO phpserver@localhost;
