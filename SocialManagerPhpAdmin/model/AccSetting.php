<?php
class AccSetting
{
    private $PK;
    private $fields;
    
    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('ACCOUNT' => null,
                              'MODULE' => null,
                              'USERNAME' => null,
                              'PSWD' => null,
                              'OTHER_SETTING' => null,
                              'EXTRA_CONTENT' => '',
                              'ACTIVE' => 0,
                              'AUTO_MODE' => null,
                              'TIME_START' => '00:00:00',
                              'TIME_END' => '00:00:00',
                              'NUM_PER_DAY' => 24,
                              'MIN_POST_INTERVAL' => 24,
                              'QUEUE_SIZE' => 48);
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
    
    public static function getByPK($PK)
    {
        $accset = new AccSetting();
        $query = sprintf("SELECT `ACCOUNT`, `MODULE`, `USERNAME`, `OTHER_SETTING`, `EXTRA_CONTENT`, `ACTIVE`, `AUTO_MODE`, `TIME_START`, `TIME_END`, `NUM_PER_DAY`, `MIN_POST_INTERVAL`, `QUEUE_SIZE` FROM `acc_setting` WHERE `PK` = %d",$PK);
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $accset->ACCOUNT = $row['ACCOUNT'];
            $accset->MODULE = $row['MODULE'];
            $accset->USERNAME = $row['USERNAME'];
            $accset->OTHER_SETTING = $row['OTHER_SETTING'];
            $accset->EXTRA_CONTENT = $row['EXTRA_CONTENT'];
            $accset->ACTIVE = $row['ACTIVE'];
            $accset->AUTO_MODE = $row['AUTO_MODE'];
            $accset->TIME_START = $row['TIME_START'];
            $accset->TIME_END = $row['TIME_END'];
            $accset->NUM_PER_DAY = $row['NUM_PER_DAY'];
            $accset->MIN_POST_INTERVAL = $row['MIN_POST_INTERVAL'];
            $accset->QUEUE_SIZE = $row['QUEUE_SIZE'];
            $accset->PK = $PK;
        }
        mysql_free_result($result);

        return $accset;
    }

    public static function getByAccMod($Acc, $Mod)
    {
        $accset = new AccSetting();
        $query = sprintf("SELECT `PK`, `ACCOUNT`, `MODULE`, `USERNAME`, `OTHER_SETTING`, `EXTRA_CONTENT`, `ACTIVE`, `AUTO_MODE`, `TIME_START`, `TIME_END`, `NUM_PER_DAY`, `MIN_POST_INTERVAL`, `QUEUE_SIZE` FROM `acc_setting` WHERE `ACCOUNT` = %d AND `MODULE` = %d", $Acc, $Mod);
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $accset->ACCOUNT = $row['ACCOUNT'];
            $accset->MODULE = $row['MODULE'];
            $accset->USERNAME = $row['USERNAME'];
            $accset->OTHER_SETTING = $row['OTHER_SETTING'];
            $accset->EXTRA_CONTENT = $row['EXTRA_CONTENT'];
            $accset->ACTIVE = $row['ACTIVE'];
            $accset->AUTO_MODE = $row['AUTO_MODE'];
            $accset->TIME_START = $row['TIME_START'];
            $accset->TIME_END = $row['TIME_END'];
            $accset->NUM_PER_DAY = $row['NUM_PER_DAY'];
            $accset->MIN_POST_INTERVAL = $row['MIN_POST_INTERVAL'];
            $accset->QUEUE_SIZE = $row['QUEUE_SIZE'];
            $accset->PK = $row['PK'];
        }
        mysql_free_result($result);

        return $accset;
    }
    
    // return an object populated based on the record's user id 
    public static function getlist($account=null)
    {
        $lst = array();
        
        $query = "SELECT `PK`, `ACCOUNT`, `MODULE`, `USERNAME`, `OTHER_SETTING`, `EXTRA_CONTENT`, `ACTIVE`, `AUTO_MODE`, `TIME_START`, `TIME_END`, `NUM_PER_DAY`, `MIN_POST_INTERVAL`, `QUEUE_SIZE` FROM `acc_setting` WHERE 1";
        if ($account!=null)
        {
            $query .= sprintf(" AND ACCOUNT = %d", $account);
        }
        $query .= " ORDER BY PK ASC";
        $result = mysql_query($query, $GLOBALS['DB']);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($lst, $row);
        }
        mysql_free_result($result);

        return $lst;
    }
    
    // return an object populated based on the record's user id 
    public static function getsize($account,$module)
    {
        $num = 0;
        $query = sprintf("SELECT `QUEUE_SIZE` FROM `acc_setting` WHERE ACCOUNT = %d AND MODULE = %d", $account, $module);
        $result = mysql_query($query, $GLOBALS['DB']);
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $num = $row['QUEUE_SIZE'];
        }
        mysql_free_result($result);

        return $num;
    }
    
    public function save($withpw=false)
    {
        if ($this->PK!=null)
        {
            if ($withpw) {
                $query = sprintf('UPDATE `acc_setting` SET `USERNAME` = "%s", `PSWD` = DES_ENCRYPT("%s","%s"), OTHER_SETTING = "%s", EXTRA_CONTENT = "%s", ACTIVE = %s, AUTO_MODE = %d, TIME_START = "%s", TIME_END = "%s", NUM_PER_DAY = %d, MIN_POST_INTERVAL = %d, QUEUE_SIZE = %d WHERE PK = %d',
                    $this->USERNAME,
                    $this->PSWD,
                    DB_KEY,
                    mysql_real_escape_string($this->OTHER_SETTING),
                    mysql_real_escape_string($this->EXTRA_CONTENT),
                    $this->ACTIVE?'True':'False',
                    $this->AUTO_MODE,
                    $this->TIME_START,
                    $this->TIME_END,
                    $this->NUM_PER_DAY,
                    $this->MIN_POST_INTERVAL,
                    $this->QUEUE_SIZE,
                    $this->PK);
                mysql_query($query, $GLOBALS['DB']);
            } else {
                $query = sprintf('UPDATE `acc_setting` SET `USERNAME` = "%s", OTHER_SETTING = "%s", EXTRA_CONTENT = "%s", ACTIVE = %s, AUTO_MODE = %d, TIME_START = "%s", TIME_END = "%s", NUM_PER_DAY = %d, MIN_POST_INTERVAL = %d, QUEUE_SIZE = %d WHERE PK = %d',
                    $this->USERNAME,
                    mysql_real_escape_string($this->OTHER_SETTING),
                    mysql_real_escape_string($this->EXTRA_CONTENT),
                    $this->ACTIVE?'True':'False',
                    $this->AUTO_MODE,
                    $this->TIME_START,
                    $this->TIME_END,
                    $this->NUM_PER_DAY,
                    $this->MIN_POST_INTERVAL,
                    $this->QUEUE_SIZE,
                    $this->PK);
                mysql_query($query, $GLOBALS['DB']);
            }
        }
        return 0;
    }
    
    public static function toggleactive($PK)
    {
        $query = sprintf('UPDATE `acc_setting` SET `ACTIVE`=(!`ACTIVE`) WHERE PK = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        return 0;
    }
}
?>
