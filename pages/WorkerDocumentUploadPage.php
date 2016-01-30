<?php // $Id: WorkerDocumentUploadPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('db/Expo.php');
require_once('properties/constants.php');
require_once('section/FileUpload.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');
require_once('util/session.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title><?php echo(SITE_NAME); ?> - Document Upload Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<div id="container">

<?php
$expo = getExpoCurrent();
if (is_null($expo))
{
    $expo = getParamItem(PARAM_LIST, PARAM_LIST_INDEX);
	setExpoCurrent($expo); // paranoia about some included section
}
$_SESSION[PARAM_LIST] = NULL;

// hardcoded here for CIW
$_SESSION[PARAM_DOCTYPE] = "waiver"; // set here as we only have 1 doc for the moment

// $docTypeArray should be selected from expodocument table - @see src/db/sql/document.sql
// note array is blanked out as cannot select on this page - @see InvitationPage vs. InvitationFileUploadPage
$docTypeList = array();
$docTypeList[0] = $_SESSION[PARAM_DOCTYPE];
$docTypeOption = array(array($docTypeList[0], $docTypeList[0]));
$defaultOption = $docTypeList[0];

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php
    if (!is_null($expo->expoid))
    {
        include('section/LinkExpoWorker.php');
    }
    ?>

	<div id="workerdocument_fileupload">
		<table>
            <tr><td class="fieldTitle">Document Type:</td>
                <td><?php swwat_createSelect(PARAM_DOCTYPE, $docTypeOption, $defaultOption, TRUE); ?></td>
            </tr>
            <tr><td></td><td></td></tr>
            <tr><td class="fieldTitle">Upload File:</td>
                <td><?php createFileUploadForm("WorkerDocumentUploadAction.php", PARAM_DOCUMENT); ?></td></tr>
		</table>
	</div><!-- workerdocument_fileupload -->

    <div id="workerdocument_fileupload_results">
        <h5>Document Upload Results</h5>
        <p><?php
            // get rid of annoying notice
            if (isset($_SESSION[PARAM_PAGE_MESSAGE]))
            {
                echo $_SESSION[PARAM_PAGE_MESSAGE];
            }
            ?></p>
    </div>

</div><!-- main -->

<?php
	$menuItemArray = array();
	$menuItemArray[] = MENU_VIEW_SITEADMIN;
	$menuItemArray[] = MENU_VIEW_WORKERLIST;
	Menu::addMenu($menuItemArray);
	include('section/footer.php');
?>

</div><!-- container -->
</body></html>
