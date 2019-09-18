<?

class historyhydro extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'historyhydro',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'historyhydro' => 'ajax.html',

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
            $block = $control->all->b_data_all($bid, 'historyhydro');
            $control->name = $block->title;			
			
			$list = new Listing('historyhydro_gallery', 'items', $bid);
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
		
		mb_internal_encoding("UTF-8");
		
        $list = new Listing('historyhydro', 'blocks', 408);
        $list->get_list();
        $list->get_item();
		
		$page->item = $list->item;
		
		foreach( $page->item as $item ) {			
			$tarray = explode(" ",$item->html);
			$tarray = array_slice($tarray,0,60); 			
			$item->text = strip_tags(implode(" ",$tarray)).'...';
			
			$list = new Listing('historyhydro_gallery', 'items', $item->id);
			$list->get_list();
			$list->get_item();
			$item->gallery = $list->item;
			$item->gsize = sizeof($item->gallery);
			if($item->gsize<10) $item->gsize = '0'.$item->gsize;
			if($item->gsize==1) $item->alone = 1;
		}

        $html = $this->sprintt($page, $this->_tplDir() . "page.html");

        //сохраняем кэш
        set_cache($_cn, $html);
        return $html;

    }


    

// <#AUTOMETHODS#>


}

?>