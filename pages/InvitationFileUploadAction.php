<?php // $Id: InvitationFileUploadAction.php 1570 2012-08-30 18:33:43Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Invitation.php');
require_once('db/Worker.php');
require_once('db/WorkerLogin.php');
require_once('section/FileUpload.php');
require_once('util/crypt.php');
require_once('util/FileWorker.php');
require_once('util/FiveDegreesCSV.php');
require_once('util/log.php');
require_once('util/mail.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');



function newInviteFromFile(FileWorker $fileWorker) // throws PDOException
{
    $invite = new Invitation();
    $invite->email = $fileWorker->email;
    $invite->phone = $fileWorker->phone;
    $invite->firstName = $fileWorker->firstName;
    $invite->middleName = $fileWorker->middleName;
    $invite->lastName = $fileWorker->lastName;
    return $invite;
} // newInviteFromFile


$expo = getExpoCurrent();
unset($_SESSION[PARAM_UPLOAD]);
unset($_SESSION[PARAM_MESSAGE]);
$name = PARAM_DOCUMENT;
$errorMessage = "";
if (UPLOAD_ERR_OK != $_FILES[$name]['error'])
{
    $errorMessage = getErrorMessage($name);
    logMessage("InvitationFileUploadAction", "upload error:$errorMessage");
    $_SESSION[PARAM_MESSAGE] = "<tr><td>entire upload</td><td></td><td>no entry</td><td>no email</td><td>$errorMessage</td></tr>\n";
    header('Location: InvitationFileUploadPage.php');
    include('InvitationFileUploadPage.php');
    return;
}

// else we are good to go
$expirationDate = swwat_parse_date(html_entity_decode($_SESSION[PARAM_STOPTIME]));
$withCode = (0 == strcmp(PARAM_WITHCODE, $_SESSION[PARAM_WITHCODE]));
$uniqueCode = (0 == strcmp(PARAM_UNIQUE, $_SESSION[PARAM_UNIQUE]));
// $uploadFileType = $_SESSION[PARAM_UPLOADFILETYPE]; // currently MUST = 5degrees
$existingWorkers = Worker::selectExpo($expo->expoid);
$checkWorkers = array();
foreach ($existingWorkers as $worker)
{
    $checkWorkers[] = $worker->workerid;
} // $worker
$existingWorkers = NULL; // gc hint

try
{
    $fileString = file_get_contents($_FILES[$name]['tmp_name']);
    $fileWorkerArray = NULL;
    if (TRUE) // $uploadFileType == 5 degrees
    {
        $fileWorkerArray = FiveDegreesCSV::parse($fileString);
        $fileString = NULL; // gc hint
    }

    $workerArray = array();
    $unknownArray = array();
    foreach ($fileWorkerArray as $fileWorker)
    {
        $error = $fileWorker->getError();
        if (is_null($error))
        {
            try
            {
                $worker = Worker::selectUsername($fileWorker->email);
                if (!is_null($worker))
                {
                    if (FALSE != array_search($worker->workerid, $checkWorkers))
                    {
                        $errorMessage .= "<tr><td>$fileWorker->index</td><td>$fileWorker->email</td><td>already assigned</td><td>no email</td><td>OK!</td></tr>\n";
                    }
                    else
                    {
                        $workerArray[] = $worker;
                        $errorMessage .= "<tr><td>$fileWorker->index</td><td>$fileWorker->email</td><td>account exists</td><td>email sent</td><td>OK!</td></tr>\n";
                    }
                    continue;
                }
                $unknownArray[] = newInviteFromFile($fileWorker);
                $errorMessage .= "<tr><td>$fileWorker->index</td><td>$fileWorker->email</td><td>invite issued</td><td>email sent</td><td>OK!</td></tr>\n";
            }
            catch (PDOException $ex)
            {
                // set on a per-line basis $_SESSION[PARAM_MESSAGE] = $ex + $error
                $errorMessage .= "<tr><td>$fileWorker->index</td><td>$fileWorker->email</td><td>bad data</td><td>no email</td><td>databse failure</td></tr>\n";
                $msg = $ex->getMessage();
                logMessage("InvitationFileUploadAction", "PDOException:$msg file line error:$error");
            }
        }
        else
        {
            // set on a per-line basis $_SESSION[PARAM_MESSAGE] = $error;
            $errorMessage .= "<tr><td>$fileWorker->index</td><td>$fileWorker->email</td><td>no invite</td><td>no email</td><td>$error</td></tr>\n";
            logMessage("InvitationFileUploadAction", "file line error:$error");
        }
    } // $fileWorker
    // codes are un-needed as the worker already has a code - their password
    Invitation::inviteWorkers($expo, $expirationDate, $workerArray);
    $workerArray = NULL; // gc hint
    Invitation::inviteUnknown($expo, $expirationDate, $unknownArray, $withCode, $uniqueCode);
    $unknownArray = NULL; // gc hint
}
catch (Exception $ex)
{
    $msg = $ex->getMessage();
    $errorMessage .= "<tr><td>entire file</td><td></td><td>no invite</td><td>no email</td><td>$msg</td></tr>\n";
    logMessage("InvitationFileUploadAction", "file error:$msg");
}
if (strlen($errorMessage) > 0)
{
    $_SESSION[PARAM_MESSAGE] = $errorMessage;
}

header('Location: InvitationFileUploadPage.php');
include('InvitationFileUploadPage.php');

?>
