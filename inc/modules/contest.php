<?

class contest extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'contest',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'contest' => 'inner.html',

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
        } else
            if ($control->oper == 'rss') {
                return $this->_rssList();
            } else {
                return $this->_showList($arParams);
            }
    }


    function _showList($arParams = array())
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

        $list = new Listing('contest', 'blocks', $control->cid);

        $list->limit = intval($page->cnt) ? $page->cnt : 10;
        $list->page = $control->page;//для постранички
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

        $html = $this->sprintt($page, $this->_tplDir() . "list.html");

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
        $_cn = sprintf("%s_%s_%s", get_class($this), 'one', cache_key());
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

        if ($bid) {
            $block = $control->all->b_data_all($bid, 'contest');
            $control->name = $block->title;
            $block->backurl = $control->all->get_url($control->cid);
            if (intval($control->page))
                $block->backurl .= '_p' . $control->page;
			
			$list = new Listing('photo_image', 'items', $bid);
			$list->get_list();
			$list->get_item();
			$block->gallery = $list->item;
			$block->gsize = sizeof($block->gallery);
			if($block->gsize<10) $block->gsize = '0'.$block->gsize;
			if($block->gsize==1) $block->alone = 1;

        }
        if ($bid == 16 or $bid == 17 or $bid == 18 or $bid == 19 or $bid == 20 or
            $bid == 21 or $bid == 4 or $bid == 2 or $bid == 3 or $bid == 5  or $bid == 6) {
            $isvote = false;
        } else {
            $isvote = true;
        }

        if ($isvote) {
            $query = 'SELECT * FROM vote_tablenew WHERE vote_id = "' . $bid . 'litra"';
            $res = $sql->query($query);
            while ($arr = $sql->fetch_assoc($res)) {
                $arrCounts[] = $arr['vote_count'];
            }
            if (count($arrCounts) > 0) {
                $count = max($arrCounts);
            }

            $block->idforvote = $bid;

            $block->voteCount = $count;
            $block->isVote = 'true';
        }

        $html = $this->sprintt($block, $this->_tplDir() . "one.html");

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

        $newsID = 405;					
		
        $list = new Listing('contest', 'blocks', $newsID);
        $list->limit = 2;        
        $list->get_list();
        $list->get_item();

        $page = new stdClass();
        $page->item = $list->item;
		
		$page->alink = All::get_url($newsID);		
		
        $html = $this->sprintt($page, $this->_tplDir() . "mainlist.html");

        //сохраняем кэш
        set_cache($_cn, $html);



        return $html;
    }


    

// <#AUTOMETHODS#>


}

?>