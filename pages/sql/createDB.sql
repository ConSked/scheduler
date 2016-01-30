-- $Id: createDB.sql 412 2012-05-29 16:41:09Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.
--

DROP DATABASE IF EXISTS chronos;
CREATE DATABASE chronos CHARACTER SET utf8;



CREATE USER dbowner@localhost IDENTIFIED BY 'ownerpass';
--
GRANT ALTER, CREATE VIEW, CREATE, DROP, INDEX, GRANT OPTION ON chronos.* TO dbowner@localhost WITH GRANT OPTION;
GRANT ALL ON chronos.* TO dbowner@localhost WITH GRANT OPTION;




CREATE USER phpserver@localhost IDENTIFIED BY 'phppass';
--
GRANT DELETE, INSERT, SELECT, SHOW VIEW, UPDATE ON chronos.* TO phpserver@localhost;
