<?php

/**
 * +-----------------------------------------------------------------------+
 * |   Компания "Софтмажор"
 * +-----------------------------------------------------------------------+
 * | Разработано (c) 2007-2010,   http://www.softmajor.ru/
 * +-----------------------------------------------------------------------+
 **/
class All
{
    var $sql;


    function All()
    {
        global $sql;
    }

    //берет значение переменной из get или post.
    function get_var($param)
    {
        return $_REQUEST[$param];
    }

    function get_fields($templ, $type)
    {
        global $sql;
        $u = $sql->fetch_assoc($sql->query("SELECT * FROM prname_" . $type . "templates WHERE `key`='$templ'"));
        $templ_fields = $sql->query("describe prname_" . $type . "templates");
        while ($bdfarr = $sql->fetch_assoc($templ_fields))
            $bdfarr2->{$bdfarr[Field]} = $u[$bdfarr[Field]]; // Массив с названиями полей.
        return $bdfarr2;
    }
    // ------------------  ДАТА И ВРЕМЯ -----------------------------
    // из yyyy-mm-dd во что-то более удобоваримое
    static function get_date($tmp_date, $param = 1)
    {
        global $ar_mon;


        if ($param == 1) {
            $year = substr($tmp_date, 0, 4);
            $mon = substr($tmp_date, 5, 2);
            $day = 0 + substr($tmp_date, 8, 2);
            $date = $day . '.' . $mon . '.' . $year;
        }

        if ($param == 2) {
            global $ar_mon;
            $year = substr($tmp_date, 0, 4);
            $mon = $ar_mon[0 + substr($tmp_date, 5, 2)];
            $day = 0 + substr($tmp_date, 8, 2);

            $date = $day . '&nbsp;' . $mon . '&nbsp;' . $year;
        }

        if ($param == 3)  //без года
        {
            global $ar_mon;
            $year = substr($tmp_date, 0, 4);
            $mon = $ar_mon[0 + substr($tmp_date, 5, 2)];
            $day = 0 + substr($tmp_date, 8, 2);

            $date = $day . '&nbsp;' . $mon;
        }

        if ($param == 4)  //в формате 2 апреля ‘11
        {
            global $ar_mon;
            $year = substr($tmp_date, 2, 2);
            $mon = $ar_mon[0 + substr($tmp_date, 5, 2)];
            $day = 0 + substr($tmp_date, 8, 2);

            $date = $day . '&nbsp;' . $mon . "&nbsp;‘" . $year;
        }


        if ($param == 'mysqltostring') {
            global $ar_mon;
            $year = substr($tmp_date, 0, 4);
            $mon = substr($tmp_date, 5, 2);
            $day = substr($tmp_date, 8, 2);

            $date = $day . '.' . $mon . '.' . $year;
        }

        if ($param == 'stringtomysql') {
            global $ar_mon;
            $year = substr($tmp_date, 6, 4);
            $mon = substr($tmp_date, 3, 2);
            $day = substr($tmp_date, 0, 2);

            $date = $year . "-" . $mon . "-" . $day;
        }

        return $date;
    }


    //умное отсечение заданного кол-ва занков из большого куска текста
    function smart_substr($text, $size = 255)
    {
        if (strlen($text) <= $size) return $text;
        $part1 = substr($text, 0, $size);
        if ($part1[strlen($part1)] != ' ') {
            $part2 = substr($text, $size, 64);
            $cpos = strpos($part2, " ");
            $part1 .= substr($part2, 0, $cpos) . "...";
        }
        return $part1;
    }


