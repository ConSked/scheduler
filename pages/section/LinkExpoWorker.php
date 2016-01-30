<?php /* $Id: LinkExpoWorker.php 1970 2012-09-14 20:59:57Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved. */

require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('util/session.php');


$titleLinkExpoWorker = (!is_null(getExpoCurrent())) ? getExpoCurrent()->titleString() : "";
if (getWorkerAuthenticated()->isOrganizer() || getWorkerAuthenticated()->isSupervisor())
{
    $title = "<a href='ExpoViewPage.php'>" . $titleLinkExpoWorker . "</a>";
}

?>
<div id="LinkExpo">
   <table>
      <?php
      if (!is_null(getExpoCurrent()))
      {
      ?>
      <tr>
         <td><h5 style="margin:0">Expo</h5></td>
         <td class="fieldLink"><?php echo($titleLinkExpoWorker);?></a></td>
      </tr>
      <?php
      }
      if (!is_null(getWorkerCurrent()))
      {
      ?>
      <tr>
         <td><h5 style="margin:0">Staff</h5></td>
         <td class="fieldLink"><a href="WorkerViewPage.php"><?php echo(getWorkerCurrent()->nameString());?></a></td>
      </tr>
      <?php
      }
      ?>
   </table>
</div>
<br />
