<?php

$c30 = chr(30);
$c31 = chr(31);

function text_view($text)
{
    global $config;

    $text = makeSpoiler(parse_table(parse_begin(stylizeimages($text))));

    $text = str_replace("../../images/", $config['upload_url'], $text);
    $text = str_replace("../../documents/", $config['upload_doc'], $text);
    $text = substr($text, 6);
    //замена блоквотов на что то более красивое

    $tmp_block_start = '
				<blockquote>

		';

    $tmp_block_end = '
			</blockquote>

		';

    $text = preg_replace("/<blockquote>/i", $tmp_block_start, $text); // оформление
    $text = preg_replace("/<\/blockquote>/i", $tmp_block_end, $text); // оформление


    return parse_href($text);
}


function makeSpoiler($text)
{

    $stbegin = '<p class="spoiler"><span>';
    $stend = '</span></p>';

    $sbegin = '<div class="spoilerBlock" style="display: none">';
    $send = '</div>';

    $text = str_replace("<p>{{spoiler}}</p>", $sbegin, $text);
    $text = str_replace("{{spoiler}}", $sbegin, $text);

    $text = str_replace("<p>{{/spoiler}}</p>", $send, $text);
    $text = str_replace("{{/spoiler}}", $send, $text);

    $text = str_replace("<p>{{sptitle}}</p>", $stbegin, $text);
    $text = str_replace("<p>{{sptitle}}", $stbegin, $text);
    $text = str_replace("{{sptitle}}", $stbegin, $text);

    $text = str_replace("<p>{{/sptitle}}</p>", $stend, $text);
    $text = str_replace("{{/sptitle}}</p>", $stend, $text);
    $text = str_replace("{{/sptitle}}", $stend, $text);

    return $text;
}

function closetag($data)
{
    $i = 0;
    $intag = 0;
    $tags = array();
    $rd = '';
    $n = strlen($data);
    while (($i = strpos($data, '<', $wasi = $i)) !== false) {
        $rd .= substr($data, $wasi, $i - $wasi);
        //$n1 = strpbrk($data, " >\r\n/");
        if (($n1 = strpos($data, ' ', $i + 2)) === false) $n1 = $n;
        if (($n2 = strpos($data, '>', $i + 2)) === false) $n2 = $n;
        if (($n3 = strpos($data, "\r", $i + 2)) === false) $n3 = $n;
        if (($n4 = strpos($data, "\n", $i + 2)) === false) $n4 = $n;
        if (($n5 = strpos($data, '/', $i + 2)) === false) $n5 = $n;
        $t = strtolower(substr($data, $i + 1, min($n1, $n2, $n3, $n4, $n5) - $i - 1));
        //echo /*($i + 1) . "_" . min($n1, $n2, $n3, $n4) . "_" . */htmlspecialchars($t) . "<br>";
        if ($t == 'p') {
            if ($tags[$intag] == 'p') $rd .= '</p>'; else $tags[++$intag] = $t;
        } elseif ($t == 'li') {
            if ($tags[$intag] == 'li') $rd .= '</li>'; else $tags[++$intag] = $t;
        } elseif ($t[0] == '/') {
            if (($tags[$intag] == 'p') && ($t != '/p')) {
                $rd .= '</p>';
                $intag--;
            };
            if (($tags[$intag] == 'li') && ($t != '/li')) {
                $rd .= '</li>';
                $intag--;
            };
            $intag--;
        } elseif (($t != 'br') && ($t != 'img') && ($t != 'hr')) $tags[++$intag] = $t;
        $rd .= $data[$i++];
    };
    return $rd . substr($data, $wasi);
}

;


