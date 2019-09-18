<?php

Class Form
{
    var $cid;
    var $key = 'mnzma_1';
    var $autocifr_error = false;
    var $dir = 'files/autocifr/';
    var $dirfont = 'fonts/';


    function print_form($form_key, $set_element = '')
    {
        global $control;
        global $sql;

        $all = new All();

        $q = "SELECT id FROM prname_blocks WHERE `key` = '$form_key'";
        $form_id = 0 + $sql->fetch_row($sql->query($q), 0);

        if ($form_id == 0) {
            return '';
        } else {
            $one_arr = $all->b_data_all($form_id);
        }


        //строим форму
        if ($this->cid == '') {
            $this->cid = $control->cid;
        }
        $form_url = $all->get_url($control->cid);
        $text = $one_arr['04'];
        $text = str_replace('<!--cid//-->', $this->cid, $text);
        $text = str_replace('<!--action//-->', $form_url, $text);
        $text = str_replace('<!--formid//-->', $form_key, $text);
        $text = str_replace('<!--set_element//-->', $set_element, $text);


        if (strstr($text, '<!--autocifr_img//-->')) {
            $this->delimgcode();
            $tmp_cifr_post = $this->digitimage();


            $text = str_replace('<!--autocifr_img//-->', '
							<img src="<!--base_url//-->/files/autocifr/' . $tmp_cifr_post . '.jpg" width="150" height="50" alt="" title="" /><br />
							<input type="hidden" name="auto_cifr_conf" value="' . $tmp_cifr_post . '" />
			
			', $text);
        }


        $text_error = '';
        if ($this->autocifr_error) {


            $tmp_mail = $one_arr['035'];
            $array_elem = explode(chr(13), $tmp_mail);
            $array_fields = array();
            if (count($array_elem) > 0) {
                $i = 0;
                foreach ($array_elem as $arr_elem_one) {
                    $arr_string = explode('{_}', $arr_elem_one);
                    $tmp_value = iconv('UTF-8', 'CP1251', stripslashes($_POST[$arr_string[1]]));
                    $tmp_field = $arr_string[1];
                    $text = str_replace("{-$tmp_field-}", $tmp_value, $text);
                    $i++;
                }
            }


            $text_error = "<blockquote><h3>Ошибка</h3> - Неверно введены контрольные цифры</blockquote>";
            $text = "
						$text_error

						$text
			";
        } else {

            //если ошибки нет то зачищаем дефолтные значения
            $text = preg_replace('/{-(.*?)-}/is', '', $text);

            $text = "
				<h2>" . $one_arr['03'] . "</h2>
					<div id=\"$form_key\">
						$text
					</div>
					$text_error
			";
        }

//		print_r($one_arr);


        return $text;
    }


    function action()
    {
        global $config;
        global $all;
        global $control;
        global $sql;

        $form_key = $_POST[formid];


        $q = "SELECT id FROM prname_blocks WHERE `key` = '$form_key'";
        $form_id = 0 + $sql->fetch_row($sql->query($q), 0);

        if ($form_id == 0) {
            return '';
        } else {
            $one_arr = $all->b_data_all($form_id);
        }


        //проверка на контрольные цифры
        $textform = $one_arr['04'];
        if (strstr($textform, '<!--autocifr_img//-->')) {

            $autocifr = $_POST[autocifr];
            $auto_cifr_conf = $_POST[auto_cifr_conf];

            $this->dir = '../files/autocifr/';
            $this->dirfont = '../fonts/';
            $this->cid = $_POST['cid'];

            if (($auto_cifr_conf != md5(base64_encode($this->key . $autocifr))) || (!is_file($this->dir . $auto_cifr_conf . '.jpg'))) {
                $this->autocifr_error = true;

                return $this->print_form($form_key);
            }

            @unlink('imgcode/' . $auto_cifr_conf . '.jpg');

        }


        //отправка по почте
        if (strstr($one_arr['02'], 'send')) {
            // тело письма

            $a_email = 'id@smhost.ru';

            if (trim($one_arr['027']) == 'admin') {
                $q = "SELECT admin_email FROM prname_sadmin WHERE admin_id = 4";
                $str = $sql->fetch_row($sql->query($q));
                $a_email = $str[0];
            }
            if (strstr($one_arr['027'], 'get_cat')) {
                $tmp_str = str_replace('get_cat', '', $one_arr[027]);
                $tmp_str = str_replace('(', '', $tmp_str);
                $tmp_str = str_replace(')', '', $tmp_str);
                $arr_tmp = explode(',', $tmp_str);
                $a_email = $all->c_data(trim($arr_tmp[0]), trim($arr_tmp[1]));
            }

            $email_sub = $one_arr['03'] . ' - ' . $config['server_url'];
            $email_text = '';

            $tmp_mail = $one_arr['035'];
            $array_elem = explode(chr(13), $tmp_mail);
            if (count($array_elem) > 0) {
                foreach ($array_elem as $arr_elem_one) {
                    $arr_string = explode('{_}', $arr_elem_one);
                    $email_text .= $arr_string[0] . ": " . iconv('UTF-8', 'CP1251', $_POST[$arr_string[1]]) . " \n";

                }
            }


            //проверка на дополнительные параметры:
            /*
            $post_keys = array_keys($_POST);
            $email_text .= "\n\n";
            foreach($post_keys as $key)	{
                if ( strstr($key, 'addparamvalue') )		{
                    $post_id_arr = explode("_", $key);
                    $post_id = $post_id_arr[1];
                    $post_var_value = $_POST[$key];
                    $post_var_value = iconv('UTF-8', 'CP1251', $post_var_value);

                    if ($post_var_value != '')		{
                        $post_var_name = $_POST['addparamname_'.$post_id];
                        $post_var_name = iconv('UTF-8', 'CP1251', $post_var_name);
                        $email_text .= $post_var_name . ": " . $post_var_value . " \n";
                    }
                }
            }
            */

            $all->send_mail($a_email, $email_sub, $email_text);

            $text = '<p>' . $one_arr['037'] . '</p>
			<div style="clear:left"><a href="' . $all->get_url($_POST['cid']) . '">Вернуться</a></div>
			';
        }


        //сохрание в базу
        if (strstr($one_arr['02'], 'add')) {

            $tmp_mail = $one_arr['035'];
            $array_elem = explode(chr(13), $tmp_mail);
            $array_fields = array();
            if (count($array_elem) > 0) {
                $i = 0;
                foreach ($array_elem as $arr_elem_one) {
                    $arr_string = explode('{_}', $arr_elem_one);
                    $tmp_value = iconv('UTF-8', 'CP1251', stripslashes($_POST[$arr_string[1]]));
                    $tmp_field = $arr_string[2];
                    $array_fields[$arr_string[2]] = $tmp_value;
                    $i++;
                }
                $all->insert_block($one_arr['025'], $array_fields, $_POST['cid']);
            }

            $text = '<p>' . $one_arr['037'] . '</p>
			<div style="clear:left"><a href="' . $all->get_url($_POST['cid']) . '">Вернуться</a></div>
			';
        }


        return $text;
    }


    function delimgcode()
    {
        $dir = $this->dir;
        if (is_dir($dir))
            if ($dh = opendir($dir)) {
                $ct = time();
                while (($file = readdir($dh)) !== false)
                    if (is_file($dir . $file))
                        if (($ct - filemtime($dir . $file)) > 3600)
                            @unlink($dir . $file);
                closedir($dh);
            }
    }


    //
    function imagettftext_cr(&$im, $size, $angle, $x, $y, $color, $fontfile, $text)
    {
        // retrieve boundingbox

        $fontfile = $this->dirfont . $fontfile;

        if ($bbox = imagettfbbox($size, $angle, $fontfile, $text)) {

        } else {
            echo $fontfile;
        }

        // calculate deviation
        $dx = ($bbox[2] - $bbox[0]) / 2.0 - ($bbox[2] - $bbox[4]) / 2.0; // deviation left-right
        $dy = ($bbox[3] - $bbox[1]) / 2.0 + ($bbox[7] - $bbox[1]) / 2.0; // deviation top-bottom

        // new pivotpoint
        $px = $x - $dx;
        $py = $y - $dy;

        imagettftext($im, $size, $angle, $px, $py, $color, $fontfile, $text);
        return $bbox[2] - $bbox[0];
    }


    function digitimage()
    {

        $x = 150;
        $y = 50;
        $bgcolor = 0xF5F5F5;
        $numsymb = 5;

        $fonts = array('arial', 'times', 'verdana', 'tahoma', 'georgia', 'gothic');
//		$fonts = array('arial', 'times', 'verdana', 'tahoma');


        $im = @ImageCreateTrueColor($x, $y)
        or die ("Cannot Initialize new GD image stream");

        ImageFilledRectangle($im, 0, 0, $x - 1, $y - 1, $bgcolor);
        $tx = 15 + rand(0, 5);
        $s = '';
        for ($i = 0; $i < $numsymb; $i++) {
            $n = rand(0, 9); // случайная цифра
            $nx = $tx;
            $ny = 15 + rand(0, 15); // координаты цифры
            $f = rand(5, 10); // размер цифры
            $font = $fonts[rand(0, count($fonts) - 1)] . '.ttf';
            $size = rand(23, 23);
            $angle = -40 + rand(0, 80);
            $color = rand(0, 180) * 65536 + rand(0, 180) * 256 + rand(0, 180);

            $dx = $this->imagettftext_cr($im, $size, $angle, $nx, $ny, $color, $font, $n);
            $s .= $n;
            $tx += 8 + $dx;
        };
        $n = rand(100, 150);
        for ($i = 0; $i < $n; $i++) {
            $color = rand(100, 255) * 65536 + rand(100, 255) * 256 + rand(100, 255);
            $nx = rand(1, $x - 2);
            $ny = rand(1, $y - 2);
            imagesetpixel($im, $nx, $ny, $color);
            imagesetpixel($im, $nx - 1, $ny, $color);
            imagesetpixel($im, $nx + 1, $ny, $color);
            imagesetpixel($im, $nx, $ny - 1, $color);
            imagesetpixel($im, $nx, $ny + 1, $color);
        };

        $tmp_cifr_post = md5(base64_encode($this->key . $s));
        ImageJpeg($im, $this->dir . $tmp_cifr_post . ".jpg", 50);
        @imagedestroy($im);
        return $tmp_cifr_post;
        // сгенерировали картинку
    }


}

?>