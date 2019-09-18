<?

class text extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'text',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'text' => 'ajax.html',

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

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'content', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        //попытка загрузить из кеша поля папки
        $_cn2 = sprintf("%s_%s_%s-fields", get_class($this), 'content', cache_key());
        $sdata = get_cache($_cn2);
        if (!is_null($sdata) && strlen($sdata))
            $fields = unserialize($sdata);
        else
            $fields = All::c_data_all($control->cid, $control->template);

        if (intval($fields->goto_sub)) //требование перехода на первый подраздел
        {
            $cats = All::get_cats($control->cid);
            if ($cats[0]['id']) {
                $url = All::get_url($cats[0]['id']);
                $url = str_replace('<!--base_url//-->', '/', $url);

                //сохраняем кэш
                set_cache($_cn2, serialize($fields));

                header('Location: ' . $url);
                die();
            }
        }

        $page = new Listing('text', 'blocks', $control->cid);
        $page->get_list();
        $page->get_item();

        if (intval($fields->show_sub)) //требование показа подменю
        {
            $tree = new Tree();
            $main_tree = $tree->tree_all($control->cid, 4);
            $page->submenu = $main_tree->item;
        }

        $html = $this->sprintt($page, $this->_tplDir() . "content.html");

        //сохраняем кэш
        set_cache($_cn, $html);
        set_cache($_cn2, serialize($fields));
		

        return $html;
    }
}

?>