function tags_text($tags, $s)
{
//функция парсит теги оставляя только выравнивание
    $data = $s;

    $n = -1;
    while (($n = strpos(strtolower($s), '<' . $tags, $n + 1)) !== false) {
        $n1 = strpos($s, ">", $n);
        $tableopen = substr($s, $n, $n1 - $n + 1);

        $regs = array();
        eregi('(align="?([^"^ ]*)"?)', $tableopen, $regs); // выделяем значение alt у тэга <img>
        $alignparam = $regs[1];
        $border = $regs[2];
        unset($regs); // очищаем переменные

        $newtags = '<' . $tags;
        if (trim($alignparam) != '') {
            $newtags .= ' ' . $alignparam;
        }
        $newtags .= '>';

        $data = str_replace($tableopen, $newtags, $data);
    }
//		echo $data;


    return $data;
}


function parse_table($s)
{
    global $config;

    require_once($config["DOCUMENT_ROOT"] . 'libs/simple_html_dom.php');
    $data = str_get_html($s);

    foreach ($data->find('table') as $table) {

        if ($table->getAttribute('border') == '1') {
            $table->setAttribute('class', 'table');

            $trnum = 1;
            foreach ($table->find('tr') as $tr) {
                if ($trnum == 1) {
                    $tr->setAttribute('class', 'thead');
                    $thnum = 1;
                    foreach ($tr->find('th,td') as $td) {
                        $td->tag = 'th';
                        if ($thnum == 1) {
                            $td->setAttribute('class', 'first');
                        }
                        $thnum++;
                    }
                    if ($thnum > 1) $td->setAttribute('class', 'last');

                } else {
                    $thnum = 1;
                    foreach ($tr->find('th,td') as $td) {
                        if ($thnum == 1) {
                            $td->setAttribute('class', 'first');
                        }
                        $thnum++;
                    }
                    if ($thnum > 1) $td->setAttribute('class', 'last');

                    if ($trnum & 1) {
                        $tr->setAttribute('class', 'odd');
                    } else {
                        $tr->setAttribute('class', 'even');
                    }

                }
                $trnum++;
            }
        }
    }

    $out = $data->innertext;
    $data->clear();
    unset($data);

    return $out;
}


///////////////////
function parse_begin($text)
{
    global $config;

    $text = str_replace('<TBODY>', '', $text);
    $text = str_replace('</TBODY>', '', $text);

    $strok = $text;
    $yes = 0;

    $text = $strok;

    $text = str_replace("../../images/", $config['server_url'] . "images/", $text);
    $text = str_replace("../../documents/", $config['server_url'] . "documents/", $text);

    $text = '&nbsp;' . $text;


    return $text;
}

/*
function parse_href($text)
{
  return $text;
    global $config;
    $ico['doc'] = '/img/icons/ico-doc.gif';
    $ico['xls'] = '/img/icons/ico-xls.gif';
    $ico['pdf'] = '/img/icons/ico-pdf.gif';
    $ico['rar'] = '/img/icons/ico-rar.gif';
    $ico['zip'] = '/img/icons/ico-zip.gif';
    $tb = '<span class="ico-link"><a href="{href}"><img src="{tmpimg}" /></a>&nbsp;';
    $ta = '&nbsp;(.{type},&nbsp;{size}Кб)</span>';

    preg_match_all('/<a(.*)href="(.*)"(.*)>(.*)<\/a>/Uis',$text, $out);
    for ($i=0;$i<count($out[1]);$i++)
    if($ico[$type = strtolower(substr($out[2][$i],-3))])$text = str_replace($out[0][$i],str_replace('{tmpimg}',$ico[$type],str_replace('{href}',$out[2][$i],$tb)).$out[0][$i].str_replace('{type}',$type,str_replace('{size}',(round(filesize(str_replace($config['server_url'],'',$out[2][$i]))/1024)),$ta)),$text);

    return $text;
}
*/

