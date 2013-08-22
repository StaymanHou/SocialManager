<form method="post"
 action="queue.php?method=save">
 <table>
  <tr>
   <td><label>Status</label></td>
   <td>
    <select name="STATUS" id="STATUS">
    <?php
        foreach ($GLOBALS['TEMPLATE']['Content']['StatusListPtN'] as $key => $value)
        {
            echo '<option value="' . $key . '" ';
            if ($qi->STATUS==$key) {echo 'selected="selected"';};
            echo '>' . $value . '</option>';
        }
    ?>
    </select> 
   </td>
  </tr>
  <tr>
   <td><label>Account</label></td>
   <td>
    <select name="ACCOUNT" id="ACCOUNT">
    <?php
        foreach ($GLOBALS['TEMPLATE']['Content']['AccountListPtN'] as $key => $value)
        {
            echo '<option value="' . $key . '" ';
            if ($qi->ACCOUNT==$key) {echo 'selected="selected"';};
            echo '>' . $value . '</option>';
        }
    ?>
    </select> 
   </td>
  </tr>
  <tr>
   <td><label>Module</label></td>
   <td>
    <select name="MODULE" id="MODULE">
    <?php
        foreach ($GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] as $key => $value)
        {
            echo '<option value="' . $key . '" ';
            if ($qi->MODULE==$key) {echo 'selected="selected"';};
            echo '>' . $value . '</option>';
        }
    ?>
    </select> 
   </td>
  </tr>
  <tr>
   <td><label for="TYPE">Type</label></td>
   <td><input type="text" name="TYPE" id="TYPE"
    value="<?php echo $qi->TYPE;?>" size="3"/></td>
  </tr>
  <tr>
   <td><label for="TITLE">Title</label></td>
   <td><textarea name="TITLE" id="TITLE"
    cols="45" rows="2"><?php echo $qi->TITLE;?></textarea></td>
  </tr>
  <tr>
   <td><label for="CONTENT">Content</label></td>
   <td><textarea name="CONTENT" id="CONTENT"
    cols="45" rows="6"><?php echo $qi->CONTENT;?></textarea></td>
  </tr>
   <td><label for="EXTRA_CONTENT">Extra content</label></td>
   <td><textarea name="EXTRA_CONTENT" id="EXTRA_CONTENT"
    cols="45" rows="1"><?php echo $qi->EXTRA_CONTENT;?></textarea></td>
  </tr>
  <tr>
   <td><label for="TAG">Tags</label></td>
   <td><textarea name="TAG" id="TAG"
    cols="45" rows="2"><?php echo $qi->TAG;?></textarea></td>
  </tr>
  <tr>
   <td><label for="IMG">Image</label></td>
   <td><img src="img/rss/<?php echo $qi->IMAGE_FILE;?>" style="max-width: 450px; height: auto;"/></td>
  </tr>
  <tr>
   <td><label for="IMAGE_FILE">Img file <a href="#" title="twitter facebook and google+ will upload this file for image post">?</a></label></td>
   <td><input type="text" name="IMAGE_FILE" id="IMAGE_FILE"
    value="<?php echo $qi->IMAGE_FILE;?>" size="45"/></td>
  </tr>
  <tr>
   <td><label for="LINK">Post link</label></td>
   <td><input type="text" name="LINK" id="LINK"
    value="<?php echo $qi->LINK;?>" size="45"/></td>
  </tr>
  <tr>
   <td><label for="OTHER_FIELD">Other field</label></td>
   <td><textarea name="OTHER_FIELD" id="OTHER_FIELD"
    cols="45" rows="3"><?php echo $qi->OTHER_FIELD;?></textarea></td>
  </tr>
  <tr>
   <td><label for="SCHEDULE_TIME">Schedule time</label></td>
   <td><input type="text" name="SCHEDULE_TIME" id="SCHEDULE_TIME"
    value="<?php echo $qi->SCHEDULE_TIME;?>"/></td>
  </tr>
  <tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save" onclick="return myconfirm('Are you sure you want to save it?');"/>
    <div class="abuttonwrapper" style="float: right; width: auto;"><a class="abutton" href="<?php echo $redirect;?>" onclick="return myconfirm('Are you sure you want to cancel it?');"><span>Cancel</span></a></div>
   </td>
   <td><input type="hidden" name="submitted" value="1"/></td>
   <td><input type="hidden" name="PK" value="<?php echo $qi->PK;?>"/></td>
   <td><input type="hidden" name="redirect" value="<?php echo $redirect;?>"/></td>
  </tr>
 </table>
</form>
