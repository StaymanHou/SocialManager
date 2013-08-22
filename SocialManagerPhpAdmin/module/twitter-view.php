<p><?php echo $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$GLOBALS['MODULE']['RSS']['ACCOUNT']];?></p>
<form method="get" action="../module/twitter-controller.php" class="embedtweetbutton">
    <input type="hidden" name="submitted" value="1"/>
    <input type="hidden" name="method" value="tweet"/>
    <input type="hidden" name="RSSPK" value="<?php echo $GLOBALS['MODULE']['RSS']['PK'];?>"/>
    <input type="hidden" name="MODPK" value="<?php echo $GLOBALS['MODULE']['MODULEPK'];?>"/>
    <table style="border-width: 0">
        <tr><td><input type="submit" name ="action" value="Link Tweet(1-click)" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?>
            title="Click this button to perform a 1-click link tweet. It will take title of the rss as tweet content, and link of the rss as link at the end of tweet content." class="oneclick"/></td></tr>
        <tr <?php if ($GLOBALS['MODULE']['RSS']['IMAGE_FILE']==null) {echo 'style="display: none;"';}?>><td><input type="submit" name ="action" value="Image Tweet(1-click)" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?>
            title="Click this button to perform a 1-click image tweet. It will take title of the rss as tweet content, link of the rss as link at the end of tweet content, and try to use the image of rss as the image of the tweet." class="oneclick"/></td></tr>
        <tr><td><input type="submit" name ="action" value="Manual Tweet" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?> class="twitter-popup"/></td></tr>
    </table>
</form>
