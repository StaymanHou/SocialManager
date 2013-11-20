<?php
class Account
{
    private $PK;
    private $fields;

    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('NAME' => null,
                              'RSS_URL' => null,
                              'TAG_LIMIT' => 0,
                              'ACTIVE' => false,
                              'LAST_UPDATE' => null,
                              'DELETED' => false);
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
    public static function getByPK($PK)
    {
        $acc = new Account();
        $query = sprintf("SELECT `NAME`, `RSS_URL`, `TAG_LIMIT`, `ACTIVE`, `LAST_UPDATE`, `DELETED` FROM `account` WHERE `PK` = %d",$PK);
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $acc->NAME = $row['NAME'];
            $acc->RSS_URL = $row['RSS_URL'];
            $acc->TAG_LIMIT = $row['TAG_LIMIT'];
            $acc->ACTIVE = $row['ACTIVE'];
            $acc->LAST_UPDATE = $row['LAST_UPDATE'];
            $acc->DELETED = $row['DELETED'];
            $acc->PK = $PK;
        }
        mysql_free_result($result);

        return $acc;
    }

    // return an object populated based on the record's user id 
    public static function getlist()
    {
        $lst = array();

        $query = "SELECT `PK`, `NAME`, `RSS_URL`, `TAG_LIMIT`, `ACTIVE`, `LAST_UPDATE`, `DELETED` FROM `account` WHERE `DELETED` = FALSE ORDER BY PK DESC";
        $result = mysql_query($query, $GLOBALS['DB']);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($lst, $row);
        }
        mysql_free_result($result);

        return $lst;
    }

    public static function getactivenum()
    {
        $num = 0;

        $query = "SELECT COUNT(*) AS COUNT FROM `account` WHERE `ACTIVE` = TRUE AND `DELETED` = FALSE";
        $result = mysql_query($query, $GLOBALS['DB']);
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $num = $row['COUNT'];
        }
        return $num;
    }

    public static function gettotalnum()
    {
        $num = 0;

        $query = "SELECT COUNT(*) AS COUNT FROM `account` WHERE `DELETED` = FALSE";
        $result = mysql_query($query, $GLOBALS['DB']);
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $num = $row['COUNT'];
        }
        return $num;
    }

    public function save()
    {
        if ($this->PK!=null)
        {
            $query = sprintf('UPDATE `account` SET NAME = "%s", RSS_URL = "%s", TAG_LIMIT = %d, ACTIVE =%d, LAST_UPDATE = "%s", DELETED = %d WHERE PK = %d',
                $this->NAME,
                $this->RSS_URL,
                $this->TAG_LIMIT,
                $this->ACTIVE,
                $this->LAST_UPDATE,
                $this->DELETED,
                $this->PK);
            mysql_query($query, $GLOBALS['DB']);
        }
        else
        {
            $query = sprintf('INSERT INTO `account` (NAME, RSS_URL, TAG_LIMIT, ACTIVE) VALUES ("%s", "%s", %d, %d)',
                $this->NAME,
                $this->RSS_URL,
                $this->TAG_LIMIT,
                $this->ACTIVE);
            mysql_query($query, $GLOBALS['DB']);
            $this->PK = mysql_insert_id($GLOBALS['DB']);
            $lst = array();
            $query = "SELECT `PK` FROM `module` WHERE 1";
            $result = mysql_query($query, $GLOBALS['DB']);
            while ($row = mysql_fetch_assoc($result)) {
                array_push($lst, $row['PK']);
            }
            mysql_free_result($result);
            foreach ($lst as $modpk)
            {
                $query = sprintf("SELECT `PK` FROM `auto_mode` WHERE MODULE = %d AND CODE = 1",$modpk);
                $result = mysql_query($query, $GLOBALS['DB']);
                if (mysql_num_rows($result))
                {
                    $row = mysql_fetch_assoc($result);
                    $ampk = $row['PK'];
                }
                mysql_free_result($result);
                $query = sprintf('INSERT INTO `acc_setting` (ACCOUNT, MODULE, USERNAME, PSWD, AUTO_MODE) VALUES (%d, %d, "N/A", "N/A", %d)', $this->PK, $modpk, $ampk);
                mysql_query($query, $GLOBALS['DB']);
            }
        }
        return 0;
    }

    public static function toggleactive($PK)
    {
        $query = sprintf('UPDATE `account` SET `ACTIVE`=(!`ACTIVE`) WHERE PK = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        return 0;
    }

    public static function setdelete($PK)
    {
        $query = sprintf('UPDATE `account` SET `DELETED`=True, `ACTIVE`=False WHERE PK = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        $query = sprintf('DELETE FROM `acc_setting` WHERE ACCOUNT = %d', $PK);
        mysql_query($query, $GLOBALS['DB']);
        return 0;
    }
}
?>
