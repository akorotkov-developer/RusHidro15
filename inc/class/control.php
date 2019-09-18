<?php


class Control
{
    var $cid;
    var $bid;
    var $oper;
    var $page;
    var $template; // текущий шаблон
    var $parents; // массив предков
    var $level; // уровень вложенности
    var $name; // название страницы
    var $module; // управляющий модуль
    var $wrapper; // текущий враппер
    var $NESTEDSETS = true;

    function Control($init = true)
    {
        global $sql;
        global $pathstring;
        global $urlparams;
        global $config;
        global $wrappers;
        $this->sql = $sql;
        $this->all = new All();
        if (!$init) {
            $this->cid = 0;
            $this->parents = array(1);
            $this->level = 1;
            $this->currentPage = new stdClass();
            return;
        }

		require_once $config["DOCUMENT_ROOT"] . 'libs/Mobile_Detect.php';
		$mobile = new Mobile_Detect();
		if ($mobile->isMobile() or $_GET['mobile']==1 ) {
			$_SESSION['mobile']=1;
		} else {
			unset($_SESSION['mobile']);
		}


        //разбираем URL
        if (isset($_REQUEST['path'])) {

            //вычленение параметров
            if (strpos($_REQUEST['path'], '_') !== FALSE) {
                $urlparams = substr($_REQUEST['path'], strpos($_REQUEST['path'], '_') + 1, strlen($_REQUEST['path']));
                $_REQUEST['path'] = substr($_REQUEST['path'], 0, strpos($_REQUEST['path'], '_'));

            }

            if (strlen($_REQUEST['path']) && $_REQUEST['path'][strlen($_REQUEST['path']) - 1] != '/' && strpos($_REQUEST['path'], '_') === FALSE) {
                $_REQUEST['path'] .= "/";
            }

            $path = explode('/', $_REQUEST['path']);
            $cnt = count($path);
            $div = "";
            for ($i = 0; $i < $cnt - 1; $i++) {
                $pathstring .= $div . $path[$i];
                $div = "/";
            }
        }


        $p = $urlparams ? '_' . $urlparams : '';
        $this->url = $config['server_url'] . $pathstring . $p;

        if ($pathstring == '/main/') {
            $pathstring = '';
        }

        $pathstring = mysql_real_escape_string($pathstring);
        $alt_path = mysql_real_escape_string($alt_path);

        $q = "SELECT * FROM prname_tree WHERE url = '$pathstring' OR url = '$pathstring/' ";
        $str = $sql->fetch_array($sql->query($q));


		// ЧПУ для блоков
		if (!$str['id']) {
			$pathstring_arr = explode('/', $pathstring);
			$pathstring_block = array_pop($pathstring_arr);
			$pathstring_parent = implode('/', $pathstring_arr) . '/';

			$q2 = sprintf("SELECT bid, parent, btemplate FROM prname_burl WHERE parent_url = '%s' AND url = '%s' LIMIT 1 ", $pathstring_parent, $pathstring_block);
			$str2 = $sql->fetch_array($sql->query($q2));
		}
		

			if ($this->cid = $str['id']){}
			elseif ($this->bid = $str2['bid'])
			{
				$this->cid = $str2['parent'];
				$this->oper = 'view';
                $this->btemplate = $str2['btemplate'];
                
                $visible = $sql->one_record(sprintf(" SELECT visible FROM prname_b_%s WHERE id = %s LIMIT 1 ", $this->btemplate, $this->bid));
                if ($visible == 0) { $this->all->error(404); die(); }
			}
            else{$this->all->error(404); die();}

        $q = "SELECT * FROM prname_tree WHERE id = '$this->cid' ";
        $str = $sql->fetch_array($sql->query($q));
        $this->cid = $str['id'];
        $this->key = strlen($str['key']) ? $str['key'] : $str['id'];
        $this->level = $str['level'];
        $this->template = $str['template'];
        $this->name = $str['name'];
        $this->module_url = $config['server_url'] . $str['url'];

        //если страница enabled=0 - отображаем 404
        if ($str['enabled'] == 0) {
            $this->all->error(404);
            die();
        }


        $this->parents = tree::getparents_new($str['left_key'], $str['right_key']);


        // Параметры урла.
        $arr_param = explode("_", $urlparams);
        if (count($arr_param) > 0) {
            foreach ($arr_param as $arr_one) {
                switch ($arr_one[0]) {
                    case "p": {
                        $this->page = substr($arr_one, 1);
                        break;
                    }
                    case "a": {
                        $this->oper = substr($arr_one, 1);
                        break;
                    }
                    case "b": {
                        $this->bid = substr($arr_one, 1);
                        break;
                    }
                    default: {
                        $this->urlparams[$arr_one[0]] = substr($arr_one, 1);
                        break;
                    }
                }
            }
            if ($this->oper == 'view' && intval($this->bid) && !$this->btemplate) { $this->all->error(404); die(); }
		}


    }


