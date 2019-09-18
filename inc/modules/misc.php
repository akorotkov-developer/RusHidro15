<?

/*
###############################################################################
#  (c)SOFTMAJOR 2011-2011
###############################################################################
#  ЗДЕСЬ РАЗМЕСТИТЬ КОНТАКТНЫЕ ДАННЫЕ АВТОРА МОДУЛЯ
################################################################################
   ЗДЕСЬ РАЗМЕСТИТЬ КРАТКОЕ ОПИСАНИЕ ЕГО НАЗНАЧЕНИЯ И Т.П.
*/

class misc extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(/* раскомментируй это при необходимости
        'misc',
*/
        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(/* раскомментируй это при необходимости
        'misc' => 'inner.html',
*/
        );
    }

    function __destruct()
    {
    }

    //базовый метод сайт-модуля
    function content($arParams = array())
    {
        global $control;
        global $config;
        global $sql;

        return "Контент модуля <b>misc</b>";

        //раскомментировать при доработке
        //return $this->sprintt($page, $this->_tplDir().'content.html');
    }


    //вернуть содержимое поля Главной страницы
    function showvar($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'showvar' . md5(serialize($arParams)), cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        if (!isset($control->indexPage)) {
            $control->indexPage = clone(All::c_data_all(1, 'index'));
        }
        $page = new stdClass();

        $page->html = $control->indexPage->{$arParams['var']};

//        $html = $this->sprintt($page, $this->_tplDir()."showvar.html");
        $html = $page->html;

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    //формирование заголовка страницы
    function head($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'head', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        $page = new stdClass();
        $sitename = $control->all->c_data(1, 'index', 'sitename');
        if ($control->cid != 1) {
            $page->title = $control->name . ($sitename ? ' - ' . $sitename : '');
            $page->inner = 1;
        } else {
            $page->title = $sitename;
            $page->index = 1;
        }

        if (isset($control->title))
            $page->title = $control->title;

        if (!isset($control->currentPage)) {
            $control->currentPage = clone(All:: c_data_all($control->cid, $control->template));
        }

        $cat = clone($control->currentPage);

        if (isset($cat->meta)) {
            $arr_data = explode('|---|', $cat->meta);
            if ($arr_data[0] != '') {
                $page->title = $arr_data[0];
            }
            $page->keywords = $arr_data[1];
            $page->description = $arr_data[2];
        }


        //контроль версий основных CSS и JS файлов
        $cfiles = array('style.css', 'resize.css');
        $jfiles = array('scripts.js');

        $files = array();
        if ($cfiles) {
            foreach ($cfiles as $cfile) {
                $files[] = $config['DOCUMENT_ROOT'] . 'styles/' . $cfile;
            }
        }
        if ($jfiles) {
            foreach ($jfiles as $jfile) {
                $files[] = $config['DOCUMENT_ROOT'] . 'scripts/' . $jfile;
            }
        }

        $version = 0;
        //если хотя-бы один из файлов моложе 7 суток,дописываем префикс версии
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) > time() - 86400 * 7) {
                if (filemtime($file) > $version)
                    $version = filemtime($file);
            }
        }

        if ($version > 0)
            $page->version = $version;
		
		if($_GET['hamburger']) $page->hamburger = $_GET['hamburger'];

        $page->template = $control->template;
		
        $html = $this->sprintt($page, $this->_tplDir() . "head.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function mainmenu($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'mainmenu' . $arParams['type'], cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        $main_tree = Tree::tree_all(1, 4);
        $page = $main_tree;

        if ($arParams['type'] == 'bottom')
            $html = $this->sprintt($page, $this->_tplDir() . "mainmenu_bottom.html");
        else
            $html = $this->sprintt($page, $this->_tplDir() . "mainmenu.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function submenu($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        if ($control->module != 'text') return '';

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'submenu', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $page = new stdClass();
        $tree = new Tree();
        $main_tree = $tree->tree_all($control->parents[1], 4);
        $page->item = $main_tree->item;


        $html = $this->sprintt($page, $this->_tplDir() . "submenu.html");
        //сохраняем кэш
        set_cache($_cn, $html);
        return $html;
    }


    function pricelist($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'pricelist', cache_key());
        $html = get_cache($_cn, 7200);
        if (!is_null($html)) return $html;

        if (!isset($control->indexPage)) {
            $control->indexPage = clone(All:: c_data_all(1, 'index'));
        }

        if (is_file('files/' . $control->indexPage->pricelist)) {
            $page->pricelist = $control->indexPage->pricelist;
            $page->sizekb = round(filesize('files/' . $control->indexPage->pricelist) / 1024);
            $pinfo = pathinfo('files/' . $control->indexPage->pricelist);
            $page->ext = strtolower($pinfo['extension']);
            //классы xls, zip, doc, jpg, pdf, pps, rtf, exe-->
            $types = array('xls', 'zip', 'doc', 'jpg', 'pdf', 'pps', 'rtf', 'exe');
            if (in_array($page->ext, $types))
                $page->filetype = $page->ext;
            else
                $page->filetype = 'doc';

            $page->filedate = date('d.m.Y', filemtime('files/' . $control->indexPage->pricelist));
        }

        $html = $this->sprintt($page, $this->_tplDir() . "pricelist.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function teaserblock($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'teaserblock', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        if (!isset($control->indexPage)) {
            $control->indexPage = clone(All::c_data_all(1, 'index'));
        }
        $page = clone($control->indexPage);

        $list = new Listing('teaser', 'blocks', 1);
        $list->get_list();
        $list->get_item();
        $page->item = $list->item;


        $html = $this->sprintt($page, $this->_tplDir() . "teaserblock.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function path($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'path', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;
		
        $page = new stdClass();
        $page->item = $control->all->get_parents();
        $page->oper = $control->oper;
		if($control->cid == 403) $page->noshow = 1;

        $html = $this->sprintt($page, $this->_tplDir() . "path.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function name($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        if ($control->template == 'news' and $control->oper !== 'view') return;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'name', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        $page = new stdClass();
        $page->name = $control->name;

        $html = $this->sprintt($page, $this->_tplDir() . "name.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function banners($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'banners', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $list = new Listing('banner', 'blocks', 391);
        $list->get_list();
        $list->get_item();

        if ($list->item)
            foreach ($list->item as $item) {
                $pinfo = pathinfo($item->img);
                $ext = strtolower($pinfo['extension']);

                if (strtolower($ext) == "swf") {
                    $item->isFlash = 1;
                    $size = getimagesize($config["DOCUMENT_ROOT"] . "images/0/" . $item->img);

                    $item->height = intval($size[1] / $size[0] * 182);
                }

            }

        $page->item = $list->item;


        $html = $this->sprintt($page, $this->_tplDir() . "banners.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function online($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'online', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        if (!isset($control->indexPage)) {
            $control->indexPage = clone(All:: c_data_all(1, 'index'));
        }
        $page = clone($control->indexPage);

        if (intval($page->online_check))
            $page->online_show = 1;


        $html = $this->sprintt($page, $this->_tplDir() . "online.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }

	function topinfo($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'topinfo', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;
        
        $list = new Listing('topinfo', 'blocks', 1);
        $list->get_list();
        $list->get_item();
        $page->item = $list->item;		
		
		foreach($page->item as $item) {
			 $f = substr($item->img, 0, strrpos($item->img, '.'));
			 $item->image = $f;
		}
		
        $html = $this->sprintt($page, $this->_tplDir() . "topinfo.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }   

   


    function counters($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s",get_class($this), 'counters',  cache_key()); 
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

// => ТУТ рабочий код метода
// ...
// ...
// ...
// <= ТУТ рабочий код метода

        $html = $this->sprintt($page, $this->_tplDir()."counters.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;        
    }

// <#AUTOMETHODS#>





}

?>