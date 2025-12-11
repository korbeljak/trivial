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
        $passwords = [];
        $passwords["Alky"] = ["DrakBílýchRun_Ticho37", "Alky.Kosan@seznam.cz"];
        $passwords["Hroše"] = ["KorunaPrastin_Záře91", "magda.ordogova@seznam.cz"];
        $passwords["Zagro"] = ["StínovýHvozd_Modlitba44", "zagro.kudla@seznam.cz"];
        $passwords["Pilly"] = ["KronikyElderské_Cesta06", "Tylai@seznam.cz"];
        $passwords["Terka"] = ["VěžVěštců_Oko28", "markova.t@seznam.cz"];
        $passwords["Světlo"] = ["OcelovýPoutník_Havrani73", "berk2@seznam.cz"];
        $passwords["Keret"] = ["RunováBrána_Pouť59", "keret@seznam.cz"];
        $passwords["Fin"] = ["OstříRytířů_Zlom12", "Finmer@email.cz"];
        $passwords["Mates"] = ["MlhaZaPrůsmykem_Herald22", "matej.rott@seznam.cz"];
        $passwords["Pachol"] = ["PlamenDruida_Svit3", "Jakub.Ventura@seznam.cz"];
        $passwords["Hádě"] = ["KorunySedmi_Echo85", "snakie9@seznam.cz"];
        $passwords["Spell"] = ["MeziSvityKnihoven_Stopa04", "andre.j@email.cz"];

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