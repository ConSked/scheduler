<?php // $Id: WorkerLoginChangePage.php 1357 2012-08-21 19:39:07Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
// custom isLoggedIn such that a temp login can change pw
require_once('properties/constants.php');
require_once('util/session.php');
session_cache_limiter('nocache');
session_start();
if (!isset($_SESSION[AUTHENTICATED_TEMP]) && !isLoggedIn())
{
    logMessage('authentication', 'worker not logged in');
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Worker Login Change Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
	function init()
	{
		var password  = $('[name=<?php echo PARAM_PASSWORD;?>]').val();
		var repeat    = $('[name=repeat]').val();
		var logic = (password !== repeat);

		if (password.length === 0 || repeat.length === 0)
		{
			$('#submit').attr("disabled", "disabled");
		}
		else
		{
			$('#repeat').remove();
			if (logic)
			{
				if ($('#repeat').length === 0)
				{
					if (password !== repeat && password.length === repeat.length)
					{
						$('[name=repeat]').after('  <span id="repeat" class="fieldError">Password and re-type are not the same.</span>');
					}
				}
			}
			$('#submit').attr("disabled", logic);
		}
		return;
	} // init

	function passwordCheck(param1)
	{
		var password = param1.value;
		var logic = (password.length === 0 || password.length > 30)

		$('#password').remove();
		if (logic)
		{
			if ($('#password').length === 0)
			{
				if (password.length === 0)
				{
					$('[name=<?php echo PARAM_PASSWORD;?>]').after('  <span id="password" class="fieldError">Password is a required field.</span>');
				}
				else
				{
					$('[name=<?php echo PARAM_PASSWORD;?>]').after('  <span id="password" class="fieldError">Password is over 30 characters.</span>');
				}
			}
		}
		$('#submit').attr("disabled", logic);
		init();
		return;
	} // passwordCheck

	function repeatCheck(param1)
	{
		var repeat = param1.value;
		var logic = (repeat.length === 0 || repeat.length > 30)

		$('#repeat').remove();
		if (logic)
		{
			if ($('#repeat').length === 0)
			{
				if (repeat.length === 0)
				{
					$('[name=repeat]').after('  <span id="repeat" class="fieldError">Repeat Password is a required field.</span>');
				}
				else
				{
					$('[name=repeat]').after('  <span id="repeat" class="fieldError">Repeat Password is over 30 characters.</span>');
				}
			}
		}
		$('#submit').attr("disabled", logic);
		init();
		return;
	} // repeatCheck
	</script>
</head>

<body onload="init()">
<div id="container">

<?php
require_once('section/Menu.php');

// ok, start the html
include('section/header.php');
?>


<div id="main">

    <div id="workerloginchangepage_change">
        <form method="POST" name="workerloginpage_change_form" action="WorkerLoginChangeAction.php"><table>
            <tr>
                <td class="fieldTitle">Password:</td>
                <td><?php echo '<input type="password" name="', PARAM_PASSWORD, '" value="" size="30"
				                       onkeyup="passwordCheck(document.forms[\'workerloginpage_change_form\'][\'password\'])"
				                       onchange="passwordCheck(document.forms[\'workerloginpage_change_form\'][\'password\'])" />' ?></td>
            </tr>
            <tr>
                <td class="fieldTitle">Repeat Password:</td>
                <td><?php echo '<input type="password" name="repeat" value="" size="30"
				                       onkeyup="repeatCheck(document.forms[\'workerloginpage_change_form\'][\'repeat\'])"
				                       onchange="repeatCheck(document.forms[\'workerloginpage_change_form\'][\'repeat\'])" />' ?></td> 
            </tr>
            <tr>
                <td colspan="2"><input type="submit" id="submit" value="Change Password"/></td>
            </tr>
        </table></form>
    </div><!-- workerloginchangepage_change -->

</div><!-- main -->

<?php
    // Change PW page handles menus differently!
    echo '<div id="menu"><table>';
    Menu::addMenuItem(MENU_LOGOUT);
    echo '</table></div><!-- menu -->';
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
