<?php namespace Core;

include_once 'Logger.php';


/**
 * Route Rule class. This route rule is used to match requests against
 * user or system specified rules.
 */
class RouteRule
{
    /**
     *  @var string $regex Regular expression to match the routing request
     *  against.
     */
    protected $regex;
    
    /**
     * @var callable $controller A Callback to call in case of routing request
     * matches this $regex.
     */
    protected $controller;
    
    /**
     * @var integer $priority Priority number. The highest priority, the sooner
     * the rule will be matched. If two rules are matching the same request,
     * the one with higher priority will take precedence.
     */
    protected $priority = 0;
    
    /**
     * @var integer $controllerType Type of the controller (either system or 
     * user).
     */
    protected $controllerType;
    
    /**
     * @var boolean $matched If the rule has been matched once per a request.
     */
    protected $matched = false;
    
    /**
     * @var array Static arguments array. These are not comming from the 
     * request itself, but they are added to the rule in time of its creation.
     */
    protected $staticArgs = array();
    
    /**
     * @var array Dynamic arguments array. These are parsed from incoming
     * request based on $regex.
     */
    protected $dynamicArgs = array();
    
    /**
     * @var integer $order Order of rule creation.
     */
    protected $order = 0;
    
    /**
     * Constructor.
     * 
     * @param string $regex Regular expression to match. 
     * Matches of subexpressions will be passed as arguments.
     * @param callable $controller Controller function pointer.
     * @param array $additionalArgs Additional arguments.
     * @param integer $priority Priority in the routing list.
     * @param integer $controllerType Controller type.
     */
    public function __construct(string $regex, 
                                callable $controller,
                                array $additionalArgs = null,
                                int $priority = 0,
                                int $order = 0,
                                int $controllerType = self::CONTROLLER_USER)
    {
        $this->regex = $regex;
        $this->controller = $controller;
        $this->priority = $priority;
        $this->order = $order;
        $this->controllerType = $controllerType;
        
        if (is_array($additionalArgs))
        {
            $this->staticArgs = $additionalArgs;
        }
    }
    
    /**
     * Matches Routing Rule and executes its Controller.
     * 
     * @param string $argStr Argument string (what usually comes
     * in as a HTTP GET part).
     */
    public function MatchAndExecute(string $argStr)
    {
        $matches = $this->Matches($argStr);
        
        if ($matches)
        {
            $this->ExecuteController();
        }
        
        return $matches;
    }
    
    
    /**
     * Quick sort comparator function for two RouteRules, A and B.
     * 
     * @param \Core\RouteRule $a Item A to compare.
     * @param \Core\RouteRule $b Item B to compare
     * @return number
     */
    public static function QCompare(\Core\RouteRule $a, \Core\RouteRule $b)
    {
        $sort = $a->priority - $b->priority;
        
        if ($sort == 0)
        {
            // The same priority, check order of Route Rule adding.
            $sort = $a->order - $b->order;
        }
        
        return $sort;
    }
    
    
    /**
     * Matches the Argument string to the registered Route Rule.
     * 
     * @param string $argStr Argument string.
     * @return boolean true if matches, false otherwise.
     */
    protected function Matches(string $argStr)
    {
        $this->matched = // preg_match shall return 1 in case of success.
            preg_match($this->regex, $argStr, $dynArgs) === 1;
        
        if (is_array($dynArgs))
        {
            $this->dynamicArgs = $dynArgs;
        }
        
        // todo: Add debug log.
        // $matchStr = $this->matched ? "did match" : "did not match";
        // echo "Rule: [".$this->regex."] ".$matchStr.".\n";
        
        return $this->matched;
    }
    
    
    /**
     * Executes the Controller callable.
     */
    protected function ExecuteController()
    {
        if (!$this->matched)
        {
            \Core\Logger::Log(LOG_INFO, "Trying to run an unmatched controller",
                              \Core\Logger::O_SYSTEM_ALL);
        }
        
        $args = array_merge($this->dynamicArgs, $this->staticArgs);
        
        call_user_func($this->controller, $args);
    }
}


/**
 * Router class.
 * 
 * Holds all routes, performs routing.
 *
 */
class Router
{
    /**
     * @var integer CONTROLLER_SYSTEM A constant of controller type: SYSTEM.
     */
    const CONTROLLER_SYSTEM = 0;
    
    /**
     * @var integer CONTROLLER_USER A constant of controller type: USER.
     */
    const CONTROLLER_USER = 1;
    
    /**
     * @var array $routeRules This array holds all routing rules to this router.
     */
    protected $routeRules = array();
    
    /**
     * @var \Core\Router A static default instance of this Router.
     */
    protected static $router = null;
    
    /**
     * @var integer $routeRuleOrder Routing Rule order cache.
     */
    protected $routeRuleOrder  = 0;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    
    /**
     * Adds a routing rule.
     * 
     * @param string $regex Regular expression.
     * @param callable $controller Controller callable.
     * @param integer $priority Priority of the Routing Rule.
     * @param integer $controllerType Controller type.
     */
    public function AddRule(string $regex,
                            callable $controller,
                            array $additionalArgs = null,
                            int $priority = 0,
                            int $controllerType = self::CONTROLLER_USER)
    {
        switch ($controllerType)
        {
            case self::CONTROLLER_SYSTEM:
            case self::CONTROLLER_USER:
                // This adds user specified controllers residing wherever.
                $rule = new RouteRule($regex,
                                      $controller,
                                      $additionalArgs,
                                      $priority,
                                      $this->routeRuleOrder++, // Rise the order
                                      $controllerType);
                $this->routeRules[] = $rule;
                break;
            default:
                \Core\Logger::Log(LOG_ERR,
                                  "Nonexistent controller type (".$controllerType.")",
                                  \Core\Logger::O_SYSTEM_ALL);
                break;
        }
    }
    
    /**
     * Finds a proper Route and Executes its controller.
     * 
     * @param string $argStr Argument string.
     * 
     * @return boolean true if found, false otherwise.
     */
    public function Route(string $argStr)
    {
        $found = false;
        if (!empty($this->routeRules))
        {
            usort($this->routeRules, "\Core\RouteRule::QCompare");
            
            foreach($this->routeRules as $rule)
            {
               if ($rule->MatchAndExecute($argStr))
               {
                   $found = true;
                   break;
               }
           }
        
            if (!$found)
            {
                \Core\Logger::Log(LOG_ERR,
                                  "Nonexistent controller for the route '$argStr'!",
                                  \Core\Logger::O_SYSTEM_ALL);
            }
        }
        
        return $found;
    }
    
    
    /**
     * Gets the default Router or lazily creates it.
     * 
     * @return \Core\Router
     */
    public static function GetDefaultRouter()
    {
        if (self::$router === null)
        {
            self::$router = new \Core\Router();
        }
        
        return self::$router;
    }
}