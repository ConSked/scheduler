<?php // $Id: DeployPage.php 2407 2012-10-22 20:22:42Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
    // NOTE WE DO NOT!!!! include('util/authenticate.php');
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title>Consked - Deploy Page</title>
	<link href="pages/jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="pages/jquery/jquery-1.7.2.min.js"></script>
	<script src="pages/jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
	function init()
	{
		var URLFromForm = $('[name="URLFromForm"]').val();
		var DBPortFromForm = $('[name="DBPortFromForm"]').val();
		var DBNameFromForm = $('[name="DBNameFromForm"]').val();
		var DBUsernameFromForm = $('[name="DBUsernameFromForm"]').val();
		var DBPasswordFromForm = $('[name="DBPasswordFromForm"]').val();
		var logic = (URLFromForm.length === 0 || DBPortFromForm.length === 0 ||
                     DBNameFromForm.length === 0 || DBUsernameFromForm.length === 0 || DBPasswordFromForm.length === 0);
		$('[name="submit"]').attr("disabled", logic);
	}
	</script>
</head>

<body onload="init()">
<div id="container">

<?php
$_POST['URLFromForm'] = NULL;
if (isset($_SESSION['URLFromForm']))
{
	$_POST['URLFromForm'] = $_SESSION['URLFromForm'];
}
else
{
	$_POST['URLFromForm'] = 'http://' . $_SERVER['SERVER_NAME'];

	$add = preg_replace('/\/DeployPage.php/', '', $_SERVER['SCRIPT_NAME']);

	if ($add != '')
	{
		$_POST['URLFromForm'] .= $add;
	}
}

$default_timezone = "America/Chicago";
if (isset($_SESSION['timezoneFromForm']))
{
	$default_timezone = $_SESSION['timezoneFromForm'];
}

$timezone = array(array('America/Boise', 'Boise'),
                  array('America/Chicago', 'Chicago'),
                  array('America/Los_Angeles', 'Los Angeles'),
                  array('America/New_York', 'New York'),
                  array('America/Toronto', 'Toronto'));

$_POST['DBPortFromForm'] = NULL;
if (isset($_SESSION['DBPortFromForm']))
{
	$_POST['DBPortFromForm'] = $_SESSION['DBPortFromForm'];
}
else
{
	$_POST['DBPortFromForm'] = "3306";
}

$_POST['DBNameFromForm'] = NULL;
if (isset($_SESSION['DBNameFromForm']))
{
	$_POST['DBNameFromForm'] = $_SESSION['DBNameFromForm'];
}
else
{
	$_POST['DBNameFromForm'] =  "MY_DATABASE";
}

$_POST['DBUsernameFromForm'] = NULL;
if (isset($_SESSION['DBUsernameFromForm']))
{
	$_POST['DBUsernameFromForm'] = $_SESSION['DBUsernameFromForm'];
}
else
{
	$_POST['DBUsernameFromForm'] =  "MY_DBUSERNAME";
}

$_POST['DBPasswordFromForm'] = NULL;
if (isset($_SESSION['DBPasswordFromForm']))
{
	$_POST['DBPasswordFromForm'] = $_SESSION['DBPasswordFromForm'];
}
else
{
	$_POST['DBPasswordFromForm'] =  "MY_DBPASSWORD";
}
?>

<div id="main">

<div id="workerloginpage_unpw">

	<form method="POST" name="deploypage_form" action="DeployAction.php">
	<table>
		<tr>
			<td>Enter your URL:&nbsp;&nbsp;</td>
			<td><input type="text" name="URLFromForm" value="<?php echo($_POST['URLFromForm']); ?>" size="40" onkeyup="init()" onchange="init()" /></td>
			<td style="padding-left: 20px">Enter your MySQL database port:&nbsp;&nbsp;</td>
			<td><input type="text" name="DBPortFromForm" value="<?php echo($_POST['DBPortFromForm']); ?>" size="5" onkeyup="init()" onchange="init()" /></td>
		</tr>
		<tr>
			<td>Pick your timezone:&nbsp;&nbsp;</td>
			<td>
				<select name="timezoneFromForm">
				<?php
				for ($k = 0; $k < count($timezone); $k++)
				{
					echo("<option value=\"".$timezone[$k][0]."\"");
					if (!strcmp($timezone[$k][0], $default_timezone))
					{
						echo(" selected=\"selected\"");
					}
					echo(">".$timezone[$k][1]."</option>");
				}
				?>
				</select>
			</td>
			<td style="padding-left: 20px">Enter your MySQL database name:&nbsp;&nbsp;</td>
			<td><input type="text" name="DBNameFromForm" value="<?php echo($_POST['DBNameFromForm']); ?>" size="30" onkeyup="init()" onchange="init()" /></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td style="padding-left: 20px">Enter your MySQL database username:&nbsp;&nbsp;</td>
			<td><input type="text" name="DBUsernameFromForm" value="<?php echo($_POST['DBUsernameFromForm']); ?>" size="30" onkeyup="init()" onchange="init()" /></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td style="padding-left: 20px">Enter your MySQL database password:&nbsp;&nbsp;</td>
			<td><input type="text" name="DBPasswordFromForm" value="<?php echo($_POST['DBPasswordFromForm']); ?>" size="30" onkeyup="init()" onchange="init()" /></td>
		</tr>
			<td colspan="4"><p /><input type="submit" name="submit" value="Start Deploy"/></td>
		</tr>
	</table>
	</form>
</div><!-- workerloginpage_unpw -->

</div><!-- main -->

</div><!-- container -->
</body></html>