    function Init()
    {
        global $sql;
        global $config;
        global $wrappers;
        global $dtimer;

        //формируем массив врапперов, подключая модули
        $this->sitemodules = array();
        $dir = $config['DOCUMENT_ROOT'] . 'inc/modules/';
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file, '_wraps.php') === false && strpos($file, '.php') !== false) {
                    if ($file == "_default_.php") continue;

                    $mname = str_replace('.php', '', $file);
                    $this->sitemodules[] = $mname;
                    //грузим для него враппер
                    if (is_file($dir . 'wrappers/' . $mname . "_wraps.php"))
                        include_once($dir . 'wrappers/' . $mname . "_wraps.php");
                    else {
                        //враппер-файл не найден, пробуем сормировать его автоматом
                        include_once($dir . $file);

                        unset($tmpobj);
                        $tmpobj = new $mname ();
                        if (method_exists($tmpobj, '_saveWrappers'))
                            $tmpobj->_saveWrappers();

                        include_once($dir . 'wrappers/' . $mname . "_wraps.php");
                    }
                }
            }
            closedir($handle);
        }


        $this->wrappers = $wrappers;


        $this->wrapper = $this->wrappers[$this->template]['html'];
        $this->module = $this->wrappers[$this->template]['module'];

        if ($dtimer)
            $dtimer->tick("control::init");

        if (trim($this->module) == '') {
            $this->all->error(404);
            die();
        }

    }

    function Make()
    {
        global $sql;
        global $config;
        global $wrappers;
        global $POST_VARS;
        global $ar_mon;
        global $dtimer;

        //каталог с модулями
        $mdir = $config['DOCUMENT_ROOT'] . 'inc/modules/';

        //каталог с шаблонами
        $tdir = $config['DOCUMENT_ROOT'] . 'templates/';		
		if($_SESSION['mobile']) $tdir = $config['DOCUMENT_ROOT'] . 'mobile/';		
		
        if (is_file($mdir . $this->module . ".php"))
            include_once($mdir . $this->module . ".php");
        else {
            $this->all->error(404);
            die();
        }

        $this->html = $this->all->read_file($tdir . $this->wrapper);
        $this->html = $this->html ? $this->html : 'Отсутствует файл ' . $tdir . $this->wrapper;

        preg_match_all('/<!--#control::(.*?)#(.*?)-->/Ui', $this->html, $main_modules);
        preg_match_all('/<!--#(.*?)#(.*?)-->/Ui', $this->html, $arr_modules);

        unset($mdl);
        unset($return_data);

        $mdl = new $this->module();
        //для текущего модуля в любом случае отрабатываем метод content
        if (method_exists($mdl, 'content'))
            $return_data = $mdl->content();

        $this->html = str_replace("<!--#control::content#-->", $return_data, $this->html);

        if ($dtimer)
            $dtimer->tick($this->module . "::content()");


        if (count($main_modules) > 0) {
            foreach ($main_modules[1] as $idx => $method) {
                if ($method == 'content') continue; //content уже отрабатывался, его не трогаем

                //формируем строку параметров
                unset($mparams);
                if (strlen($main_modules[2][$idx])) {
                    $parr = explode(' ', $main_modules[2][$idx]);
                    if (count($parr)) {
                        foreach ($parr as $one) {
                            if (trim($one) == "") continue;
                            $parr2 = explode('=', $one);
                            $mparams[$parr2[0]] = $parr2[1];
                        }
                    }
                }

                if ($method != 'content') {
                    unset($return_data);
                    $return_data = $mdl->$method($mparams);
                    $this->html = preg_replace('/<!--#control::' . $method . '#(.*?)-->/Ui', $return_data, $this->html, 1);

                    if ($dtimer)
                        $dtimer->tick($this->module . "::" . $method . "()");
                }
            }
        }

        /*
                $arr_keys = @array_keys($mdl->html);
                if (count($arr_keys) > 0)
                {
                    foreach ($arr_keys as $one_arr)
                    {
                        $this->html = str_replace("<!--#control::$one_arr#-->", $mdl->html[$one_arr], $this->html);
                    }
                }
        */
        unset($mdl);

        //миски (включаемые области)
        if (count($arr_modules) > 0) {
            foreach ($arr_modules[1] as $idx => $one_arr) {
                if (strstr($one_arr, 'control::')) continue;
                if (strpos($one_arr, '::') === false) continue;

                //название состоит из имени модуля и имени метода
                $name_arr = explode("::", $one_arr);
                $mname = $name_arr[0];
                //ограничение на длину названия модуля - 128 символов
                if (!strlen($mname) || strlen($mname) > 128) continue;
                $method = $name_arr[1];

                if (!is_file($mdir . $mname . ".php")) {
                    $tmp_file_body = $this->all->read_file($mdir . "_default_.php");
                    $tmp_file_body = str_replace('<!--name//-->', $mname, $tmp_file_body);
                    $tmp_file_body = str_replace('<!--year//-->', date('Y'), $tmp_file_body);

                    if ($fp = @fopen($mdir . $mname . ".php", 'w+')) {
                        fputs($fp, $tmp_file_body);
                        fclose($fp);
                    }
                }

                //формируем строку параметров
                unset($mparams);
                if (strlen($arr_modules[2][$idx])) {
                    $parr = explode(' ', $arr_modules[2][$idx]);
                    if (count($parr)) {
                        foreach ($parr as $one) {
                            if (trim($one) == "") continue;
                            $parr2 = explode('=', $one);
                            $mparams[$parr2[0]] = $parr2[1];
                        }
                    }
                }

                include_once($mdir . $mname . ".php");
                unset($misk);
                unset($miskhtml);
                $misk = new $mname ();
                $miskhtml = $misk->$method($mparams);//передаем параметры миску

                $this->html = preg_replace('/<!--#' . $one_arr . '#(.*?)-->/Ui', $miskhtml, $this->html, 1);

                if ($dtimer) {
                    $sparams = "";
                    if (is_array($mparams) && count($mparams))
                        foreach ($mparams as $f => $v)
                            $sparams .= sprintf("%s=%s ", $f, $v);

                    $dtimer->tick($mname . "::" . $method . "(" . $sparams . ")");
                }


            }
        }

        //$mail_admin = @mysql_result($sql->query("SELECT admin_email FROM prname_sadmin WHERE admin_id=4"), 0, 0);
        $this->html = str_replace(array('{base_url}', '<!--base_url//-->'), $config['server_url'], $this->html);

//       $this->parseMail($this->html);
        echo $this->html;

        // глобальное кеширование
        if (check_cache_enable()) {
            $uri = str_replace('/', '#', $_SERVER['REQUEST_URI']);
            set_cache($uri, $this->html);
        }
    }


    function parseMail(&$str)
    {
        $reg = "/<a[^>]*href=?\"mailto:([\w\.\-]+)@([\w\.\-]+)\.([a-zA-Z]{2,})?\"[^>]*>[^<]*<\/a>/i";
        $rep = "<script language=\"javascript\">putAddress('" . chr(rand(97, 120)) . chr(rand(97, 120)) . "\\2" . chr(rand(97, 120)) . chr(rand(97, 120)) . "', '\\1', '\\3');</script><noscript>Для отображения e mail адреса требуется поддержка javascript</noscript>";
        $str = preg_replace($reg, $rep, $str);
        return $str;
    }


//название страницы
    function name()
    {
        global $sql;
        global $all;
        if ($this->array_parent[0] != $this->cid) {
            $q = "SELECT name FROM prname_categories WHERE id = '$this->cid' ";
            $str1 = $sql->fetch_row($sql->query($q));
            $name = $str1[0];
        }
        return $name;
    }


//ошибки
    function error()
    {
        global $sql;
        global $config;

        $q = "SELECT id FROM prname_categories WHERE id = '$this->cid' ";
        $str1 = $sql->fetch_row($sql->query($q));
        if ($str1[0] > 0) {
        } else {
            $purl = $config['server_url'] . "404.php";
            header("location: $purl");
            exit;
        }
        return $text;
    }

}

?>