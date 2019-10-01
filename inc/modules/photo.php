<?
class photo extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'photo',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'photo' => 'inner.html',

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
            return $this->showOne($arParams);
        } else {
            return $this->showList($arParams);
        }

    }

    function showList($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s",get_class($this), 'showList',  cache_key()); 
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $page = All::c_data_all($control->cid, $control->template);

        $list = new Listing('photo', 'blocks', $control->cid);
        $list->limit = 100;
        $list->no_text_view = 1;
        $list->get_list();
        $list->get_item();
        $page->item = $list->item;
        

        $html = $this->sprintt($page, $this->_tplDir()."showList.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;        
    }
    
    function showOne($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;


        //попытка загрузить из кэша
        $_cn = sprintf("%s_%s_%s",get_class($this), 'showOne',  cache_key()); 
        $html = get_cache($_cn);
        if (!is_null($html)) return $html;


        $bid = intval($control->bid);
        $page = All::b_data_all($bid, 'photo');
        $control->name = $page->title;
        $page->backurl = All::get_url($control->cid);
        if (intval($control->page))
            $page->backurl .= '_p' . $control->page;

        $list = new Listing('photo_image', 'items', $bid);
        $list->limit = intval($page->cnt) ? $page->cnt : 12;
        $list->page = $control->page;
        $list->tmp_url = $list->all->get_url($control->cid) . $page->alt_url . '/';
        $list->no_text_view = 1;
        $list->get_list();
        $list->get_item();
        $page->item = $list->item;

        $page->navigation = $list->navigation;
        $page->url_prev = $list->url_prev;
        $page->url_next = $list->url_next;

        foreach ($page->item as $key => $item) {
            $arrCounts = array();
            $count = '';

            $query = 'SELECT * FROM vote_table WHERE vote_id = "' . $item->id . '"';
            $res = $sql->query($query);
            while ($arr = $sql->fetch_assoc($res)) {
                $arrCounts[] = $arr['vote_count'];
            }
            if (count($arrCounts) > 0) {
                $count = max($arrCounts);
            }

            $page->item[$key]->voteCount = $count;
        }

        if (true) {
            /*Записываем результаты конкурса*/
            $query = "SELECT DISTINCT work_name FROM vote_table WHERE sectioncolumn = ' photo '";
            $res = $sql->query($query);
            while ($arr = $sql->fetch_assoc($res)) {
                $arrData[] = $arr;
            }


            $i = 0; $arrContent = array();
            foreach ($arrData as $item) {
                if (!empty($item["work_name"])) {
                    $query = "SELECT * FROM vote_table WHERE work_name = '" . $item["work_name"] . "' ORDER BY vote_count DESC LIMIT 1";
                    $res = $sql->query($query);
                    while ($arr = $sql->fetch_assoc($res)) {
                        $arrContent[$i]["vote"] = $arr["vote_count"];
                        $arrContent[$i]["text"] = "<b>" . $arr["work_name"] . "</b> - голосов " . $arr["vote_count"] . "<br>";
                    }
                }
                $i++;
            }

            //По убыванию:
            function cmp_function($a, $b){
                return ($a['vote'] < $b['vote']);
            }
            uasort($arrContent, 'cmp_function');

            $content = "<div class=\"results\">";
            foreach ($arrContent as $item) {
                $content .= $item["text"];
            }
            $content .=  "</div>";

            $resultFile = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/concursresults/photo/index.php');
            $resultFile = preg_replace('|(<!--begin-photo-->).+(<!--end-photo-->)|isU', "$1".$content."$2",$resultFile);

            $fp = fopen($_SERVER['DOCUMENT_ROOT'] ."/concursresults/photo/index.php", "w"); // Открываем файл в режиме записи
            fwrite($fp, $resultFile); // Запись в файл
            fclose($fp); //Закрытие файла*/
            /*************/
        }

        $html = $this->sprintt($page, $this->_tplDir()."showOne.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;        
    }
    
// <#AUTOMETHODS#>

}

?>