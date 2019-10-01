<?php
//if ( get_magic_quotes_gpc() ) die('magic_quotes_gpc включен');
//if ( get_magic_quotes_runtime() ) die('magic_quotes_runtime включен');
//if ( ini_get('register_globals') == 1) die('register_globals включен');

//экранируем от HTML GET-запросы
if ($_GET) {

    foreach ($_GET as $f => $v)
        if (!is_array($v)) {
            unset($_GET[$f]);
            $_GET[strip_tags($f)] = strip_tags($v);
        }

}


// если нет POST данных
if (count($_POST) == 0) {

//склейка www и no-www домена
    /*
        if ($_SERVER['HTTP_HOST'] != 'NO-WWW.ru')
        {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: http://NO-WWW.ru".$_SERVER['REQUEST_URI']);
            exit();
        }
    */

    //Настройка склейки (301 редирект) страниц со слешем и без  - УНИВЕРСАЛЬНЫЙ ВАРИАНТ
    $uriarr_get = explode("?", $_SERVER['REQUEST_URI']);
    if ($uriarr_get[0] == "/konkursy/raboty/") {
        header("Location: http://" . $_SERVER['SERVER_NAME'] . "/konkursy/literaturnyy-detskiy-konkurs/");
        exit();
    }
    if ($uriarr_get[0] == "/konkursy/fotogalereya/") {
        header("Location: http://" . $_SERVER['SERVER_NAME'] . "/konkursy/konkurs-detskogo-risunka/");
        exit();
    }

    //Http авторизация для результатов конкурса
   //mail("89065267799@mail.ru", "Тема письма", print_r($uriarr_get, true));
/*    if ($uriarr_get[0] == "/concursresults/photo/") {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Текст, отправляемый в том случае,если пользователь нажал кнопку Cancel';
            exit;
        } else {
            echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
            echo "<p>Вы ввели пароль {$_SERVER['PHP_AUTH_PW']}.</p>";
        }
    }*/

    $uriarr = explode("_", $uriarr_get[0]);
    if (substr($uriarr[0], -1) != '/') {
        $uri = str_replace('//', '/', $uriarr[0] . '/');
        unset($uriarr[0]);
        if (count($uriarr)) {
            $uri .= '_' . implode('_', $uriarr);
        }

        if (isset($uriarr_get[1]))
            $uri .= "?" . $uriarr_get[1];

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: http://" . $_SERVER['SERVER_NAME'] . $uri);
        exit();
    }


}


//проверка блокировки сайта
if (is_file('siteblock.html')) {
    echo file_get_contents('siteblock.html');
    die();
}


//функции кеширования
include "inc/var.php";
include "inc/libs/caching.php";
include "inc/libs/dtimer.php";

$dtimer = new dtimer();

//глобальное кеширование
if (check_cache_enable()) {
    $uri = str_replace('/', '#', $_SERVER['REQUEST_URI']);
    $html = get_cache($uri, 1800);
    if (!is_null($html)) {
        echo $html;

        if (in_array($_SERVER['REMOTE_ADDR'], $config['admin_IP'])) {
            $dtimer->tick("Done (global cache).");
            echo $dtimer->show();
        }

        die();
    }
}

//псевдослучайная очистка кеша
/* суть - при долгом отсутствии администрирования файлы с кешем накапливаются в большом количестве 
   полезно с определенной периодичностью их грохать	
*/
if (rand(1, 500) == 250) {
    clear_cache(0);
}

include "includes.php";
session_start();
//phpinfo();
$sql = new Sql();
$sql->connect();

check_access_to_site();

// crone - рассылка по 10 писем за один раз
// include "cron/cronfunc.php";
// subscribe(5);

$control = new Control();

$control->Init();
$control->Make();

$sql->close();

if (in_array($_SERVER['REMOTE_ADDR'], $config['admin_IP'])) {
    $dtimer->tick("Done.");
    echo $dtimer->show();
}

?>