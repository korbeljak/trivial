<?php namespace Core;

class Sql
{
    protected static $sql = null;
    
    public string $schema;
    protected string $dsn;
    protected \PDO $pdo;

    public function __construct(string $host,
                                string $db,
                                string $user,
                                string $pass,
                                $schema="")
    {
        $this->schema = $schema;
        $this->dsn = "pgsql:host={$host};dbname={$db}";
        $this->pdo = new \PDO($this->dsn, $user, $pass);
    }
    
    
    public function run($query_str, $params = [])
    {
        $stmt = $this->pdo->prepare($query_str);
        $stmt->execute($params);
        return $stmt;
    }

    public function get_one($query_str, $params = [])
    {
        $row = $this->run($query_str, $params)->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function get_map($query_str, $params = [])
    {
        return $this->run($query_str, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function get_cnt($query_result)
    {
        return $query_result.rowCount();
    }

    public static function Configure($host, $db, $user, $pass, $schema="")
    {
        self::$sql = new \Core\Sql($host, $db, $user, $pass, $schema);
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
