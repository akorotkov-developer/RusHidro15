<?php

class Listing
{
    var $cid;
    var $type = 'blocks';
    var $template;
    var $sortfield = '';
    var $sortby = 'ASC';
    var $pageshag = 6; // Количество страниц
    var $this = 0;
    var $start = 0;
    var $limit = 1000;
    var $rand = false;
    var $count;
    var $countlist = 0;
    var $items = array();
    var $thiss = array();
    var $all;

    function Listing($template, $type = 'blocks', $cid = '', $critery = '')
    {
        global $control;
        global $sql;
        $this->all = new All();
        $this->cid = $cid ? $cid : $control->cid;
        $this->parent = $this->cid == 'all' ? '' : "parent = '$this->cid' and";
        $this->template = $template;
        $this->critery = $critery;
        $this->type = $type;
        //$this->page = $control->page;

        if ($this->type == 'blocks') {
            $q = "SELECT count(id) FROM prname_b_$template WHERE $this->parent $this->critery visible = 1";
            $d = $sql->query("select p2.* from prname_btemplates p1, prname_bdatarel p2 where p1.key = '$this->template' and p2.templid=p1.id");
            while ($arr = $sql->fetch_assoc($d)) {
                $this->fields[$arr['key']] = new stdClass();
                $this->fields[$arr['key']]->datatkey = $arr[datatkey];
                $this->fields[$arr['key']]->comment = $arr[comment];
            }

        }

        if ($this->type == 'cats') {
            if (strlen($this->parent)) {
                $q = "SELECT count(id) FROM prname_categories WHERE parent='$this->cid' and $this->critery  visible = 1 AND template = '$this->template' ";
            } else {
                $q = "SELECT count(id) FROM prname_categories WHERE $this->critery  visible = 1 AND template = '$this->template' ";
            }

            $d = $sql->query("select p2.* from prname_ctemplates p1, prname_cdatarel p2 where p1.key = '$this->template' and p2.templid=p1.id");
            while ($arr = $sql->fetch_assoc($d)) {
                $this->fields[$arr['key']] = new stdClass();
                $this->fields[$arr['key']]->datatkey = $arr['datatkey'];
                $this->fields[$arr['key']]->comment = $arr['comment'];
            }

        }

        if ($this->type == 'items') {
            $q = "SELECT count(id) FROM prname_b_$template WHERE `blockparent`='$this->cid' and $this->critery visible = 1";
            $d = $sql->query("select p2.* from prname_btemplates p1, prname_bdatarel p2 where p1.key = '$this->template' and p2.templid=p1.id");
            while ($arr = $sql->fetch_assoc($d)) {
                $this->fields[$arr['key']] = new stdClass();
                $this->fields[$arr['key']]->datatkey = $arr[datatkey];
                $this->fields[$arr['key']]->comment = $arr[comment];
            }
        }
        $this->count = $sql->one_record($q);
    }


    function get_list()
    {
        global $sql;
        if ($this->page) $this->start = 0 + $this->page * $this->limit;
        if ($this->type == 'blocks') {
            //echo "select * from prname_b_$this->template where $this->parent $this->critery `visible` = '1' "."order by ".($this->sortfield && $this->sortfield!=='sort'? (!stristr($this->sortfield,'date') && !stristr($this->sortfield,'shou') && !stristr($this->sortfield,'blockparent')?'binary UPPER('.$this->sortfield.')':$this->sortfield):'sort')." $this->sortby"."".($this->limit?' limit '.($this->start?$this->start:'0').', '.$this->limit:'')."";
           $this->items = $sql->query("select * from prname_b_$this->template where $this->parent $this->critery `visible` = '1' " . "order by " . ($this->sortfield && $this->sortfield !== 'sort' ? (!stristr($this->sortfield, 'date') && !stristr($this->sortfield, 'shou') && !stristr($this->sortfield, 'blockparent') ? ' ' . $this->sortfield . ' ' : $this->sortfield) : 'sort') . " $this->sortby" . "" . ($this->limit ? ' limit ' . ($this->start ? $this->start : '0') . ', ' . $this->limit : '') . "");

        }

        if ($this->type == 'cats') {
            if (strlen($this->parent))
                $this->items = $sql->query("select p1.*,p2.name as page_name from prname_c_$this->template p1, prname_tree p2 where p2.parent='$this->cid' and  p2.visible = '1' and p1.parent=p2.id   " . ($this->sortfield ? 'order by p1.' . $this->sortfield . ' ' . $this->sortby : ' order by  p2.sort') . " " . ($this->limit ? ' limit ' . ($this->start ? $this->start : '0') . ', ' . $this->limit : '') . "");
            else
                $this->items = $sql->query("select p1.*,p2.name as page_name from prname_c_$this->template p1, prname_tree p2 where p2.visible = '1' and p1.parent=p2.id   " . ($this->sortfield ? 'order by p1.' . $this->sortfield . ' ' . $this->sortby : ' order by  p2.sort') . " " . ($this->limit ? ' limit ' . ($this->start ? $this->start : '0') . ', ' . $this->limit : '') . "");
        }

        if ($this->type == 'items') {
            $this->items = $sql->query("select * from prname_b_$this->template  where blockparent='$this->cid' and $this->critery `visible` = '1'  " . ($this->sortfield ? 'order by ' . $this->sortfield . ' ' . $this->sortby : ' order by sort') . " " . ($this->limit ? ' limit ' . ($this->start ? $this->start : '0') . ', ' . $this->limit : '') . "");
        }

        $this->countlist = count($this->items);

        //сразу формируем пейджер
        $this->set_pager($this->tmp_url);
    }

