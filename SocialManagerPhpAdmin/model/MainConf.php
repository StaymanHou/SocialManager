<?php
class MainConf
{
    private $PK;     // user id
    private $fields;  // other record fields

    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('TITLE' => '',
                              'CACHING_TIME' => 7,
                              'IMAGE_FILE_DIR' => '/',
                              'LOAD_ITERATION' => 1,
                              'PULLER_ITERATION' => 300,
                              'POSTER_ITERATION' => 60);
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
    public static function get()
    {
        $mc = new MainConf();

        $query = 'SELECT `TITLE`, `CACHING_TIME`, `IMAGE_FILE_DIR`, `LOAD_ITERATION`, `PULLER_ITERATION`, `POSTER_ITERATION` FROM `main_conf` WHERE `PK`=1';
        $result = mysql_query($query, $GLOBALS['DB']);

        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            $mc->TITLE = $row['TITLE'];
            $mc->CACHING_TIME = $row['CACHING_TIME'];
            $mc->IMAGE_FILE_DIR = $row['IMAGE_FILE_DIR'];
            $mc->LOAD_ITERATION = $row['LOAD_ITERATION'];
            $mc->PULLER_ITERATION = $row['PULLER_ITERATION'];
            $mc->POSTER_ITERATION = $row['POSTER_ITERATION'];
            $mc->PK = 1;
        }
        mysql_free_result($result);

        return $mc;
    }

    public function save()
    {
        if ($this->PK)
        {
            $query = sprintf('UPDATE `main_conf` SET `TITLE`="%s",`CACHING_TIME`=%d,`IMAGE_FILE_DIR`="%s",`LOAD_ITERATION`=%d,`PULLER_ITERATION`=%d,`POSTER_ITERATION`=%d WHERE `PK`=1',
                $this->TITLE,
                $this->CACHING_TIME,
                $this->IMAGE_FILE_DIR,
                $this->LOAD_ITERATION,
                $this->PULLER_ITERATION,
                $this->POSTER_ITERATION);
            mysql_query($query, $GLOBALS['DB']);
        }
    }
}
?>
