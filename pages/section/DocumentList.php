<?php // $Id: DocumentList.php 1795 2012-09-07 04:13:14Z wnm $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('db/Document.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');
require_once('util/ReviewEnum.php');


function makeDocumentListHTMLHeader($showWorker)
{
	echo "<tr>\n<th class='rowTitle'>";
	echo ($showWorker ? "Staff" : "");
	echo "</th>\n\t<th class='rowTitle'>Document Type</th>\n";
	echo "\t<th class='rowTitle'>Uploaded</th>\n";
	echo "\t<th class='rowTitle'>Reviewed</th>\n";
	echo "\t<th class='rowTitle'>Review Status</th>\n";
	echo "</tr>\n";
    return;
} // makeDocumentListHTMLRow

function makeDocumentListHTMLRow(Document $document, $position, $isEditable, $isOrganizer, Worker $worker = NULL)
{
    echo "<tr>\n<td class='fieldValueFirst'>";
    if (!is_null($worker))
    {
        // reset to match up; note duplicates ok
        echo "<a href='WorkerViewPage.php?" . PARAM_LIST2_INDEX . "=$position'>"
            . htmlspecialchars($worker->nameString()) . "</a>";
    }
	echo "</td>\t<td class='fieldValue'><a href='DocumentViewAction.php?".PARAM_LIST2_INDEX."=$position'>"
        . htmlspecialchars($document->docType) . "</a></td>\n";

    $date =  htmlspecialchars(swwat_format_usdate($document->uploadDate));
	echo "\t<td class='fieldValue'>$date</td>\n";

    // todo - should this also check (0 == strcmp(UNREVIEWED, $document->reviewStatus))?
    $date = is_null($document->reviewDate) ? "" : htmlspecialchars(swwat_format_usdate($document->reviewDate));
	echo "\t<td class='fieldValue'>$date</td>\n";

	echo "\t<td class='fieldValue'>";
    // permissions checks - $isEditable is like a potential to be edited
    if (!$isOrganizer)
    {
        // workers can only delete a non-reviewed document
        $isEditable &= (0 == strcmp(UNREVIEWED, $document->reviewStatus));
    }
    if ($isEditable)
    {
        echo "<form method='POST' action='WorkerDocumentAction.php'>";
        swwat_createInputHidden(PARAM_LIST2_INDEX, $position);
        // set up options
        // if Organizer: UnReviewed->Approve, Decline, Delete; Approve->UnReviewed, Decline; Decline->UnReviewed, Approve
        // repeat if Organizer: Delete only from UnReviewed
        // if Worker: UnReviewed->Delete
        $options = ReviewEnum::getList($isOrganizer, $document->reviewStatus);
        swwat_createSelect(PARAM_STATUSTYPE, $options, $document->reviewStatus, !$isEditable);
        echo "&nbsp;";
        swwat_createInputSubmit(is_null($worker) ? PARAM_LASTNAME : "", ($isOrganizer ? "Change Status" : "Delete"));
        echo "</form>";
    }
    else
    {
        echo htmlspecialchars(ReviewEnum::getString($document->reviewStatus));
    }
	echo "</td>\n</tr>\n";
    return;
} // makeDocumentListHTMLRow

function createDocumentHTMLList(array $documentList, array $workerList, $isEditable = FALSE)
{
    if (!is_null($workerList))
    {
        $reindexedWorkers = array();
        foreach ($workerList as $w)
        {
            $reindexedWorkers[$w->workerid] = $w;
        } // $w
        // now convert workerList into a duplicate entry list in documentList order
        $workerList = array();
        foreach ($documentList as $d)
        {
            $workerList[] = $reindexedWorkers[$d->workerid];
        } // $w
        $reindexedWorkers = NULL; // gc
    }

    // prepare for selections
    $_SESSION[PARAM_LIST2] = $documentList;
    $_SESSION[PARAM_LIST] = $workerList;

    // now set up the html
    echo "<div>\n<table>\n";
    makeDocumentListHTMLHeader(TRUE);

	if (0 == count($documentList))
    {
		echo "<tr><td class='fieldError' colspan='5'>There are No Documents for this Expo.</td></tr>\n";
    }

    for ($k = 0; $k < count($documentList); $k++)
    {
        $d = $documentList[$k];
        $w = $workerList[$k];
        // must be organizer on this page
        makeDocumentListHTMLRow($d, $k, $isEditable, TRUE, $w);
    }

	echo "</table>\n</div>\n";
} // createDocumentHTMLList

?>
