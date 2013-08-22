<div class="rsscontent" style="text-align: left;">
<?php 
    $tempothersetting = $accset->OTHER_SETTING;
    $tempjson = json_decode($tempothersetting);
    $circlenamelist = $tempjson->{'circle_name_list'};
?>
<form method="post"
 action="../module/googleplus-controller.php?method=save" class="embedgpshareform">
 <table>
  <tr>
   <td><label>Account</label></td>
   <td><input type="text" value="<?php echo $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$rsspost->ACCOUNT];?>" readonly/></td>
  </tr>
  <tr>
   <td><label for="TYPE">Type</label></td>
   <td><input type="text" name="TYPE" id="TYPE"
    value="1" size="45"/></td>
  </tr>
  <tr>
   <td><label for="CONTENT">G+ content</label></td>
   <td><textarea name="CONTENT" id="CONTENT" cols="45" rows="2"><?php echo $rsspost->TITLE;?></textarea></td>
  </tr>
  <tr>
   <td><label for="TAG"># tags</label></td>
   <td><textarea name="TAG" id="TAG" cols="45" rows="2"><?php echo $rsspost->TAG;?></textarea></td>
  </tr>
  <tr>
   <td><label for="IMG">Image</label></td>
   <td><img src="img/rss/<?php echo $rsspost->IMAGE_FILE;?>" style="max-width: 450px; height: auto;"/></td>
  </tr>
  <tr>
   <td><label for="LINK">Share link</label></td>
   <td><input type="text" name="LINK" id="LINK"
    value="<?php echo $rsspost->LINK;?>" size="45"/></td>
  </tr>
  <tr>
   <td><label for="OTHER_FIELD">To circles</label></td>
   <td><textarea name="OTHER_FIELD" id="OTHER_FIELD" cols="45" rows="2">{"circle_name_list": [<?php if (count($circlenamelist)) {echo '"'.join('","',$circlenamelist).'"';}?>]}</textarea></td>
  </tr>
  <tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save"/>
   </td>
   <input type="hidden" name="submitted" value="1"/>
   <input type="hidden" name="PK" value="<?php echo $rsspost->PK;?>"/>
   <input type="hidden" name="ACCOUNT" id="ACCOUNT" value="<?php echo $rsspost->ACCOUNT;?>"/>
   <input type="hidden" name="MODULE" id="MODULE" value="<?php echo $GLOBALS['TEMPLATE']['Content']['MODULE'];?>"/>
   <input type="hidden" name="TITLE" id="TITLE" value="<?php echo $rsspost->TITLE;?>"/>
   <input type="hidden" name="IMAGE_FILE" id="IMAGE_FILE" value="<?php echo $rsspost->IMAGE_FILE;?>"/>
   <input type="hidden" name="RSS_SOURCE_PK" id="RSS_SOURCE_PK" value="<?php echo $rsspost->PK;?>"/>
  </tr>
 </table>
</form>
</div>
<script type="text/javascript">
        $(document).ready(function(){
            $(".embedgpshareform").submit( function () {    
              $.post(
               '../module/googleplus-controller.php?method=save',
                $(this).serialize(),
                function(data){
                  alert(data);
                  window.location.reload(true);
                }
              );
              return false;   
            });   
        });
</script>
