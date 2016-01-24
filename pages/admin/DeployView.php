<?php // $Id: DeployView.php 2219 2012-09-23 00:53:41Z preston $

$docRoot = $_SERVER['DOCUMENT_ROOT'];
$PHP_FILES = "find " . $docRoot . " -name '*.php' | sort";
$LSA_FILE  = "/bin/ls -l ";
$LSB_FILE  = ' | awk \'{print $6, $7, $8}\'';
$SUM_FILE  = "sum ";
$ID_FILE   = 'grep "\$Id\:" ';

$files = shell_exec($PHP_FILES);
$files = explode("\n", $files);
$docRoot .= "/";
$docRootLen = strlen($docRoot);
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title>SwiftShift - Deploy View Page</title>
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
    font-family: monospace, sans-serif;
    font-size: 0.75em;
    font-weight: darker;
    border: 1px solid lightgrey;
    padding: 1px 4px 1px 4px;
    }
table {border-collapse:collapse;}
    </style>
</head>

<body>
<div id="container">

<div id="deployview">
    <h5>Deploy View</h5>
    <table>
        <tr><th>file path</th><th>last touch</th><th>sum(file)</th><th>svn Id</th></tr>
<?php
foreach ($files as $file)
{
    if (0 == strlen(trim($file)))  {  continue;  }

    $fileOnly = (strpos($file, $docRoot) >= 0) ? substr($file, $docRootLen) : $file;
    $fileOnly = htmlentities(trim($fileOnly));

    $ls = shell_exec($LSA_FILE . $file . $LSB_FILE);
    $ls = htmlentities(trim($ls));
    $ls = str_replace(" ", "&nbsp;", $ls);

    $sum = shell_exec($SUM_FILE . $file);
    $sum = htmlentities(trim($sum));
    $sum = str_replace("  ", " ", $sum);
    $sum = str_replace("  ", " ", $sum);
    $sum = str_replace("  ", " ", $sum);
    $sum = str_replace(" ", "&nbsp;", $sum);

    $id = shell_exec($ID_FILE . $file);

    if (!is_null($id))
    {
        $a = strpos($id, '$');
        if (FALSE != $a)
        {
            $b = strpos($id, '$', $a+1);
            if (FALSE == $b)
            {
                $b = $a+1;
            }
            $id = substr($id, $a, $b-$a+1);
        }
        $id = htmlentities(trim($id));
    }

    echo "<tr>";
    echo "\n\t<td>$fileOnly</td>";
    echo "\n\t<td>$ls</td>";
    echo "\n\t<td>$sum</td>";
    echo "\n\t<td>$id</td>";
    echo "</tr>\n";
} // $file
$files = NULL;
?>
</div><!-- deployview -->
</div><!-- container -->
</table>
</body>
</html>
