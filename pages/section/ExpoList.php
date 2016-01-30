<?php // $Id: ExpoList.php 2430 2003-01-07 20:06:24Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Document.php');
require_once('db/Expo.php');
require_once('db/ShiftStatus.php');
require_once('db/Worker.php');
require_once('section/DocumentList.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');


function expoListStartTable()
{
    echo "<div id='expolist_table'>\n";
	echo "<table>\n";
	echo "<tr>\n";
	echo "  <th class='rowTitle'>Expo</th>\n";
	echo "  <th class='rowTitle'>Start</th>\n";
	echo "  <th class='rowTitle'>Stop</th>\n";
} // expoListStartTable

function expoListWorkerTable()
{
    expoListStartTable();
    echo "  <th class='rowTitle'>Hours</th>\n";
    echo "  <th class='rowTitle'>Documents</th>\n";
    echo "</tr>\n";
} // expoListWorkerTable

// used by WorkerRegistrationPage
function makeExpoRegistrationRow(Expo $expo, $position)
{
    expoListStartRow($expo, $position, FALSE);
    echo "\t<td class='fieldValue'>";
    swwat_createRadioOption(PARAM_LIST_MULTIPLE, array($position, ""), SWWAT_CHECKBOX, TRUE, FALSE);
    echo "</td>\n</tr>\n";
    return;
} // makeExpoRegistrationRow

function expoListStartRow(Expo $expo, $position, $isOrganizer)
{
    echo "<tr>\n  <td class='fieldValueFirst'>";
    if ($isOrganizer)
    {
    	echo "<a href='ExpoViewPage.php?".PARAM_LIST_INDEX."=".$position."'>".htmlspecialchars($expo->title)."</a>";
    }
    else if (!$expo->isPast()) // isWorker TRUE
    {
    	echo "<a href='PreferenceWelcomePage.php?".PARAM_LIST_INDEX."=".$position."'>".htmlspecialchars($expo->title)."</a>";
    }
    else
    {
        echo htmlspecialchars($expo->title);
    }
	echo "</td>\n";
	echo "  <td class='fieldValue'>".htmlspecialchars(swwat_format_usdate($expo->startTime))."</td>\n";
	echo "  <td class='fieldValue'>".htmlspecialchars(swwat_format_usdate($expo->stopTime))."</td>\n";
} // expoListStartRow

function expoListWorkerRow(Expo $expo, $position, $isOrganizer, Worker $worker, array $docList)
{
    expoListStartRow($expo, $position, $isOrganizer);
    if ($expo->isPast() || $expo->isRunning())
    {
        $hours = ShiftStatus::WorkerHours($worker->workerid, $expo->expoid);
    }
    else
    {
        $hours = "-";
    }
    echo "  <td class='fieldValue'>".htmlspecialchars($hours)."</td>\n";
    if ($isOrganizer || !$expo->isPast())
    {
        echo "  <td class='fieldValue'><a href='WorkerDocumentUploadPage.php?".PARAM_LIST_INDEX."=".$position."'>Upload</a></td>\n";
    }
    else
    {
        echo "  <td class='fieldValue'>Upload</td>\n";
    }
	echo "</tr>\n";
    // insert a non-expo row in the expo table!
    if (($isOrganizer || !$expo->isPast()) && !is_null($docList))
    {
        if (count($docList) > 0)
        {
            makeDocumentListHTMLHeader(FALSE);
            for ($d = 0; $d < count($docList); $d++)
            {
                $document = $docList[$d];
                if ($document->expoid == $expo->expoid)
                {
                    makeDocumentListHTMLRow($document, $d, !$expo->isPast(), $isOrganizer, NULL);
                }
            } // $d
        }
    }
} // expoListWorkerRow

/*
 * It is presumed that expoList is provided in the order required.
 */
