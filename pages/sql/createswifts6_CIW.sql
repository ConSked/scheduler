-- $Id: createswifts6_CIW.sql 2352 2012-10-05 23:26:47Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.
--

DROP DATABASE IF EXISTS swifts6_CIW;
CREATE DATABASE swifts6_CIW CHARACTER SET utf8;



CREATE USER swifts6_EmailXL@localhost IDENTIFIED BY 'ownerpass';
--
GRANT ALTER, CREATE VIEW, CREATE, DROP, INDEX, GRANT OPTION ON swifts6_CIW.* TO swifts6_EmailXL@localhost WITH GRANT OPTION;
GRANT ALL ON swifts6_CIW.* TO swifts6_EmailXL@localhost WITH GRANT OPTION;




CREATE USER phpserver@localhost IDENTIFIED BY 'phppass';
--
GRANT DELETE, INSERT, SELECT, SHOW VIEW, UPDATE ON swifts6_CIW.* TO phpserver@localhost;
