<?

class history extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'history',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'history' => 'ajax.html',

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

       
    }


    function page($arParams = array())
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'page', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        $list = new Listing('history', 'blocks', 350);
        $list->get_list();
        $list->get_item();
		
        $page->item = $list->item;

        $html = $this->sprintt($page, $this->_tplDir() . "page.html");

        //сохраняем кэш
        set_cache($_cn, $html);
        return $html;

    }

	function load()
    {
        global $control;
        global $config;
        global $sql;
		
		$q = $sql->query("SELECT image FROM prname_b_history WHERE visible=1");
		$i = 1;
		while($res = mysql_fetch_array($q)){			
			$page->item[$i]->image = $res['image'];
			$i++;
		};
        
        $html = $this->sprintt($page, $this->_tplDir() . "load.html");
        
        return $html;

    }
    

// <#AUTOMETHODS#>


}

?>