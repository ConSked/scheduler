DROP TABLE IF EXISTS remindersent CASCADE;
CREATE TABLE remindersent
(
    date       DATE NULL
) ENGINE=InnoDB;
GRANT DELETE, INSERT, SELECT, UPDATE ON TABLE remindersent TO phpserver@localhost;
