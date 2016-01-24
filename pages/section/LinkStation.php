<?php /* $Id: LinkStation.php 1706 2012-09-05 01:51:53Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved. */ ?>

<?php
require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('util/session.php');
?>

<div id="LinkStation">
   <table>
      <tr>
         <td><h5 style="margin:0">Expo</h5></td>
         <td class="fieldLink"><a href="ExpoViewPage.php"><?php echo(getExpoCurrent()->titleString());?></a></td>
      </tr>
      <tr>
         <td><h5 style="margin:0">Station</h5></td>
         <td class="fieldLink"><a href="StationViewPage.php"><?php echo(getStationCurrent()->titleString());?></a></td>
      </tr>
    </table>
</div>
<br />
