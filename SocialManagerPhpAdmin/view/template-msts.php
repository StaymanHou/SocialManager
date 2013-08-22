<div id="MS_MainConf">
 <h2>Main Config</h2>
 <table border=1>
  <tr>
   <td>Title</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->TITLE;?></td>
  </tr>
  <tr>
   <td>Caching time</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->CACHING_TIME . ' (day)';?></td>
  </tr>
  <tr>
   <td>Image file dir</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->IMAGE_FILE_DIR;?></td>
  </tr>
  <tr>
   <td>Load iteration</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->LOAD_ITERATION . ' (sec)';?></td>
  </tr>
  <tr>
   <td>Puller iteration</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->PULLER_ITERATION . ' (sec)';?></td>
  </tr>
  <tr>
   <td>Poster iteration</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->POSTER_ITERATION . ' (sec)';?></td>
  </tr>
 </table>
 <div class="abuttonwrapper"><a class="abutton" href="MainConf.php?method=get"><span>Modify main config</span></a></div>
</div>
<div id="MS_AccInfo">
 <h2>Accounts Info</h2>
 <table border=1>
  <tr>
   <td>Account Number</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['ActAccNum'] . ' / ' . $GLOBALS['TEMPLATE']['Content']['TotAccNum'] . ' active/total'?></td>
  </tr>
 </table>
</div>
<div id="MS_ModInfo">
 <h2>Module Info: Available Module</h2>
 <table border=1>
  <?php foreach ($GLOBALS['TEMPLATE']['Content']['ModuleList'] as $module) {echo '<tr><td>' . $module['NAME'] . '</tr></td>';}?>
 </table>
</div>
<div id="MS_RssInfo">
 <h2>Rss Info</h2>
 <table border=1>
  <tr>
   <td>Total</td>
   <td><?php echo $GLOBALS['TEMPLATE']['Content']['RssTotalNum'];?></td>
  </tr>
<?php
foreach ($GLOBALS['TEMPLATE']['Content']['RssNum'] as $key => $value) {
    echo '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
}
?>
 </table>
</div>
<div id="MS_QueueInfo">
 <h2>Queue Info: Pending Number</h2>
 <table border=1>
  <tr>
   <td>Total=<?php echo $GLOBALS['TEMPLATE']['Content']['QueueTotalNum'];?></td>
   <?php 
    foreach ($modlst as $mod)
    {
        echo '<td>' . $mod['NAME'] . '</td>';
    }
   ?>
   <?php
    foreach ($acclst as $acc)
    {
        echo '<tr><td>' . $acc['NAME'] . '</td>';
        foreach ($modlst as $mod)
        {
            echo '<td>' . $GLOBALS['TEMPLATE']['Content']['QueueNum'][$acc['NAME']][$mod['NAME']] . '</td>';
        }
        echo '</tr>';
    }
   ?>
  <tr>
 </table>
</div>

