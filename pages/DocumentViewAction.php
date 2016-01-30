<?php // $Id: DocumentViewAction.php 1635 2012-08-31 21:22:03Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Document.php');
require_once('util/log.php');
require_once('util/session.php');


$document = getParamItem(PARAM_LIST2, PARAM_LIST2_INDEX);
if (!is_null($document))
{
    try
    {
        $document->selectContent(); // can throw PDOException

        // send the file to the browser
        header("Content-type: $document->docMime");
        header("Content-Disposition: attachment; filename='$document->docName'");
        echo $document->content;

        $document->content = NULL; // gc hint
        $document = NULL; // gc hint
        return;
    }
    catch (Exception $ex)
    {
        $msg = $ex->getMessage();
        logMessage("DocumentViewAction", "file error:$msg");
        $_SESSION[PARAM_PAGE_MESSAGE] = "Your document has failed to download.";
    }
}
header('Location: WorkerViewPage.php');
include('WorkerViewPage.php');

?>