    function get_item()
    {
        global $sql;
        $i = 0;
        $tr = 0;
        $this->item = array();
        while ($one_arr = $sql->fetch_assoc($this->items)) {
            $this->item[$i] = new stdClass();
            if ($this->row && $tr == $this->row) {
                $tr = 0;
                $this->item[$i]->tr = true;
            } else {
                $tr++;
                $this->item[$i]->tr = false;
            };
            $ak = array_keys($one_arr);
            if (!$this->url[$one_arr['parent']] && !$this->is_global)
                $this->url[$one_arr['parent']] = $this->all->get_url($one_arr['parent']);
            if ($this->type !== 'cats') {
                if (strlen($one_arr['alt_url'])) {
                    $this->item[$i]->url = ($this->url[$one_arr['parent']]?$this->url[$one_arr['parent']]:$this->tmp_url) .$one_arr['alt_url'].'/';
                } else {
                    $this->item[$i]->url = ($this->url[$one_arr['parent']]?$this->url[$one_arr['parent']]:$this->tmp_url) .$this->all->add_url(($this->nopage?'':$this->page), 'view', ($this->type=='c'?$one_arr['parent']:$one_arr['id']));
                }
            }
            if ($this->type == 'cats') $this->item[$i]->url = $this->url[$one_arr['parent']];
            for ($ii = 0; $ii < count($ak); $ii++) {
                switch ($this->fields[$ak[$ii]]->datatkey) {
                    case 'html':
                        if (isset($this->no_text_view))
                            $this->item[$i]->$ak[$ii] = 'NO_TEXT_VIEW';
                        else
                            $this->item[$i]->$ak[$ii] = text_view($one_arr['' . $ak[$ii] . '']);
                        break;
                    case 'textarea': {
                        if (strpos($this->fields[$ak[$ii]]->comment, 'nobr') !== FALSE)
                            $this->item[$i]->$ak[$ii] = $one_arr[$ak[$ii]];
                        else
                            $this->item[$i]->$ak[$ii] = nl2br($one_arr[$ak[$ii]]);
                    }
                        break;
                    case 'select':
                        // block:template:field_name:[PID]
                        //пр. block:news:news_theme:1234
                        // ИЛИ
                        // cat:PID
                        //пр. cat:56
                        // ИЛИ
                        // variant1;variant2;variant3
                        if (strpos($this->fields[$ak[$ii]]->comment, ':') !== FALSE) //связанное поле
                        {
                            $d = explode(":", $this->fields[$ak[$ii]]->comment);
                            $type = $d[0];

                            if ($type == "block") {
                                $tpl = $d[1];
                                $fname = $d[2];

                                $pid = intval($one_arr[$ak[$ii]]);
                                $q = sprintf("SELECT %s AS name FROM prname_b_%s WHERE id = %d ORDER BY sort;", $fname, $tpl, $pid);
                            }

                            if ($type == "cat") {
                                $q = sprintf("SELECT name FROM prname_categories WHERE id = %d;", intval($one_arr[$ak[$ii]]));
                            }

                            $this->item[$i]->{$ak[$ii]} = $sql->one_record($q);
                            $this->item[$i]->{$ak[$ii] . '_id'} = $one_arr[$ak[$ii]];
                        } else //просто значение
                            $this->item[$i]->{$ak[$ii]} = $one_arr[$ak[$ii]];
                        break;
                    case 'date':
                        $this->item[$i]->{$ak[$ii]} = $one_arr[$ak[$ii]];
                        $this->item[$i]->{$ak[$ii] . '_1'} = $this->all->get_date($one_arr['' . $ak[$ii] . ''], 1);
                        $this->item[$i]->{$ak[$ii] . '_2'} = $this->all->get_date($one_arr['' . $ak[$ii] . ''], 2);
                        $this->item[$i]->{$ak[$ii] . '_3'} = $this->all->get_date($one_arr['' . $ak[$ii] . ''], 3);
                        $this->item[$i]->{$ak[$ii] . '_4'} = $this->all->get_date($one_arr['' . $ak[$ii] . ''], 4);
                        break;
                    case 'image':
                        $this->item[$i]->$ak[$ii] = $one_arr['' . $ak[$ii] . ''];
                        if (($n = strpos($this->fields[$ak[$ii]]->comment, 'resize:')) !== false) {
                            $fn1 = explode('resize:', $this->fields[$ak[$ii]]->comment);
                            $fs = explode(',', $fn1[1]);
                            for ($if = 0; $if < count($fs); $if++) {
                                if (!is_file('images/' . ($if + 1) . '/' . $one_arr['' . $ak[$ii] . ''])) {
                                    //ресайз изображений
                                    $imgres = resize_image($one_arr[$ak[$ii]], $fs[$if], $if + 1, '');
                                    if ($imgres == false)
                                        unset($this->item[$i]->$ak[$ii]);
                                }

                                //определяем размер изображений
                                //формируются формата ->image_1_width

                                if (is_file('images/' . ($if + 1) . '/' . $one_arr['' . $ak[$ii] . ''])) {
                                    $sizes = getimagesize('images/' . ($if + 1) . '/' . $one_arr['' . $ak[$ii] . '']);
                                    $this->item[$i]->{$ak[$ii] . '_' . ($if + 1) . '_width'} = $sizes[0];
                                    $this->item[$i]->{$ak[$ii] . '_' . ($if + 1) . '_height'} = $sizes[1];
                                    $this->item[$i]->{$ak[$ii] . '_' . ($if + 1) . '_mime'} = $sizes['mime'];
                                }
                            }
                        };
                        //определение размеров исходной картинки
                        if (is_file('images/0/' . $one_arr['' . $ak[$ii] . ''])) {
                            $sizes = getimagesize('images/0/' . $one_arr['' . $ak[$ii] . '']);
                            $this->item[$i]->{$ak[$ii] . '_0_width'} = $sizes[0];
                            $this->item[$i]->{$ak[$ii] . '_0_height'} = $sizes[1];
                            $this->item[$i]->{$ak[$ii] . '_0_mime'} = $sizes['mime'];
                        }

                        break;
                    case 'shoumum':
                        $this->item[$i]->$ak[$ii] = $one_arr['' . $ak[$ii] . ''];
                        if (($n = strpos($this->fields[$ak[$ii]]->comment, 'resize:')) !== false) {
                            $fn1 = explode('resize:', $this->fields[$ak[$ii]]->comment);
                            $fs = explode(',', $fn1[1]);
                            for ($if = 0; $if < count($fs); $if++) {
                                if (!is_file('images/findmom' . ($if + 1) . '/' . $one_arr['' . $ak[$ii] . ''])) {
                                    //ресайз изображений
                                    $imgres = resize_image($one_arr[$ak[$ii]], $fs[$if], $if + 1, '', 'findmom');
                                    if ($imgres == false)
                                        unset($this->item[$i]->$ak[$ii]);
                                }

                                //определяем размер изображений
                                //формируются формата ->image_1_width

                                if (is_file('images/findmom' . ($if + 1) . '/' . $one_arr['' . $ak[$ii] . ''])) {
                                    $sizes = getimagesize('images/findmom' . ($if + 1) . '/' . $one_arr['' . $ak[$ii] . '']);
                                    $this->item[$i]->{$ak[$ii] . '_' . ($if + 1) . '_width'} = $sizes[0];
                                    $this->item[$i]->{$ak[$ii] . '_' . ($if + 1) . '_height'} = $sizes[1];
                                    $this->item[$i]->{$ak[$ii] . '_' . ($if + 1) . '_mime'} = $sizes['mime'];
                                }
                            }
                        };
                        //определение размеров исходной картинки
                        if (is_file('images/findmom0/' . $one_arr['' . $ak[$ii] . ''])) {
                            $sizes = getimagesize('images/findmom0/' . $one_arr['' . $ak[$ii] . '']);
                            $this->item[$i]->{$ak[$ii] . '_0_width'} = $sizes[0];
                            $this->item[$i]->{$ak[$ii] . '_0_height'} = $sizes[1];
                            $this->item[$i]->{$ak[$ii] . '_0_mime'} = $sizes['mime'];
                        }

                        break;

                    case 'double':
                        $this->item[$i]->{$ak[$ii] . '_format'} = number_format($one_arr[$ak[$ii]], 2, ',', ' ');
                        $this->item[$i]->{$ak[$ii] . '_format0'} = number_format($one_arr[$ak[$ii]], 0, ',', ' ');
                        $this->item[$i]->{$ak[$ii] . '_format1'} = number_format($one_arr[$ak[$ii]], 1, ',', ' ');
                        $this->item[$i]->$ak[$ii] = $one_arr[$ak[$ii]];
                        break;
                    default:
                        $this->item[$i]->$ak[$ii] = $one_arr['' . $ak[$ii] . ''];
                        break;
                }
            }
            $i++;
        }
    }

