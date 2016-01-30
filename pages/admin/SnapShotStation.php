<?php // $Id: SnapShotStation.php 2227 2012-09-23 21:12:29Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('report.php');
require_once('db/dbutil.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/html.php');
require_once('swwat/gizmos/parse.php');


$stationid = NULL;
$rows = array();
if (isset($_POST[PARAM_SAVE]))
{
    $lname = swwat_parse_string(html_entity_decode($_POST[PARAM_TITLE]), TRUE);
    $email = swwat_parse_string(html_entity_decode($_POST[PARAM_LOCATION]), TRUE);
    $sql = "SELECT stationid, lastName, email FROM station WHERE ";
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
        $params[] = "%" . $email . "%";
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
                $stationid = $rows[0]['stationid'];
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

	<title><?php echo(SITE_NAME); ?> - Snap Shot Worker Page</title>
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

<div id="snapshotstation_form">
    <h5>Select</h5>
    <form method="POST" name="snapshotstation_select" action="SnapShotStation.php">
    <table>
        <tr><td class="fieldTitle">Title:</td>
            <td><?php swwat_createInputValidate(PARAM_TITLE, "snapshotstation_select", NULL, FALSE); ?></td></tr>
        <tr><td class="fieldTitle">Location:</td>
            <td><?php swwat_createInputValidate(PARAM_LOCATION, "snapshotstation_select", NULL, FALSE); ?></td></tr>
        <tr><td><?php swwat_createInputSubmit(PARAM_SAVE, "Submit"); ?></td></tr>
    </table>
    </form>
</div><!-- snapshotstation_form -->

<div id="snapshotstation_results">
    <h5>Result</h5>
    <table>
        <tr><th>Last Name</th><th>Email</th></tr>
        <?php
        foreach ($rows as $row)
        {
            echo "<tr><td>" . $row['Title'] . "</td><td>" . $row['description'] . "</td></tr>\n";
        }
        if (0 == count($rows))
        {
            echo "<tr><td colspan='2'>no such stations</td></tr>\n";
        }
        ?>
    </table>
</div><!-- snapshotstation_results -->

<?php
if (!is_null($stationid))
{
?>
<div>
    <h5>Station Table</h5>
    <table>
        <?php Report::tableReport("station", " WHERE station = ?", array($stationid)); ?>
    </table>
</div>

<?php
} // !is_null($stationid)
?>



</div><!-- container -->
</body></html>
