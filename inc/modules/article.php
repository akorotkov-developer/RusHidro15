<?

class article extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'article',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'article' => 'inner.html',

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

        if ($control->template == 'article' && $control->oper != 'view') {
            return $this->_showList();
        } elseif ($control->template == 'article' && $control->oper == 'view') {
            return $this->_showOne();
        }
    }

    function _showList()
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'list', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        //достаем настройки
        $page = All::c_data_all($control->cid, $control->template);

        $list = new Listing('article', 'blocks', $control->cid);//достаем блоки статей
        $list->limit = intval($page->cnt) ? $page->cnt : 10;
        $list->page = $control->page;//для постранички
        $list->sortfield = 'date DESC, id';//сортируем по дате
        $list->sortby = 'desc';
        $list->tmp_url = $list->all->get_url($control->cid); //для постранички
        $list->no_text_view = 1; //не обрабатывать HTML-содержимое статей

        $list->get_list();
        $list->get_item();
        $page->item = $list->item;

        $page->navigation = $list->navigation;
        $page->url_prev = $list->url_prev;
        $page->url_next = $list->url_next;


        $html = $this->sprintt($page, $this->_tplDir() . "list.html");

        //сохраняем кэш
        set_cache($_cn, $html);
        return $html;

    }

    function _showOne()
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'one', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $block = all::b_data_all($control->bid, 'article');
        $control->name = $block->title;
        $block->backurl = All::get_url($control->cid);
        if (intval($control->page))
            $block->backurl .= '_p' . $control->page;


        $html = $this->sprintt($block, $this->_tplDir() . "one.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }
}

?>