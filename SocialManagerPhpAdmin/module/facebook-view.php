<p><?php echo $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$GLOBALS['MODULE']['RSS']['ACCOUNT']];?></p>
<form method="get" action="../module/facebook-controller.php" class="embedfbsharebutton">
    <input type="hidden" name="submitted" value="1"/>
    <input type="hidden" name="method" value="share"/>
    <input type="hidden" name="RSSPK" value="<?php echo $GLOBALS['MODULE']['RSS']['PK'];?>"/>
    <input type="hidden" name="MODPK" value="<?php echo $GLOBALS['MODULE']['MODULEPK'];?>"/>
    <table style="border-width: 0">
        <tr><td><input type="submit" name ="action" value="Link Share(1-click)" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?>
            title="Click this button to perform a 1-click link share. It will take link of the rss as the link to share, and replace the content with the content of the rss." class="oneclick"/></td></tr>
        <tr <?php if ($GLOBALS['MODULE']['RSS']['IMAGE_FILE']==null) {echo 'style="display: none;"';}?>><td><input type="submit" name ="action" value="Image Share(1-click)" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?>
            title="Click this button to perform a 1-click image share. It will take content + link of the rss as the share content, and try to use the image of rss as the image of the share." class="oneclick"/></td></tr>
        <tr><td><input type="submit" name ="action" value="Manual Share" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?> class="fbshare-popup"/></td></tr>
    </table>
</form>
