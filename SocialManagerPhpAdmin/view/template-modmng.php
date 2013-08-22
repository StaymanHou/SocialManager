<div id="MM_Main">
 <h2>Detailed List</h2>
 <div class="abuttonwrapper"><a class="abutton" href="Module.php?method=create"><span>Create new module</span></a></div>
  <table border=1 width=720px>
  <tr><td width=5px></td><td><b>Code</b></td><td><b>Title</b></td><td><b>Setting</b></td></tr>
  <?php
   foreach ($GLOBALS['TEMPLATE']['Content']['ModuleList'] as $mod)
   {
       $key = $mod['NAME'];
       echo '<tr><td colspan=4><h3>' . $key . '<div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="Module.php?method=delete&pk=' . $mod['PK'] . '" style="background: transparent url(\'media/square-orange-left.gif\') no-repeat top left" onclick="return myconfirm(\'Are you sure you want to delete the module \\\'' . $key . '\\\'?\');"><span style="background: transparent url(\'media/square-orange-right.gif\') no-repeat top right">Delete</span></a></div>' . '<div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="Module.php?method=get&pk=' . $mod['PK'] . '"><span>Modify</span></a></div>' . '<h3></td></tr>';
       echo '<tr><td width=5px><b>|</b></td><td colspan=3><div class="abuttonwrapper"><a class="abutton" href="AutoMode.php?method=create&modpk=' . $mod['PK'] . '"><span>Create new auto_mode</span></a></div></td></tr>';
       foreach ($GLOBALS['TEMPLATE']['Content']['AutoMode'][$key] as $automode)
       {
           echo '<tr><td width=5px><b>';
           echo ($automode===end($GLOBALS['TEMPLATE']['Content']['AutoMode'][$key]))?'-':'|';
           echo '</b></td><td>' . $automode['CODE'] . '</td><td>' . $automode['TITLE'];
           if ($automode['CODE']!=1) {echo '<div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="AutoMode.php?method=delete&modpk=' . $mod['PK'] . '&pk=' . $automode['PK'] . '" style="background: transparent url(\'media/square-orange-left.gif\') no-repeat top left" onclick="return myconfirm(\'Are you sure you want to delete the auto_mode \\\'' . $automode['TITLE'] . '\\\'?\');"><span style="background: transparent url(\'media/square-orange-right.gif\') no-repeat top right">Delete</span></a></div>';}
           echo '<div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="AutoMode.php?method=get&pk=' . $automode['PK'] . '"><span>Modify</span></a></div>' . '</td><td>' . $automode['OTHER_SETTING'] . '</td></tr>';
       }
   }
  ?>
 </table>
</div>

