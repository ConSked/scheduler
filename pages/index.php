<?php // $Id: WorkerLoginPage.php 2358 2012-10-09 00:32:12Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
// NOTE WE DO NOT!!!! include('util/authenticate.php');

require_once('properties/constants.php');
require_once('util/session.php');
require_once('swwat/gizmos/html.php');

// if a session existed before, it doesn't now
logout();

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title>SwiftShift - Worker Login Page</title>
	<link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php
$error = NULL;
if (isset($_REQUEST['error']))
{
	$error = $_REQUEST['error'];
}

// ok, start the html
include('section/header.php');
?>

<script type="text/javascript">
<!--

function setEmailParam()
{
    // note that 'email' is not set via PHP - a small hole
    document.forms['workerloginpage_reset_form']['email'].value = document.forms['workerloginpage_unpw_form']['email'].value;
    return;
} // setEmailParam

// -->
</script>

<div id="main">

<div id="workerloginpage_unpw">

	<form method="POST" name="workerloginpage_unpw_form" action="WorkerLoginAction.php">
	<table>
		<tr>
			<td><span class="fieldError"><?php echo $error; ?></span></td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="fieldTitle">Email:</td>
			<td><?php swwat_createInputValidate(PARAM_EMAIL, "workerloginpage_unpw_form", NULL, NULL); ?></td>
		</tr>
		<tr>
			<td class="fieldTitle">Password:</td>
			<td><?php echo '<input type="password" name="', PARAM_PASSWORD, '" value="" size="30" />' ?></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Login"/></td>
		</tr>
	</table>
	</form>
	<form method="POST" name="workerloginpage_reset_form" action="WorkerLoginResetAction.php"
	      onsubmit="setEmailParam()">
	<?php swwat_createInputHidden(PARAM_EMAIL, ""); ?>
	<table>
		<tr>
			<td><input type="submit" value="Reset Password"/></td>
		</tr>
	</table>
	</form>
<?php
if(OPENREGISTRATION=="true")
{
  echo("<a href=RegistrationPage.php>Register</a>");
}
?>
</div><!-- workerloginpage_unpw -->

</div><!-- main -->

<?php
include('section/footer.php');
?>

</div><!-- container -->
</body></html>
