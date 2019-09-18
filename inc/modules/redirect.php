<?

class redirect extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'redirect',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'redirect' => 'inner.html',

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
        $sobj = get_cache($_cn);
        if (!is_null($sobj))
            $page = unserialize($sobj);
        else
            $page = All::c_data_all($control->cid, $control->template);

        if (is_null($sobj)) {    //сохраняем кэш
            $sobj = serialize($page);
            set_cache($_cn, $sobj);
        }

        if (strlen(trim($page->link))) {
            header('Location: ' . $page->link);
            die();
        }

    }
}

?>