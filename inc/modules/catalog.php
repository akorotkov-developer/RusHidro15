<?

class catalog extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'catalog',
            'group',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'catalog' => 'inner.html',
            'group' => 'inner.html',

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

        if ($control->oper == "view")
            return $this->showGood();
        else
            return $this->showGrouplist();

    }


    function showGrouplist($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'showGrouplist', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $page = All::c_data_all($control->cid, $control->template);


        $tree = new Tree();
        $main_tree = $tree->tree_all($control->cid, 4);
        if ($main_tree->item)
            foreach ($main_tree->item as &$item) {
                $cat = All::c_data_all($item->id, 'group');
                $item->img = $cat->img;
                $item->description = $cat->description;
            }


        if ($main_tree->item)
            $page->item = $main_tree->item;
        else {
            //вложенных папок нет, наверное есть товары
            return $this->showGoodslist();
        }


        $html = $this->sprintt($page, $this->_tplDir() . "showGrouplist.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function showGoodslist($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'showGoodslist', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $page = All::c_data_all($control->cid, $control->template);

        $list = new Listing('goods', 'blocks', $control->cid);
        $list->limit = 20;
        $list->page = $control->page;
        $list->tmp_url = All::get_url($control->cid);
        $list->no_text_view = 1;

        $list->get_list();
        $list->get_item();

        $page->item = $list->item;


        $page->navigation = $list->navigation;
        $page->url_prev = $list->url_prev;
        $page->url_next = $list->url_next;


        $html = $this->sprintt($page, $this->_tplDir() . "showGoodslist.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function showGood($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'showGood', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $page = All::b_data_all($control->bid, 'goods');
        $control->name = $page->title;
        $page->price_format = number_format($page->price, 0, ',', ' ');

        $page->order_url = all::get_url(347) . "_b" . $page->id . "#sendform";

        $page->backurl = All::get_url($control->cid);
        if ($control->page > 0)
            $page->backurl .= '_p' . intval($control->page);


        $html = $this->sprintt($page, $this->_tplDir() . "showGood.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


    function catmenu($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'catmenu', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $tree = new Tree();
        $main_tree = $tree->tree_all(364, 4);
        $page->item = $main_tree->item;

        if ($control->template == 'catalog') $page->root = true;


        $html = $this->sprintt($page, $this->_tplDir() . "catmenu.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }


// <#AUTOMETHODS#>


}

?>