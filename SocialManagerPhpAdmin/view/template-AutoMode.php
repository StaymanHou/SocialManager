<form method="post"
 action="AutoMode.php?method=save">
 <table>
  <tr>
   <td><label for="MODULENAME">Module</label></td>
   <td><input type="text" name="MODULENAME" id="MODULENAME"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['ModuleList'][$am->MODULE];?>" readonly/></td>
  </tr>
  <tr>
   <td><label for="CODE">Code</label></td>
   <td><input type="text" name="CODE" id="CODE"
    value="<?php echo $am->CODE;?>"/></td>
  </tr>
  <tr>
   <td><label for="TITLE">Title</label></td>
   <td><input type="text" name="TITLE" id="TITLE"
    value="<?php echo $am->TITLE;?>"/></td>
  </tr>
  <tr>
   <td><label for="OTHER_SETTING">Other setting</label></td>
   <td><textarea name="OTHER_SETTING" id="OTHER_SETTING"
    cols="60" rows="4"><?php echo $am->OTHER_SETTING;?></textarea></td>
  </tr>
  <tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save" onclick="return myconfirm('Are you sure you want to save it?');"/>
    <div class="abuttonwrapper" style="float: right; width: auto;"><a class="abutton" href="modmng.php" onclick="return myconfirm('Are you sure you want to cancel it?');"><span>Cancel</span></a></div>
   </td>
   <td><input type="hidden" name="submitted" value="1"/></td>
   <td><input type="hidden" name="MODULE" value="<?php echo $am->MODULE;?>"/></td>
   <td><input type="hidden" name="PK" value="<?php echo $am->PK;?>"/></td>
  </tr>
 </table>
</form>

