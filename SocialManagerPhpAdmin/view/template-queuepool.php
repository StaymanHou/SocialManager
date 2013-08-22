<div id="QP_Main">
    <form method="post" action="queue.php?method=deletelist">
        <input type="submit" style="position:fixed;left:5px;top:200px;" value="Delete checked queue"/>
        <table id="QP_Main_FixTable" width=960px>
            <thead>
                <tr class="modulequeue">
                    <?php 
                        $modnum = count($GLOBALS['TEMPLATE']['Content']['ModuleListPtN']);
                        foreach ($GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] as $key => $value)
                        {
                            $qpntsum = is_null($GLOBALS['TEMPLATE']['curacc'])?array_sum($GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$key]):$GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$key][$GLOBALS['TEMPLATE']['curacc']];
                            $qstsum = is_null($GLOBALS['TEMPLATE']['curacc'])?array_sum($GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$key]):$GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$key][$GLOBALS['TEMPLATE']['curacc']];
                            echo '<td width="' . 100/$modnum . '%"><div class="queuebar" style="width: ' . (100*$qpntsum/$qstsum) . '%">';
                            echo '<div style="float: left;">' . $qpntsum . '/' . $qstsum . '</div></div>';
                            echo '</td>';
                        }
                    ?>
                </tr>
                <tr>
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
                    for ($i=0; $i<$GLOBALS['TEMPLATE']['curnum']; $i++)
                    {
                        echo '<tr class="';
                        echo $even?'even':'odd';
                        echo '">';
                        foreach ($modlst as $mod)
                        {
                            if (empty($GLOBALS['TEMPLATE']['Content']['QueueList'][$mod['PK']])) {echo '<td class="empty">empty</td>'; continue;}
                            $qi = array_shift($GLOBALS['TEMPLATE']['Content']['QueueList'][$mod['PK']]);
                            echo '<td><div class="queuearticle" status="';
                            switch ($qi['STATUS']) {
                                case 1:
                                    echo "pending";
                                    break;
                                case 2:
                                    echo "posted";
                                    break;
                                case 3:
                                    echo "postfail";
                                    break;
                            }
                            echo '" pk="' . $qi['PK'] . '"><p style="font-size: 10px;">' . $qi['TITLE'] . '</p>';
                            if (!empty($qi['IMAGE_FILE']))
                            {
                                echo '<div style="width: 100%;"><img src="img/rss/' . $qi['IMAGE_FILE'] . '" style="max-width: 150px; height: auto;"/></div>';
                            } else {
                                echo '<div class="no-img">No Image</div>';
                            }
                            if ($qi['SCHEDULE_TIME']=='0000-00-00 00:00:00')
                            {
                                echo '<p style="font-size: 10px;">Not Scheduled Yet</p>';
                            } else {
                                echo '<p style="font-size: 10px;">Scheduled Time: ' . $qi['SCHEDULE_TIME'] . '</p>';
                            }
                            if ($qi['STATUS']==1) {echo '<div class="abuttongroup" style="overflow:hidden;"><div class="abuttonwrapper" style="float:left; width:auto;"><a class="abutton" href="queue.php?method=get&pk=' . $qi['PK'] . '"><span>Modify</span></a></div>' . '<div class="abuttonwrapper" style="float:right; width:auto;"><a class="abutton" href="queue.php?method=delete&pk=' . $qi['PK'] . '" style="background: transparent url(\'media/square-orange-left.gif\') no-repeat top left" onclick="return myconfirm(\'Are you sure you want to delete it?\');"><span style="background: transparent url(\'media/square-orange-right.gif\') no-repeat top right">Delete</span></a></div><div style="float: right;"><input type="checkbox" name="pk_list[]" value="' . $qi['PK'] . '"/></div></div></td>';}
                            else if ($qi['STATUS']==3) {echo '<div class="abuttongroup" style="overflow:hidden;"><div class="abuttonwrapper" style="float:left; width:auto;"><a class="abutton" href="queue.php?method=reset&pk=' . $qi['PK'] . '" style="background: transparent url(\'media/square-blue-left.gif\') no-repeat top left"><span style="background: transparent url(\'media/square-blue-right.gif\') no-repeat top right">Retry</span></a></div></td>';}
                            echo '</div></td>';
                        } 
                        echo '</tr>';
                        $even = $even?false:true;
                    }
                ?>
            </tbody>
        </table>
    </form>
</div>

<div id="QP_Widget">
    <form method="get" action="queuepool.php">
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
            <tr><td><label for="queuenum"># per page</label></td>
            <td>
                <select name="queuenum" id="queuenum" onchange="this.form.submit()">
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

<!-- preload the images -->
<div style='display:none'>
	<img src='img/basic/x.png' alt='' />
</div>
<!-- preload fixed header -->
<table id="header-fixed" width="960px"></table>
