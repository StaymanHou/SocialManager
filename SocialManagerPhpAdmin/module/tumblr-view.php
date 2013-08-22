<p><?php echo $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$GLOBALS['MODULE']['RSS']['ACCOUNT']];?></p>
<form method="get" action="../module/tumblr-controller.php" class="embedtblogbutton">
    <input type="hidden" name="submitted" value="1"/>
    <input type="hidden" name="method" value="tblog"/>
    <input type="hidden" name="RSSPK" value="<?php echo $GLOBALS['MODULE']['RSS']['PK'];?>"/>
    <input type="hidden" name="MODPK" value="<?php echo $GLOBALS['MODULE']['MODULEPK'];?>"/>
    <table style="border-width: 0">
        <tr><td><input type="submit" name ="action" value="Link Tblog(1-click)" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?>
            title="Click this button to perform a 1-click link tblog. It will take title as tblog title, and description of the rss as tblog content, and link of the rss as tblog link." class="oneclick"/></td></tr>
        <tr <?php if ($GLOBALS['MODULE']['RSS']['IMAGE_FILE']==null) {echo 'style="display: none;"';}?>><td><input type="submit" name ="action" value="Image Tblog(1-click)" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?>
            title="Click this button to perform a 1-click image tblog. It will take title and description of the rss as tblog content, and link of the rss as link at the end of tblog content and click through link of the image." class="oneclick"/></td></tr>
        <tr><td><input type="submit" name ="action" value="Manual Tblog" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?> class="tblog-popup"/></td></tr>
    </table>
</form>
