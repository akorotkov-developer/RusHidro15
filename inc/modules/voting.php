<?

class voting extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'voting',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'voting' => 'inner.html',

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
		
		
		//setcookie('smfilter','',-1000,'/');
		
        return $this->_showList($arParams);
        
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
        
        $page->name = $control->name;
		
		$list = new Listing('golosovanie', 'blocks', $control->cid);
        $list->no_text_view = 1; //не обрабатывать HTML-содержимое

        $list->get_list();
        $list->get_item();        
		
		$smfilter = json_decode($_COOKIE['smfilter'],true);
		
		if($smfilter)
		foreach($list->item as $item) {
			if ($smfilter['bid'][$item->id]) $item->voted = 1; 			
		}	
	
		$ar = 0;
		foreach($list->item as $item) {
			
			if($item->genre == 1) { $page->song[$ar] = $item; }
			if($item->genre == 2) { $page->dance[$ar] = $item; }
			if($item->genre == 3) { $page->original[$ar] = $item; }
			$ar ++;
		}
		
        $html = $this->sprintt($page, $this->_tplDir() . "list.html");

		mail("89065267799@mail.ru", "Тема письма", print_r($html, true));
        //сохраняем кэш
        set_cache($_cn, $html);
        return $html;

    }
    
	
	function voteinfo($bid)
    {
        global $control;
        global $config;
        global $sql;
		
		$page = All::b_data_all($bid, 'golosovanie');
		$voted = '';
		$smfilter = json_decode($_COOKIE['smfilter'],true);
		if($smfilter && $smfilter['bid'][$bid]) $voted = ' voted';
		
		if(!$page->video_link) {
			$addHtml ='<div class="voting-video-player"><video width="100%" height="350" controls="controls" poster="" ><source src="/files/'.$page->video.'" type="video/mp4" /></video></div>';
		} else {
			$addHtml ='<div class="voting-video-player"><video width="100%" height="350" controls="controls" poster="" ><source src="/files/voting/'.$page->video_link.'" type="video/mp4" /></video></div>';
		}
		
		$addHtml .= '<div class="voting-video-info'.$voted.'">';
		if($page->photo) $addHtml .= '<img class="voting-video-person" src="/images/1/'.$page->photo.'" alt="" />';
		$addHtml .= '<div class="voting-item-info">';
		$addHtml .= '<div class="voting-item-name">'.$page->fio.'<div class="voting-item-count"><span>'.$page->count.'</span><i></i></div></div>';
		$addHtml .= '<div class="voting-item-link"><a href="'.$page->org_link.'">'.$page->org.'</a><div class="voting-item-button" data-id="vote'.$page->id.'" data-genre="'.$page->genre.'"><i></i><span>Голосовать</span></div></div>';
		$addHtml .= '<div class="voting-item-text">'.$page->about.'</div>';
		$addHtml .= '</div>';
		$addHtml .= '</div>';
		
        return $addHtml;
    }
	
	function voteadd($bid,$genre)
    {
        global $control;
        global $config;
        global $sql;
		
		$ip = false;
		$ip_text = '';
		$ip_text = All::b_data($bid,'ip','golosovanie');
		$block_count = All::b_data($bid,'count','golosovanie');
		$fio = All::b_data($bid,'fio','golosovanie');
		$votemax = All::c_data(410, 'voting', 'votemax');
		$ending = '';
		if($votemax>1 && $votemax<5) $ending = 'а';
		$pref = '';
		$remote = $_SERVER['REMOTE_ADDR'];
		
		if($ip_text) {
			$pref = ',';
			$ip_arr = explode(',',$ip_text);						
			$ip = in_array($remote,$ip_arr);			
		}		
		
		if(!isset($_COOKIE['smfilter'])) {
			$smfilter = array();						
			
		} else $smfilter = json_decode($_COOKIE['smfilter'],true);
		
		if(isset($smfilter)) {
			
			if($smfilter['genre'][$genre]===NULL) $smfilter['genre'][$genre] = $votemax;
			
			if($smfilter['genre'][$genre]>0) {
				
				if(!$smfilter['bid'][$bid]) {
					$smfilter['bid'][$bid] = 1; 
					$ip_text = $ip_text.$pref.$remote; 
					if(!$ip) All::update_block($bid, 'golosovanie', $ip_text, 'ip');  
					$block_count++;
					All::update_block($bid, 'golosovanie', $block_count, 'count');  
					$smfilter['genre'][$genre] = $smfilter['genre'][$genre] -1;
					$data->bcount = $block_count;
					
					setcookie('smfilter',json_encode($smfilter),time()+31536000, '/');	
					
					//собираем данные
					$textdata = '
					IP адрес: '.$remote.'
					';
					$textdata .= '
					Дата и время голосования: '.date('d.m.y H:i').'
					';
					$textdata .= '
					ID блока, за который проголосовали: '.$bid.', ФИО: '.$fio.'
					';
					$textdata .= $_SERVER['HTTP_USER_AGENT'];		
					$textdata .= '
					---------------------------------------
					';
					
					$arr['text'] = $textdata;
					All::insert_block('vote_log',410, $arr, 1 );  
					
				} else {
					$data->message = 'Вы можете проголосовать за одну работу раз';
				}
			} else {
				$data->message = 'Вы можете проголосовать за один из жанров только '.$votemax.' раз'.$ending;
			}
		}
		
		
        return $data;
    }

// <#AUTOMETHODS#>


}


















?>