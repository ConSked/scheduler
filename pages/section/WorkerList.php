<?php // $Id: WorkerList.php 2429 2003-01-07 19:24:24Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');


function makeWorkerListHTMLRows(Worker $w, $position, Worker $author, $checkBoxFlag, $isSelected)
{
    echo "<tr>\n";
    if ($checkBoxFlag)
    {
        $option = array($position, "");
        echo "  <td class='checkValue'>";
        swwat_createRadioOption(PARAM_LIST_MULTIPLE, $option, SWWAT_CHECKBOX, $isSelected, FALSE);
        echo "  </td>\n";
    }
    echo '  <td class="fieldValueFirst">';
    if (!$checkBoxFlag && $author->isOrganizer()) // no link if checkBox
    {
        echo '<a href="WorkerViewPage.php?', PARAM_LIST_INDEX, '=', $position, '">';
    }
    echo(htmlspecialchars($w->nameString()));
    if (!$checkBoxFlag && $author->isOrganizer())
    {
        echo "</a>";
    }
    echo "</td>\n";
	if (!$author->isCrewMember())
	{
    	echo "<td class='fieldValue'>";
	    echo(htmlspecialchars($w->email));
	    echo "</td>\n";
	    echo "<td class='fieldValue'>";
		echo swwat_format_phone($w->phone);
	    echo "</td>\n";
	}
    echo "</tr>\n";
} // makeWorkerListHTMLRows


/*
 * This module is executed as a function (rather than call-outs from HTML)
 * in order to pass-in the $workerList as a variable (rather than lookup some $_SESSION variable)
 *
 * It is presumed that workerList is provided in the order required.
 */
function createWorkerHTMLList(array $workerList, Worker $author, $checkBoxTitle = NULL, $fromOrganizer = TRUE)
{
    $checkBoxFlag = !is_null($checkBoxTitle);
	if (!$author->isCrewMember())
	{
    	$numColumns = 3;
	}
	else
	{
		$numColumns = 1;
	}
    echo "<div id='workerlist_table'><table>\n<tr>\n";
    if ($checkBoxFlag)
    {
        echo "<th class='rowTitle'>", $checkBoxTitle, "</th>\n";
        $numColumns += 1;
    }
    echo "  <th class='rowTitle'>Name</th>\n";
	if (!$author->isCrewMember())
	{
    	echo "  <th class='rowTitle'>Email</th>\n";
    	echo "  <th class='rowTitle'>Phone</th>\n";
	}
    echo '</tr><tr class="rowTitle"><td colspan="', $numColumns, '">Supervisors</td></tr>';

    $c = count($workerList);
	$supervisors = 0;
    for ($k = 0; $k < $c; $k++)
    {
        $w = $workerList[$k];
        if ($w->isDisabled)  {  continue;  } // skip to next
        if (FALSE == ($w->isSupervisor()))  {  continue;  } // skip to next
        // else
        makeWorkerListHTMLRows($w, $k, $author, $checkBoxFlag, $fromOrganizer);
		$supervisors++;
    } // $k
	if ($supervisors == 0)
	{
        echo '<tr><td class="fieldError" colspan="', $numColumns, '">No Supervisors in this list.</td></tr>';
	}

    echo '<tr class="rowTitle"><td colspan="', $numColumns, '">Crew</td></tr>';
    $c = count($workerList);
	$crew = 0;
    for ($k = 0; $k < $c; $k++)
    {
        $w = $workerList[$k];
        if ($w->isDisabled)  {  continue;  } // skip to next
        if (FALSE == ($w->isCrewMember()))  {  continue;  } // skip to next
        // else
        makeWorkerListHTMLRows($w, $k, $author, $checkBoxFlag, $fromOrganizer);
		$crew++;
    } // $k
	if ($crew == 0)
	{
        echo '<tr><td class="fieldError" colspan="', $numColumns, '">No Crew in this list.</td></tr>';
	}

    echo '<tr class="rowTitle"><td colspan="', $numColumns, '">Organizers</td></tr>';
    $c = count($workerList);
	$organizers = 0;
    for ($k = 0; $k < $c; $k++)
    {
        $w = $workerList[$k];
        if ($w->isDisabled)  {  break;  } // skip to next
        if (FALSE == ($w->isOrganizer()))  {  continue;  } // skip to next
        // else
        makeWorkerListHTMLRows($w, $k, $author, $checkBoxFlag, !$fromOrganizer);
		$organizers++;
    } // $k
	if ($organizers == 0)
	{
        echo '<tr><td class="fieldError" colspan="', $numColumns, '">No Organizers in this list.</td></tr>';
	}

    if (!$checkBoxFlag && ($k < $c) && $w->isDisabled)
    {
        echo '<tr class="rowTitle"><td colspan="', $numColumns, '">Disabled</td></tr>';
        $c = count($workerList);
        for ($k = 0; $k < $c; $k++)
        {
            $w = $workerList[$k];
            if (FALSE == $w->isDisabled)  {  continue;  }
            // else
            makeWorkerListHTMLRows($w, $k, $author, FALSE, FALSE);
        } // $k
    }
    if ($k < $c)
    {
        logMessage('ERROR: WorkerList', 'some workers did not fit in the list!');
    }
    echo "</table></div><!-- workerlist_table -->\n";
} // createWorkerHTMLList

?>
