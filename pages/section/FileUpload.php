<?php // $Id: FileUpload.php 2122 2012-09-21 16:21:47Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('util/log.php');

// @see http://www.php.net/manual/en/features.file-upload.php

function createFileUploadForm($action, $name)
{
    echo "<form enctype='multipart/form-data' method='POST' action='$action'>\n"
        . "\t<input type='hidden' name='MAX_FILE_SIZE' value='5000000000000'/>\n"
        . "\t<input type='file' name='$name'/>\n"
        . "\t<br/>\n\t<input type='submit' name='" . PARAM_UPLOAD . "' value='Upload File'/>\n</form>\n";
    return;
} // createFileUploadForm


function getErrorMessage($name)
{
    $clientName = $_FILES[$name]['name'];
    if (UPLOAD_ERR_OK == $_FILES[$name]['error'])
    {
        return "Your file, $clientName, uploaded correctly.";
    }
    else if ((UPLOAD_ERR_INI_SIZE  == $_FILES[$name]['error']) ||
             (UPLOAD_ERR_FORM_SIZE == $_FILES[$name]['error']))
    {
        return "Your file, $clientName, is too big for the server to handle.";
    }
    else if (UPLOAD_ERR_NO_FILE == $_FILES[$name]['error'])
    {
        return "Please specify a file to upload.";
    }
    // else
    $error = "";
    if (UPLOAD_ERR_PARTIAL == $_FILES[$name]['error'])
    {
        $error = "The uploaded file was only partially uploaded.";
    }
    else if (UPLOAD_ERR_NO_TMP_DIR == $_FILES[$name]['error'])
    {
        $error = "Missing a temporary folder.";
    }
    else if (UPLOAD_ERR_CANT_WRITE == $_FILES[$name]['error'])
    {
        $error = "ailed to write file to disk.";
    }
    else if (UPLOAD_ERR_EXTENSION == $_FILES[$name]['error'])
    {
        $error = "A PHP extension stopped the file upload.";
    }
    else
    {
        $error = "error unknown, code:" . $_FILES[$name]['error'];
    }
    logMessage("FileUpload.php - getErrorMessage($name)", "error:$error");
    return "Your file did not upload correctly, please try again.";
} // getErrorMessage

?>
