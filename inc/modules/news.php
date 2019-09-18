<?

class news extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'news',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'news' => 'inner.html',

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


        if ($control->oper == 'view' && $control->cid == 404) {
            return $this->_showOne($control->bid);
        } if ($control->oper == 'view' && $control->cid == 403) {
			return $this->_showOneAdd($control->bid);
		}	
		else            
            return $this->_showList($arParams);
            
    }


    function _showList($arParams = array())
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'newsList', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        //достаем настройки
        $page = All::c_data_all($control->cid, $control->template);

        $list = new Listing('news_landing', 'blocks', $control->cid);//достаем блоки

        $list->limit = intval($page->cnt) ? $page->cnt : 10;
        $list->page = $control->page;//для постранички
        $list->sortfield = 'date DESC, id';//сортируем по дате
        $list->sortby = 'DESC';
        $list->tmp_url = $list->all->get_url($control->cid); //для постранички
        $list->no_text_view = 1; //не обрабатывать HTML-содержимое

        $list->get_list();
        $list->get_item();
        $page->item = $list->item;

        $page->navigation = $list->navigation;
        $page->url_prev = $list->url_prev;
        $page->url_next = $list->url_next;

        $page->rss_url = $list->tmp_url . '_arss';
        $page->name = $control->name;

        $html = $this->sprintt($page, $this->_tplDir() . "newsList.html");

        //сохраняем кэш
        set_cache($_cn, $html);
        return $html;

    }


    function _showOne($bid)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'newsOne', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        if ($bid) {
            $block = $control->all->b_data_all($bid, 'news_landing');
            $control->name = $block->title;
            $block->backurl = $control->all->get_url($control->cid);
            if (intval($control->page))
                $block->backurl .= '_p' . $control->page;
			
			$list = new Listing('news_landing_image', 'items', $bid);
			$list->get_list();
			$list->get_item();
			$block->gallery = $list->item;
			$block->gsize = sizeof($block->gallery);
			if($block->gsize<10) $block->gsize = '0'.$block->gsize;
			if($block->gsize==1) $block->alone = 1;

        }

        $html = $this->sprintt($block, $this->_tplDir() . "newsOne.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }

	function _showOneAdd($bid)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s", get_class($this), 'newsOne', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        if ($bid) {
            $block = $control->all->b_data_all($bid, 'news');
            $control->name = $block->title;
            $block->backurl = $control->all->get_url($control->cid);
            if (intval($control->page))
                $block->backurl .= '_p' . $control->page;
			
			$list = new Listing('news_image', 'items', $bid);
			$list->get_list();
			$list->get_item();
			$block->gallery = $list->item;
			$block->gsize = sizeof($block->gallery);
			if($block->gsize<10) $block->gsize = '0'.$block->gsize;
			if($block->gsize==1) $block->alone = 1;

        }

        $html = $this->sprintt($block, $this->_tplDir() . "newsOneAdd.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }

    function mainblock($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s", get_class($this), 'mainblock');
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        $newsID = 403;	
        
        date_default_timezone_set('Europe/Moscow');
		$adate1 = date(m);
		$adate2 = date(d);
		$adate3 = date(Y);

		$q = $sql->query("SELECT * FROM prname_b_news WHERE MONTH(`date`) = $adate1 AND DAYOFMONTH(`date`) = $adate2");
		if(!mysql_num_rows($q)) $q = $sql->query("SELECT * FROM prname_b_news WHERE MONTH(`date`) = $adate1 AND DAYOFMONTH(`date`) < $adate2");
		
		$i = 0;
		while($res = mysql_fetch_array($q)){
			$list->item[$i]->res = $adate3 - substr($res['date'],0,4);
			if($list->item[$i]->res==1) $list->item[$i]->restext = 'год';
			if($list->item[$i]->res>1 && $list->item[$i]->res<5 ) $list->item[$i]->restext = 'года';
			if($list->item[$i]->res>=5) $list->item[$i]->restext = 'лет';
			$list->item[$i]->date = all::get_date($res['date'],1);						
			$list->item[$i]->title = $res['title'];
			$list->item[$i]->url = '/novosti/'.$res['alt_url'];
			$i++;
		};        


        $page = new stdClass();
        $page->item = $list->item;		
		
        $html = $this->sprintt($page, $this->_tplDir() . "mainblock.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }

	function mainlist($arParams = NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s", get_class($this), 'mainblock');
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        $newsID = 404;			
		
        $list = new Listing('news_landing', 'blocks', $newsID);
        $list->limit = 2;
        $list->sortfield = 'date DESC, id';
        $list->sortby = 'desc';        
        $list->get_list();
        $list->get_item();

        $page = new stdClass();
        $page->item = $list->item;
		
        $html = $this->sprintt($page, $this->_tplDir() . "mainlist.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;
    }
    


// <#AUTOMETHODS#>


}

?>