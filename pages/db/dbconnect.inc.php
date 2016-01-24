<?
    /* Connect to Database */
    $db_user="swifts6_EmailXL";
    $db_password="ySR2wm1o7a";
    $database="swifts6_CISCFF";
    mysql_connect("localhost",$db_user,$db_password);
    @mysql_select_db($database) or die("Unable to connect to database ".$database. " .");
?>
