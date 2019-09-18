<?

/*
    В чем фишка использования технологии memcache:
    помимо хранения на диске кеш сохраняется в оперативной памяти
    и при достаточно интенсивных запросах это даст большую экономию обращений к диску.
    Т.е. теперь цепочка кеширования выглядит так:
    запрос - кеш из памяти - кеш с диска - отработка "вживую"
*/


//функции дополнительного кеширования
global $config;

if (!isset($config["DOCUMENT_ROOT"]))
    $config["DOCUMENT_ROOT"] = $_SERVER['DOCUMENT_ROOT'];

//каталог кеширования
$CDIR = $config["DOCUMENT_ROOT"] . '_cache/';


//исключения для кэша (всякие яндекс-директы и т.п.)
$CACHE_ARR_EXCLUDE = array(
    'PHPSESSID',
    '__utma',
    '__utmz',
    '__utmc',
    '__utmb'
);


//необходимые условия для использования глобального кеша
function check_cache_enable()
{
    global $config;

    if ($config["cache"] != 2) return false;
    if (count($_POST) > 0) return false;

    return true;
}

function get_cache($filename, $timediff = 7200)
{
    global $config;
    global $CDIR;

    if ($config['cache'] == 0) return NULL;

    /* сперва попытка реализации через MEMCACHE*/
    //логика такая - часто используемые данные будут храниться в memcache
    //c фиксированным временем жизни в 120 секунд.
    if ($config['cache_memcache'] == 1 && extension_loaded('memcache')) {
        global $memcache_obj;
        if (!$memcache_obj) {
            $memcache_obj = new Memcache;
            $memcache_obj->connect('localhost', 11211);
        }
        $content = $memcache_obj->get($filename);
        if ($content !== FALSE) return $content;
    }


    $now = time();
    $ftime = @filemtime($CDIR . $filename);
    if (!$ftime) return NULL;
    if ($now - $ftime > $timediff) return NULL;
    $file = fopen($CDIR . $filename, "r");
    $content = @fread($file, filesize($CDIR . $filename));
    fclose($file);

    //если memcache используется - запихнуть в него результат
    if ($config['cache_memcache'] == 1 && extension_loaded('memcache')) {
        $memcache_obj->set($filename, $content, 0, 120);
    }


    return $content;
}

function set_cache($filename, $content)
{
    global $config;
    global $CDIR;
    if ($config['cache'] == 0) return;

    $out = fopen($CDIR . $filename, "w");
    fwrite($out, $content);
    fclose($out);

    //если memcache используется - запихнуть в него результат
    if ($config['cache_memcache'] == 1 && extension_loaded('memcache')) {
        global $memcache_obj;
        if (!$memcache_obj) {
            $memcache_obj = new Memcache;
            $memcache_obj->connect('localhost', 11211);
        }

        $memcache_obj->set($filename, $content, 0, 120);
    }
}


function my_rm($dir)
{
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                @unlink($dir . $file);
            }
        }
        closedir($handle);
    }
}

function clear_cache($cachetime = 600)
{
    global $config;
    global $CDIR;

    //очистка кеша
    $now = time();
    $ftime = @filemtime($CDIR . ".cachetime");
    if (!$ftime || $now - $ftime > $cachetime) {
        //универсальный вариант
        my_rm($CDIR);

        $out = fopen($CDIR . ".cachetime", "w");
        fwrite($out, date('Y-m-d H:i:s'));
        fclose($out);
    }

    //если memcache используется - сбрасываем там все
    if ($config['cache_memcache'] == 1 && extension_loaded('memcache')) {
        global $memcache_obj;
        if (!$memcache_obj) {
            $memcache_obj = new Memcache;
            $memcache_obj->connect('localhost', 11211);
        }

        $memcache_obj->flush();
    }
}

function cache_grep_array(&$arr)
{
    global $CACHE_ARR_EXCLUDE;

    $res = '';
    if (is_array($arr)) {
        foreach ($arr as $f => $v) {
            //исключение
            if (in_array($f, $CACHE_ARR_EXCLUDE) && $f != "0") continue;
            if (is_object($v))
                $res .= sprintf("%s=%s;", get_class($arr[$f]), serialize($arr[$f]));
            else
                $res .= sprintf("%s=%s;", $f, cache_grep_array($arr[$f]));
        }
        return $res;
    } else
        return $arr;
}

//сгенерировать ключ по всем данным GET, SESSION и COOKIE
function cache_key($use_session = false)
{
    if ($use_session)
        session_start();
    $raw = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $raw .= cache_grep_array($_GET);
    $raw .= cache_grep_array($_POST);

    if ($use_session) {
        $raw .= cache_grep_array($_SESSION);
        /*
        if ($_SERVER['REMOTE_ADDR'] == "85.12.198.87")
        var_dump(cache_grep_array($_SESSION));
        */

        $raw .= cache_grep_array($_COOKIE);
    }

    return md5($raw);
}

?>