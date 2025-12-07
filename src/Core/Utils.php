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

public static function get_absolute_path($koncovka = FALSE){
   $path = Utils::get_web_root();
   if($koncovka) return $path = $path."/".$koncovka;
   return $path;
   
}

public static function redirect_now($relativni_adresa = FALSE){
   $hlavicka = "Location: ".Utils::get_absolute_path($relativni_adresa);
   header($hlavicka, true, 303);
   die();
}



    public function current_url(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
              ? 'https'
              : 'http';

        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }

}