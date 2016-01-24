<?php /* $Id: LinkExpo.php 2263 2012-09-26 15:19:20Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved. */

require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('util/session.php');

$titleLinkExpo = getExpoCurrent()->titleString();
if (getWorkerAuthenticated()->isOrganizer() || getWorkerAuthenticated()->isSupervisor())
{
    $titleLinkExpo = "<a href='ExpoViewPage.php'>" . $titleLinkExpo . "</a>";
}

?>
<div id="LinkExpo">
   <table>
      <tr>
         <td><h5 style="margin:0">Expo</h5></td>
         <td class="fieldLink"><?php echo($titleLinkExpo);?></td>
      </tr>
   </table>
</div>
<br />
