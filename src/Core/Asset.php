<?php namespace Core;

class Asset
{
    public static function GetAsset($args)
    {
        $ok = false;
        if (isset($args["assetPath"]) && !empty( $args["assetPath"]))
        {
            // Argument found.
            $path = $args["assetPath"];
            
            if (strpos($path, "../") === false && substr($path, -3) === ".js")
            {
                $fullPath = $GLOBALS["CORE_PATH"]."/coreassets/".$path;
                if (file_exists($fullPath))
                {
                    $modDateTime = gmdate("D, d M Y H:i:s", filemtime($fullPath));
                    
                    // Set last-modified header.
                    header("Last-Modified: ".$modDateTime." GMT");
                    
                    // Make sure caching is turned on.
                    header('Cache-Control: public');
                    
                    // This is javascript, UTF-8.
                    header('Content-Type: application/x-javascript; charset=utf-8');
                    
                    $fileContents = file_get_contents($fullPath);
                    echo $fileContents;
                    $ok = true;
                }
            }
        }
        
        if (!$ok)
        {
            header("HTTP/1.0 404 Not Found");
            echo "404 Not found.";
        }
    }
}