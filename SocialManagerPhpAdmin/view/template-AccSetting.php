<form method="post"
 action="AccSetting.php?method=save">
 <table>
  <tr>
   <td><label for="ACCOUNTE">Account</label></td>
   <td><input type="text" name="ACCOUNT" id="ACCOUNT"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['AccountList'][$accset->ACCOUNT];?>" readonly/></td>
  </tr>
  <tr>
   <td><label for="MODULE">Module</label></td>
   <td><input type="text" name="MODULE" id="MODULE"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['ModuleList'][$accset->MODULE];?>" readonly/></td>
  </tr>
  <tr>
   <td><label for="USERNAME">Username</label></td>
   <td><input type="text" name="USERNAME" id="USERNAME"
    value="<?php echo $accset->USERNAME;?>"/></td>
  </tr>
  <tr>
   <td><label for="PSWD">Password</label></td>
   <td><input type="password" name="PSWD" id="PSWD"
    value="<?php echo $accset->PSWD;?>"/></td>
  </tr>
  <tr>
   <td><label for="OTHER_SETTING">Other setting<br/><a href="#" style="border-bottom: 1px dashed #999; display: inline; margin-left: 20px; font-size: 20px;" title="For facebook: please set something like &quot;{&quot;page_name&quot;: &quot;kpopstarzfacebookpage&quot;, &quot;page_path&quot;:&quot;/kpopstarz&quot;, &quot;page_id&quot;:&quot;122361684544935&quot;}&quot;&#013;For google+: please set something like &quot;{&quot;page_path&quot;: &quot;/b/110422726213868653185/&quot;}&quot;&#013;For tumblr: please set something like &quot;{&quot;blog_name&quot;: &quot;kpopstarztumblrblog&quot;,&quot;link_anchor_text&quot;:&quot;Continue Reading&quot;}&quot;&#013;For pinterest: please set something like &quot;{&quot;board_name&quot;: &quot;KpopStarzBoard&quot;}&quot;">&nbsp;?&nbsp;</a></label></td>
   <td><textarea name="OTHER_SETTING" id="OTHER_SETTING"
    cols="60" rows="4"><?php echo $accset->OTHER_SETTING;?></textarea></td>
  </tr>
  <tr>
   <td><label for="EXTRA_CONTENT">Extra common content<br/></label></td>
   <td><textarea name="EXTRA_CONTENT" id="EXTRA_CONTENT"
    cols="60" rows="1"><?php echo $accset->EXTRA_CONTENT;?></textarea></td>
  </tr>
  <tr>
   <td><label for="ACTIVE">Active</label></td>
   <td>
    <select name="ACTIVE" id="ACTIVE">
     <option value="0" <?php if ($accset->ACTIVE=="0") {echo 'selected="selected"';}?>>False</option>
     <option value="1" <?php if ($accset->ACTIVE=="1") {echo 'selected="selected"';}?>>True</option>
   </select> 
  </tr>
  <tr>
   <td><label for="AUTO_MODE">Auto mode</label></td>
   <td>
    <select name="AUTO_MODE" id="AUTO_MODE">
    <?php
        foreach ($GLOBALS['TEMPLATE']['Content']['AMList'] as $am)
        {
            echo '<option value="' . $am['PK'] . '" ';
            if ($accset->AUTO_MODE==$am['PK']) {echo 'selected="selected"';};
            echo '>' . $am['TITLE'] . '</option>';
        }
    ?>
    </select> 
   </td>
  </tr>
  <tr>
   <td><label for="TIME_START">Start time</label></td>
   <td><input type="text" name="TIME_START" id="TIME_START"
    value="<?php echo $accset->TIME_START;?>"/></td>
  </tr>
  <tr>
   <td><label for="TIME_END">End time</label></td>
   <td><input type="text" name="TIME_END" id="TIME_END"
    value="<?php echo $accset->TIME_END;?>"/></td>
  </tr>
  <tr>
   <td><label for="NUM_PER_DAY">Post # / day</label></td>
   <td><input type="text" name="NUM_PER_DAY" id="NUM_PER_DAY"
    value="<?php echo $accset->NUM_PER_DAY;?>"/></td>
  </tr>
  <tr>
   <td><label for="MIN_POST_INTERVAL">Min post interval(sec)</label></td>
   <td><input type="text" name="MIN_POST_INTERVAL" id="MIN_POST_INTERVAL"
    value="<?php echo $accset->MIN_POST_INTERVAL;?>"/></td>
  </tr>
  <tr>
   <td><label for="QUEUE_SIZE">Queue size</label></td>
   <td><input type="text" name="QUEUE_SIZE" id="QUEUE_SIZE"
    value="<?php echo $accset->QUEUE_SIZE;?>"/></td>
  </tr>
  <tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save" onclick="return myconfirm('Are you sure you want to save it?');"/>
    <div class="abuttonwrapper" style="float: right; width: auto;"><a class="abutton" href="accmng.php" onclick="return myconfirm('Are you sure you want to cancel it?');"><span>Cancel</span></a></div>
   </td>
   <td><input type="hidden" name="submitted" value="1"/></td>
   <td><input type="hidden" name="PK" value="<?php echo $accset->PK;?>"/></td>
  </tr>
 </table>
</form>

