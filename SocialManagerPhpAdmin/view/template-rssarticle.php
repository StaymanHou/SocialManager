<div class="rsscontent" style="text-align: left;">
<form method="post"
 action="rssarticle.php?method=save" class="embedrssform">
 <table>
  <tr>
   <td><label>Account</label></td>
   <td>
    <?php 
        $pk = $rsspost->PK;
        if (empty($pk)) {
            echo '<select name="ACCOUNT" id="ACCOUNT">';
            foreach ($GLOBALS['TEMPLATE']['Content']['AccountListPtN'] as $key => $value)
            {
                echo '<option value="' . $key . '" >' . $value . '</option>';
            }
            echo '</select> ';
        } else {
            echo '<input type="text" value="' . $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$rsspost->ACCOUNT] . '" readonly/>';
        }
    ?>
   </td>
  </tr>
  <tr>
   <td><label for="TITLE">Title</label></td>
   <td><input type="text" name="TITLE" id="TITLE"
    value="<?php echo $rsspost->TITLE;?>" size="45"/></td>
  </tr>
  <tr>
   <td><label for="DESCRIPTION">Description</label></td>
   <td><textarea name="DESCRIPTION" id="DESCRIPTION"
    cols="45" rows="2"><?php echo $rsspost->DESCRIPTION;?></textarea></td>
  </tr>
  <tr>
   <td><label for="CONTENT">Content</label></td>
   <td><textarea name="CONTENT" id="CONTENT"
    cols="45" rows="6"><?php echo $rsspost->CONTENT;?></textarea></td>
  </tr>
  <tr>
   <td><label for="TAG">Tags</label></td>
   <td><textarea name="TAG" id="TAG"
    cols="45" rows="2"><?php echo $rsspost->TAG;?></textarea></td>
  </tr>
  <tr>
   <td><label for="IMG">Image</label></td>
   <td><img src="img/rss/<?php echo $rsspost->IMAGE_FILE;?>" style="max-width: 450px; height: auto;"/></td>
  </tr>
  <tr>
   <td><label for="IMAGE_FILE">Img file <a href="#" title="twitter facebook and google+ will upload this file for image post">?</a></label></td>
   <td><input type="text" name="IMAGE_FILE" id="IMAGE_FILE"
    value="<?php echo $rsspost->IMAGE_FILE;?>" size="45"/></td>
  </tr>
  <tr>
   <td><label for="IMAGE_LINK">Img link <a href="#" title="tumblr and pinterest will us this link for image post">?</a></label></td>
   <td><input type="text" name="IMAGE_LINK" id="IMAGE_LINK"
    value="<?php echo $rsspost->IMAGE_LINK;?>" size="45"/></td>
  </tr>
  <tr>
   <td><label for="LINK">Post link</label></td>
   <td><input type="text" name="LINK" id="LINK"
    value="<?php echo $rsspost->LINK;?>" size="45"/></td>
  </tr>
  <tr>
   <td><label for="OTHER_FIELD">Other field</label></td>
   <td><textarea name="OTHER_FIELD" id="OTHER_FIELD"
    cols="45" rows="3"><?php echo $rsspost->OTHER_FIELD;?></textarea></td>
  </tr>
  <tr>
   <td><label for="SOCIAL_SCORE">Social score</label></td>
   <td><input type="text" name="SOCIAL_SCORE" id="SOCIAL_SCORE"
    value="<?php echo $rsspost->SOCIAL_SCORE;?>"/></td>
  </tr>
  <tr>
   <td><label for="CREATE_TIME">Create time</label></td>
   <td><input type="text" name="CREATE_TIME" id="CREATE_TIME"
    value="<?php if (empty($pk)) {echo date('Y-m-d H:i:s');} else {echo $rsspost->CREATE_TIME;}?>" readonly/></td>
  </tr>
  <tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save"/>
   </td>
   <td><input type="hidden" name="submitted" value="1"/></td>
   <td><input type="hidden" name="PK" value="<?php echo $rsspost->PK;?>"/></td>
  </tr>
 </table>
</form>
</div>
<?php
    if (!empty($pk)) {
        echo '<script type="text/javascript"> $(document).ready(function(){$(".embedrssform").submit( function () {$.post("rssarticle.php?method=save", $(this).serialize(), function(data){ alert(data); window.location.reload(true);}); return false; }); }); </script>';
    }
?>
