<?php namespace Core;

class Sql
{
    protected static $sql = null;

    private $connstr;  
    private $conn;    
    private $chyby;
    public string $schema;
    private string $dsn;

    public function __construct(string $db,
                                string $user,
                                string $pass,
                                string $socket_dir = "/var/run/postgresql",
                                $schema=""){
        $this->schema = $schema;
        $this->dsn = "pgsql:host={$socket_dir};dbname={$db}";
        $this->pdo = new \PDO($this->dsn, $user, $pass);
    }
    
    
    public function run($query_str, $params = [])
    {
        $stmt = $this->pdo->prepare($query_str);
        $stmt->execute($params);
        return $stmt;
    }

    public function get_query_one($query_str, $params = [])
    {
        $row = $this->run($query_str, $params)->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function get_query_map($query_st, $params = [])
    {
        return $this->run($query_str, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function get_row_cnt($query_result)
    {
        return $query_result.rowCount();
    }

    public static function Configure($db, $user, $pass, $schema="")
    {
        self::$sql = new \Core\Sql($db, $user, $pass, $schema);
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
