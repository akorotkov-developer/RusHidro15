<?

class statuspage extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'statuspage',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'statuspage' => 'ajax.html',

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
		
		if ($control->oper == 'view') {
            return $this->_showOne($control->bid);
		}
       
    }

	function _showOne($bid)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'one', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        if ($bid) {
            $block = $control->all->b_data_all($bid, 'statuspage');
            $control->name = $block->title;			
			
			$list = new Listing('statuspage_gallery', 'items', $bid);
			$list->get_list();
			$list->get_item();
			$block->gallery = $list->item;
			$block->gsize = sizeof($block->gallery);
			if($block->gsize<10) $block->gsize = '0'.$block->gsize;
            if($block->gsize==1) $block->alone = 1;
        }

        $html = $this->sprintt($block, $this->_tplDir() . "one.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
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

        $list = new Listing('statuspage', 'blocks', 402);
        $list->get_list();
        $list->get_item();
		
        $page->item = $list->item;		
		
		$list = new Listing('objects_gen', 'blocks', 402);
        $list->get_list();
        $list->get_item();
		
        $page->objects = $list->item;
		
        $html = $this->sprintt($page, $this->_tplDir() . "page.html");

        //сохраняем кэш
        set_cache($_cn, $html);
        return $html;

    }


    

// <#AUTOMETHODS#>


}

?>