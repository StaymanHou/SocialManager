<div id="AM_Main">
 <h2>Detailed List</h2>
 <div class="abuttonwrapper"><a class="abutton" href="Account.php?method=create"><span>Create new account</span></a></div>
  <table border=1 width=720px>
  <tr><td width=5px></td><td><b>Module</b></td><td><b>AutoMode</b></td><td><b>Active</b></td><td><b>Post Number Per Day</b></td><td><b>Queue Size</b></td><td><b>Start Time</b></td><td><b>End Time</b></td><td><b>Operation</b></td></tr>
  <?php
   foreach ($GLOBALS['TEMPLATE']['Content']['AccountList'] as $acc)
   {
       $key = $acc['NAME'];
       echo '<tr><td colspan=9><h3>' . $key;
       echo '<span style="color: ';
       echo $acc['ACTIVE']?'green':'red';
       echo '; font-size: x-small;">';
       echo $acc['ACTIVE']?' (Active)':' (Inactive)';
       echo '</span> <a href="' . $acc['RSS_URL'] . '" target="_blank" style="font-size: x-small">' . $acc['RSS_URL'] . '</a><div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="Account.php?method=delete&pk=' . $acc['PK'] . '" style="background: transparent url(\'media/square-orange-left.gif\') no-repeat top left" onclick="return myconfirm(\'Are you sure you want to delete the Account \\\'' . $key . '\\\'?\');"><span style="background: transparent url(\'media/square-orange-right.gif\') no-repeat top right">Delete</span></a></div>' . '<div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="Account.php?method=get&pk=' . $acc['PK'] . '"><span>Modify</span></a></div>';
       echo '<div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="Account.php?method=toggleactive&pk=' . $acc['PK'] . '" style="background: transparent url(\'media/square-';
       echo $acc['ACTIVE']?'red':'green';
       echo '-left.gif\') no-repeat top left"><span style="background: transparent url(\'media/square-';
       echo $acc['ACTIVE']?'red':'green';
       echo '-right.gif\') no-repeat top right">';
       echo $acc['ACTIVE']?'Deactive':'Active';
       echo '</span></a></div>' . '<h3></td></tr>';
       foreach ($GLOBALS['TEMPLATE']['Content']['AccSetting'][$key] as $accsetting)
       {
            echo '<tr><td width=5px><b>';
            echo ($accsetting===end($GLOBALS['TEMPLATE']['Content']['AccSetting'][$key]))?'-':'|';
            echo '</b></td><td>' . $GLOBALS['TEMPLATE']['Content']['ModuleList'][$accsetting['MODULE']] . '</td><td>' . $GLOBALS['TEMPLATE']['Content']['AutoMode'][$accsetting['MODULE']][$accsetting['AUTO_MODE']] . '</td>';
            echo '<td style="color: ';
            echo $accsetting['ACTIVE']?'green':'red';
            echo '">';
            echo $accsetting['ACTIVE']?'True':'False';
            echo '</td>';
            echo '<td>' . $accsetting['NUM_PER_DAY'] . '</td><td>' . $accsetting['QUEUE_SIZE'] . '</td><td>' . $accsetting['TIME_START'] . '</td><td>' . $accsetting['TIME_END'] . '</td><td>';
            echo '<div class="abuttonwrapper" style="float:left; width:auto;"><a class="abutton" href="AccSetting.php?method=toggleactive&pk=' . $accsetting['PK'] . '" style="background: transparent url(\'media/square-';
            echo $accsetting['ACTIVE']?'red':'green';
            echo '-left.gif\') no-repeat top left"><span style="background: transparent url(\'media/square-';
            echo $accsetting['ACTIVE']?'red':'green';
            echo '-right.gif\') no-repeat top right">';
            echo $accsetting['ACTIVE']?'Deactive':'Active';
            echo '</span></a></div>';
            echo '<div class="abuttonwrapper" style="float:left; width:auto;"><a class="abutton" href="AccSetting.php?method=get&pk=' . $accsetting['PK'] . '"><span>Modify</span></a></div>' . '</td>';
       }
   }
  ?>
 </table>
</div>
