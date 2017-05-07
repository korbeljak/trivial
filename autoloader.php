<?php

function Core_Autoload(string $className)
{
    $fileName = __DIR__.DS."src".DS.str_replace('\\', '/', $className).'.php';
    if (file_exists($fileName))
    {
        require $fileName;
    }
}

spl_autoload_register("Core_Autoload");