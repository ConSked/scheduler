-- $Id: document.sql 1593 2012-08-30 23:40:40Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.
--
--
--
--
DROP TABLE IF EXISTS document CASCADE;
CREATE TABLE document
(
    documentid      BIGINT AUTO_INCREMENT PRIMARY KEY,
    expoid          BIGINT NOT NULL,
    workerid        BIGINT NULL,
    uploadDate      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewDate      TIMESTAMP NULL,
    reviewStatus    ENUM('UNREVIEWED', 'APPROVED', 'DECLINED')  NOT NULL DEFAULT 'UNREVIEWED',
    docType         VARCHAR(255) NOT NULL,
    docMime         VARCHAR(255) NOT NULL,
    docName         VARCHAR(255) NOT NULL,
    content         MEDIUMBLOB NOT NULL,
    UNIQUE (expoid, workerid, uploadDate, docType),
    FOREIGN KEY (expoid) REFERENCES expo(expoid) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (workerid) REFERENCES worker(workerid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE document TO phpserver@localhost;
--
--
-- commented out until later @see #86
-- CREATE TABLE expodocument
-- docid, expoid, required, disables, etc.
-- this table is used by registration wizard to create templates for workers to download
-- by workers to choose which filetype to upload
-- by reports to indicate full document set
--