function parse_href($text)
{
    global $config;
    $ico['doc'] = '/img/icons/ico-doc.gif';
    $ico['docx'] = '/img/icons/ico-doc.gif';
    $ico['xls'] = '/img/icons/ico-xls.gif';
    $ico['xlsx'] = '/img/icons/ico-xls.gif';
    $ico['pdf'] = '/img/icons/ico-pdf.gif';
    $ico['rar'] = '/img/icons/ico-rar.gif';
    $ico['zip'] = '/img/icons/ico-zip.gif';
    $ico['rtf'] = '/img/icons/ico-rtf.gif';
    $ico['pps'] = '/img/icons/ico-pps.gif';
    $ico['file'] = '/img/icons/ico-file.gif';
    $tb = '<a href="{href}"><img src="{tmpimg}" /></a>&nbsp;';
    $ta = '&nbsp;<small>(.{type},&nbsp;{size}Кб)</small>';

    preg_match_all('/<a(.*) href="(.*)"(.*)>(.*)<\/a>/Uis', $text, $out);

    for ($i = 0; $i < count($out[1]); $i++) {
        $type = strtolower(pathinfo($out[2][$i], PATHINFO_EXTENSION));
        $ext = $type;
        if (!isset($ico[$type])) {
            $type = 'file';
        }

        if ($ext == 'jpg' || $ext == 'gif' || $ext == 'png' || $ext == 'jpeg') continue;

        //игнорируем ссылки на сторонние сайты
        if (strpos($out[2][$i], 'http://') !== FALSE && strpos($out[2][$i], $config['server_url']) === FALSE) continue;

        //ограничиваем выделяемый текст 255 символами, все что длиннее - идет лесом
        if (strlen($out[0][$i]) > 512) continue;

        $fpath = str_replace($config['server_url'], '', $out[2][$i]);
        $fpath = $config['DOCUMENT_ROOT'] . $fpath;
        $fpath = str_replace('//', '/', $fpath);
        if (!is_file($fpath)) continue;
        $fsize = round(@filesize($fpath) / 1024);
        $text = str_replace($out[0][$i], str_replace('{tmpimg}', $ico[$type], str_replace('{href}', $out[2][$i], $tb)) . $out[0][$i] . str_replace('{type}', $ext, str_replace('{size}', $fsize, $ta)), $text);
    }

    return $text;
}


