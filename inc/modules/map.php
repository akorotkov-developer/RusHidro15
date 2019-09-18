<?


class map extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'map',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'map' => 'inner.html',

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
        $_cn = sprintf("%s_%s", get_class($this), 'map');
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        $page = new stdClass();
        $tree = new Tree();
        $main_tree = $tree->tree_all(1, 4);
        $page->item = $main_tree->item;

        $html = $this->sprintt($page, $this->_tplDir() . "map.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;


    }

    function sitemap($arParams = array())
    {

        global $control;
        global $config;
        global $sql;


        $item->url = "/";//$config['server_url'];
        $page->item[0] = $item;


        $page->server = $config['server_url'];
        if (substr($page->server, -1, 1) == "/") {
            $page->server = substr($page->server, 0, -1);
        }


        $tree = new Tree();
        $main_tree = $tree->tree_all(1, 4);
        $page->item[0]->item = $main_tree->item;


        $html = $this->sprintt($page, $this->_tplDir() . "sitemap.xml");

        echo $html;
        die();

        return $html;

    }


}

?>
