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
    case "pili":

        foreach ($passwords as $nick => $info)
        {
            echo "INSERT INTO users (nick, email, name, role, password_hash)".
            "VALUES ('$nick', '$info[1]', '', 'member', '".password_hash($info[0], PASSWORD_DEFAULT)."');\n";
        }

    default:
        echo "Unsupported command: ".$options["c"];
        exit(-1);

}



?>