    //-------------------- Склонение --------------------------------
    function declOfNum($number, $titles, $show_number = true)
    {

        $cases = array(2, 0, 1, 1, 1, 2);
        if ($show_number)
            return $number . " " . $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        else
            return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    //---------------------  ПОЧТА  ---------------------------------
    function send_mail($a_email, $email_sub, $email_text, $file = '', $filename = '', $variant = 0)
    {
        global $sql;
        global $config;
        $mailer = new PHPMailer();

//$mailer->IsSMTP(); // telling the class to use SMTP
//$mailer->Host       = "127.0.0.1"; // SMTP server

        $mailer->Subject = $email_sub;
        $mailer->Body = $email_text;
        $q = count($filename);
        for ($i = 0; $i < $q; $i++) {
            $mailer->AddAttachment($file[$i], $filename[$i]);
        }
        
        $to_email = str_replace(' ', '', $a_email);
		if(strpos($a_email, ',')) {
			$to_array = explode(',', $a_email);
		} elseif(strpos($a_email, ';')) {
			$to_array = explode(';', $a_email);
		}
		
		if($to_array) {
			foreach($to_array as $key => $val) {
				$mailer->AddAddress($val, '');
			}
		} else {
			$mailer->AddAddress($a_email, '');
		}
		
        $mailer->From = @mysql_result($sql->query("SELECT admin_email FROM prname_sadmin WHERE admin_id=4"), 0, 0);
        if ($variant !== 0) $mailer->FromName = $variant; else
            $mailer->FromName = $config['site_name'];
        $mailer->AltBody = $email_sub; // optional, comment out and test
        $mailer->Send();
        $mailer->ClearAddresses();
    }


//послать письмо со вложенными картинками embedded attach
//если требуется отображать их внутри письма
    function send_mail_images($a_email, $email_sub, $email_text, $files = '', $links = '', $variant = 0)
    {
        global $sql;
        global $config;
        $mailer = new PHPMailer();

//$mailer->IsSMTP(); // telling the class to use SMTP
//$mailer->Host       = "127.0.0.1"; // SMTP server

        $mailer->Subject = $email_sub;
        $q = count($files);
        for ($i = 0; $i < $q; $i++) {
            $mailer->AddEmbeddedImage($files[$i], $i);
            $email_text = str_replace('src="' . $links[$i] . '"', 'src="cid:' . $i . '"', $email_text);
        }

        $mailer->Body = $email_text;
        
        $to_email = str_replace(' ', '', $a_email);
		if(strpos($a_email, ',')) {
			$to_array = explode(',', $a_email);
		} elseif(strpos($a_email, ';')) {
			$to_array = explode(';', $a_email);
		}
		
		if($to_array) {
			foreach($to_array as $key => $val) {
				$mailer->AddAddress($val, '');
			}
		} else {
			$mailer->AddAddress($a_email, '');
		}
        
        $mailer->From = @mysql_result($sql->query("SELECT admin_email FROM prname_sadmin WHERE admin_id=4"), 0, 0);
        if ($variant !== 0) $mailer->FromName = $variant; else
            $mailer->FromName = $config['site_name'];
        $mailer->AltBody = $email_sub; // optional, comment out and test
        $mailer->Send();
        $mailer->ClearAddresses();
    }


    // -- отправить письмо с явным указанием отправителя ----------------------------------
    function send_mail_from($to_email, $from_email, $email_sub, $email_text, $file = '', $filename = '', $from_name = '')
    {
        global $config;
        $mailer = new PHPMailer();

        //$mailer->IsSMTP(); // telling the class to use SMTP
        //$mailer->Host       = "127.0.0.1"; // SMTP server

        $mailer->Subject = $email_sub;
        $mailer->Body = $email_text;
        $q = count($filename);
        for ($i = 0; $i < $q; $i++) {
            $mailer->AddAttachment($file[$i], $filename[$i]);
        }

        $to_email = str_replace(' ', '', $to_email);
		if(strpos($to_email, ',')) {
			$to_array = explode(',', $to_email);
		} elseif(strpos($to_email, ';')) {
			$to_array = explode(';', $to_email);
		}
		
		if($to_array) {
			foreach($to_array as $key => $val) {
				$mailer->AddAddress($val, '');
			}
		} else {
			$mailer->AddAddress($to_email, '');
		}
		
        $mailer->From = $from_email;

        if (strlen($from_name))
            $mailer->FromName = $from_name;
        else
            $mailer->FromName = $config['site_name'];

        $mailer->Send();
        $mailer->ClearAddresses();
    }

//------------------  РАБОТА С ФАЙЛАМИ ----------------------------
    function read_file($path)
    {
        $file = file("$path");
        $page = '';
        for ($i = 0; $i < count($file); $i++) {
            $page .= $file[$i];
        }
        return $page;
    }

    function filesize($path)
    {
        $size = filesize($path);
        if (($size / 1024) < 1) {
            $text = $size . ' б';
        } else {
            if (($size / (1024 * 1024)) < 1) {
                $text = round($size / (1024), 2) . ' кб';
            } else {
                $text = round($size / (1024 * 1024), 4) . ' Мб';
            }
        }
        return $text;
    }

//--------------------- URL, ПУТИ, ОШИБКИ  и прочее ------------------------
    //вывод ошибки
    function error($num = 404)
    {
        global $config;

        global $control;
        $control = new Control(false);
        $control->template = 'error';
        $control->Init();
        $control->Make();
        die;
    }

    //построение ссылки - две функции
    function getUrl_path($id, $parent, $key)
    {
        global $sql;

        $old_id = $id;
        $old_id1 = $id;
        $l = 1;
        while ($l != 0) {
            if ($key == 'yes') {
                $q = "SELECT `key` FROM prname" . "_categories WHERE id = '$id'";
                $str1 = $sql->fetch_row($sql->query($q));
                if (strlen($str1[0]) > 1) {
                    $megaurl .= ($str1[0]) . '|';
                } else {
                    $megaurl .= ($id) . '|';
                }
            } else {
                $megaurl .= ($id) . '|';
            }

            $q = "SELECT parent FROM prname" . "_categories WHERE id = '$id'";// AND parent <> $parent";
            $str = $sql->fetch_row($sql->query($q));
            if ($str[0] == 0) {
                $l = 0;
                $old_id1 = $old_id;
            } else {
                $old_id1 = $old_id;
                $old_id = $str[0];
                $id = $str[0];
            }
        }

        return $megaurl;
    }

    function get_url($main, $key = 'yes')
    {
        global $sql;
        global $control;
        if ($control->NESTEDSETS) {
            $url = tree::GetUrl($main);
            $url = str_replace('//', '/', $url);
            $url = '<!--base_url//-->' . $url . '/';
            return $url;
        }

        $string = $this->getUrl_path($main, 0, $key);
        $arr_str = explode('|', $string);

        $location = '';
        for ($i = count($arr_str) - 2; $i > -1; $i--) {
            if ($i != 0) {
                $location .= $arr_str[$i] . "/";
            } else {
                $location .= $arr_str[$i];
            }
        }

        $location = str_replace('main/', '', $location);
        $location = str_replace('main', '', $location);
        $url = '<!--base_url//-->' . $location;
        return $url;
    }

    function add_url($page = '', $oper = '', $id = '', $sort = '', $sortv = '')
    {
        $text .= '';
        if ($page != '') {
            $text .= '_p' . $page;
        }
        if ($oper != '') {
            $text .= '_a' . $oper;
        }
        if ($id != '') {
            $text .= '_b' . $id;
        }
        if ($sortv != '') {
            $text .= '_v' . $sortv;
        }
        if ($sort != '') {
            $text .= '_s' . $sort;
        }
        /*
                if ($text != '')	{
                    $text = '/'.$text;
                }
        */
        return $text;
    }

//---------------------------------------------------------------------------


    static function get_node($id, $depth = 1000000, $type = 'full')
    {
        return tree::GetNode($id, $depth, $type);
    }

////////////////////////////////////////////////////////
    function get_nav($id)
    {
        global $control;
        global $sql;

        $q = "SELECT *  FROM prname_tree WHERE id = '$control->cid'";
        $str = $sql->fetch_array($sql->query($q));

        $all[] = $str;
        do {
            $q = "SELECT *  FROM prname_tree WHERE id = '$str[parent]'";
            $str = $sql->fetch_array($sql->query($q));
            $all[] = $str;
        } while ($str['parent'] > 0);
        asort($all);
        if ($control->id) {
            $r = $this->b_data_all($control->id);
            $all[] = $r;
        };
        return $all;
    }

///////////////////////////////////////////////////////////
    function get_list($id, $depth = 1000000, $type = 'full')
    {
        global $control;
        return tree::GetList($id);
    }

    function get_cats($cId, $template = '', $sortField = '', $sortUp = 'ASC', $start = 0, $limit = 0, $rand = '')
    {
        global $sql;


        if ($template) {
//определяем кол-во полей у шаблона папки
            $q = "SELECT count(pr1.id)
			 FROM
				prname_cdatarel pr1, 
				prname_ctemplates pr2

			WHERE 
				pr1.templid = pr2.id
				AND pr2.`key` = '$template'
			";

            $str1 = $sql->fetch_row($sql->query($q));
            $numfields = 0 + $str1[0];

        } else {
            $numfields = 0;
        }


        if ($numfields > 0) {

            if (trim($sortField) != '') {
///СОРТИРОВКЕ ДА!!!!!!!!!
                $q = "SELECT
					pr1.id,
					pr2.data AS data,
					pr2.relkey AS relkey,
					pr1.name AS name
				FROM
					prname" . "_categories pr1,
					prname" . "_data pr2,
					prname" . "_data pr3
				WHERE
					pr2.catid = pr1.id 
					AND pr1.parent = '" . $cId . "'
					AND pr1.template = '$template' 
					AND pr1.visible = 1 
					AND pr3.catid = pr1.id 
					AND pr3.relkey = '$sortField' 

				ORDER BY
					0 + pr3.data $sortUp, 
					pr1.sort, 
					pr1.id,
					pr2.relkey
			";
            } else {
                $q = "SELECT
					pr1.id,
					pr2.data AS data,
					pr2.relkey AS relkey,
					pr1.name AS name
				FROM
					prname" . "_categories pr1,
					prname" . "_data pr2
				WHERE
					pr2.catid = pr1.id 
					AND pr1.parent = '" . $cId . "'
					AND pr1.template = '$template' 
					AND pr1.visible = 1 

				ORDER BY
					pr1.sort $sortUp, 
					pr1.id,
					pr2.relkey

			";
            }
            if ($limit > 0) {
                $q .= "
				LIMIT " . ($start * $numfields) . ", " . ($limit * $numfields) . "
			";
            }

        } else {
            $q = "SELECT
					id,
					name 
				FROM prname_categories 
				WHERE 
					parent = '" . $cId . "'
					AND visible = 1
			";
            if ($template) {
                $q .= "
					AND template = '$template'
				";
            }
            $q .= "
				ORDER BY
				sort $sortUp

			";

        }


        $res = $sql->query($q);
        if ($sql->num_rows($res) > 0) {
            $i = 0;
            $j = 1;
            while ($str = $sql->fetch_array($res)) {
                $tmp_id = $str['id'];
                $tmp_name = $str['name'];
                $tmp_relkey = $str['relkey'];
                $tmp_relkey = str_replace($template . "_", '', $tmp_relkey);
                $tmp_data = $str['data'];

                $arr[$i]['id'] = $tmp_id;
                $arr[$i]['name'] = $tmp_name;
                if ($tmp_relkey) {
                    $arr[$i][$tmp_relkey] = $tmp_data;
                }


                if ($numfields > 0) {
                    if ($j == $numfields) {
                        $j = 0;
                        $i++;
                    }
                    $j++;
                } else {
                    $i++;
                }
            };
        }


        if ($rand) {
            srand((double)microtime() * 10000000);
            $arr_key = array_rand($arr, count($arr));

            $n_arr = array();
            foreach ($arr_key as $key) {
                array_push($n_arr, $arr[$key]);
            }
            $arr = $n_arr;
        }


        return $arr;
    }


    // из контейнера блока
    function b_data($blockid, $relkey, $table)
    {
        global $sql;
        $q = $sql->fetch_assoc($sql->query("SELECT $relkey FROM prname_b_" . "$table WHERE id = '$blockid'"));
        return $q[$relkey];
    }

    // все что мы знаем о блоке
    function b_data_all($catid, $template)
    {
        global $sql;
        $d = $sql->query("select p2.* from prname_btemplates p1, prname_bdatarel p2 where p1.key = '$template' and p2.templid=p1.id");
        $fields = array();
        while ($arr = $sql->fetch_assoc($d)) {
            $fields[$arr['key']] = new stdClass();
            $fields[$arr['key']]->datatkey = $arr['datatkey'];
            $fields[$arr['key']]->comment = $arr['comment'];
        }
        $page = new stdClass();
        $res = $sql->query("SELECT * FROM prname_b_$template WHERE id = '$catid'");
        while ($arr = $sql->fetch_assoc($res)) {

            $qarr = array_keys($arr);
            for ($i = 0; $i < count($qarr); $i++) {
                switch ($fields[$qarr[$i]]->datatkey) {
                    case 'html':
                        $page->$qarr[$i] = text_view($arr[$qarr[$i]]);
                        break;
                    case 'textarea':
                        if (strpos($fields[$qarr[$i]]->comment, 'nobr') !== FALSE)
                            $page->$qarr[$i] = $arr[$qarr[$i]];
                        else
                            $page->$qarr[$i] = str_replace(chr(13), "<br>", $arr[$qarr[$i]]);
                        break;
                    case 'select':
                        // block:template:field_name:[PID]
                        //пр. block:news:news_theme:1234
                        // ИЛИ
                        // cat:PID
                        //пр. cat:56
                        // ИЛИ
                        // variant1;variant2;variant3
                        if (strpos($fields[$qarr[$i]]->comment, ':') !== FALSE) //связанное поле
                        {
                            $d = explode(":", $fields[$qarr[$i]]->comment);
                            $type = $d[0];

                            if ($type == "block") {
                                $tpl = $d[1];
                                $fname = $d[2];

                                $pid = intval($arr[$qarr[$i]]);
                                $q = sprintf("SELECT %s AS name FROM prname_b_%s WHERE id = %d ORDER BY sort;", $fname, $tpl, $pid);
                            }

                            if ($type == "cat") {
                                $q = sprintf("SELECT name FROM prname_categories WHERE id = %d;", intval($arr[$qarr[$i]]));
                            }

                            $page->$qarr[$i] = $sql->one_record($q);
                            $page->{$qarr[$i] . '_id'} = $arr[$qarr[$i]];
                        } else //просто значение
                            $page->{$qarr[$i]} = $arr[$qarr[$i]];
                        break;
                    case 'date':
                        $page->{$qarr[$i]} = $arr[$qarr[$i]];
                        $page->{$qarr[$i] . '_1'} = all::get_date($arr[$qarr[$i]], 1);
                        $page->{$qarr[$i] . '_2'} = all::get_date($arr[$qarr[$i]], 2);
                        break;
                    case 'image':
                        $page->$qarr[$i] = $arr[$qarr[$i]];
                        if (($n = strpos($fields[$qarr[$i]]->comment, 'resize:')) !== false) {
                            $fn1 = explode('resize:', $fields[$qarr[$i]]->comment);
                            $fs = explode(',', $fn1[1]);
                            for ($if = 0; $if < count($fs); $if++) {
                                if (!is_file('images/' . ($if + 1) . '/' . $arr[$qarr[$i]]))
                                    resize_image($arr[$qarr[$i]], $fs[$if], $if + 1, '');

                                //размеры
                                //формируются формата ->image_1_width
                                if (is_file('images/' . ($if + 1) . '/' . $arr[$qarr[$i]])) {
                                    $sizes = getimagesize('images/' . ($if + 1) . '/' . $arr[$qarr[$i]]);
                                    $page->{$qarr[$i] . '_' . ($if + 1) . '_width'} = $sizes[0];
                                    $page->{$qarr[$i] . '_' . ($if + 1) . '_height'} = $sizes[1];
                                    $page->{$qarr[$i] . '_' . ($if + 1) . '_mime'} = $sizes['mime'];
                                }

                            }
                        };

                        //размеры оригинала
                        if (is_file('images/0/' . $arr[$qarr[$i]])) {
                            $sizes = getimagesize('images/0/' . $arr[$qarr[$i]]);
                            $page->{$qarr[$i] . '_0_width'} = $sizes[0];
                            $page->{$qarr[$i] . '_0_height'} = $sizes[1];
                            $page->{$qarr[$i] . '_0_mime'} = $sizes['mime'];
                        }
                        break;
                    case 'file': {
                        $page->$qarr[$i] = $arr[$qarr[$i]];

                        $page->{$qarr[$i] . '_isFile'} = @is_file('files/' . $arr[$qarr[$i]]);
                        $page->{$qarr[$i] . '_sizeB'} = @filesize('files/' . $arr[$qarr[$i]]);
                        $page->{$qarr[$i] . '_sizeKb'} = round(@filesize('files/' . $arr[$qarr[$i]]) / 1024);
                        $page->{$qarr[$i] . '_sizeMb'} = round(@filesize('files/' . $arr[$qarr[$i]]) / (1024 * 1024));

                        $path_parts = @pathinfo('files/' . $arr[$qarr[$i]]);
                        $page->{$qarr[$i] . '_ext'} = $path_parts['extension'];
                    }
                        break;
                    case 'double':
                        $page->{$qarr[$i] . '_format'} = number_format($arr[$qarr[$i]], 2, ',', ' ');
                        $page->{$qarr[$i] . '_format0'} = number_format($arr[$qarr[$i]], 0, ',', ' ');
                        $page->{$qarr[$i] . '_format1'} = number_format($arr[$qarr[$i]], 1, ',', ' ');
                        $page->{$qarr[$i]} = $arr[$qarr[$i]];
                        break;
                    default:
                        $page->$qarr[$i] = $arr[$qarr[$i]];
                        break;
                }
            }
        }
        return $page;
    }

    // Прибавление на чило данных в контейнере блока
    function update_block($blockid, $key, $data, $relkey)
    {
        global $sql;
        $data = $sql->escape_string($data);
        $sql->query("UPDATE prname_b_$key SET `$relkey` = '$data' WHERE id = '$blockid'");
    }


// Прибавление на чило данных в контейнере каталога
    function update_data($catid, $data, $relkey)
    {
        global $sql;
        $sql->query("UPDATE prname_data SET `data` = $data WHERE catid = $catid AND relkey = '$relkey'");
    }

    // изменение данных в контейнере блока
    function update_b_data($blockid, $data, $relkey)
    {
        global $sql;
        $q = "UPDATE prname_data SET data = '$data' WHERE blockid = '$blockid' AND relkey = '$relkey'";
        $sql->query($q);
    }

    // вставка в  контейнер блока
    function insert_b_data($blockid, $data, $relkey)
    {
        global $sql;
        $data = $sql->escape_string($data);
        $q = "INSERT INTO prname_data (blockid, data, relkey) VALUES ($blockid, '$data', '$relkey')";
        $sql->query($q);
    }

    // вставка блока
    function insert_block($templ, $parent, $arr, $visible = 1)
    {
        global $sql;
        $q = $sql->query("select p2.* from prname_btemplates p1, prname_bdatarel p2 where p1.key = '$templ' and p2.templid=p1.id");
        $qs = '';
        while ($qww = $sql->fetch_assoc($q)) {
            if ($qww['datatkey'] == "date") {
                if (strlen($arr[$qww['key']]) == 0)
                    $arr[$qww['key']] = date("Y-m-d");
            }

            if (strlen($arr[$qww['key']]) == 0)
                $arr[$qww['key']] = $qww['default'];

            $qs .= " `$qww[key]` = '" . $sql->escape_string($arr[$qww['key']]) . "', ";
        }
        $sort = $sql->fetch_row($sql->query("select MAX(sort) from prname_b_$templ"), 0, 1) + 1;
        $sql->query("insert into prname_b_$templ set $qs `parent`='$parent',`visible`='$visible',`sort`='$sort'");
        return $sql->insert_id();

    }

    function get_name($cid)
    {
        global $sql;
        return $sql->fetch_row($sql->query("select name from prname_tree where `id`='$cid'"), 0, 1);
    }

// из контейнера папки
    function c_data($catid, $templ, $relkey)
    {
        global $sql;
        $q = $sql->fetch_assoc($sql->query("SELECT $relkey FROM prname_c_" . "$templ WHERE parent = '$catid'"));
        return $q[$relkey];
    }

    // все что мы знаем о папке
    static function c_data_all($catid, $templ)
    {
        global $sql;
        $d = $sql->query("select p2.* from prname_ctemplates p1, prname_cdatarel p2 where p1.key = '$templ' and p2.templid=p1.id");
        $fields = array();
        while ($arr = $sql->fetch_assoc($d)) {
            $fields[$arr['key']] = new stdClass();
            $fields[$arr['key']]->datatkey = $arr[datatkey];
            $fields[$arr['key']]->comment = $arr[comment];
        }
        $page = new stdClass();
        $res = $sql->query("select * from prname_c_$templ where parent = '$catid'");
        while ($arr = $sql->fetch_assoc($res)) {
            $qarr = array_keys($arr);
            for ($i = 0; $i < count($qarr); $i++) {
                switch ($fields[$qarr[$i]]->datatkey) {
                    case 'html':
                        $page->$qarr[$i] = text_view($arr[$qarr[$i]]);
                        break;
                    case 'textarea':
                        if (strpos($fields[$qarr[$i]]->comment, 'nobr') !== FALSE)
                            $page->$qarr[$i] = $arr[$qarr[$i]];
                        else
                            $page->$qarr[$i] = str_replace(chr(13), "<br>", $arr[$qarr[$i]]);
                        $page->$qarr[$i] = str_replace('{{{year}}}', date("Y"), $page->$qarr[$i]);
                        break;
                    case 'select':
                        // block:template:field_name:[PID]
                        //пр. block:news:news_theme:1234
                        // ИЛИ
                        // cat:PID
                        //пр. cat:56
                        // ИЛИ
                        // variant1;variant2;variant3
                        if (strpos($fields[$qarr[$i]]->comment, ':') !== FALSE) //связанное поле
                        {
                            $d = explode(":", $fields[$qarr[$i]]->comment);
                            $type = $d[0];

                            if ($type == "block") {
                                $tpl = $d[1];
                                $fname = $d[2];

                                $pid = intval($arr[$qarr[$i]]);
                                $q = sprintf("SELECT %s AS name FROM prname_b_%s WHERE id = %d ORDER BY sort;", $fname, $tpl, $pid);
                            }

                            if ($type == "cat") {
                                $q = sprintf("SELECT name FROM prname_categories WHERE id = %d;", intval($arr[$qarr[$i]]));
                            }

                            $page->$qarr[$i] = $sql->one_record($q);
                            $page->{$qarr[$i] . '_id'} = $arr[$qarr[$i]];
                        } else //просто значение
                            $page->{$qarr[$i]} = $arr[$qarr[$i]];
                        break;

                    case 'date':
                        $page->{$qarr[$i]} = $arr[$qarr[$i]];
                        $page->{$qarr[$i] . '_1'} = all::get_date($arr[$qarr[$i]], 1);
                        $page->{$qarr[$i] . '_2'} = all::get_date($arr[$qarr[$i]], 2);
                        break;

                    case 'image':
                        $page->$qarr[$i] = $arr[$qarr[$i]];
                        if (($n = strpos($fields[$qarr[$i]]->comment, 'resize:')) !== false) {
                            $fn1 = explode('resize:', $fields[$qarr[$i]]->comment);
                            $fs = explode(',', $fn1[1]);
                            for ($if = 0; $if < count($fs); $if++) {
                                if (!is_file('images/' . ($if + 1) . '/' . $arr[$qarr[$i]]))
                                    resize_image($arr[$qarr[$i]], $fs[$if], $if + 1, '');

                                //размеры
                                //формируются формата ->image_1_width
                                if (is_file('images/' . ($if + 1) . '/' . $arr[$qarr[$i]])) {
                                    $sizes = getimagesize('images/' . ($if + 1) . '/' . $arr[$qarr[$i]]);
                                    $page->{$qarr[$i] . '_' . ($if + 1) . '_width'} = $sizes[0];
                                    $page->{$qarr[$i] . '_' . ($if + 1) . '_height'} = $sizes[1];
                                    $page->{$qarr[$i] . '_' . ($if + 1) . '_mime'} = $sizes['mime'];
                                }
                            }
                        };

                        //размеры оригинала
                        if (is_file('images/0/' . $arr[$qarr[$i]])) {
                            $sizes = getimagesize('images/0/' . $arr[$qarr[$i]]);
                            $page->{$qarr[$i] . '_0_width'} = $sizes[0];
                            $page->{$qarr[$i] . '_0_height'} = $sizes[1];
                            $page->{$qarr[$i] . '_0_mime'} = $sizes['mime'];
                        }

                        break;
                    default:
                        $page->$qarr[$i] = $arr[$qarr[$i]];
                        break;
                }
            }
        }

        return $page;
    }

    //получение ключа папки
    function key($cid)
    {
        global $sql;
        $q = "SELECT `key` FROM prname_categories WHERE id = '$cid'";
        $str1 = $sql->fetch_row($sql->query($q));
        return $str1[0];
    }

    //шаблон страницы
    function template($cid)
    {
        global $sql;

        $q = "SELECT template FROM prname" . "_categories WHERE id = '$cid'";
        $str = $sql->fetch_row($sql->query($q));
        $template = $str[0];

        return $template;
    }

    //  изыманние картинки
    function get_img($tmp_img_data, $number)
    {
        $tmp_img_arr = get2_file($tmp_img_data, $number);

        $tmp_img = $tmp_img_arr[0];
        $tmp_x = $tmp_img_arr[1];
        $tmp_y = $tmp_img_arr[2];
        $tmp_img_razm = $tmp_img_arr[3];

        if (is_file("files/" . $tmp_img)) {
            $tmp_img_is = 1;
        } else {
            $tmp_img_is = 0;
        }

        $arr['name'] = $tmp_img;
        $arr['razm'] = $tmp_img_razm;
        $arr['x'] = $tmp_x;
        $arr['y'] = $tmp_y;
        $arr['is'] = $tmp_img_is;

        return $arr;
    }

    function get_parents()
    {
        global $control;
        $id = 1;
        $parents = array();
        for ($i = 1; $i < (sizeof($control->parents)); $i++) {
            $parents[$i] = new stdClass();
            $parents[$i]->name = All::get_name($control->parents[$i]);
            $parents[$i]->url = All::get_url($control->parents[$i]);
        }

        return $parents;
    }

    function translit($string)
    {
        $converter = array(
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ё" => "yo",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "y",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "kh",
            "ц" => "ts",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "shch",
            "ы" => "y",
            "э" => "e",
            "ю" => "yu",
            "я" => "ya",
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Е" => "E",
            "Ё" => "Yo",
            "Ж" => "Zh",
            "З" => "Z",
            "И" => "I",
            "Й" => "Y",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "Kh",
            "Ц" => "Ts",
            "Ч" => "Ch",
            "Ш" => "Sh",
            "Щ" => "Shch",
            "Ы" => "Y",
            "Э" => "E",
            "Ю" => "Yu",
            "Я" => "Ya",
            "ь" => "",
            "Ь" => "",
            "ъ" => "",
            "Ъ" => "",
			"№" => "N",
			" " => "-",
			"_" => "-",
            "—" => "-"
		);
        
		$string = strtr(trim($string), $converter);
		$string = preg_replace('/[^\w-]+/', '', $string);

		return $string;
    }

// конец 
// конец класса
}

?>