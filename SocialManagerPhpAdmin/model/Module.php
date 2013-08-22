<?php
class Module
{
    private $PK;
    private $fields;

    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('NAME' => null);
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
        $mod = new Module();
        $query = sprintf("SELECT `NAME` FROM `module` WHERE `PK` = %d",$PK);
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $mod->NAME = $row['NAME'];
            $mod->PK = $PK;
        }
        mysql_free_result($result);

        return $mod;
    }

    // return an object populated based on the record's user id 
    public static function getlist()
    {
        $lst = array();

        $query = sprintf("SELECT `PK`, `NAME` FROM `module`");
        $result = mysql_query($query, $GLOBALS['DB']);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($lst, $row);
        }
        mysql_free_result($result);

        return $lst;
    }
    
    public function save()
    {
        if ($this->PK!=null)
        {
            $query = sprintf('UPDATE `module` SET `NAME`="%s" WHERE `PK`=%d',
                $this->NAME,
                $this->PK);
            mysql_query($query, $GLOBALS['DB']);
        } else {
            $query = sprintf('INSERT INTO `module` (NAME) VALUES ("%s")',
                $this->NAME);
            mysql_query($query, $GLOBALS['DB']);
            $newmodid = mysql_insert_id();
            $query = sprintf('INSERT INTO `auto_mode` (MODULE, CODE, TITLE, OTHER_SETTING) VALUES (%d, 1, "Off", NULL)', $newmodid);
            mysql_query($query, $GLOBALS['DB']);
            $newamid = mysql_insert_id();
            $lst = array();
            $query = "SELECT `PK` FROM `account` WHERE `DELETED` = FALSE";
            $result = mysql_query($query, $GLOBALS['DB']);
            while ($row = mysql_fetch_assoc($result)) {
                array_push($lst, $row['PK']);
            }
            mysql_free_result($result);
            foreach ($lst as $accpk)
            {
                $query = sprintf('INSERT INTO `acc_setting` (ACCOUNT, MODULE, USERNAME, PSWD, AUTO_MODE) VALUES (%d, %d, "N/A", "N/A", %d)', $accpk, $newmodid, $newamid);
                mysql_query($query, $GLOBALS['DB']);
            }
        }
    }

    public static function delete($PK)
    {
        $query = sprintf('DELETE FROM `acc_setting` WHERE MODULE = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        $query = sprintf('DELETE FROM `auto_mode` WHERE MODULE = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        $query = sprintf('DELETE FROM `module` WHERE PK = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        return 0;
    }
}
?>
