<p><?php echo $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$GLOBALS['MODULE']['RSS']['ACCOUNT']];?></p>
<form method="get" action="../module/pinterest-controller.php" class="embedpinbutton">
    <input type="hidden" name="submitted" value="1"/>
    <input type="hidden" name="method" value="pin"/>
    <input type="hidden" name="RSSPK" value="<?php echo $GLOBALS['MODULE']['RSS']['PK'];?>"/>
    <input type="hidden" name="MODPK" value="<?php echo $GLOBALS['MODULE']['MODULEPK'];?>"/>
    <table style="border-width: 0">
        <tr <?php if ($GLOBALS['MODULE']['RSS']['IMAGE_FILE']==null) {echo 'style="display: none;"';}?>><td><input type="submit" name ="action" value="Pin(1-click)" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?>
            title="Click this button to perform a 1-click pin. It will take title + description of the rss as the share content, use the image of rss as the image of the pin, and take link of rss as the click through link of the pin." class="oneclick"/></td></tr>
        <tr><td><input type="submit" name ="action" value="Manual Pin" <?php if($GLOBALS['MODULE']['INQUEUE']) {echo 'disabled="disabled"';} ?> class="pin-popup"/></td></tr>
    </table>
</form>