function createExpoHTMLList(array $expoList, $isOrganizer)
{
    $_SESSION[PARAM_LIST] = $expoList;
    $_REQUEST[PARAM_LIST_INDEX] = NULL;
    setExpoCurrent(NULL);

    $CURRENT_TIME_STAMP = new DateTime();
    expoListStartTable();
    echo "</tr>\n"; // close header row


    echo "<tr class='rowTitle'><td colspan='3'>Future Expos</td></tr>\n";
	$future = 0;
    for ($k = 0; $k < count($expoList); $k++)
    {
        $expo = $expoList[$k];
        if (dateCompare($expo->startTime, $CURRENT_TIME_STAMP) <= 0)  {  break;  }
        expoListStartRow($expo, $k, $isOrganizer);
        echo "</tr>\n"; // close row
		$future++;
    } // $k
	if ($future == 0)
	{
		echo "<tr><td class='fieldError' colspan='3'>There are no future Expos.</td></tr>\n";
	}


    echo "<tr class='rowTitle'><td colspan='3'>Current Expos</td></tr>\n";
	$future = 0;
    for (; $k < count($expoList); $k++)
    {
        $expo = $expoList[$k];
        if (dateCompare($expo->stopTime, $CURRENT_TIME_STAMP) < 0)  {  break;  }
        expoListStartRow($expo, $k, $isOrganizer);
        echo "</tr>\n"; // close row
		$future++;
    } // $k
	if ($future == 0)
	{
		echo "<tr><td class='fieldError' colspan='3'>There are no current Expos.</td></tr>\n";
	}


    echo "<tr class='rowTitle'><td colspan='3'>Past Expos</td></tr>\n";
	$future = 0;
	for (; $k < count($expoList); $k++)
	{
		$expo = $expoList[$k];
        expoListStartRow($expo, $k, $isOrganizer);
        echo "</tr>\n"; // close row
		$future++;
	} // $k
	if ($future == 0)
	{
		echo "<tr><td class='fieldError' colspan='3'>There are no past Expos.</td></tr>\n";
	}

    echo "</table>\n";
    echo "</div><!-- expolistpage_table -->\n";
} // createExpoHTMLList


/*
 * It is presumed that expoList is provided in the order required.
 */
function createWorkerExpoHTMLList(array $expoList, Worker $worker, $isOrganizer)
{
    $_SESSION[PARAM_LIST] = $expoList;
    $_REQUEST[PARAM_LIST_INDEX] = NULL;
    //setExpoCurrent(NULL);

    $docList = Document::selectWorker($worker->workerid);
    $_SESSION[PARAM_LIST2] = $docList;
    $_REQUEST[PARAM_LIST2_INDEX] = NULL;

    $CURRENT_TIME_STAMP = new DateTime();
    expoListWorkerTable(); // don't need to close


    echo "<tr class='rowTitle'><td colspan='5'>Future Expos</td></tr>\n";
	$future = 0;
    for ($k = 0; $k < count($expoList); $k++)
    {
        $expo = $expoList[$k];
        if (dateCompare($expo->startTime, $CURRENT_TIME_STAMP) <= 0)  {  break;  }
        expoListWorkerRow($expo, $k, $isOrganizer, $worker, $docList); // don't need to close
		$future++;
    } // $k
	if ($future == 0)
	{
		echo "<tr><td class='fieldError' colspan='5'>There are no future Expos.</td></tr>\n";
	}


    echo "<tr class='rowTitle'><td colspan='5'>Current Expos</td></tr>\n";
	$future = 0;
    for (; $k < count($expoList); $k++)
    {
        $expo = $expoList[$k];
        if (dateCompare($expo->stopTime, $CURRENT_TIME_STAMP) < 0)  {  break;  }
        expoListWorkerRow($expo, $k, $isOrganizer, $worker, $docList); // don't need to close
		$future++;
    } // $k
	if ($future == 0)
	{
		echo "<tr><td class='fieldError' colspan='5'>There are no current Expos.</td></tr>\n";
	}


    echo "<tr class='rowTitle'><td colspan='5'>Past Expos</td></tr>\n";
	$future = 0;
	for (; $k < count($expoList); $k++)
	{
		$expo = $expoList[$k];
        expoListWorkerRow($expo, $k, $isOrganizer, $worker, $docList); // don't need to close
		$future++;
	} // $k
	if ($future == 0)
	{
		echo "<tr><td class='fieldError' colspan='5'>There are no past Expos.</td></tr>\n";
	}

    echo "</table>\n";
    echo "</div><!-- expolistpage_table -->\n";
} // createWorkerExpoHTMLList


?>
