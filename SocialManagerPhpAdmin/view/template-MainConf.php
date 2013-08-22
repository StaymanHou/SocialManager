<form method="post"
 action="MainConf.php?method=save">
 <table>
  <tr>
   <td title="The title of the configuration. There's no meaning so far."><label for="TITLE">Title</label></td>
   <td><input type="text" name="TITLE" id="TITLE"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->TITLE;?>"/></td>
  </tr>
  <tr>
   <td><label for="CACHING_TIME">Caching time</label></td>
   <td><input type="text" name="CACHING_TIME" id="CACHING_TIME"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->CACHING_TIME;?>"/></td>
  </tr>
  <tr>
   <td><label for="IMAGE_FILE_DIR">Image file dir</label></td>
   <td><input type="text" name="IMAGE_FILE_DIR" id="IMAGE_FILE_DIR"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->IMAGE_FILE_DIR;?>"/></td>
  </tr>
  <tr>
   <td><label for="LOAD_ITERATION">Load iteration</label></td>
   <td><input type="text" name="LOAD_ITERATION" id="LOAD_ITERATION"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->LOAD_ITERATION;?>"/></td>
  </tr>
  <tr>
   <td><label for="PULLER_ITERATION">Puller check iteration</label></td>
   <td><input type="text" name="PULLER_ITERATION" id="PULLER_ITERATION"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->PULLER_ITERATION;?>"/></td>
  </tr>
  <tr>
   <td><label for="POSTER_ITERATION">Post check iteration</label></td>
   <td><input type="text" name="POSTER_ITERATION" id="POSTER_ITERATION"
    value="<?php echo $GLOBALS['TEMPLATE']['Content']['MainConf']->POSTER_ITERATION;?>"/></td>
  </tr>
   <td> </td>
   <td>
    <input type="submit" style="float: left;" value="Save" onclick="return myconfirm('Are you sure you want to save the changes?');"/>
    <div class="abuttonwrapper" style="float: right; width: auto;"><a class="abutton" href="mainstatus.php" onclick="return myconfirm('Are you sure you want to cancel the changes?');"><span>Cancel</span></a></div>
   </td>
   <td><input type="hidden" name="submitted" value="1"/></td>
  </tr>
 </table>
</form>

