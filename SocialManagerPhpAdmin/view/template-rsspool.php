<div id="RP_Main">
    <table id="RP_Main_FixTable" width=960px>
        <thead>
            <tr class="modulequeue">
                <td width="20%">Queue</td>
                <?php 
                    $modnum = count($GLOBALS['TEMPLATE']['Content']['ModuleListPtN']);
                    foreach ($GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] as $key => $value)
                    {
                        $qpntsum = is_null($GLOBALS['TEMPLATE']['curacc'])?array_sum($GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$key]):$GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$key][$GLOBALS['TEMPLATE']['curacc']];
                        $qstsum = is_null($GLOBALS['TEMPLATE']['curacc'])?array_sum($GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$key]):$GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$key][$GLOBALS['TEMPLATE']['curacc']];
                        echo '<td width="' . 80/$modnum . '%"><div class="queuebar" style="width: ' . (100*$qpntsum/$qstsum) . '%">';
                        echo '<div style="float: left;">' . $qpntsum . '/' . $qstsum . '</div></div>';
                        echo '</td>';
                    }
                ?>
            </tr>
            <tr>
                <td>Rss Content <a class="abutton" href="rssarticle.php?method=create"><span style="font-size: 12px">Create</span></a></td>
                <?php
                    foreach ($GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] as $key => $value)
                    {
                        echo '<td>' . $value . '</td>';
                    }
                ?>
            </tr>
        <thead>
        <tbody>
            <?php
                $even = false;
                foreach ($GLOBALS['TEMPLATE']['Content']['RssList'] as $rss)
                {
                    $in_every_queue = true;
                    foreach ($GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] as $key => $value) {
                        if (!in_array($rss['PK'], $GLOBALS['TEMPLATE']['Content']['QueueRSSPKTable'][$key])) {$in_every_queue = false; break;}
                    }
                    if ($in_every_queue) {continue;}
                    echo '<tr class="';
                    echo $even?'even':'odd';
                    echo '">';
                    echo '<td><div class="rssarticle" pk="' . $rss['PK'] . '"><p style="font-size: 10px;">' . $rss['TITLE'] . '</p>';
                    if (isset($rss['IMAGE_FILE'])&&!empty($rss['IMAGE_FILE']))
                    {
                        echo '<div style="width: 100%;"><img src="img/rss/' . $rss['IMAGE_FILE'] . '" style="max-width: 150px; height: auto;"/><div>';
                    }
                    echo '</div><p style="font-size: 10px;">' . $rss['CREATE_TIME'] . '</p></td>';
                    foreach ($GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] as $key => $value)
                    {
                        echo '<td><div class="td-module-' . $value . '" style="padding: 5px;">';
                        $GLOBALS['MODULE']['MODULEPK'] = $key;
                        $GLOBALS['MODULE']['RSS'] = $rss;
                        $GLOBALS['MODULE']['INQUEUE'] = false;
                        if (in_array($rss['PK'], $GLOBALS['TEMPLATE']['Content']['QueueRSSPKTable'][$key])) {$GLOBALS['MODULE']['INQUEUE'] = true;}
                        include '../module/'.$value.'-view.php';
                        echo '</div></td>';
                    }
                    echo '</tr>';
                    $even = $even?false:true;
                }
            ?>
        </tbody>
    </table>
</div>

<div id="RP_Widget">
    <form method="get" action="rsspool.php">
        <table border=1>
            <tr><td><label for="account">Account</label></td>
            <td>
                <select name="account" id="account" onchange="this.form.submit()">
                <?php
                    echo '<option value="null" ';
                    if ($GLOBALS['TEMPLATE']['curacc']==null) {echo 'selected="selected"';}
                    echo '>All</option>';
                    foreach ($GLOBALS['TEMPLATE']['Content']['AccountListPtN'] as $key => $value)
                    {
                        echo '<option value="' . $key . '" ';
                        if ($GLOBALS['TEMPLATE']['curacc']==$key) {echo 'selected="selected"';}
                        echo '>' . $value . '</option>';
                    }
                ?>
                </select> 
            </td>
            </tr>
            <tr><td><label for="rssnum"># per page</label></td>
            <td>
                <select name="rssnum" id="rssnum" onchange="this.form.submit()">
                <?php
                    $numlst = array('10','30','50','100','200');
                    foreach ($numlst as $num)
                    {
                        echo '<option value="' . $num . '" ';
                        if ($GLOBALS['TEMPLATE']['curnum']==$num) {echo 'selected="selected"';}
                        echo '>' . $num . '</option>';
                    }
                ?>
                </select> 
            </td>
            </tr>
            <tr>
            <td><label for="action">Page</label></td>
            <td><input type="submit" name="action" value="<" /><input type="submit" name="action" value=">" /></td>
            <input type="hidden" name="offset" value="<?php echo $GLOBALS['TEMPLATE']['curoffset'];?>">
            </tr>
        </table>
    </form>
</div>

<div id="RP_Status" style="display: none;">
    something
</div>

<!-- preload the images -->
<div style='display:none'>
	<img src='img/basic/x.png' alt='' />
</div>
<!-- preload fixed header -->
<table id="header-fixed" width="960px"></table>
