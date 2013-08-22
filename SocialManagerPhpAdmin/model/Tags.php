<?php
class Tags
{
    private $PK;
    private $fields;

    // initialize a User object
    public function __construct()
    {
        $this->PK = null;
        $this->fields = array('TITLE' => null,
                              'MAP_TAG' => null);
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
    public static function getmaptaglist($tag_list)
    {
        $lst = array();
        if (empty($tag_list)) {return $lst;}
        
        $query = sprintf('SELECT DISTINCT MAP_TAG FROM tags WHERE MAP_TAG IS NOT NULL AND TITLE IN ("%s")', join('","',$tag_list));
        $result = mysql_query($query, $GLOBALS['DB']);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($lst, $row['MAP_TAG']);
        }
        mysql_free_result($result);

        return $lst;
    }
}
?>
