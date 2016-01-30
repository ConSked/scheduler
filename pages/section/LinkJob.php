<?php /* $Id: LinkJob.php 1747 2012-09-06 18:49:16Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved. */ ?>

<?php
require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('db/Job.php');
require_once('util/session.php');
?>

<div id="LinkJob">
   <table>
      <tr>
         <td><h5 style="margin:0">Expo</h5></td>
         <td class="fieldLink"><a href="ExpoViewPage.php"><?php echo(getExpoCurrent()->titleString());?></a></td>
      </tr>
      <tr>
         <td><h5 style="margin:0">Station</h5></td>
         <td class="fieldLink"><a href="StationViewPage.php"><?php echo(getStationCurrent()->titleString());?></a></td>
      </tr>
      <tr>
         <td><h5 style="margin:0">Job</h5></td>
         <!-- note this could be a drop-down or an <a>getJobCurrent</a> depending -->
         <td class="fieldLink"><?php echo(getStationCurrent()->jobTitleString());?></td>
      </tr>
    </table>
</div>
<br />
