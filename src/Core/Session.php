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

class Session
{
    public $user;
    public $notifications = [];

    private int $login_time_s;
    private int $abs_tout_s;
    const MAX_IDLE_S = 1800;

    public function __construct($user = null)
    {
        $this->user = $user;
        $this->login_time_s = time();
        $this->abs_tout_s = $this->login_time_s + self::MAX_IDLE_S;
    }

    public function log_out()
    {
        $this->user = null;
        redirect_hop_to();
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
        return ($user != null) && !$user->anonymous;
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
        session_start();
        Session::get_or_create_session();
        Session::session_check_timer();
        
        var_dump(Session::get_session());
    }
}



?>
