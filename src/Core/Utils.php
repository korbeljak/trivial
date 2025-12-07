<?php namespace Core;

class Utils
{

public static function get_web_root(){
    return "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
}

public static function format_date_czech($a = "now", $milis = false)
{
    if ($milis)
    {
        return date("j. n. Y H.i:s.u", strtotime($a));
    }
    
    return date("j. n. Y H.i:s", strtotime($a));
}

}