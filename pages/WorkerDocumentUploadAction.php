<?php // $Id: WorkerDocumentUploadAction.php 1590 2012-08-30 23:34:03Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Document.php');
require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('section/FileUpload.php');
require_once('util/crypt.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');


$errorMessage = "";
if (UPLOAD_ERR_OK != $_FILES[PARAM_DOCUMENT]['error'])
{
    $errorMessage = getErrorMessage(PARAM_DOCUMENT);
    logMessage("WorkerDocumentUploadPage", "upload error:$errorMessage");
    $_SESSION[PARAM_PAGE_MESSAGE] = $errorMessage;
    header('Location: WorkerDocumentUploadPage.php');
    include('WorkerDocumentUploadPage.php');
    return;
}

// else we are good to go
$expo = getExpoCurrent();
$worker = getWorkerCurrent();
$doc = new Document();
$doc->workerid = $worker->workerid;
$doc->expoid = $expo->expoid;
try
{
    $fileString = file_get_contents($_FILES[PARAM_DOCUMENT]['tmp_name']);
    $doc->docType = $_SESSION[PARAM_DOCTYPE];
    $doc->docMime = $_FILES[PARAM_DOCUMENT]['type'];
    $doc->docName = $_FILES[PARAM_DOCUMENT]['name'];
    $doc->content = $fileString;
    $$fileString = NULL;

    $doc->insert(); // can throw PDOException
    $_SESSION[PARAM_PAGE_MESSAGE] = "Your document has uploaded successfully.";
}
catch (Exception $ex)
{
    $msg = $ex->getMessage();
    logMessage("WorkerDocumentUploadAction", "file error:$msg");
    $_SESSION[PARAM_PAGE_MESSAGE] = "Your document has failed to upload.";
}
$doc = NULL; // gc hint

header('Location: WorkerDocumentUploadPage.php');
include('WorkerDocumentUploadPage.php');

?>
