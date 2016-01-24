<?php  // $Id: Invitation.php 2174 2012-09-22 16:57:00Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('db/WorkerLogin.php');
require_once('util/crypt.php');
require_once('util/date.php');
require_once('util/log.php');
require_once('util/mail.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');


define("INVITATION_SELECT_PREFIX", "SELECT DISTINCT expoid, workerid, email, expirationDate, code, phone, firstName, middleName, lastName FROM invitationview WHERE ");
define("INVITATION_SELECT_WORKER", INVITATION_SELECT_PREFIX
    . " (workerid = ? OR (email IS NULL AND code IS NULL))"
    . " AND expoid NOT IN (SELECT expoid FROM workerexpo WHERE workerid = ?)");
define("INVITATION_SELECT_EMAIL",  INVITATION_SELECT_PREFIX . " lower(email) = lower(?)");


class Invitation
{

public $expoid;
public $workerid;
public $email;
public $expirationDate;
public $code;
public $firstName;
public $middleName;
public $lastName;
public $phone;

public function worker()
{
    $w = new Worker();
    $w->workerid = $this->workerid;
    $w->email = $this->email;
    $w->firstName = $this->firstName;
    $w->middleName = $this->middleName;
    $w->lastName = $this->lastName;
    $w->phone = $this->phone;
    return $w;
} // worker

private function fixDates()
{
    if (is_string($this->expirationDate))
    {
        $this->expirationDate = swwat_parse_date($this->expirationDate);
    }
} // fixDates

public static function selectEmail($email, $code)
{
    self::deleteBulk(NULL, NULL); // clear garbage
    try
    {
        $rows = simpleSelect("Invitation", INVITATION_SELECT_EMAIL, array($email));
        foreach ($rows as $invitation)
        {
            $invitation->fixDates();
            // == works in the case where $code = NULL
            if (($code == $invitation->code) || compareField($code, $invitation->code))
            {
                return $invitation; // first is OK; after Invitation they become a worker!
            }
        }
        return NULL;
    }
    catch (PDOException $pe)
    {
        logMessage("Invitation::selectEmail($email, $code)", $pe->getMessage());
    }
} // selectEmail

public static function selectWorker($workerId)
{
    self::deleteBulk(NULL, NULL); // clear garbage
    try
    {
        $rows = simpleSelect("Invitation", INVITATION_SELECT_WORKER, array($workerId, $workerId));
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage("Invitation::selectWorker($workerId)", $pe->getMessage());
    }
} // selectWorker

private static function getInvitationForm($withCode)
{
    $body = "Hello,\n\nYou are invited to join EXPONAME.\nPlease go to the following page to register.\n\n"
        . BASE_URL . "/pages/RegistrationPage.php?" . PARAM_EMAIL . "=EMAIL";
    $paramNames = array("EXPONAME", "EMAIL");
    if ($withCode)
    {
        $body .= "&" . PARAM_WITHCODE . "=CODE\n\nYour registration code is: CODE\n\n";
        $paramNames[] = "CODE";
    }
    $body .= "\n\nSincerely,\nThe SwiftShift Team";
    return new FormMail("SwiftShift Expo Invitation", $paramNames, $body);
} // getInvitationForm

public static function inviteWorkers(Expo $expo, $expirationDate, array $workerArray)
{
    $body = "Hello FIRSTNAME,\n\nYou are invited to join EXPONAME.\nPlease login and proceed to the following page to register.\n\n"
        . BASE_URL . "/pages/WorkerRegistrationPage.php";
    $paramNames = array("FIRSTNAME", "EXPONAME"); // using NAME and EXPONAME leads to bad results; because NAME might get replaced first!
    $body .= "\n\nSincerely,\nThe SwiftShift Team";
    $welcomeForm = new FormMail("SwiftShift Expo Invitation", $paramNames, $body);
    $welcomeParams = array("EXPONAME" => $expo->title);

    $invite = new Invitation();
    $invite->expoid = $expo->expoid;
    $invite->expirationDate = is_null($expirationDate) ? $expo->stopTime : $expirationDate;

    foreach ($workerArray as $worker)
    {
        $invite->email = $worker->email;
        $invite->workerid = $worker->workerid;
        $welcomeParams["FIRSTNAME"] = $worker->firstName;
        $invite->insert($welcomeForm, $welcomeParams);
    } // $worker
    $invite = NULL;
    return;
} // inviteWorkers

public static function inviteUnknown(Expo $expo, $expirationDate, array $invitationArray, $withCode = TRUE, $uniqueCode = TRUE)
{
    $welcomeForm = self::getInvitationForm($withCode);
    $welcomeParams = array("EXPONAME" => $expo->title);
    $expirationDate = is_null($expirationDate) ? $expo->stopTime : $expirationDate;
    $code = WorkerLogin::generate_random_password();

    foreach ($invitationArray as $invite)
    {
        $invite->expoid = $expo->expoid;
        $invite->expirationDate = $expirationDate;
        if ($withCode)
        {
            $invite->code = $code;
            if ($uniqueCode)
            {
                $invite->code = WorkerLogin::generate_random_password();
            }
            $welcomeParams["CODE"] = $invite->code;
        }
        $welcomeParams["EMAIL"] = $invite->email;
        $invite->insert($welcomeForm, $welcomeParams);
    } // $invite

    $invite = NULL;
    return;
} // inviteUnknown

// for generic invitation, roll your own expoid=4, NULL, NULL, exDate, code=y
public function insert($welcomeForm = NULL, $params = NULL)
{
    $sqlParams = array();
    $sqlParams[] = $this->expoid;
    $sqlParams[] = swwat_format_isodate($this->expirationDate);
    $sqlParams[] = hashField($this->code);
    $sqlParams[] = $this->workerid; // null or not, is good

    if (is_null($this->workerid))
    {
        $sqlParams[] = $this->email;
        $sqlParams[] = $this->phone;
        $sqlParams[] = $this->firstName;
        $sqlParams[] = $this->middleName;
        $sqlParams[] = $this->lastName;
    }
    else // workerid is set; therefore do NOT set these fields
    {
        $sqlParams[] = NULL; // email
        $sqlParams[] = NULL; // phone
        $sqlParams[] = NULL; // firstName
        $sqlParams[] = NULL; // middleName
        $sqlParams[] = NULL; // lastName
    }
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("INSERT INTO invitation (expoid, expirationDate, code, workerid, " .
                              " email, phone, firstName, middleName, lastName) VALUES " .
                              " (?, ?, ?, ?, lower(?), ?, ?, ?, ?)");
        $stmt->execute($sqlParams);
        $dbh->commit();
        if (!is_null($welcomeForm))
        {
            $welcomeForm->sendForm($this->email, $params);
        }
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Invitation::insert()', $pe->getMessage());
    }
} // insert

public function delete()
{
    self::deleteBulk($this->expoid, $this->workerid);
    return;
} // delete

public static function deleteBulk($expoid, $workerid)
{
    $sql = "DELETE FROM invitation WHERE expirationDate < CURRENT_DATE ";
    $params = array();
    if (!is_null($expoid))
    {
        $sql .= " OR (expoid = ? ";
        $params[] = $expoid;
        if (!is_null($workerid))
        {
            $sql .= " AND workerid = ? ";
            $params[] = $workerid;
        }
        $sql .= ")"; // from (expoid ...
    }
    logMessage('Invitation::delete() called with ', $sql);
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        $dbh->commit();
        return;
    }
    catch (PDOException $pe)
    {
        logMessage('Invitation::delete()', $pe->getMessage());
    }
} // deleteBulk


} // Invitation

?>