    function get_page()
    {

        //оставлено для совместимости
        /*
                $rescount = ceil($this->count/$this->limit);
                if ($rescount > 1)
                {
                    for ($i = 0; $i < $rescount; $i++)
                    {
                        $this->pages[$i]['num']=$i;
                        $this->pages[$i]['current']= $i== $this->page?'1':'';
                    }
                }
        */
        return 0;
    }

    function check_page()
    {
        return 1;
        /*
                     $rescount = ceil($this->count/$this->limit);
                    if (($this >= $rescount) && ($rescount > 0))  {
                        $this->all->error(404);
                    }
        */
    }



    //------------- функция "усечения" массива постранички --------------
    /*на входе массив объектов типа
    $obj->title
    $obj->num
    $obj->current
    */
    function pageSplit($pages, $area = 10, $curr = 0)
    {

        if (count($pages) <= $area) return $pages;
        $idx_l = $curr - intval($area / 2);
        if ($idx_l < 0) $idx_l = 0;

        $idx_r = $curr + intval($area / 2);
        if ($idx_r > count($pages) - 1)
            $idx_r = count($pages) - 1;

        //бежим и выкашиваем ненужное
        $result = array();
        foreach ($pages as $idx => $page) {
            //первую и последнюю страницу не трогаем
            if ($idx == 0 || $idx == count($pages) - 1) {
                $result[] = clone($page);
                continue;
            }

            //достигли левого края
            if ($idx == $idx_l) {
                if ($idx_l > 1) {
                    $obj->current = 1;
                    $obj->title = '...';
                    $obj->divider = 1;
                    $obj->num = $pages[$idx - 1]->num;
                    $obj->url = $pages[$idx - 1]->url;
                    $result[] = clone($obj);
                }
                $result[] = clone($page);
                continue;
            }

            //между левым и правым
            if ($idx > $idx_l && $idx < $idx_r) {
                $result[] = clone($page);
                continue;
            }

            //достигли правого края
            if ($idx == $idx_r) {
                $result[] = clone($page);
                if ($idx_r < count($pages) - 2) {
                    $obj->current = 1;
                    $obj->title = '...';
                    $obj->divider = 1;
                    $obj->num = $pages[$idx + 1]->num;
                    $obj->url = $pages[$idx + 1]->url;
                    $result[] = clone($obj);
                }
                continue;
            }
        }

        return $result;

    }


