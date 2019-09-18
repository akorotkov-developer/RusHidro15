<?

/**
 * +-----------------------------------------------------------------------+
 * | Компания "Софтмажор"
 * +-----------------------------------------------------------------------+
 * | Разработано (c) 2007-2008,   http://www.softmajor.ru/
 * +-----------------------------------------------------------------------+
 * | Набор функций для работы с файлами и директориями.
 * | - check_dir()  (Проверяет директорию на наличие.)
 * | - greate_file()(Создаёт файл.)
 * | - del_file()   (Удаляет файл во всех вложенных директориях)
 * | - read_file()  (Читает файл как есть.)
 * | - copy_file()  (Копирует файл)
 * | - filesize()   (Размер файла.)
 * | - giveFile()   (Експорт файла.)
 * | - get_file_name()   (Имя файла.)
 * | - get_patch_name()   (Имя род директории.)
 * | - get_patch()   (url/ без файла.)
 * | - RecurseDir()   (Карта директорий.)
 * +-----------------------------------------------------------------------+
 **/
class file
{
    function check_dir($dirName, $rights = 0777)
    {
        $dir = explode("/", $dirName);
        for ($i = 0; $i < count($dir); $i++) {
            $direct .= $dir[$i];
            if (!is_dir($direct)) mkdir($direct, $rights);
            $direct .= "/";
        }
        return $direct;
    }

    function greate_file($file_name, $dirName, $content, $rights = 0777)
    {
        if (file::check_dir($dirName)) {
            $file = $dirName . "/" . $file_name;
            $fd = fopen($file, "w");
            flock($fd, LOCK_EX);
            fputs($fd, "$content");
            fflush($fd);
            fclose($fd);
            @chmod("$file", $rights);
        }
    }

    function del_file($file_name, $dirName)
    {
        $d = opendir($dirName);
        while (($e = readdir($d)) !== false) {
            if (is_dir($dirName . "$e")) {
                $c = opendir($dirName . "/" . $e);
                while (($f = readdir($c)) !== false) {
                    if (stristr($fname, $f) && strlen($f) == strlen($fname)) {
                        unlink($dirName . "$e" . "/" . $f);
                    }
                }
            }
        }
    }

    function read_file($file_name, $dirName)
    {
        return file_get_contents($dirName . "/" . $filname);
    }

    function copy_file($file_name, $dirName, $dirName_to, $file_name_new)
    {
        if (!$file_name_new) $file_name_new = all::get_random_name() . "." . strtoupper(substr($file_name, -3));
        file::greate_file($file_name_new, file::check_dir($dirName_to), file::read_file($file_name, $dirName));
        return $file_name_new;
    }

    function filesize($path)
    {
        $size = filesize($path);
        if (($size / 1024) < 1) {
            $text = $size . ' б';
        } else {
            if (($size / (1024 * 1024)) < 1) {
                $text = round($size / (1024), 2) . ' кб';
            } else {
                $text = round($size / (1024 * 1024), 4) . ' Мб';
            }
        }
        return $text;
    }

    function giveFile($data, $filename)
    {

        if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
            define('PMA_USR_BROWSER_VER', $log_version[2]);
            define('PMA_USR_BROWSER_AGENT', 'OPERA');
        } else if (ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
            define('PMA_USR_BROWSER_VER', $log_version[1]);
            define('PMA_USR_BROWSER_AGENT', 'IE');
        } else if (ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
            define('PMA_USR_BROWSER_VER', $log_version[1]);
            define('PMA_USR_BROWSER_AGENT', 'OMNIWEB');
        } else if (ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
            define('PMA_USR_BROWSER_VER', $log_version[2]);
            define('PMA_USR_BROWSER_AGENT', 'KONQUEROR');
        } else if (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)
            && ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2)
        ) {
            define('PMA_USR_BROWSER_VER', $log_version[1] . '.' . $log_version2[1]);
            define('PMA_USR_BROWSER_AGENT', 'SAFARI');
        } else if (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
            define('PMA_USR_BROWSER_VER', $log_version[1]);
            define('PMA_USR_BROWSER_AGENT', 'MOZILLA');
        } else {
            define('PMA_USR_BROWSER_VER', 0);
            define('PMA_USR_BROWSER_AGENT', 'OTHER');
        }
        header('Content-Type: application/octet-stream');
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        if (PMA_USR_BROWSER_AGENT == 'IE') {
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
        }
        echo $data;
        exit;
    }

    function get_file_name($url)
    {
        $patch = explode("/", $url);
        if (strstr($patch[count($patch) - 1], '.')) return $patch[count($patch) - 1];
    }

    function get_patch_name($url)
    {
        $patch = explode("/", $url);
        if (strstr($patch[count($patch) - 1], '.') or $patch[count($patch) - 1] == "") return $patch[count($patch) - 2];
        else return $patch[count($patch) - 1];
    }

    function get_patch($url)
    {
        $patch = explode("/", $url);
        if (strstr($patch[count($patch) - 1], '.')) return str_replace($patch[count($patch) - 1], "", $url);
        else if ($patch[count($patch) - 1] == "") return $url; else return $url . "/";
    }

    function RecurseDir($basedir, $AllDirectories = array())
    {
        #Create array for current directories contents
        $ThisDir = array();
        #switch to the directory we wish to scan
        chdir($basedir);
        $current = getcwd();
        #open current directory for reading
        $handle = opendir(".");
        while ($file = readdir($handle)) {
            #Don't add special directories '..' or '.' to the list
            if (($file != '..') & ($file != '.')) {
                if (is_dir($file)) {
                    #build an array of contents for this directory
                    array_push($ThisDir, $current . '/' . $file);
                }
            }
        }
        closedir($handle);
        #Loop through each directory,  run RecurseDir function on each one
        foreach ($ThisDir as $key => $var) {
            array_push($AllDirectories, $var);
            $AllDirectories = RecurseDir($var, $AllDirectories);
        }
        #make sure we go back to our origin
        chdir($basedir);
        return $AllDirectories;
    }
}

?>