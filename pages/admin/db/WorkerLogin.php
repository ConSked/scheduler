<?php // $Id: WorkerLogin.php 1462 2012-08-26 18:32:17Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Worker.php');
require_once('swwat/gizmos/format.php');
require_once('util/crypt.php');
require_once('util/mail.php');
require_once('util/session.php');

class LoginException extends Exception
{
} // LoginException


class RequirePasswordReset extends Exception
{
    public $workerid;
    public function __construct($workerid)
    {
        $this->workerid = $workerid;
    }
} // RequirePasswordReset


class WorkerLogin
{

/**
 * This method should only be used by authenticate.php
 * @param type $workerid
 */
public static function isDisabled($workerid)
{
    $rows = NULL;
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare("SELECT 1 FROM worker WHERE workerid = ? AND isDisabled = FALSE");
        $stmt->execute(array($workerid));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) == 1)  {  return false;  }
    }
    catch (PDOException $pe)
    {
        logMessage("WorkerLogin::isDisabled($workerid)", $pe->getMessage());
    }
    return true;
} // isDisabled

public static function password_authenticate($email, $password)
{
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare("SELECT workerid, isDisabled, passwordHash, resetCodeHash FROM worker WHERE lower(email) = lower(?)");
        $stmt->execute(array($email));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) == 0)
        {
            throw new LoginException('Can not find worker account.');
        }
        else if (count($rows) > 1)
        {
            throw new LoginException('There are more than one worker account with the same email address.');
        }
        $workerid = $rows[0]['workerid'];
        $isDisabled = $rows[0]['isDisabled'];
        $passwordHash = $rows[0]['passwordHash'];
        $resetCodeHash = $rows[0]['resetCodeHash'];

        if ($isDisabled == TRUE)
        {
            throw new LoginException('Worker account is disabled.');
        }

        if (compareField($password, $passwordHash)) // passwords match; normal login
        {
            $dbh->beginTransaction();
            $stmt = $dbh->prepare("UPDATE worker SET lastLoginTime = CURRENT_TIMESTAMP WHERE workerid = ?");
            $stmt->execute(array($workerid));
            $dbh->commit();
            // we login
            logout(); // paranoia
            session_cache_limiter('nocache');
            session_start();
            $worker = Worker::selectID($workerid);
            $_SESSION[AUTHENTICATED] = $worker;
            $_SESSION[AUTHENTICATED_TEMP] = NULL; // paranoia
            return;
        }
        else if (is_null($passwordHash) && compareField($password, $resetCodeHash)) // pw MUST be null; resets match; reset login
        {
            $dbh->beginTransaction();
            $stmt = $dbh->prepare("UPDATE worker SET lastLoginTime = CURRENT_TIMESTAMP, resetCodeHash = NULL WHERE workerid = ?");
            $stmt->execute(array($workerid));
            $dbh->commit();
            // we login, but only to the temp
            logout(); // paranoia
            session_cache_limiter('nocache');
            session_start();
            $worker = Worker::selectID($workerid);
            $_SESSION[AUTHENTICATED_TEMP] = $worker; // only permit access to pw change
            throw new RequirePasswordReset($workerid);
        }
        else // password does not match; normal pw typo
        {
            throw new LoginException('Worker failed to login.');
        }
    }
    catch (PDOException $pe)
    {
        // do NOT log password
        logMessage('WorkerLogin::password_authenticate(' . $email . ", $password)", $pe->getMessage());
        throw new LoginException('Worker failed to login.');
    }
} // password_authenticate

public static function password_change($workerId, $password)
{
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare("SELECT isDisabled, externalAuthentication FROM worker WHERE workerid = ?");
        $stmt->execute(array($workerId));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) != 1)
        {
            throw new Exception('Can not find worker account.');
        }
        $isDisabled = $rows[0]['isDisabled'];
        $externalAuthentication = $rows[0]['externalAuthentication'];

        if ($isDisabled == TRUE)
        {
            throw new Exception('Worker account is disabled.');
        }
        if ($externalAuthentication == TRUE)
        {
            throw new Exception('This worker account uses external authentication.');
        }

        $dbh->beginTransaction();
        // with new password; resetCode is no longer needed
        $stmt = $dbh->prepare("UPDATE worker SET passwordHash = ?, resetCodeHash = NULL WHERE workerid = ?");
        $stmt->execute(array(hashField($password), $workerId));
        $dbh->commit();
        // we login, proper this time
        logout(); // paranoia
        session_cache_limiter('nocache');
        session_start();
        $worker = Worker::selectID($workerId);
        $_SESSION[AUTHENTICATED] = $worker;
        $_SESSION[AUTHENTICATED_TEMP] = NULL;
        return;
    }
    catch (PDOException $pe)
    {
        // do NOT log password
        logMessage('WorkerLogin::password_change(' . $workerId . ", $password)", $pe->getMessage());
    }
} // password_change

/**
 * This method does NOT call FormMail::sendPasswordReset;
 * that is the responsibility of the calling function.
 */
public static function password_reset($email)
{
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare("SELECT isDisabled, externalAuthentication FROM worker WHERE lower(email) = lower(?)");
        $stmt->execute(array($email));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) == 0)
        {
            throw new Exception('Can not find worker account.');
        }
        else if (count($rows) > 1)
        {
            throw new Exception('There are more than one worker account with the same email address.');
        }
        $isDisabled = $rows[0]['isDisabled'];
        $externalAuthentication = $rows[0]['externalAuthentication'];

        if ($isDisabled == TRUE)
        {
            throw new Exception('Worker account is disabled.');
        }

        if ($externalAuthentication == TRUE)
        {
            throw new Exception('This worker account uses external authentication.');
        }

        $resetCodeHash = self::generate_random_password();

        $dbh->beginTransaction();
        // note the reset forces pw NULL
        $stmt = $dbh->prepare("UPDATE worker SET passwordHash = NULL, resetCodeHash = ? WHERE lower(email) = lower(?)");
        $stmt->execute(array(hashField($resetCodeHash), $email));
        $dbh->commit();

        return $resetCodeHash;
    }
    catch (PDOException $pe)
    {
        // do NOT log password
        logMessage('WorkerLogin::password_reset(' . $email . ')', $pe->getMessage());
    }
    return NULL;
} // password_reset

public static function generate_random_password()
{
    // todo - make these DEFINEs for CONST
    $num ='23456789';
    $alph = 'abcdefghijkmnopqrstuvwxyz';
    $ALPH = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    $alnum = $num.$alph.$ALPH;
    $size = strlen($alnum);

    $new_password = '';

    for ($i = 0; $i < 8; $i++)
    {
        $new_password .= $alnum[(mt_rand(0, $size-1))];
    }
    return $new_password;
} // generate_random_password

public static function enable($email) { WorkerLogin::set_isDisabled($email, FALSE); }
public static function disable($email) { WorkerLogin::set_isDisabled($email, TRUE); }

public static function set_isDisabled($workerid, $disabledFlag)
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE worker SET isDisabled = ? WHERE workerid = ?");
        $stmt->execute(array($disabledFlag, $workerid));
        $dbh->commit();
        return NULL;
    }
    catch (PDOException $pe)
    {
        // do NOT log password
        logMessage('WorkerLogin::set_isDisabled(' . $workerid . ', ' . $disabledFlag . ')', $pe->getMessage());
    }
} // set_isDisabled

} // WorkerLogin
?>