    //сформировать пейджер
    function set_pager($tmpurl = '')
    {
        if ($this->count <= $this->limit) return;
        if (!$this->limit) return;

        unset($this->navigation);

        $page_cnt = intval($this->count / $this->limit);
        if ($this->count % $this->limit)
            $page_cnt++;

        $shag = intval($this->pageshag);// Количество страниц.
        $cpage = intval($this->page);

        for ($i = 0; $i < $page_cnt; $i++) {
            $this->navigation[$i] = new stdClass();
            $this->navigation[$i]->title = $i + 1;
            $this->navigation[$i]->num = $i;
            if ($i == $cpage)
                $this->navigation[$i]->current = 1;

            $this->navigation[$i]->url = ($tmpurl ? $tmpurl : $this->tmp_url) .
                ($this->all->add_url($i))
                . $this->url->prefix;
        }

        //обсекаем нашу постраничку
        $this->navigation = $this->pageSplit($this->navigation, intval($this->pageshag), $cpage);


        //ссылка на первую страницу
        $this->url_first = ($tmpurl ? $tmpurl : $this->tmp_url) . $this->all->add_url(0) . $this->url->prefix;

        //ссылка на последнюю страницу
        $this->url_last = ($tmpurl ? $tmpurl : $this->tmp_url) . $this->all->add_url($page_cnt - 1) . $this->url->prefix;


        //ссылка на след. страницу
        if ($cpage < $page_cnt - 1)
            $this->url_next = ($tmpurl ? $tmpurl : $this->tmp_url) . $this->all->add_url($cpage + 1) . $this->url->prefix;

        //ссылка на предыдущую страницу
        if ($cpage > 0)
            $this->url_prev = ($tmpurl ? $tmpurl : $this->tmp_url) . $this->all->add_url($cpage - 1) . $this->url->prefix;
    }
}

?>