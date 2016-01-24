-- $Id: zzzorganizerhosted.sql 2398 2012-10-22 01:08:19Z wnm $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

-- RC Org
INSERT INTO worker (phone, email, smsemail, passwordHash, firstName, middleName, lastName) VALUES
    ('3129930363', 'rcross@chicagobooth.edu', '3129930363@messaging.sprintpcs.com', '$2a$07$et3qvodYihk5UngRis6Rh.8UuowSWlwYUNWpTAuo7OM1Y/3hZ3jCm', 'Rich', 'Nelson', 'Cross');
UPDATE workerrole SET authrole = 'ORGANIZER'
    WHERE workerid = (SELECT workerid FROM worker WHERE email = 'rcross@chicagobooth.edu');
