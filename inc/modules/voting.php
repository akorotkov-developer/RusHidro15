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
		$i = 0;

        //Определим что мы находимся на странице победители
        $isPobediteli = strpos($_SERVER['REQUEST_URI'], "/energiya-talanta/pobediteli/");

		foreach($list->item as $item) {
		    if ($isPobediteli) {
                $query = 'SELECT * FROM vote_table_finalvoting WHERE vote_id = "' . $item->id . 'video"';
            } else {
                $query = 'SELECT * FROM vote_tablenew WHERE vote_id = "' . $item->id . 'video"';
            }
            $res = $sql->query($query);
            $arrCounts = array();
            while ($arr = $sql->fetch_assoc($res)) {
                $arrCounts[] = $arr['vote_count'];
            }
            if (count($arrCounts) > 0) {
                $count = max($arrCounts);
                $item->count = $count;
            } else {
                $item->count = "";
            }

			if($item->genre == 1) {
                $item->nomination = "song";
			    $page->song[$ar] = $item;
			}
			if($item->genre == 2) {
                $item->nomination = "dance";
			    $page->dance[$ar] = $item;
			}
			if($item->genre == 3) {
                $item->nomination = "original";
			    $page->original[$ar] = $item;
			}
			$ar ++;
		}

        //Определим что мы находимся на странице победители
        $isPobediteli = strpos($_SERVER['REQUEST_URI'], "/energiya-talanta/pobediteli/");
        if ($isPobediteli) {
            $page->isVotingTrue = true;
        } else {
            $page->noVoting = true;
        }
		
        $html = $this->sprintt($page, $this->_tplDir() . "list.html");

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

        $query = 'SELECT * FROM vote_tablenew WHERE vote_id = "' . $bid . 'video"';
        $res = $sql->query($query);
        $arrCounts = array();
        while ($arr = $sql->fetch_assoc($res)) {
            $arrCounts[] = $arr['vote_count'];
        }
        if (count($arrCounts) > 0) {
            $count = max($arrCounts);
            $page->count = $count;
        } else {
            $page->count = "";
        }

		$addHtml .= '<div class="voting-video-info'.$voted.'">';
		if($page->photo) $addHtml .= '<img class="voting-video-person" src="/images/1/'.$page->photo.'" alt="" />';
		$addHtml .= '<div class="voting-item-info">';
		$addHtml .= '<div class="voting-item-name"><div class="work_name">'.$page->fio.'</div><div class="voting-item-count"><span>'.$page->count.'</span><i></i></div></div>';
		$addHtml .= '<div class="voting-item-link"><p><b>'.$page->org.'</b></p><div class="voting-item-button" data-id="vote'.$page->id.'" data-genre="'.$page->genre.'"><i></i><span>Голосовать</span></div></div>';
		$addHtml .= '<div class="voting-item-text">'.$page->about.'</div>';
		$addHtml .= '</div>';
		$addHtml .= '</div>';

        return $addHtml;
    }

    function voteaddphoto($bid, $useragent, $sect, $work_name, $islitra, $isvideo, $nomination)
    {
        global $control;
        global $config;
        global $sql;

        //Ищем голосовал ли наш пользователь уже или нет
        $arrData = array();
        $vowels = array(";", "(", ")", "/", ",");
        $useragentStart = $useragent;
        $useragent = str_replace($vowels, "", $useragent);

        $remote = $_SERVER['REMOTE_ADDR'].$useragent;
        if ($islitra != "true" and $isvideo != "true") {
            $bid = str_replace("litra", "", $bid);
            $bid = str_replace("video", "", $bid);
        }
        if ($isvideo == "true") {
            $bid = str_replace("litra", "", $bid);
        }
        if ($islitra == "true") {
            $bid = str_replace("video", "", $bid);
        }

        if (!$isvideo and !$islitra) return "Голосование закрыто";

        $query = 'Select *, max(vote_count) From vote_table_finalvoting WHERE sectioncolumn = " video " AND vote_ipadress = "' . $remote . '" AND nomination = " ' . $nomination . ' "  group by work_name ';

        $res = $sql->query($query);
        while ($arr = $sql->fetch_assoc($res)) {
            $arItems[] = $arr;
        }

        if (count($arItems) >= 1) {
            $data->message = 'Вы не можете голосовать больше чем за 1 работу в одной номинации';
            return $data;
        }

        $query = 'SELECT * FROM vote_table_finalvoting WHERE vote_ipadress = "' . $remote . '" AND vote_id ="' . $bid . '"';
        $res = $sql->query($query);
        while ($arr = $sql->fetch_assoc($res)) {
            $arrData[] = $arr;
        }

        if (count($arrData) > 0) {
            //Если нашлась запись, значит наш пользователь уже голосовал
            $data->message = 'Вы можете проголосовать за одну работу раз';
        } else {
            //Если запись не нашлась, то ищем все голоса за эту картинку, получаем максимальное значение и прибавляем 1
            $arrCounts = array();
            $query = 'SELECT * FROM vote_table_finalvoting WHERE vote_id = "' . $bid . '"';
            $res = $sql->query($query);
            while ($arr = $sql->fetch_assoc($res)) {
                $arrCounts[] = $arr['vote_count'];
            }
            if (count($arrCounts) > 0) {
                $count = max($arrCounts) + 1;
            } else {
                $count = 1;
            }

            $query = 'INSERT INTO vote_table_finalvoting (vote_id, vote_ipadress, sectioncolumn, work_name, vote_count, nomination) VALUES ("' . $bid . '", "' . $remote . '", " ' . $sect . ' ", " ' . addslashes($work_name) . ' ", " ' . $count . ' ", " ' . $nomination . ' ")';
            $sql->query($query);

            $data->bcount = $count;
        }

        return $data;
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