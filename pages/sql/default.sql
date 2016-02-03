-- $Id: default.sql 2398 2012-10-22 01:08:19Z wnm $ Copyright (c) ConSked, LLC. All Rights Reserved.

-- RC Org
INSERT INTO worker (phone, email, smsemail, passwordHash, firstName, middleName, lastName) VALUES
    ('7734577238', 'rcross@chicagobooth.edu', '7734577238@dummy.com', '$2a$07$yT2UU/clu5sOOKrp80Tm9OjBP62lbL32S7eGb39VKVUxMfjPIbp9W', 'Richard', 'Nelson', 'Cross'),
    ('8472076317', 'wnm@healthcodex.com', '8472076317@dummy.com', '$2a$07$L2wBMOwSyiblFc5Hm/6YCu7VBg4ucoGpwQdVZEbgWual475Ol8wN2', 'William', 'Norton', 'Murray'),
    ('7083580760', 'ecgero@comcast.net', '7083580760@dummy.com', '$2a$07$x1Vp0YJEhpVabDp5zsi1xukM1Vp8MD87TOJAoltmIoibRcWwlco9a', 'Earl', 'Carl', 'Gero');
UPDATE workerrole SET authrole = 'ORGANIZER'
    WHERE workerid = (SELECT workerid FROM worker WHERE email = 'rcross@chicagobooth.edu');
UPDATE workerrole SET authrole = 'SUPERVISOR'
    WHERE workerid = (SELECT workerid FROM worker WHERE email = 'wnm@healthcodex.com');
UPDATE workerrole SET authrole = 'CREWMEMBER'
    WHERE workerid = (SELECT workerid FROM worker WHERE email = 'ecgero@comcast.net');
