<form method="post"
 action="Module.php?method=save">
 <table>
  <tr>
   <td><label for="NAME">Name</label></td>
   <td><input type="text" name="NAME" id="NAME"
    value="<?php echo $mod->NAME;?>"/></td>
  </tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save" onclick="return myconfirm('Are you sure you want to save it?');"/>
    <div class="abuttonwrapper" style="float: right; width: auto;"><a class="abutton" href="modmng.php" onclick="return myconfirm('Are you sure you want to cancel it?');"><span>Cancel</span></a></div>
   </td>
   <td><input type="hidden" name="submitted" value="1"/></td>
   <td><input type="hidden" name="PK" value="<?php echo $mod->PK;?>"/></td>
  </tr>
 </table>
</form>

