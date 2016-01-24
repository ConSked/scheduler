<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<!-- Copyright 2012 SwiftShift. All rights reserved. -->
<title>Contact the SwiftShift Team</title>
<meta name="Author" content="Richard Cross" />
<meta name="description" content="SwiftShift makes scheduling events easy." />
<meta name="keywords" content="SwiftShift" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/main.css" />

<script language="JavaScript" type="text/javascript">
//<![CDATA[
function setFocus()
{
	document.toplogin.email.focus();
}

function genericLogin(form)
{
	var frm = eval("document."+form);
	var email = frm.email.value;
	var password = frm.password.value;

	if (email == '')
	{
		alert("Please enter an email address");
	}
	else
	{
		sp_email = (email.split('@'))[1];
		if (sp_email == 'gmail.com')
		{
			alert("Please use the Google login.");
		}
		else if (sp_email == null)
		{
			alert("Please enter a valid email address.");
		}
		else
		{
			url = "<?php echo(SITELINK); ?>/genericlogin_action.php";
			frm.action = url;
			frm.submit();
		}
	}
}
//]]>
</script>

</head>
