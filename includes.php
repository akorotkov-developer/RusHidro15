<?
// fix for php 5.4
function fix_session_register()
{

    function session_register()
    {

        $args = func_get_args();

        foreach ($args as $key) {
            $_SESSION[$key] = $GLOBALS[$key];
        }
    }

    function session_is_registered($key)
    {

        return isset($_SESSION[$key]);
    }

    function session_unregister($key)
    {

        unset($_SESSION[$key]);
    }
}

if (!function_exists('session_register')) fix_session_register();

////// <-fix

include "libs/ets.php";
include "inc/libs/funcfortext.php";
include "inc/libs/sql.php";
include "inc/libs/form.php";
include "inc/class/tree.php";
include "inc/libs/all.php";
include "inc/libs/listing.php";
require_once('libs/PHPMailer/class.phpmailer.php');
include "inc/var.php";
include "inc/datafunc.php";
include "inc/inc.php";
include "inc/class/control.php";
include "inc/class/metamodule.php";
?>