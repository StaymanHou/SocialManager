<?php
class AutoMode
{
    private $PK;
    private $fields;

    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('MODULE' => null,
                              'CODE' => null,
                              'TITLE' => null,
                              'OTHER_SETTING' => null);
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

    // return an object populated based on the record's user id 
    public static function getlist($module=null)
    {
        $lst = array();

        $query = "SELECT `PK`, `MODULE`, `CODE`, `TITLE`, `OTHER_SETTING` FROM `auto_mode` WHERE 1";
        if ($module!=null)
        {
            $query .= sprintf(" AND MODULE = %d", $module);
        }
        $query .= " ORDER BY PK ASC";
        $result = mysql_query($query, $GLOBALS['DB']);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($lst, $row);
        }
        mysql_free_result($result);

        return $lst;
    }
    
    public static function getByPK($PK)
    {
        $am = new AutoMode();
        $query = sprintf("SELECT `MODULE`, `CODE`, `TITLE`, `OTHER_SETTING` FROM `auto_mode` WHERE `PK` = %d",$PK);
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $am->MODULE = $row['MODULE'];
            $am->CODE = $row['CODE'];
            $am->TITLE = $row['TITLE'];
            $am->OTHER_SETTING = $row['OTHER_SETTING'];
            $am->PK = $PK;
        }
        mysql_free_result($result);

        return $am;
    }
    
    public function save()
    {
        if ($this->PK!=null)
        {
            $query = sprintf('UPDATE `auto_mode` SET `MODULE`=%d, `CODE`=%d, `TITLE`="%s", `OTHER_SETTING`="%s" WHERE `PK`=%d',
                $this->MODULE,
                $this->CODE,
                $this->TITLE,
                mysql_real_escape_string($this->OTHER_SETTING),
                $this->PK);
            mysql_query($query, $GLOBALS['DB']);
        } else {
            $query = sprintf('INSERT INTO `auto_mode` (`MODULE`, `CODE`, `TITLE`, `OTHER_SETTING`) VALUES (%d, %d, "%s", "%s")',
                $this->MODULE,
                $this->CODE,
                $this->TITLE,
                mysql_real_escape_string($this->OTHER_SETTING));
            mysql_query($query, $GLOBALS['DB']);
        }
    }
    
    public static function delete($MODPK, $PK)
    {
        $query = sprintf('SELECT PK FROM `auto_mode` WHERE MODULE = %d AND CODE = 1', $MODPK);
        $result = mysql_query($query, $GLOBALS['DB']);
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $amoffpk = $row['PK'];
        }
        mysql_free_result($result);
        $query = sprintf('UPDATE `acc_setting` SET `AUTO_MODE` = %d WHERE `AUTO_MODE` = %d', $amoffpk, $PK);
        mysql_query($query, $GLOBALS['DB']);
        $query = sprintf('DELETE FROM `auto_mode` WHERE PK = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        return 0;
    }    
    

}
?>
