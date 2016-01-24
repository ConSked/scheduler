<?php  // $Id: SMSEnum.php 1285 2012-08-01 15:34:16Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

/**
 * Data herein is taken from: http://www.emailtextmessages.com/
 */
class SMSEnum
{
public static $SMS_ARRAY = NULL;
public static $NONE = "none";

private static $STRING_ARRAY = NULL;
public static function getString($role)
{
    $value = SMSEnum::$STRING_ARRAY[$role];
    if (is_null($value))
    {
       throw new Exception("SMSEnum::getString(" . $role . ") - no such role");
    }
    return $value;
} // getString

public static $OPTION = NULL;

static function PrivateStaticConstructor()
{
    SMSEnum::$OPTION = array(array(SMSEnum::$NONE, "No SMS Service"),
            array("txt.att.net", "AT&T"),
            array("messaging.nextel.com", "Nextel"),
            array("messaging.sprintpcs.com", "Sprint"),
            array("tmomail.net", "T-Mobile"),
            array("email.uscc.net", "US Cellular"),
            array("vtext.com", "Verizon"),
            array("vmobl.com", "Virgin Mobile"),
            array("vmobile.ca", "Virgin Mobile Canada"),

            array("sms.3rivers.net", "3 River Wireless"),
            array("paging.acswireless.com", "ACS Wireless"),
            array("message.alltel.com", "Alltel"),
            array("bellmobility.ca", "Bell Canada"),
            array("txt.bellmobility.ca", "Bell Mobility"),
            array("blueskyfrog.com", "Blue Sky Frog"),
            array("sms.bluecell.com", "Bluegrass Cellular"),
            array("myboostmobile.com", "Boost Mobile"),
            array("bplmobile.com", "BPL Mobile"),
            array("cwwsms.com", "Carolina West Wireless"),
            array("mobile.celloneusa.com", "Cellular One"),
            array("csouth1.com", "Cellular South"),
            array("cwemail.com", "Centennial Wireless"),
            array("messaging.centurytel.net", "CenturyTel"),
            array("msg.clearnet.com", "Clearnet"),
            array("comcastpcs.textmsg.com", "Comcast"),
            array("corrwireless.net", "Corr Wireless Communications"),
            array("mobile.dobson.net", "Dobson"),
            array("sms.edgewireless.com", "Edge Wireless"),
            array("fido.ca", "Fido"),
            array("sms.goldentele.com", "Golden Telecom"),
            array("text.houstoncellular.net", "Houston Cellular"),
            array("ideacellular.net", "Idea Cellular"),
            array("ivctext.com", "Illinois Valley Cellular"),
            array("inlandlink.com", "Inland Cellular Telephone"),
            array("pagemci.com", "MCI"),
            array("page.metrocall.com", "Metrocall"),
            array("my2way.com", "Metrocall 2-way"),
            array("mymetropcs.com", "Metro PCS"),
            array("clearlydigital.com", "Midwest Wireless"),
            array("mobilecomm.net", "Mobilcomm"),
            array("text.mtsmobility.com", "MTS"),
            array("onlinebeep.net", "OnlineBeep"),
            array("pcsone.net", "PCS One"),
            array("sms.pscel.com", "Public Service Cellular"),
            array("qwestmp.com", "Qwest"),
            array("pcs.rogers.com", "Rogers Canada"),
            array("satellink.net", "Satellink"),
            array("txt.bell.ca", "Solo Mobile"),
            array("email.swbw.com", "Southwestern Bell"),
            array("tms.suncom.com", "Sumcom"),
            array("mobile.surewest.com", "Surewest Communications"),
            array("msg.telus.com", "Telus"),
            array("utext.com", "Unicel"),
            array("uswestdatamail.com", "US West"),
            array("sms.wcc.net", "West Central Wireless"),
            array("cellularonewest.com", "Western Wireless"));

    // key duplicates
            // array("messaging.sprintpcs.com", "Helio"),
            // array("fido.ca", "Microcell"),
            // array("txt.bell.ca", "President's Choice"),
            // array("pcs.rogers.com", "Rogers AT&T Wireless"),
            // array("tms.suncom.com", "Triton"),

    for ($k = 0; $k < count(SMSEnum::$OPTION); $k++)
    {
        $opt = SMSEnum::$OPTION[$k];
        SMSEnum::$SMS_ARRAY[] = $opt[0];
        SMSEnum::$STRING_ARRAY[$opt[0]] = $opt[1];
    }
} // PrivateStaticConstructor()
} // SMSEnum

SMSEnum::PrivateStaticConstructor(); // call static constructor

?>
