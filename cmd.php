<?php 

/// @file install.php
///
/// Installs the Trivial Core components for a given location.
///
///


$options = getopt("c:p:");

switch($options["c"])
{
    case "pwdgen":
        $password = $options["p"];
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $alg = PASSWORD_DEFAULT;

        echo "$hash $alg";
        break;

    default:
        echo "Unsupported command: ".$options["c"];
        exit(-1);

}



?>