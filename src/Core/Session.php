<?php namespace Core;

enum NotificationType: string {
    case Info = 'info';
    case Error  = 'error';
    case Success = 'success';
    case Warning = 'warning';
}



class Notification
{
    public NotificationType $type;
    public string $text;

    public function __construct(NotificationType $type, string $text)
    {
        $this->type = $type;
        $this->text = $text;
    }
}

class RememberMe
{
    protected int $user_id;
    protected string $selector;
    protected string $validator_hash;
    protected \DateTime $expiration;

    private const int SELECTOR_LEN = 16;
    private const int VALIDATOR_LEN = 32;
    private const int VALIDITY_DAYS = 30;
    private const string HASH = "sha256";

    public static function forget($db, int $user_id)
    {
        $q = "DELETE FROM remember_me WHERE user_id=:user_id;";
        $db->run($q, ["user_id" => $user_id]);

        self::delete_cookie();

    }

    public static function delete_cookie()
    {
        setcookie('__Host-remember_me', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

    }


    public static function issue($db, int $user_id)
    {
        $q = "INSERT INTO remember_me (user_id, selector, validator_hash, expiration)
        VALUES (:user_id, :selector, :validator_hash, :expiration);";

        $selector = base64_encode(random_bytes(self::SELECTOR_LEN));
        $validator = base64_encode(random_bytes(self::VALIDATOR_LEN));
        $validator_hash = hash(self::HASH, $validator);
        $validator_hash_str = self::HASH.":".$validator_hash;

        $cookie_val = "$selector:$validator";

        $expiration = new \DateTime();
        $expiration->modify('+'.self::VALIDITY_DAYS.' days');

        $expiration_str = $expiration->format("c");

        $db->run($q, ["user_id" => $user_id,
                      "selector" => $selector,
                      "validator_hash" => $validator_hash_str,
                      "expiration" => $expiration_str]);
        

        setcookie('__Host-remember_me', $cookie_val, [
            'expires'  => $expiration->getTimestamp(),
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

    }

    public static function check($db)
    {
        $ok = false;
        try
        {
            [$selector, $validator] = explode(":", $_COOKIE['__Host-remember_me']);
            $ok = true;
        }
        catch (Throwable $t){}

        if ($ok)
        {
            self::delete_cookie();

            $q = "SELECT id, user_id, validator_hash, expiration FROM remember_me
            WHERE selector=:selector AND expiration > NOW() LIMIT 1;";

            $result = $db->get_one($q, ["selector" => $selector]);
            if ($result)
            {
                $ok = false;
                try
                {
                    [$algo, $hash_str] = explode(":", $result["validator_hash"]);
                    $ok = true;
                }
                catch (Throwable $t){}

                $q = "DELETE FROM remember_me WHERE id=:id;";
                $db->run($q, ["id" => $result["id"]]);

                if ($ok && hash($algo, $validator) == $hash_str)
                {
                    if (\Core\Session::$get_user_by_id != null)
                    {
                        $user = (\Core\Session::$get_user_by_id)($db, $result["user_id"]);
                        \Core\Session::session_log_in($user);
                    }
                }
            }
        }

    }

    public static function create_table($db)
    {
        $q = "CREATE TABLE remember_me (
                id SERIAL PRIMARY KEY,
                user_id INT NOT NULL,
                selector TEXT NOT NULL,
                validator_hash TEXT NOT NULL,
                expiration TIMESTAMPTZ
            );";
        $db->run($q);
    }
}

class Session
{
    public $user;
    public $notifications = [];
    protected static $redirect_url;
    public $redirect_to;
    public $redirect_wait_s;
    protected $xsrf = [];
    public static ?\Closure $get_user_by_id = null;

    private int $login_time_s;
    private int $abs_tout_s;
    const MAX_IDLE_S = 3600;
    const MAX_TOKEN_VALIDITY_S = 2400;
    const TOKENS_MAX = 10;

    public function __construct($user = null)
    {
        $this->user = $user;
        $this->login_time_s = time();
        $this->abs_tout_s = $this->login_time_s + self::MAX_IDLE_S;
    }

    public function log_out()
    {
        RememberMe::forget(\Core\Sql::get(), $this->user->id);
        $this->user = null;

        self::redirect_hop_to("");
    }

    public function check_timer(): bool
    {
        if ($this->user == null)
        {
            return true;
        }

        if ($this->abs_tout_s < time())
        {
            $this->log_out();
            return false;
        }
        else
        {
            $this->abs_tout_s = time() + self::MAX_IDLE_S;
            return true;
        }
    }
    
    public function get_session_duration(): string
    {
        $a = time() - $this->login_time_s;
        return floor($a/60)." m";
    }

    public static function get_session()
    {
        return isset($_SESSION['session']) ? $_SESSION['session'] : null;
    }

    public static function get_or_create_session()
    {
        if (self::get_session() == null)
        {
            $_SESSION['session'] = new Session();

            if (isset($_COOKIE['__Host-remember_me']))
            {
                \Core\RememberMe::check(\Core\Sql::get());
            }
        }
        return $_SESSION['session'];
    }

    public static function session_log_in($user): void
    {
        $session = self::get_session();
        if ($session != null)
        {
            $session->user = $user;
            $session->login_time_s = time();
            $session->abs_tout_s =  $session->login_time_s + self::MAX_IDLE_S;

            \Core\RememberMe::issue(\Core\Sql::get(), $user->id);
        }
    }


    public static function session_check_timer(): bool
    {
        $session = self::get_session();
        return ($session != null) && $session->check_timer();
    }

    public static function is_logged_in(): bool
    {
        $session = self::get_session();
        return ($session != null) && ($session->user != null);
    }

    public static function get_user()
    {
        $session = self::get_session();
        return ($session != null) && ($session->user != null) ? $session->user : null;
    }

    public static function is_member(): bool
    {
        $user = self::get_user();
        return ($user != null) && !$user->is_anonymous();
    }

    public static function is_admin(): bool
    {
        $user = self::get_user();
        return ($user != null) && $user->is_admin();
    }

    public static function session_log_out(): bool
    {
        $session = self::get_session();
        return ($session != null) ? $session->log_out() : false;
    }

    public static function notify(NotificationType $type, string $message)
    {
        $session = self::get_session();
        if ($session != null)
        {
            $session->notifications[] = new Notification($type, $message);
        }
    }

    public static function extract_notifications(): array
    {
        $session = self::get_session();
        if ($session != null)
        {
            $notices = $session->notifications;
            $session->notifications = [];
            return $notices;
        }
        return [];
    }

    public static function start()
    {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        ini_set('session.use_strict_mode', '1');
        session_start();
        Session::get_or_create_session();
        Session::session_check_timer();
    }

    public static function issue_xsrf(): string
    {
        $xsrf_token = "";
        $session = self::get_session();
        if ($session != null)
        {
            if (count($session->xsrf) > self::TOKENS_MAX)
            {
                asort($session->xsrf); // keeps assoc keys
                
                $to_remove_cnt = $count - self::TOKENS_MAX;
                $keys_to_remove = array_slice(array_keys($session->xsrf), 0, $to_remove_cnt);

                foreach ($keys_to_remove as $key)
                {
                    unset($session->xsrf[$key]);
                }
            }

            $xsrf_token = bin2hex(random_bytes(32));
            $session->xsrf[$xsrf_token] = time();
        }

        return $xsrf_token;
    }

    public static function validate_xsrf($xsrf_token)
    {
        $ok = false;
        $session = self::get_session();
        if ($session != null)
        {
            $now = time();
            foreach ($session->xsrf as $xsrf => $time)
            {
                if ($now - $time > self::MAX_TOKEN_VALIDITY_S)
                {
                    unset($session->xsrf[$xsrf]);
                }
            }

            if (isset($session->xsrf[$xsrf_token]))
            {
                unset($session->xsrf[$xsrf_token]);
                $ok = true;
            }
        }

        return $ok;
    }

    public static function SetRedirectUrl(string $url="redirect/")
    {
        self::$redirect_url = $url;
    }

    public static function SetGetUserById(callable $get_user_by_id)
    {
        self::$get_user_by_id = \Closure::fromCallable($get_user_by_id);
    }

    public static function redirect_hop_to($path = null, $wait_s=0)
    {
        $session = self::get_session();
        if ($session != null)
        {
            $session->redirect_to = $path == null ? "" : $path;
            $session->redirect_wait_s = $wait_s;
        }

        $header = "Location: ".Utils::get_absolute_path(self::$redirect_url);
        header($header, true, 303);
        die();
    }
}



?>
