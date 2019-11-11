<?php namespace Core;

/**
 * Logger Class.
 * 
 * @author Jakub Korbel, korbel.jak@gmail.com
 *
 */
class Logger
{
    /** Log output: E-mail. */
    const O_EMAIL = 1;
    
    /** Log output: Syslog. */
    const O_SYSLOG = 2;
    
    /** Log output: File. */
    const O_FILE = 4;
    
    /** Log output: User. */
    const O_USER = 8;
    
    /** Log output: All system. */
    const O_SYSTEM_ALL = self::O_EMAIL | self::O_SYSLOG | self::O_FILE;
    
    /** Log output: All. */
    const O_ALL = self::O_EMAIL | self::O_SYSLOG | self::O_FILE | self::O_USER;
    
    /** Log level from syslog parsed into strings. */
    public $lvlStrings = 
        array(LOG_EMERG => "EMERG",
              LOG_ALERT => "ALERT",
              LOG_CRIT => "CRIT",
              LOG_ERR => "ERR",
              LOG_WARNING => "WARNING",
              LOG_NOTICE => "NOTICE",
              LOG_INFO => "INFO",
              LOG_DEBUG => "DEBUG");
    
    /** E-mail to log to. */
    public $email = CFG_LOG_EMAIL;
    
    /** Log File to log to. */
    public $logFile = CFG_LOG_FILE;

    /** User log. */
    public $userlog = array();
    
    /** A global logger device */
    public static $logger = null;
    
    /** If the logger is emitting debug logs or not. */
    public static $isDebugging = false;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    public static function GetUserIpAddr()
    {
        $ip = "";
        
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
    
    /**
     * Gets a Timestamp.
     * 
     * @return string Timestamp.
     */
    public function GetTimestamp()
    {
        return date("Y-m-d_H:i:s");
    }
    
    /**
     * Logs an Event to customizable outputs.
     * 
     * @param int $level Constant, the same as per php syslog() manual.
     * @param string $message User defined message.
     * @param int $outputs Bitmask for possible outputs.
     */
    public function Log0(int $level,
                         string $message,
                         int $outputs)
    {
        $msg = "[".$this->GetTimestamp()."] [".$this->lvlStrings[$level]."] [".self::GetUserIpAddr()."] ".$message."\n";
        
        if ($outputs & self::O_EMAIL)
        {
            error_log($msg, 1, $this->email); // no extra headers.
        }
        
        if ($outputs & self::O_SYSLOG)
        {
            error_log($msg, 0);
        }
        
        if ($outputs & self::O_FILE)
        {
            error_log($msg, 3, $this->logFile);
        }
        
        if ($outputs & self::O_USER)
        {
            $this->userlog[] = array("msg" => $message,
                                     "lvl" => $this->lvlStrings[$level]);
        }
        
        if ($level === LOG_CRIT)
        {
            // In case of critical level, die. No point in continuing the
            // errorneous processing.
            die("Critical error, dying: ".$msg);
        }
    }
    
    public static function Log(int $level,
                               string $message,
                               int $outputs)
    {
        self::GetDefaultLogger()->Log0($level, $message, $outputs);
    }
    
    /**
     * Logs an Event to customizable outputs if debugging is enabled.
     * 
     * @param int $level Constant, the same as per php syslog() manual.
     * @param string $message User defined message.
     * @param int $outputs Bitmask for possible outputs.
     */
    public static function DebugLog(int $level,
                                    string $message,
                                    int $outputs)
    {
        if (self::$isDebugging)
        {
            self::GetDefaultLogger()->DebugLog($level, $message, $outputs);
        }
    }
    
    /**
     * Gets User Log's HTML representation.
     * 
     * @return string HTML output.
     */
    public function GetUserHtml()
    {
        $str = "";
        foreach ($this->userlog as $logEntry)
        {
            $str .= "<p class=\"log ".$logEntry["lvl"]."\">\n";
            $str .= "\t". $logEntry["msg"] . "\n";
            $str .= "</p>\n";
            
        }
        
        return $str;
    }
    
    /**
     * Flushes User Log.
     */
    public function FlushUserlog()
    {
        unset($this->userlog);
        $this->userlog = array();
    }
    
    /**
     * Sets the default logger.
     * 
     * @param \Core\Logger $logger Logger instance.
     */
    public static function SetDefaultLogger(\Core\Logger $logger)
    {
        self::$logger = $logger;
    }
    
    /**
     * Gets the default Logger or lazily creates it.
     *
     * @return \Core\Logger
     */
    public static function GetDefaultLogger()
    {
        if (self::$logger === null)
        {
            self::$logger = new \Core\Logger();
        }
    
        return self::$logger;
    }

    /**
     * Sets debugging mode.
     *
     * @return \Core\Logger
     */
    public static function SetDebugging(bool $debugging)
    {
        self::$isDebugging = $debugging;
    }
}