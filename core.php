<?php
use Core\Router;

const CORE_PATH =__DIR__;

// function say_hi($args)
// {
//     var_dump($args);
//     echo "HI, $args[whom]!";
// }
// function say_hello($args)
// {
//     var_dump($args);
//     echo "HELLO, $args[whom]!";
// }


/**
 * @var array ILVL Indentation level.
 */
const ILVL = array("",                      //  0
                  "\t",                     //  1
                  "\t\t",                   //  2
                  "\t\t\t",                 //  3
                  "\t\t\t\t",               //  4
                  "\t\t\t\t\t",             //  5
                  "\t\t\t\t\t\t",           //  6
                  "\t\t\t\t\t\t\t",         //  7
                  "\t\t\t\t\t\t\t\t",       //  8
                  "\t\t\t\t\t\t\t\t\t",     //  9
                  "\t\t\t\t\t\t\t\t\t\t");  // 10

\Core\Page::SetDefaultTitle(CFG_SITE_DEFAULT_TITLE);
\Core\Page::SetDefaultDescription(CFG_SITE_DEFAULT_DESCRIPTION);
\Core\Page::SetDefaultKeywords(CFG_SITE_DEFAULT_KEYWORDS);
\Core\Page::SetDefaultThemePath(CFG_SITE_DEFAULT_THEME_PATH);

// \Core\Router::GetDefaultRouter()->AddRule("/(?P<name>ahoj)\/(?P<whom>.+)\//", 'say_hi');
// \Core\Router::GetDefaultRouter()->AddRule("/(?P<name>ahoj)\/(?P<whom>.+)\//", 'say_hello');
\Core\Router::GetDefaultRouter()->AddRule("/coreassets\/(?P<assetPath>.+)/", '\Core\Asset::GetAsset');

if (!array_key_exists("pg", $_GET) || !is_string($_GET["pg"]))
{
    $_GET["pg"] = "";
}

$found = \Core\Router::GetDefaultRouter()->Route($_GET["pg"]);
if (!$found)
{
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    echo "404 Not Found";
}


//$logger->Log(LOG_INFO, "Hi From Core", Logger::O_ALL);
//echo $logger->GetUserHtml();
//$logger->FlushUserlog();

