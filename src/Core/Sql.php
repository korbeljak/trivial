<?php namespace Core;

class Sql
{
    protected static $sql = null;

    private $connstr;  
    private $conn;    
    private $chyby;
    public $schema;

    public function __construct($host, $db, $user, $pass, $port, $schema=""){
        $this->schema = $schema;
        if ($host === null)
        {
            $connstr = "dbname=".$db." user=".$user." password=".$pass;
        }
        else
        {

            $connstr = "host=".$host." dbname=".$db." user=".$user." password=".$pass;
            if ($port != null)
            {
                $connstr .= " port=".$port;
            }
        }

        $this->connstr = $connstr;
        $this->_connect();
        $this->print_errors("spojeni");
    }
    
    private function _connect()
    {
        $this->conn = pg_connect($this->connstr);
        if(!$this->conn)
        {
            $this->chyby['spojeni'][] = "spojení z databázovým serverem selhalo";
        }
    }
    
    public function query($query)
    {
        $vysledek = @pg_query($this->conn, $query);
        if (!$vysledek)
        {
            $this->chyby['dotazy'][] = "Dotaz selhal: ".pg_last_error($this->conn);
        }

        return $vysledek;
    }
    
    private function _get_one_item($vysledek_dotazu)
    {
        $vysledek = pg_fetch_assoc($vysledek_dotazu);
        if(!$vysledek){
           $this->chyby['dotazy'][] = "Získání pole selhalo: ".pg_last_error($this->conn);
        }
        return $vysledek;
    }
    
    public function get_query_one($query)
    {
        $one = null;
        $query_result = $this->query($query);
        if ($query_result)
        {
            $one = pg_fetch_assoc($query_result);
            
            if (!$one)
            {
                $this->chyby['dotazy'][] = "Získání pole selhalo: ".pg_last_error($this->conn);
            }
        }

        return $one;
    }

    public function get_query_map($query)
    {
        $out_array = array();
        $query_result = pg_query($this->conn, $query);
        if (!$query_result)
        {
            $this->chyby['dotazy'][] = "Dotaz selhal: ".pg_last_error($this->conn);
        }

        while ($one = $this->_get_one_item($query_result))
        {
            array_push($out_array, $one);
        }

        return $out_array;
    }
    
    public function get_row_cnt($query_result)
    {
        return @pg_num_rows($query_result);
    }
    
    public function disconnect()
    {
        return pg_close($this->conn);
    }
    
    public function print_errors($oddil = "dotazy")
    {
        if(!empty($this->chyby[$oddil])){
            $ret = "";
            foreach ($this->chyby[$oddil] as $chyba){
                $ret .= $chyba.$this->oddelovac;
            }
            return $ret;
        }
        else return FALSE;
    }

    public function __destruct()
    {
        $this->disconnect();
    }


    public static function Configure($host, $db, $user, $pass, $port, $schema="")
    {
        self::$sql = new \Core\Sql($host, $db, $user, $pass, $port, $schema);
    }

    /**
     * Gets the default Router or lazily creates it.
     * 
     * @return \Core\Sql
     */
    public static function Get()
    {
        if (self::$sql === null)
        {
            \Core\Logger::Log(LOG_ERR,
                              "Default SQL connection not configured.",
                              \Core\Logger::O_SYSTEM_ALL);
        }
        return self::$sql;
    }
}
