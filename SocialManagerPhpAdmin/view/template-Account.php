<form method="post"
 action="Account.php?method=save">
 <table>
  <tr>
   <td><label for="NAME">Name</label></td>
   <td><input type="text" name="NAME" id="NAME"
    value="<?php echo $acc->NAME;?>"/></td>
  </tr>
  <tr>
   <td><label for="RSS_URL">Rss url</label></td>
   <td><input type="text" name="RSS_URL" id="RSS_URL"
    value="<?php echo $acc->RSS_URL;?>" size="60"/></td>
  </tr>
  <tr>
   <td><label for="TAG_LIMIT">Tag limit</label></td>
   <td><input type="number" name="TAG_LIMIT" id="TAG_LIMIT"
    value="<?php echo $acc->TAG_LIMIT;?>"/></td>
  </tr>
  <tr>
   <td><label for="ACTIVE">Active</label></td>
   <td>
    <select name="ACTIVE" id="ACTIVE">
     <option value="0" <?php if ($acc->ACTIVE=="0") {echo 'selected="selected"';}?>>False</option>
     <option value="1" <?php if ($acc->ACTIVE=="1") {echo 'selected="selected"';}?>>True</option>
    </select> 
   </td>
  </tr>
  <tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save" onclick="return myconfirm('Are you sure you want to save it?');"/>
    <div class="abuttonwrapper" style="float: right; width: auto;"><a class="abutton" href="accmng.php" onclick="return myconfirm('Are you sure you want to cancel it?');"><span>Cancel</span></a></div>
   </td>
   <td><input type="hidden" name="submitted" value="1"/></td>
   <td><input type="hidden" name="PK" value="<?php echo $acc->PK;?>"/></td>
  </tr>
 </table>
</form>

