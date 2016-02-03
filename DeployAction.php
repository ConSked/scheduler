<?php // $Id: DeployAction.php 2393 2012-10-16 14:09:58Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

session_start();

$constants_file = "constants.php";
$constants_dir = "pages/properties/";

// values in constants.php
$URL = "URL_DUMMY";
$timezone = "America/Chicago";
$DBHost = "MYDB_HOST";
$DBPort = "MYDB_PORT";
$DBName = "MYDB_NAME";
$DBUsername = "MYDB_USERNAME";
$DBPassword = "MYDB_PASSWORD";

// values from form
$URLFromForm = NULL;
if (isset($_POST['URLFromForm']))
{
	$URLFromForm = $_POST['URLFromForm'];
}
$_SESSION['URLFromForm'] = $URLFromForm;

$timezoneFromForm = NULL;
if (isset($_POST['timezoneFromForm']))
{
	$timezoneFromForm = $_POST['timezoneFromForm'];
}
$_SESSION['timezoneFromForm'] = $timezoneFromForm;

$DBPortFromForm = NULL;
if (isset($_POST['DBPortFromForm']))
{
	$DBPortFromForm = $_POST['DBPortFromForm'];
}
$_SESSION['DBPortFromForm'] = $DBPortFromForm;

$DBNameFromForm = NULL;
if (isset($_POST['DBNameFromForm']))
{
	$DBNameFromForm = $_POST['DBNameFromForm'];
}
$_SESSION['DBNameFromForm'] = $DBNameFromForm;

$DBUsernameFromForm = NULL;
if (isset($_POST['DBUsernameFromForm']))
{
	$DBUsernameFromForm = $_POST['DBUsernameFromForm'];
}
$_SESSION['DBUsernameFromForm'] = $DBUsernameFromForm;

$DBPasswordFromForm = NULL;
if (isset($_POST['DBPasswordFromForm']))
{
	$DBPasswordFromForm = $_POST['DBPasswordFromForm'];
}
$_SESSION['DBPasswordFromForm'] = $DBPasswordFromForm;

//preserve pages directory
$shell_output = shell_exec('mv pages pages.old');
echo "<pre>$shell_output</pre>";

// untar tarball
$shell_output = shell_exec('tar -xvf pages.tar');
echo "<pre>$shell_output</pre>";

// make new constants file
$shell_output = shell_exec('cp '.$constants_dir.$constants_file.' .');
echo "<pre>$shell_output</pre>";

$shell_output = shell_exec('sed -i -e "s|'.$URL.'|'.$URLFromForm.'|g" '.$constants_file);
$shell_output = shell_exec('sed -i -e "s|'.$timezone.'|'.$timezoneFromForm.'|g" '.$constants_file);

if ($DBPortFromForm == 3306)
{
	$shell_output = shell_exec('sed -i -e "s|'.$DBHost.'|localhost|g" '.$constants_file);
}
else
{
	$shell_output = shell_exec('sed -i -e "s|'.$DBHost.'|127.0.0.1|g" '.$constants_file);
}

$shell_output = shell_exec('sed -i -e "s|'.$DBPort.'|'.$DBPortFromForm.'|g" '.$constants_file);
$shell_output = shell_exec('sed -i -e "s|'.$DBName.'|'.$DBNameFromForm.'|g" '.$constants_file);
$shell_output = shell_exec('sed -i -e "s|'.$DBUsername.'|'.$DBUsernameFromForm.'|g" '.$constants_file);
$shell_output = shell_exec('sed -i -e "s|'.$DBPassword.'|'.$DBPasswordFromForm.'|g" '.$constants_file);

$shell_output = shell_exec('mv '.$constants_dir.$constants_file.' '.$constants_dir.$constants_file.'.back');
echo "<pre>$shell_output</pre>";

$shell_output = shell_exec('cp '.$constants_file.' '.$constants_dir.$constants_file);
echo "<pre>$shell_output</pre>";

echo "Done";

//header('Location: DeployPage.php');
//include('DeployPage.php');
//return;

?>
