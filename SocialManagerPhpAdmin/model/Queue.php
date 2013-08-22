<?php
class Queue
{
    private $PK;
    private $fields;

    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('STATUS' => null,
                              'ACCOUNT' => null,
                              'MODULE' => null,
                              'TYPE' => null,
                              'TITLE' => null,
                              'CONTENT' => null,
                              'EXTRA_CONTENT' => null,
                              'TAG' => null,
                              'IMAGE_FILE' => null,
                              'LINK' => null,
                              'OTHER_FIELD' => '{}',
                              'SCHEDULE_TIME' => '0000-00-00 00:00:00',
                              'RSS_SOURCE_PK' => null);
    }

    // override magic method to retrieve properties
    public function __get($field)
    {
        if ($field == 'PK')
        {
            return $this->PK;
        }
        else 
        {
            return $this->fields[$field];
        }
    }

    // override magic method to set properties
    public function __set($field, $value)
    {
        if (array_key_exists($field, $this->fields))
        {
            $this->fields[$field] = $value;
        }
    }
    
    public static function getlist($account=null, $module=null, $queuenum=30, $offset=0)
    {
        $lst = array();
        
        $query = 'SELECT * FROM queue WHERE 1';
        if ($account!=null) {
            $query .= sprintf(' AND ACCOUNT = %d',$account);
        }
        if ($module!=null)
        {
            $query .= sprintf(' AND MODULE = %d', $module);
        }
        $query .= sprintf(' AND STATUS = 1 ORDER BY PK ASC LIMIT %d, %d', $offset, $queuenum);
        $result = mysql_query($query, $GLOBALS['DB']);
        $rows_num = mysql_num_rows($result);
        if ($rows_num==0)
        {
            $query = 'SELECT COUNT(*) AS COUNT FROM queue WHERE STATUS = 1';
            if ($account!=null)
            {
                $query .= sprintf(' AND ACCOUNT = %d',$account);
            }
            if ($module!=null)
            {
                $query .= sprintf(' AND MODULE = %d', $module);
            }
            $result = mysql_query($query, $GLOBALS['DB']);
            if (mysql_num_rows($result))
            {
                $row = mysql_fetch_assoc($result);
                $pendingnum = $row['COUNT'];
            }
            mysql_free_result($result);
            $offset-=$pendingnum;
        }
        else
        {
            while ($row = mysql_fetch_assoc($result)) {
                array_push($lst, $row);
            }
            mysql_free_result($result);
            $queuenum-=$rows_num;
            $offset = 0;
        }
        if ($queuenum<=0) {return $lst;}
        $query = 'SELECT * FROM queue WHERE 1';
        if ($account!=null)
        {
            $query .= sprintf(' AND ACCOUNT = %d',$account);
        }
        if ($module!=null)
        {
            $query .= sprintf(' AND MODULE = %d', $module);
        }
        $query .= sprintf(' AND STATUS != 1 ORDER BY SCHEDULE_TIME DESC LIMIT %d, %d', $offset, $queuenum);
        $result = mysql_query($query, $GLOBALS['DB']);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($lst, $row);
        }
        mysql_free_result($result);
        
        return $lst;
    }
    
    public static function getrsspklist($account=null, $module=null)
    {
        $lst = array();
        
        $query = 'SELECT RSS_SOURCE_PK FROM queue WHERE 1';
        if ($account!=null) {
            $query .= sprintf(' AND ACCOUNT = %d',$account);
        }
        if ($module!=null)
        {
            $query .= sprintf(' AND MODULE = %d', $module);
        }
        $result = mysql_query($query, $GLOBALS['DB']);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($lst, $row['RSS_SOURCE_PK']);
        }
        mysql_free_result($result);
        
        return $lst;
    }

    // return an object populated based on the record's user id
    public static function gettotalnum($account=null,$module=null)
    {
        $num = 0;

        $query = 'SELECT COUNT(*) AS COUNT FROM queue WHERE 1';
        if ($account!=null) {
            $query .= sprintf(' AND ACCOUNT = %d',$account);
        }
        if ($module!=null)
        {
            $query .= sprintf(' AND MODULE = %d', $module);
        }
        $result = mysql_query($query, $GLOBALS['DB']);
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $num = $row['COUNT'];
        }
        mysql_free_result($result);

        return $num;
    }

    // return an object populated based on the record's user id
    public static function getpendingnum($account=null,$module=null)
    {
        $num = 0;

        $query = 'SELECT COUNT(*) AS COUNT FROM queue WHERE STATUS = 1';
        if ($account!=null) {
            $query .= sprintf(' AND ACCOUNT = %d',$account);
        }
        if ($module!=null)
        {
            $query .= sprintf(' AND MODULE = %d', $module);
        }
        $result = mysql_query($query, $GLOBALS['DB']);
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $num = $row['COUNT'];
        }
        mysql_free_result($result);

        return $num;
    }
    
    public static function getByPK($PK)
    {
        $qi = new Queue();
        $query = sprintf("SELECT `STATUS`, `ACCOUNT`, `MODULE`, `TYPE`, `TITLE`, `CONTENT`, `EXTRA_CONTENT`, `TAG`, `IMAGE_FILE`, `LINK`, `OTHER_FIELD`, `SCHEDULE_TIME`, `RSS_SOURCE_PK` FROM `queue` WHERE `PK` = %d",$PK);
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $qi->STATUS = $row['STATUS'];
            $qi->ACCOUNT = $row['ACCOUNT'];
            $qi->MODULE = $row['MODULE'];
            $qi->TYPE = $row['TYPE'];
            $qi->TITLE = $row['TITLE'];
            $qi->CONTENT = $row['CONTENT'];
            $qi->EXTRA_CONTENT = $row['EXTRA_CONTENT'];
            $qi->TAG = $row['TAG'];
            $qi->IMAGE_FILE = $row['IMAGE_FILE'];
            $qi->LINK = $row['LINK'];
            $qi->OTHER_FIELD = $row['OTHER_FIELD'];
            $qi->SCHEDULE_TIME = $row['SCHEDULE_TIME'];
            $qi->RSS_SOURCE_PK = $row['RSS_SOURCE_PK'];
            $qi->PK = $PK;
        }
        mysql_free_result($result);

        return $qi;
    }
    
    public function save()
    {
        if ($this->PK!=null)
        {
            $query = sprintf('UPDATE `queue` SET STATUS = %d, ACCOUNT = %d, MODULE = %d, TYPE = %d, TITLE = "%s", CONTENT = "%s", EXTRA_CONTENT = "%s", TAG = "%s", IMAGE_FILE = "%s", LINK = "%s", OTHER_FIELD = "%s", SCHEDULE_TIME = "%s", RSS_SOURCE_PK =%d WHERE PK = %d',
                $this->STATUS,
                $this->ACCOUNT,
                $this->MODULE,
                $this->TYPE,
                mysql_real_escape_string($this->TITLE),
                mysql_real_escape_string($this->CONTENT),
                mysql_real_escape_string($this->EXTRA_CONTENT),
                mysql_real_escape_string($this->TAG),
                $this->IMAGE_FILE,
                $this->LINK,
                mysql_real_escape_string($this->OTHER_FIELD),
                $this->SCHEDULE_TIME,
                $this->RSS_SOURCE_PK,
                $this->PK);
            mysql_query($query, $GLOBALS['DB']);
            var_dump($query);
        }
        else
        {
            $query = sprintf('INSERT INTO `queue` (STATUS, ACCOUNT, MODULE, TYPE, TITLE, CONTENT, EXTRA_CONTENT, TAG, IMAGE_FILE, LINK, OTHER_FIELD, SCHEDULE_TIME, RSS_SOURCE_PK) VALUES (%d, %d, %d, %d, "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", %d)',
                $this->STATUS,
                $this->ACCOUNT,
                $this->MODULE,
                $this->TYPE,
                mysql_real_escape_string($this->TITLE),
                mysql_real_escape_string($this->CONTENT),
                mysql_real_escape_string($this->EXTRA_CONTENT),
                mysql_real_escape_string($this->TAG),
                $this->IMAGE_FILE,
                $this->LINK,
                mysql_real_escape_string($this->OTHER_FIELD),
                $this->SCHEDULE_TIME,
                $this->RSS_SOURCE_PK);
            mysql_query($query, $GLOBALS['DB']);
            $this->PK = mysql_insert_id($GLOBALS['DB']);
        }
        return 0;
    }
    
    public function delete()
    {
        $query = sprintf('DELETE FROM queue WHERE PK = %d', $this->PK);
        mysql_query($query, $GLOBALS['DB']);
    }
    
    public static function deletelist($pk_list)
    {
        $query = sprintf('DELETE FROM queue WHERE PK IN (%s)', join(',',$pk_list));
        mysql_query($query, $GLOBALS['DB']);
    }
}
?>
