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
              <p>Would you like to know more potential customers and investors.
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
