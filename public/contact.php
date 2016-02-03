<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<!-- Copyright 2012 ConSked. All rights reserved. -->
<title>Contact the ConSked Team</title>
<meta name="Author" content="Richard Cross" />
<meta name="description" content="ConSked makes scheduling events easy." />
<meta name="keywords" content="ConSked" />
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
<body bgcolor="#327a94" style="font-family: Arial, Helvetica, sans-serif" onload="setFocus()">
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%">
<?php
//--------------------------------------------------------------------------------//

//list ($logo, $width, $height) = get_logo($_SERVER['SERVER_NAME'], $Obj);

//--------------------------------------------------------------------------------//
?>
      <img src="images/ConSked_Web_-_tag_line.original.jpg" width="760" height="200" alt="ConSked logo" />
    </td>
  </tr>
  <tr>
    <th height="21" colspan="2" align="left" scope="col" style="background-image: url(images/bar_compressed.jpg)">
      <div align="center">
        <span style="color:#FFFFFF">
          <a href="index.php" class="menu">Home</a>
          <a href="tour.php" class="menu">Tour</a>
          <a href="contact.php" class="menu">Contact Us</a>
        </span>
      </div>
    </th>
  </tr>
  <tr>
    <td width="69%" align="center" valign="top" bgcolor="#FFFFFF">
      <table border="0">
        <tr>
          <td width="5%"></td>
          <td align="left" valign="top" scope="col">
            <p style="font-size:24px" />
            <h1>Contact Us</h1>
            <div style="color:#000000;font-size:12px" align="left">
<?php
if(isset($_POST['emailaddress']))
{
//If we URL poke to get to a directory
  $emailaddress=$_POST['emailaddress'];
} else {
  $emailaddress="";
}
if(isset($_POST['message']))
{
//If we URL poke to get to a directory
  $message=$_POST['message'];
} else {
  $message="";
}
if(($message == "") && ($emailaddress == "")) {
  ;
} else {
                $subject="Message from $emailaddress";
                $from=$emailaddress;
                $headers="MIME-Version: 1.0\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\n";
                $headers .= "From: $emailaddress\n"."Reply-To: $emailaddress\n"."X-Mailer: PHP/".phpversion();
                mail('ajaxchess@gmail.com',$subject,$message,$headers);
}

?>
<?php
if(isset($_POST['message']))
{
  echo "Thank you for your comments.";
} else {

echo <<<END
              <p>We welcome inquiries from potential customers and investors.
              <br/>Our office contact information is below, or fill out this form.</p>
              <form method="post" name="contact" action="contact.php">
                <br/><label id="emailaddress">Your email address</label>
                <br/>
                <input type="text" name="emailaddress" size="40" />
                <br/>
                <textarea name="message" rows="5" cols="60"></textarea>
                <br/>
                <input type="submit" name="submit" value="Send your feedback" />
              </form>
END;
}
?>
              <br/>
              <p>
              ConSked Headquarters
              <br/>Richard Cross, President
              <br/>William Murray, CTO
              <br/>345 N. Canal #404
              <br/>Chicago, IL 60606
              <br/>773-457-7238
              <br/>312-224-1752
              </p>
            </div>
          </td>
          <td width="5%"></td>
        </tr>
      </table>
    </td>
    <td width="31%" align="center" valign="top" bgcolor="#FFFFFF" style="border: solid 0 #000; border-left-width:2px;">
      <table width="314" border="0">
        <tr>
          <td height="184" scope="col">
            <span style="color:#0000aa">
              <br /><b>ConSked<br /></b>
            </span>
            <p />
            <i>We are taking on beta customers now. Please fill out the contact us page.</i>
            <br />
            <p />
            <i></i>
            <br />
          </td>
        </tr>
      </table>
      <table width="314" border="0">
        <tr>
          <td height="21" scope="col"></td>
        </tr>
      </table>
      <table width="314" border="0">
        <tr>
          <td height="184" scope="col">
            <span style="color:#0000aa">
              <b>ConSked<br /></b>
            </span>
          </td>
          <td></td>
        </tr>
      </table>
      <br />
    </td>
  </tr>
  <tr>
    <td height="19" colspan="2" align="center" bgcolor="#e1f4fb">
      <div style="font-size:9px">ConSked - 345 N Canal St. #404, Chicago, IL 60606 - Phone: 773-457-7238 - Copyright 2012 ConSked</div>
    </td>
  </tr>
</table>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25707149-1']);
  _gaq.push(['_setDomainName', '.consked.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