function stylizeimages($s)
{
    global $config;
    global $code_imgclass;
    global $code_open;
    global $code_close;
    $n = -1;

    while (($n = strpos(strtolower($s), '<img', $n + 1)) !== false) {
        $n1 = strpos(strtolower($s), '</a>', $n + 1); // ищем закрывающий тэг A для определения - в ссылке или нет
        $n2 = strpos(strtolower($s), '<a ', $n + 1); // ищем открывающий тэг A
        $s2 = '';
        if (($n1 !== false) && (($n1 < $n2) || ($n2 == false))) { // если внутри ссылки
            $ina = true;
            // проверяем, только ли картинка внутри тэга ссылки
            $n2 = $n - strpos(strtolower(strrev(substr($s, 0, $n - 1))), 'a<') - 3;
            $s2 = substr($s, $n2, $n1 + 4 - $n2); // выделили конструкцию <a> .. <img> .. </a>
            $onlya = preg_match('/^<a[^>]*><img[^>]*><\/a>$/i', $s2) !== false;

            $n3 = strpos($s2, '>');
            if ($n3 !== false) $aopen = substr($s2, 0, $n3 + 1); // выделили открывающий A
            $aclose = substr($s2, -4); // выделили закрывающий A
            $aopen2 = substr($s2, $n3 + 1, $n - $n2 - $n3 - 1); // выделили то что между открывающим A и IMG
            $n3 = strpos($s, '>', $n + 1);
            $aclose2 = substr($s2, $n3 + 1 - $n2, -4); // выделили то что между IMG и закрывающим A
            $img = substr($s, $n, $n3 - $n + 1); // выделили сам IMG

            $regs = array();
            if (!preg_match('/href="([^"]*)"/i', $aopen, $regs))
                preg_match('/href=([^ ^\>]*)( |\>)/i', $aopen, $regs); // выделяем значение href у тэга <a>
            if (!$regs[1]) $regs[1] = ''; // если не найден href - делаем его пустым
            $href = $regs[1];
            unset($regs); // очищаем переменные

            $regs = array();
            if (!preg_match('/target="([^"]*)"/i', $aopen, $regs))
                preg_match('/target=([^ ^\>]*)( |\>)/i', $aopen, $regs); // выделяем значение href у тэга <a>
            if (!$regs[1]) $regs[1] = ''; // если не найден href - делаем его пустым
            $target = $regs[1];
            unset($regs); // очищаем переменные
        } else {
            $ina = false;
            $n1 = strpos($s, ">", $n);
            if ($n1 !== false) $s2 = substr($s, $n, $n1 - $n + 1); // выделили конструкцию <img>
            $img = $s2;
        };

        $regs = array();
        preg_match('/(align="?([^"^ ]*)"?)/i', $img, $regs); // выделяем значение align у тэга <img>
        if (!in_array(strtolower($regs[2]), array('left', 'right', ''))) continue; // если align не left и не right - сваливаем
        $alignparam = $regs[1];
        $align = $regs[2];
        unset($regs); // очищаем переменные

        $regs = array();
        preg_match('/(class="?([^"^ ]*)"?)/i', $img, $regs); // выделяем значение class у тэга <img>
        if (!in_array(strtolower($regs[2]), array('left', 'right', ''))) continue; // если class не left и не right - сваливаем
        $alignparam = $regs[1];
        $align = $regs[2];
        unset($regs); // очищаем переменные

        $regs = array();
        preg_match('/(float: ?left?)/i', $img, $regs); // выделяем значение float: left у тэга <img>
        if ($regs) $align = 'left';
        unset($regs); // очищаем переменные
        $regs = array();
        preg_match('/(float: ?right?)/i', $img, $regs); // выделяем значение float: right у тэга <img>
        if ($regs) $align = 'right';
        unset($regs); // очищаем переменные

        $regs = array();
        preg_match('/(alt="?([^"]*)"?)/i', $img, $regs); // выделяем значение alt у тэга <img>
        $altparam = $regs[1];
        $alt = $regs[2];
        unset($regs); // очищаем переменные

        //высота и ширина
        $regs = array();
        preg_match('/(width="?([^"]*)"?)/i', $img, $regs);
        $wparam = $regs[1];
        $width = $regs[2];
        unset($regs); // очищаем переменные

        $regs = array();
        preg_match('/(width: ?([0-9]*)px?)/i', $img, $regs); //width: 266px
        if (intval($regs[2]))
            $wparam = 'width="' . $regs[2] . '"';
        $width = $regs[2];
        unset($regs); // очищаем переменные


        $regs = array();
        eregi(' (border\-width: ?([0-9]*)px?)', $img, $regs); //width: 266px
        if (intval($regs[2]))
            $border = 'border="' . $regs[2] . '"';
        else $border = '';
        unset($regs); // очищаем переменные

        $regs = array();
        eregi('(height="?([^"]*)"?)', $img, $regs);
        $hparam = $regs[1];
        $height = $regs[2];
        unset($regs); // очищаем переменные

        $regs = array();
        preg_match('/(height: ?([0-9]*)px?)/i', $img, $regs); //height: 266px
        if (intval($regs[2])) {
            $hparam = 'height="' . $regs[2] . '"';
            $height = $regs[2];
        }
        unset($regs); // очищаем переменные

        $regs = array();
        preg_match('/(title="?([^"^ ]*)"?)/i', $img, $regs); // выделяем значение title у тэга <img>
        $titleparam = $regs[1];
        $title = $regs[2];
        unset($regs); // очищаем переменные

        $regs = array();
        preg_match('/(src="?([^"^ ]*)"?)/i', $img, $regs); // выделяем значение title у тэга <img>
        $srcparam = $regs[1];
        $src = $regs[2];
        unset($regs); // очищаем переменные


        if ($ina) {
            $onclick = '';
            if ((strtolower(substr($href, -4)) == '.jpg') ||
                (strtolower(substr($href, -4)) == '.gif') ||
                (strtolower(substr($href, -5)) == '.jpeg') ||
                (strtolower(substr($href, -4)) == '.png') ||
                (strtolower(substr($href, -4)) == '.bmp')
            ) {
                //$target = "_blank";
                $target = "";
                $onclick = 'rel="lightbox"';
                //if (substr($href, 0, strlen($config['server_url'])) == $config['server_url']) {
                //	$onclick = ' onclick="javascript:window.open(\'' . $config['server_url'] . '' . substr($href, strlen($config['server_url'])) . '\', \'_blank\', \'height=500,width=600,status=0,toolbar=0,menubar=0,resizable=1,scrollbars=0,titlebar=0\');return false;"';
                //};
            };

            $class = 'class="' . $code_imgclass[$align] . '"';

            $newimg = '<a href="' . $href . '" target="' . $target . '" title="' . $alt . '" ' . $onclick . ' ><img src="' . $src . '" title="Нажмите, чтобы увеличить изображение" alt="' . $alt . '" ' . $class . ' width="' . $width . '" height="' . $height . '" ' . $border . '></a>';
            $newimg .= '';
//				$newimg = '<img src="' . $src . '" title="' . $alt . '" alt="' . $alt . '">';
//				$newaopen = '<a href="' . $href . '" target="' . $target . '"' . $onclick . ' >';
//				$newaclose = '</a>';

//				$newaopen = '</div><div class="zoom"><a href="' . $href . '" target="' . $target . '"' . $onclick . ' >+ увеличить +';


            global $code_plus;
            $newaopen = $code_plus;
            $newaopen = str_replace('{href}', $href, $newaopen);
            $newaopen = str_replace('{target}', $target, $newaopen);
            $newaopen = str_replace('{onclick}', $onclick, $newaopen);


//				$newaopen = '';

            $newaclose = '';

            //echo $newimg; die();
            $newblock = $code_open[$align] . $newimg . $newaopen . $newaclose . $code_close[$align] . '';

            if ($onlya) {
                $snew = $newblock;
                $s = substr($s, 0, $n2) . $snew . substr($s, $n1 + 4);
                $n = $n2 + strlen($snew);
            } else {
                $snew = '';
                if ($aopen2) $snew .= $aopen . $aopen2 . $aclose;
                $snew .= $newblock;
                if ($aclose2) $snew .= $aopen . $aclose2 . $aclose;
                $s = substr($s, 0, $n2) . $snew . substr($s, $n1 + 4);
                $n = $n2 + strlen($snew);
            };
        } else {


            $class = 'class="' . $code_imgclass[$align] . '"';

            $newimg = sprintf('<img src="%s" title="%s" alt="%s" %s %s %s %s>', $src, $alt, $alt, $wparam, $hparam, $class, $border);
            $newblock = $code_open[$align] . $newimg . $code_close[$align];
            $snew = $newblock;
            $s = substr($s, 0, $n) . $snew . substr($s, $n1 + 1);
            $n = $n + strlen($snew);
        };

    };

    /*
    echo htmlspecialchars($s2) . "<br>";
    echo "ina - " . ($ina ? 1 : 0) . "<br>";
    echo "onlya - " . ($onlya ? 1 : 0) . "<br>";
    echo "aopen - " . htmlspecialchars($aopen) . "<br>";
    echo "aclose - " . htmlspecialchars($aclose) . "<br>";
    echo "aopen2 - " . htmlspecialchars($aopen2) . "<br>";
    echo "aclose2 - " . htmlspecialchars($aclose2) . "<br>";
    echo "href - " . htmlspecialchars($href) . "<br>";
    echo "img - " . htmlspecialchars($img) . "<br>";
    echo "align - " . htmlspecialchars($align) . "<br>";
    echo "alt - " . htmlspecialchars($alt) . "<br>";
    echo "title - " . htmlspecialchars($title) . "<br>";
    echo "snew - " . nl2br(htmlspecialchars($snew)) . "<br>";
    exit;
    */

    return $s;
}

;


?>