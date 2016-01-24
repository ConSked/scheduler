<?php // $Id: SnapShotWorker.php 2294 2012-09-28 20:07:57Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('report.php');
require_once('db/dbutil.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/html.php');
require_once('swwat/gizmos/parse.php');


$workerid = NULL;
$rows = array();
if (isset($_POST[PARAM_SAVE]))
{
    $lname = swwat_parse_string(html_entity_decode($_POST[PARAM_LASTNAME]), TRUE);
    $email = swwat_parse_string(html_entity_decode($_POST[PARAM_EMAIL]), TRUE);
    $workerid = swwat_parse_string(html_entity_decode($_POST[PARAM_WORKERID]), TRUE);
    $sql = "SELECT workerid, lastName, email FROM worker WHERE ";
    $params = array();
    if (!is_null($lname))
    {
        $sql .= " lastName LIKE lower(?) ";
        $params[] = "%" . $lname . "%";
    }
    if (!is_null($email))
    {
        if (!is_null($lname))
        {
            $sql .= " OR ";
        }
        $sql .= " email LIKE lower(?) ";
        $params[] = $email . "%";
    }
    if (!is_null($workerid))
    {
        if (!is_null($workerid))
        {
            $sql .= " OR ";
        }
        $sql .= " workerid LIKE lower(?) ";
        $params[] = $workerid . "%";
    }
    $sql .= " ORDER BY lastName ASC, email ASC";
    if (count($params) > 0)
    {
        try
        {
            $dbh = getPDOConnection();
            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (1 == count($rows))
            {
                $workerid = $rows[0]['workerid'];
            }
        }
        catch (PDOException $pe)
        {
            logMessage("SnapShotWorker - $lname, $email", $pe->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title>SwiftShift - Snap Shot Worker Page</title>
	<link href="css/site.css" rel="stylesheet" type="text/css">

    <!-- over-rides site.css if needed -->
    <style type="text/css">
th {text-align: center;
    font-size: 0.75em;
    font-weight: lighter;
    font-style: italic;
    border: 2px solid lightgrey;
    padding: 1px 4px 1px 4px;
    }
td {text-align: left;
    font-family: Times New Roman, serif;
    font-weight: darker;
    border: 1px solid lightgrey;
    padding: 1px 4px 1px 4px;
    }
table {border-collapse:collapse;}
    </style>
</head>

<body>
<div id="container">

<div id="snapshotworker_form">
    <h5>Select</h5>
    <form method="POST" name="snapshotworker_select" action="SnapShotWorkerNew.php">
    <table>
        <tr><td class="fieldTitle">Last Name:</td>
            <td><?php swwat_createInputValidate(PARAM_LASTNAME, "snapshotworker_select", NULL, FALSE); ?></td></tr>
        <tr><td class="fieldTitle">Email:</td>
            <td><?php swwat_createInputValidate(PARAM_EMAIL, "snapshotworker_select", NULL, FALSE); ?></td></tr>
        <tr><td class="fieldTitle">Workerid:</td>
            <td><?php swwat_createInputValidate(PARAM_WORKERID, "snapshotworker_select", NULL, FALSE); ?></td></tr>
        <tr><td><?php swwat_createInputSubmit(PARAM_SAVE, "Submit"); ?></td></tr>
    </table>
    </form>
</div><!-- snapshotworker_form -->

<div id="snapshotworker_results">
    <h5>Result</h5>
    <table>
        <tr><th>Last Name</th><th>Email</th></tr>
        <?php
        foreach ($rows as $row)
        {
            echo "<tr><td>" . $row['lastName'] . "</td><td>" . $row['email'] . "</td></tr>\n";
        }
        if (0 == count($rows))
        {
            echo "<tr><td colspan='2'>no such workers</td></tr>\n";
        }
        ?>
    </table>
</div><!-- snapshotworker_results -->

<?php
if (!is_null($workerid))
{
?>
<div>
    <h5>Worker Table</h5>
    <table>
        <?php Report::tableReport("worker", " WHERE workerid = ?", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>WorkerRole Table</h5>
    <table>
        <?php Report::tableReport("workerrole", " WHERE workerid = ?", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>WorkerExpo Table</h5>
    <table>
        <?php Report::tableReport("workerexpo", " WHERE workerid = ? ORDER BY expoid ASC", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>Invitation Table</h5>
    <table>
        <?php Report::tableReport("invitation", " WHERE workerid = ? ORDER BY expoid ASC", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>ShiftAssignment Table</h5>
    <table>
        <?php Report::tableReport("shiftassignment", " WHERE workerid = ? ORDER BY expoid ASC, stationid ASC, jobid ASC", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>ShiftPreference Table</h5>
    <table>
        <?php Report::tableReport("shiftpreference", " WHERE workerid = ? ORDER BY expoid ASC, stationid ASC, jobid ASC", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>JobPreference Table</h5>
    <table>
        <?php Report::tableReport("jobpreference", " WHERE workerid = ?", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>TimePreference Table</h5>
    <table>
        <?php Report::tableReport("timepreference", " WHERE workerid = ?", array($workerid)); ?>
    </table>
</div>

<div>
    <h5>Document Table</h5>
    <table>
        <?php Report::tableReportRaw("document",
            "SELECT documentid, expoid, workerid, uploadDate, reviewDate, reviewStatus, docType, docMime, docName " .
            " WHERE workerid = ? ORDER BY jobid ASC", array($workerid)); ?>
    </table>
</div>

<?php
} // !is_null($workerid)
?>



</div><!-- container -->
</body></html>
