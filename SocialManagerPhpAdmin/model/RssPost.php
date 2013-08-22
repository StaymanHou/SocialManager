<?php
class RssPost
{
    private $PK;
    private $fields;

    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('ACCOUNT' => 0,
                              'TITLE' => null,
                              'DESCRIPTION' => null,
                              'CONTENT' => null,
                              'TAG' => null,
                              'IMAGE_FILE' => null,
                              'IMAGE_LINK' => null,
                              'LINK' => null,
                              'OTHER_FIELD' => null,
                              'SOCIAL_SCORE' => 0,
                              'CREATE_TIME' => null);
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
        $rp = new RssPost();
        $query = sprintf("SELECT `ACCOUNT`, `TITLE`, `DESCRIPTION`, `CONTENT`, `TAG`, `IMAGE_FILE`, `IMAGE_LINK`, `LINK`, `OTHER_FIELD`, `SOCIAL_SCORE`, `CREATE_TIME` FROM `rss_post` WHERE `PK` = %d",$PK);
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $rp->ACCOUNT = $row['ACCOUNT'];
            $rp->TITLE = $row['TITLE'];
            $rp->DESCRIPTION = $row['DESCRIPTION'];
            $rp->CONTENT = $row['CONTENT'];
            $rp->TAG = $row['TAG'];
            $rp->IMAGE_FILE = $row['IMAGE_FILE'];
            $rp->IMAGE_LINK = $row['IMAGE_LINK'];
            $rp->LINK = $row['LINK'];
            $rp->OTHER_FIELD = $row['OTHER_FIELD'];
            $rp->SOCIAL_SCORE = $row['SOCIAL_SCORE'];
            $rp->CREATE_TIME = $row['CREATE_TIME'];
            $rp->PK = $PK;
        }
        mysql_free_result($result);

        return $rp;
    }

    // return an object populated based on the record's user id
    public static function gettotalnum($account=null)
    {
        $num = 0;

        $query = 'SELECT COUNT(*) AS COUNT FROM rss_post WHERE 1';
        if ($account!=null) {
            $query .= sprintf(' AND ACCOUNT = %d',$account);
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
    public static function getlist($account=null,$num=30,$offset=0)
    {
        $lst = array();

        $query = 'SELECT PK, ACCOUNT, TITLE, DESCRIPTION, CONTENT, TAG, IMAGE_FILE, IMAGE_LINK, LINK, OTHER_FIELD, SOCIAL_SCORE, CREATE_TIME FROM rss_post';
        if ($account!=null) {
            $query .= sprintf(' WHERE ACCOUNT = %d',$account);
        }
        $query .= sprintf(' ORDER BY PK DESC LIMIT %d, %d',$offset,$num);
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
            $query = sprintf('UPDATE `rss_post` SET `TITLE`="%s", `DESCRIPTION`="%s", `CONTENT`="%s", `TAG`="%s", `IMAGE_FILE`="%s", `IMAGE_LINK`="%s", `LINK`="%s", `SOCIAL_SCORE`=%d WHERE `PK`=%d',
                mysql_real_escape_string($this->TITLE),
                mysql_real_escape_string($this->DESCRIPTION),
                mysql_real_escape_string($this->CONTENT),
                mysql_real_escape_string($this->TAG),
                $this->IMAGE_FILE,
                $this->IMAGE_LINK,
                $this->LINK,
                $this->SOCIAL_SCORE,
                $this->PK);
            mysql_query($query, $GLOBALS['DB']);
        }
        else
        {
            $query = sprintf('INSERT INTO `rss_post` (`ACCOUNT`, `TITLE`, `DESCRIPTION`, `CONTENT`, `TAG`, `IMAGE_FILE`, `IMAGE_LINK`, `LINK`, `SOCIAL_SCORE`, `CREATE_TIME`) VALUES (%d, "%s", "%s", "%s", "%s", "%s", "%s", "%s", %d, "%s")',
                $this->ACCOUNT,
                mysql_real_escape_string($this->TITLE),
                mysql_real_escape_string($this->DESCRIPTION),
                mysql_real_escape_string($this->CONTENT),
                mysql_real_escape_string($this->TAG),
                $this->IMAGE_FILE,
                $this->IMAGE_LINK,
                $this->LINK,
                $this->SOCIAL_SCORE,
                $this->CREATE_TIME);
            mysql_query($query, $GLOBALS['DB']);
        }
    }
}
?>
