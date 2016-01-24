<?php /* $Id: LinkWorker.php 1536 2012-08-29 17:23:44Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved. */ ?>

<?php
require_once('db/Worker.php');
require_once('util/session.php');
?>

<div id="LinkWorker">
   <table>
      <tr>
         <td><h5 style="margin:0">Staff</h5></td>
         <td class="fieldLink"><a href="WorkerViewPage.php"><?php echo(getWorkerCurrent()->nameString());?></a></td>
      </tr>
   </table>
</div>
