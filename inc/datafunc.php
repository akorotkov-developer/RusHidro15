<?php
// ====================================Внедрение блоков=============================================================
function input_addblocks($name, $attr, $data, $comment = '')
{
    global $config;
    $s = "<input name=\"" . htmlspecialchars($name) . "\" type=\"file\" $attr value=\"\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_wasuploaded\" type=\"hidden\" value=\"" . bin2hex($data) . "\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_comment\" type=\"hidden\" value=\"" . htmlspecialchars($comment) . "\">";
    if ($data != '') {
        $n = strpos($data, ' ');
        $fn = substr($data, 0, $n);
        if ($fn == '') {
            $fn = trim($data);
        }
        $s .= "<br><a href=\"" . $config['server_url'] . "files/0/$fn\" target=\"_blank\">просмотр..</a>";
        $s .= "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"" . htmlspecialchars($name) . "_remove\" value=1 $attr> удалить";
    };
    return $s;
}

;
function save_addblocks($name)
{
    global $sql;
    global ${"$name"};
    global ${"$name" . "_wasuploaded"};
    global ${"$name" . "_comment"};
    global ${"$name" . "_name"};
    global ${"$name" . "_type"};
    global ${"$name" . "_size"};
    global ${"$name" . "_remove"};
    global $error;
    global $prname;
    $uploaded = false;
    if (is_uploaded_file(${"$name"})) {
        $uploaded = true;
        $comment = ${"$name" . "_comment"};
        $fn = ${"$name" . "_name"};
        $n = strrpos($fn, ".");
        $ext = substr($fn, $n);

        if ($uploaded) {
// Список переменных.
            $parent = $_POST['parent']; // Родительский каталог.
            $b_template = $comment; // Шаблон блока.
            $visible = '1';
            $file_tmpdir = '../files/tmp/';
// Выбираем список полей шаблона в массив $keys.
            $q = $sql->query("select * from prname_bdatarel where MATCH(`key`) AGAINST ('$comment" . "_*' IN BOOLEAN MODE) order by sort");
            $i = 1;
            while ($arr = $sql->fetch_assoc($q)) {
                $keys[$i][key] = $arr[key];
                $keys[$i][datatkey] = $arr[datatkey];
                $keys[$i++][comment] = $arr[comment];
            }

// коментарии tovars:поле1;поле2;поле3
// ==================================== Чтение файла =====================================================
//
//$fn = (rand(0, 255) + 256 * rand(0, 255) + 65536 * rand(0, 255)) . time() . $ext;
//copy(${"$name"}, "../files/0/$fn");
//$data = $fn;
            //Формируем sql запрос  обновлений.


            $handle = fopen(${$name}, "r");
            while ($data = fgetcsv($handle, 1000, ";"))// Создание блока.
            {
                if ($data[0][0] !== '#') {
                    mysql_query("insert into $prname" . "_blocks set `template` = '" . $b_template . "', `parent`='$parent'");
                    $bid = $sql->insert_id();
                    for ($ii = 0; $ii < count($data); $ii++)// формирование запроса в строке.
                    {
                        if ($keys[$ii][datatkey] == 'file') {
                            if (($n = strpos($keys[$ii][comment], 'resize:')) !== false) {
                                $fn = $data[$ii];
                                $n = strrpos($fn, ".");
                                $ext = substr($fn, $n);
                                $fn = (rand(0, 255) + 256 * rand(0, 255) + 65536 * rand(0, 255)) . time() . $ext;
                                copy($file_tmpdir . $data[$ii], "../files/0/$fn");
                                $fn1 = explode('resize:', $keys[$ii][comment]);
                                $fs = explode(',', $fn1[1]);
                                for ($if = 0; $if < count($fs); $if++) {

                                    resize_image($fn, $fs[$if], $if + 1);
                                }

                                $data[$ii] = $fn;
                            };

                        }
                        if ($data[$ii] && $keys[$ii][key]) $str .= '`' . $keys[$ii][key] . '`' . " = '" . $data[$ii] . "' , ";
                    }
                    $sort = $sql->fetch_row($sql->query("select MAX(sort) from prname_b_$b_template"), 0, 1);
                    $sql->query("insert into prname_b_$b_template set $str `parent`='$parent', `bid`='$bid', `visible`='$visible', `sort`='$sort'");
                    $str = '';
                }
            }
        };
    };

    if (!$uploaded) {
        if (${"$name" . "_wasuploaded"} != '') {
            if (${"$name" . "_remove"} != '') {
                $data = '';
            } else {
                $data = @pack("H*", stripslashes(${"$name" . "_wasuploaded"}));
            };
        } else $data = '';
    };
    return $data;
}

;
function get_addblocks($data, $comment = '')
{
    if ($data != '') {
        return "0/$data";
    } else return false;
}

;
// =================================================================================================================

// ====================================Карта=============================================================
function input_map($name, $attr, $data, $comment = '')
{
    return '	<style type="text/css">
	input.text{font-family: Tahoma; font-size: 11px; color: #808080; font-weight: bold;border: 1px #808080 solid;padding-left:3px} 
	a.alayer {font-family: Tahoma; font-size: 11px; color: #333333; font-weight: bold;text-decoration:none}
	a.alayer:hover{text-decoration:underline}
	div.layerparent{overflow:hidden; width:407px; height:329px;cursor:pointer}
	div.layermarker{position: absolute; z-index: 2; left:0px; top:0px; width:32px; height:32px}
    span.subname{font-family: Tahoma; font-size: 11px; color: #000000; font-weight: bold;}
    span.subbody{font-family: Tahoma; font-size: 11px; color: #000000;}
    div.layersub{display:none;padding:8px;border:1px solid #C0C0C0; position: relative; width:228px;background-color:#E8F3FF}
	</style>
	


	<div class="layerparent" id="layerparent" onclick="fun1(event)">
<img src="tmp/geomap.jpg" width="407" id="layerimg" height="329" >
<!-- ========================================== Див с фрагментом =================================================-->
<div class="layermarker" id="layer2">
   <img border="0" id="marker" src="tmp/marker.gif" width="32" height="32" align="left" >
      <a href="#" class="alayer" id="name"></a>

</div>

</div>

<script language="javascript">
<!--

    $(\'layer2\').style.left=($(\'data1text\').value-18)+\'px\';
  $(\'layer2\').style.top=($(\'data2text\').value-15)+\'px\';
//-->
</script>';
}

function save_map($name)
{
    global ${"$name"};
    return '';
}

;

function get_map($data, $comment = '')
{
    return $data;
}

;
// ====================================Карта=============================================================

function input_file($name, $attr, $data, $comment = '')
{
    global $config;
    $s = "<input name=\"" . htmlspecialchars($name) . "\" type=\"file\" $attr value=\"\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_wasuploaded\" type=\"hidden\" value=\"" . bin2hex($data) . "\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_comment\" type=\"hidden\" value=\"" . htmlspecialchars($comment) . "\">";
    if ($data != '') {
        $n = strpos($data, ' ');
        $fn = substr($data, 0, $n);
        if ($fn == '') {
            $fn = trim($data);
        }
        $s .= "<br><a href=\"" . $config['server_url'] . "files/$fn\" target=\"_blank\">просмотр..</a>";
        $s .= "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"" . htmlspecialchars($name) . "_remove\" value=1 $attr> удалить";
    };
    return $s;
}

;

function save_file($name)
{
    global ${"$name"};
    global ${"$name" . "_wasuploaded"};
    global ${"$name" . "_comment"};
    global ${"$name" . "_name"};
    global ${"$name" . "_type"};
    global ${"$name" . "_size"};
    global ${"$name" . "_remove"};
    global $error;

    //массив для транслитерации
    $trans = array(
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
        " " => "_"
    );

    $uploaded = false;
    ${"$name"} = $_FILES[$name]['tmp_name'];
    ${"$name" . "_type"} = $_FILES[$name]['type'];
    ${"$name" . "_size"} = $_FILES[$name]['size'];
    $comment = $_POST[$name . '_comment'];
    ${"$name" . "_wasuploaded"} = $_POST[$name . '_wasuploaded'];
    ${"$name" . "_remove"} = $_POST[$name . '_remove'];
//Array ( [data4] => Array ( [name] => 634032.jpg [type] => image/pjpeg [tmp_name] => /tmp\php24A.tmp [error] => 0 [size] => 155585 ) )

    if (is_uploaded_file(${"$name"})) {
        $uploaded = true;
        $fn = $_FILES[$name]['name'];
        $n = strrpos($fn, ".");
        $trans_fn = strtr(substr($fn, 0, $n), $trans);//транслитерация
        $ext = substr($fn, $n);
        if (($n = strpos($comment, 'maxsize:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);
            if (${"$name" . "_size"} > $val) {
                $uploaded = false;
                $error = "Размер файла не должен превышать $val байт";
            };
        };
        if (($n = strpos($comment, 'minsize:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);
            if (${"$name" . "_size"} < $val) {
                $uploaded = false;
                $error = "Размер файла должен быть не менее $val байт";
            };
        };


        if (($n = strpos($comment, 'nomime:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 7;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (in_array(${"$name" . "_type"}, $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . ${"$name" . "_type"} . ". Пожалуйста, не используйте файлы типа $val";
            };
        };
        if (($n = strpos($comment, 'noext:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 6;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (in_array(strtolower(substr($ext, 1)), $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . substr($ext, 1) . ". Пожалуйста, не используйте файлы типа $val";
            };
        };

        if (($n = strpos($comment, 'onlymime:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 9;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (!in_array(${"$name" . "_type"}, $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . ${"$name" . "_type"} . ". Пожалуйста, используйте файлы типа $val";
            };
        };

        if (($n = strpos($comment, 'onlyext:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);

            $mimes = explode(",", $val);
            if (!in_array(strtolower(substr($ext, 1)), $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . substr($ext, 1) . ". Пожалуйста, используйте файлы типа $val";
            };

        };


        if ($uploaded) {
            //$fn = (rand(0, 255) + 256 * rand(0, 255) + 65536 * rand(0, 255)) . time() . $ext;
            $fn = $trans_fn . $ext;
            //проверка на уникальность
            for ($i = 2; $i < 9999; $i++) {
                if (!is_file("../files/" . $fn)) break;

                $fn = $trans_fn . "_" . $i . $ext;
            }
            copy(${"$name"}, "../files/$fn");

            $data = $fn;
        };
    };

    if (!$uploaded) {
        if (${"$name" . "_wasuploaded"} != '') {
            if (${"$name" . "_remove"} != '') {
                $data = '';
            } else {
                $data = @pack("H*", stripslashes(${"$name" . "_wasuploaded"}));
            };
        } else $data = '';
    };

    return $data;
}

;


function get_file($data, $comment = '')
{
    if ($data != '') {
        /*$n = strpos($data, ' ');
        $fn = substr($data, 0, $n);
        return "$fn";*/
        return $data;
    } else return false;
}

;

function get2_file($data, $comment = '', $cantry = true)
{
    if ($data == '') return false;
    $comment = 0 + $comment;
    if ($cantry && !is_file("files/$comment/$data")) {
        if ($comment < 1) return false;
        if (!is_file("files/0/$data")) return false;
        if (!update_images($data, $comment)) $comment = 0; else return get2_file($data, $comment, false);
    };
    $a = array();
    $a[0] = $comment . "/" . $data;
    $sizes = getimagesize("files/$comment/$data");
    $a[1] = $sizes[0];
    $a[2] = $sizes[1];
    $a[3] = 'width="' . trim($a[1]) . '" height="' . trim($a[2]) . '"';
    return $a;
}

function save_image_multi()
{

}

function input_image_multi($name, $attr, $data, $comment = '')
{
    global $config, $actions;
    global $cparent, $btmplate, $actions, $blockid;
    $comment = explode(',', $comment);
    foreach ($comment as $comm) {
        list($key, $var) = explode(':', $comm);
        $$key = $var;
    }
    /**
     * @var $field string
     */
    if ($actions === 'edit') {
        return <<<HTML
<style>
.multi-image {
    min-width:200px;
    min-height:200px;
    background-color:#C0FFAA;
    border-radius: 10px;
    text-align:center;
    vertical-align: middle;
}
.multi-image. {
    color: #555;
    font-size: 18px;
    text-align: center;

    width: 400px;
    padding: 50px 0;
    margin: 50px auto;

    background: #eee;
    border: 1px solid #ccc;

    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

.multi-image.error {
    background: #faa;
    border-color: #f00;
}
.multi-image.hover {
    background: #ddd;
    border-color: #aaa;
}
.multi-image.drop {
    background: #afa;
    border-color: #0f0;
}

</style>
<div class="multi-image" id="drop-{$name}"><b>Перетащи сюда все фотографии</b></div>
<script>
var setaction = '';
_jQuery(document).ready(function(){
    var $ = _jQuery;
    var dz = _jQuery('#drop-{$name}');
    if (typeof(window.FileReader) == 'undefined') {
        dz.children('b').text('Не поддерживается браузером!');
        dz.addClass('error');
    }
    dz[0].ondragover = function() {
        dz.addClass('hover');
        return false;
    };
    dz[0].ondragleave = function() {
        dz.removeClass('hover');
        return false;
    };
    dz[0].ondrop = function(event) {
        event.preventDefault();
        dz.removeClass('hover');
        dz.addClass('drop');
       // console.log(event.dataTransfer);
        var finp = _jQuery('[value={$field}]').parent().children('input').eq(0);
        var fname = finp.attr('name');
            var photos = 0;
            var act = _jQuery('form').eq(0).attr('action').replace('actions=save', 'actions=addnew').replace('&blockid={$blockid}', '');
            $('[name=set_block_parent_catalog]').parents('tr').eq(0).remove();
            $('[name=blockid]').val('');
        for (var i = 0; i < event.dataTransfer.files.length; i++) {
            var file = event.dataTransfer.files[i];
            if (!/image/.test(file.type)) {
                continue;
            }
            photos++;
            (function(file){
                var data = new FormData();
                _jQuery('[name]').each(function(){
                    var name = $(this).attr('name');
                    if (name == fname) {
                        data.append(fname, file);
                    } else {
                        data.append(name, $(this).val());
                    }
                    //console.log('append ' + name);
                });
                _jQuery.ajax(act, {processData: false, type: 'post', contentType: false, data: data}).done(function(){
                    photos--;
                    var reader = new FileReader();
                    //console.log(file);
                    reader.onload = function(e){
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        dz.append(img);
                    };
                    reader.readAsDataURL(file);
                    if (photos <= 0) {
                      //  back_b();
                    }
                });
                dz.removeClass('drop');
            })(file);
        }
    };
});
</script>

<input name="{$name}" type=hidden $attr value=""/>
HTML;
    }
    return '';

}

// --------- ИЗОБРАЖЕНИЕ -------------------
function input_image($name, $attr, $data, $comment = '')
{
    global $config;
    $s = "<input name=\"" . htmlspecialchars($name) . "\" type=\"file\" $attr value=\"\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_wasuploaded\" type=\"hidden\" value=\"" . bin2hex($data) . "\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_comment\" type=\"hidden\" value=\"" . htmlspecialchars($comment) . "\">";
    if ($data != '') {
        $n = strpos($data, ' ');
        $fn = substr($data, 0, $n);
        if ($fn == '') {
            $fn = trim($data);
        }
        $s .= "<br><a href=\"" . $config['server_url'] . "images/0/$fn\" target=\"_blank\">просмотр..</a>";
        $s .= "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"" . htmlspecialchars($name) . "_remove\" value=1 $attr> удалить";
    };
    return $s;
}

;


function save_image($name)
{
    global ${"$name"};
    global ${"$name" . "_wasuploaded"};
    global ${"$name" . "_comment"};
    global ${"$name" . "_name"};
    global ${"$name" . "_type"};
    global ${"$name" . "_size"};
    global ${"$name" . "_remove"};
    global $error;
    $uploaded = false;
    ${"$name"} = $_FILES[$name]['tmp_name'];
    ${"$name" . "_type"} = $_FILES[$name]['type'];
    ${"$name" . "_size"} = $_FILES[$name]['size'];
    $comment = $_POST[$name . '_comment'];
    ${"$name" . "_wasuploaded"} = $_POST[$name . '_wasuploaded'];
    ${"$name" . "_remove"} = $_POST[$name . '_remove'];
//Array ( [data4] => Array ( [name] => 634032.jpg [type] => image/pjpeg [tmp_name] => /tmp\php24A.tmp [error] => 0 [size] => 155585 ) )

    if (is_uploaded_file(${"$name"})) {
        $uploaded = true;
        $fn = $_FILES[$name]['name'];
        $n = strrpos($fn, ".");
        $ext = substr($fn, $n);


        $isizes = @getimagesize($_FILES[$name]['tmp_name']);
        if (($isizes[0] > 2000 || $isizes[1] > 2000) && strpos($comment, 'resize:') !== false) {
            $uploaded = false;
            $error = "Размер изображения не должен превышать 2000 точек по любой из сторон.";
        }


        if (($n = strpos($comment, 'maxsize:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);
            if (${"$name" . "_size"} > $val) {
                $uploaded = false;
                $error = "Размер файла не должен превышать $val байт";
            };
        };
        if (($n = strpos($comment, 'minsize:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);
            if (${"$name" . "_size"} < $val) {
                $uploaded = false;
                $error = "Размер файла должен быть не менее $val байт";
            };
        };


        if (($n = strpos($comment, 'nomime:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 7;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (in_array(${"$name" . "_type"}, $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . ${"$name" . "_type"} . ". Пожалуйста, не используйте файлы типа $val";
            };
        };
        if (($n = strpos($comment, 'noext:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 6;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (in_array(strtolower(substr($ext, 1)), $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . substr($ext, 1) . ". Пожалуйста, не используйте файлы типа $val";
            };
        };

        if (($n = strpos($comment, 'onlymime:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 9;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (!in_array(${"$name" . "_type"}, $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . ${"$name" . "_type"} . ". Пожалуйста, используйте файлы типа $val";
            };
        };

        if (($n = strpos($comment, 'onlyext:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);

            $mimes = explode(",", $val);
            if (!in_array(strtolower(substr($ext, 1)), $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . substr($ext, 1) . ". Пожалуйста, используйте файлы типа $val";
            };

        };


        if ($uploaded) {
            $fn = (rand(0, 255) + 256 * rand(0, 255) + 65536 * rand(0, 255)) . time() . $ext;
            copy(${"$name"}, "../images/0/$fn");


            /*
		if (($n = strpos($comment, 'resize:')) !== false)
                {
                    $fn1 = explode('resize:',$comment);
                    $fs = explode(',',$fn1[1]);
                    for ($if=0;$if<count($fs);$if++)
				resize_image($fn, $fs[$if], $if+1);
                };

            */

            $data = $fn;
        };

    };
    if (!$uploaded) {
        if (${"$name" . "_wasuploaded"} != '') {
            if (${"$name" . "_remove"} != '') {
                $data = '';
            } else {
                $data = @pack("H*", stripslashes(${"$name" . "_wasuploaded"}));
            };
        } else $data = '';
    };

    return $data;
}

;


function get_image($data, $comment = '')
{
    if ($data != '') {
        /*$n = strpos($data, ' ');
        $fn = substr($data, 0, $n);
        return "$fn";*/
        return "0/$data";
    } else return false;
}

;


// --------- ИЗОБРАЖЕНИЕ (ИЩУ МАМУ) -------------------
function input_shoumum($name, $attr, $data, $comment = '')
{
    global $config;
    $s = "<input name=\"" . htmlspecialchars($name) . "\" type=\"file\" $attr value=\"\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_wasuploaded\" type=\"hidden\" value=\"" . bin2hex($data) . "\">";
    $s .= "<input name=\"" . htmlspecialchars($name) . "_comment\" type=\"hidden\" value=\"" . htmlspecialchars($comment) . "\">";
    if ($data != '') {
        $n = strpos($data, ' ');
        $fn = substr($data, 0, $n);
        if ($fn == '') {
            $fn = trim($data);
        }
        $s .= "<br><a href=\"" . $config['server_url'] . "images/findmom0/$fn\" target=\"_blank\">просмотр..</a>";
        $s .= "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"" . htmlspecialchars($name) . "_remove\" value=1 $attr> удалить";
    };
    return $s;
}

;


function save_shoumum($name)
{
    global ${"$name"};
    global ${"$name" . "_wasuploaded"};
    global ${"$name" . "_comment"};
    global ${"$name" . "_name"};
    global ${"$name" . "_type"};
    global ${"$name" . "_size"};
    global ${"$name" . "_remove"};
    global $error;
    $uploaded = false;
    ${"$name"} = $_FILES[$name]['tmp_name'];
    ${"$name" . "_type"} = $_FILES[$name]['type'];
    ${"$name" . "_size"} = $_FILES[$name]['size'];
    $comment = $_POST[$name . '_comment'];
    ${"$name" . "_wasuploaded"} = $_POST[$name . '_wasuploaded'];
    ${"$name" . "_remove"} = $_POST[$name . '_remove'];
//Array ( [data4] => Array ( [name] => 634032.jpg [type] => image/pjpeg [tmp_name] => /tmp\php24A.tmp [error] => 0 [size] => 155585 ) )

    if (is_uploaded_file(${"$name"})) {
        $uploaded = true;
        $fn = $_FILES[$name]['name'];
        $n = strrpos($fn, ".");
        $ext = substr($fn, $n);
        if (($n = strpos($comment, 'maxsize:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);
            if (${"$name" . "_size"} > $val) {
                $uploaded = false;
                $error = "Размер файла не должен превышать $val байт";
            };
        };
        if (($n = strpos($comment, 'minsize:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);
            if (${"$name" . "_size"} < $val) {
                $uploaded = false;
                $error = "Размер файла должен быть не менее $val байт";
            };
        };


        if (($n = strpos($comment, 'nomime:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 7;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (in_array(${"$name" . "_type"}, $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . ${"$name" . "_type"} . ". Пожалуйста, не используйте файлы типа $val";
            };
        };
        if (($n = strpos($comment, 'noext:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 6;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (in_array(strtolower(substr($ext, 1)), $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . substr($ext, 1) . ". Пожалуйста, не используйте файлы типа $val";
            };
        };

        if (($n = strpos($comment, 'onlymime:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 9;
            $val = substr($comment, $n, $n2 - $n);
            $mimes = explode(",", $val);
            if (!in_array(${"$name" . "_type"}, $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . ${"$name" . "_type"} . ". Пожалуйста, используйте файлы типа $val";
            };
        };

        if (($n = strpos($comment, 'onlyext:')) !== false) {
            if (($n2 = strpos($comment, ' ', $n)) == false) {
                $n2 = strlen($comment);
            };
            $n += 8;
            $val = substr($comment, $n, $n2 - $n);

            $mimes = explode(",", $val);
            if (!in_array(strtolower(substr($ext, 1)), $mimes)) {
                $uploaded = false;
                $error = "Недопустимый тип файла " . substr($ext, 1) . ". Пожалуйста, используйте файлы типа $val";
            };

        };


        if ($uploaded) {
            $fn = (rand(0, 255) + 256 * rand(0, 255) + 65536 * rand(0, 255)) . time() . $ext;
            copy(${"$name"}, "../images/findmom0/$fn");


            /*
		if (($n = strpos($comment, 'resize:')) !== false)
                {
                    $fn1 = explode('resize:',$comment);
                    $fs = explode(',',$fn1[1]);
                    for ($if=0;$if<count($fs);$if++)
				resize_image($fn, $fs[$if], $if+1);
                };

            */

            $data = $fn;
        };

    };
    if (!$uploaded) {
        if (${"$name" . "_wasuploaded"} != '') {
            if (${"$name" . "_remove"} != '') {
                $data = '';
            } else {
                $data = @pack("H*", stripslashes(${"$name" . "_wasuploaded"}));
            };
        } else $data = '';
    };

    return $data;
}

;


function get_shoumum($data, $comment = '')
{
    if ($data != '') {
        /*$n = strpos($data, ' ');
        $fn = substr($data, 0, $n);
        return "$fn";*/
        return "findmom0/$data";
    } else return false;
}

;


function input_html($name, $attr, $data, $comment = '')
{
    $append = '';
    if (!defined('CKEDITOR_LOADED')) {
        // если экземпляр скрипта CKEDITOR еще не загружен
        // добавляем его для загрузки
        $append = <<<HTML
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
HTML;
        define('CKEDITOR_LOADED', true); // определяем, что экземпляр CKEDITOR загружен
    }
    return <<<CKEDITOR
{$append}
<textarea id="idCTemp$name" name="{$name}" class="ckeditor">{$data}</textarea>
CKEDITOR;
}

function save_html($name)
{
    global ${"$name"};

    return stripslashes(${"$name"});
}

;

function get_html($data, $comment = '')
{
    return $data;
}

;


function input_text($name, $attr, $data, $comment = '')
{
    return "<input name=\"" . htmlspecialchars($name) . "\" id=\"" . htmlspecialchars($name) . "text\" type=\"text\" $attr value=\"" . htmlspecialchars($data) . "\">";
}

function save_text($name)
{
    global ${"$name"};
    return stripslashes(trim(${"$name"}));
}

;

function get_text($data, $comment = '')
{
    return $data;
}

;


function input_find($name, $attr, $data, $comment = '')
{
    return "
	<a href=\"find.php?event=start\" target=\"fframe\" onclick=\"$('fframe').style.display='none';\">Обновить индекс</a><br>

<iframe frameborder=\"no\"  style=\"display:none;border:0px\" name=\"fframe\" id=\"fframe\" width=\"200\" height=\"100\"></iframe>

	
	";
}

function save_find($name)
{
    global ${"$name"};
    return stripslashes(${"$name"});
}

;

function get_find($data, $comment = '')
{
    return $data;
}

;


function input_date($name, $attr, $data, $comment = '')
{
    if (!@checkdate(substr($data, 5, 2), substr($data, 8), substr($data, 0, 4))) {
        $data = strftime("%Y-%m-%d");
    };
    $data = substr($data, 8) . "." . substr($data, 5, 2) . "." . substr($data, 0, 4);

    $id_datefild = "data_" . rand(1, 1000) . rand(1, 1000) . rand(1, 1000);
    return "<input maxlength=10 id=\"$id_datefild\" name=\"" . htmlspecialchars($name) . "\" type=\"text\" $attr value=\"" . htmlspecialchars($data) . "\" readonly=\"readonly\">	<a href=\"\" onclick=\"popUpCalendar(this, document.getElementById('$id_datefild'), 'dd.mm.yyyy','right');return false;\" ><img src=\"img/calendar.gif\" alt=\"\" width=\"34\" height=\"20\"  class=\"calendar\" onclick=\"popUpCalendar(this, document.getElementById('$id_datefild'), 'dd-mm-yyyy','right');return false;\" /></a>";
}

function save_date($name)
{
    global ${"$name"};
    $data = substr(${"$name"}, 6) . "-" . substr(${"$name"}, 3, 2) . "-" . substr(${"$name"}, 0, 2);
    if (!@checkdate(substr($data, 5, 2), substr($data, 8), substr($data, 0, 4))) {
        $data = strftime("%Y-%m-%d");
    };
    return stripslashes($data);
}

;

function get_date($data, $comment = '')
{
    $data = substr($data, 8, 2) . substr($data, 4, 4) . substr($data, 0, 4);
    return $data;
}

;

function input_textarea($name, $attr, $data, $comment = '')
{
    //return "<textarea name=\"".htmlspecialchars($name)."\" $attr>".htmlspecialchars($data)."</textarea>";
    return "<textarea name=\"" . htmlspecialchars($name) . "\" $attr id=\"my$name\">" . htmlspecialchars($data) . "</textarea>";

}

function save_textarea($name)
{
    global ${"$name"};
    return stripslashes(trim(${"$name"}));
}

;

function get_textarea($data, $comment = '')
{
    return $data;
}

;

function input_textare($name, $attr, $data, $comment = '')
{
    //return "<textarea name=\"".htmlspecialchars($name)."\" $attr>".htmlspecialchars($data)."</textarea>";
    return "<textarea name=\"" . htmlspecialchars($name) . "\" $attr id=\"my$name\">" . htmlspecialchars($data) . "</textarea>
	<br />
	<button id=\"butt1$name\" onClick=\"qmyArea$name = new nicEditor({fullPanel : true}).panelInstance('my$name');$('butt2$name').style.display='';$('butt1$name').style.display='none';\">Показать панель форматирования</button> 
	<button id=\"butt2$name\" style=\"display:none\" onClick=\"qmyArea$name.removeInstance('my$name');$('butt1$name').style.display='';$('butt2$name').style.display='none';\">Убрать панель форматирования</button>";

}

function save_textare($name)
{
    global ${"$name"};
    return stripslashes(${"$name"});
}

;

function get_textare($data, $comment = '')
{
    return $data;
}

;

function input_checkbox($name, $attr, $data, $comment = '')
{
    $ch = ($data > 0) ? " checked" : "";
    return "<input type=\"checkbox\" name=\"" . htmlspecialchars($name) . "\" id=\"" . htmlspecialchars($name) . "checkbox\" $attr value=\"1\"$ch>";
}

function save_checkbox($name)
{
    global ${"$name"};
    return (${"$name"} > 0) ? 1 : 0;
}

function get_checkbox($data, $comment = '')
{
    return ($data > 0);
}

;

function input_select($name, $attr, $data, $comment)
{
    global $sql;
    global $prname;

    // block:template:field_name:[PID]:[SORT]
    //пр. block:news:news_theme:1234
    // ИЛИ
    // cat:PID
    //пр. cat:56
    // ИЛИ
    // variant1;variant2;variant3

    $s = "<select name=\"" . htmlspecialchars($name) . "\" $attr>
		<option value=\"0\">---</option>
	";

    if (strpos($comment, ":") !== FALSE) //если связанные блоки
    {

        $d = explode(":", $comment);
        $type = $d[0];

        if ($type == "block") {
            $tpl = $d[1];
            $fname = $d[2];

            $pid = "";
            if (intval($d[3]))
                $pid = sprintf("AND parent = %d", intval($d[3]));

            $orderby = 'sort';
            if (strlen($d[4]))
                $orderby = $d[4];


            $q = sprintf("SELECT id, %s AS name FROM prname_b_%s WHERE visible <> 0 %s ORDER BY %s;", $fname, $tpl, $pid, $orderby);
        }

        if ($type == "cat") {
            $q = sprintf("select id, name FROM prname_categories WHERE parent = %d ORDER BY sort;", intval($d[1]));
        }

        $res = $sql->query($q);
        while ($arr = $sql->fetch_assoc($res)) {
            $s .= "<option value=\"" . $arr['id'] . "\" " . (($data == $arr['id']) ? ' selected ' : '') . " $attr>" . htmlspecialchars($arr['name']) . "</option>";
        }

    } else // если просто перечень значений
    {
        //Вида 0((голосование));1((завершено));2((напечатан)) - 0,1,2 - значения, в скобках подписи
        if ((strstr($comment, '((')) && (strstr($comment, '))'))) {
            $flag_comment = true;
        }
        $val = explode(';', $comment);

        for ($i = 0; $i < count($val); $i++) {
            if ($flag_comment) {
                $tmp_val = substr($val[$i], 0, strpos($val[$i], '(('));
                $tmp_title = substr($val[$i], strpos($val[$i], '(('));
                $tmp_title = str_replace('((', '', $tmp_title);
                $tmp_title = str_replace('))', '', $tmp_title);
            } else {
                $tmp_val = $val[$i];
                $tmp_title = $val[$i];
            }
            $s .= "<option value=\"" . htmlspecialchars($tmp_val) . "\" " . (htmlspecialchars($tmp_val) == $data ? ' selected ' : '') . ">" . htmlspecialchars($tmp_title) . "</option>";
            //$s .= "<option value=\"" . htmlspecialchars($val[$i]) . "\" ".(htmlspecialchars($val[$i]) == $data ? ' selected ':'').">" . htmlspecialchars($val[$i]) . "</option>";
        };
    }

    $s .= "</select>";
    return $s;
}

function save_select($name)
{
    global ${"$name"};
    return ${"$name"};
}

function get_select($data, $comment = '')
{

    global $sql;
    if (strpos($comment, ":") !== FALSE) //если связанные блоки
    {

        $d = explode(":", $comment);
        $type = $d[0];

        if ($type == "block") {
            $tpl = $d[1];
            $fname = $d[2];

            $pid = "";
            if (intval($d[3]))
                $pid = sprintf("AND parent = %d", intval($d[3]));

            $q = sprintf("SELECT id, %s AS name FROM prname_b_%s WHERE visible <> 0 %s AND id = '$data'  ORDER BY sort;", $fname, $tpl, $pid);
        }

        if ($type == "cat") {
            $q = sprintf("select id, name FROM prname_categories WHERE parent = %d AND id = '$data' ORDER BY sort;", intval($d[1]));
        }

        $res = $sql->query($q);
        while ($arr = $sql->fetch_assoc($res)) {
            if ($data == $arr['id']) {
                $s_data = $arr['name'];
            }
            $s .= "<option value=\"" . $arr['id'] . "\" " . (($data == $arr['id']) ? ' selected ' : '') . " $attr>" . htmlspecialchars($arr['name']) . "</option>";
        }

    } else // если просто перечень значений
    {
        //Вида 0((голосование));1((завершено));2((напечатан)) - 0,1,2 - значения, в скобках подписи
        if ((strstr($comment, '((')) && (strstr($comment, '))'))) {
            $flag_comment = true;
        }
        $val = explode(';', $comment);
        for ($i = 0; $i < count($val); $i++) {
            if ($flag_comment) {
                $tmp_val = substr($val[$i], 0, strpos($val[$i], '(('));
                $tmp_title = substr($val[$i], strpos($val[$i], '(('));
                $tmp_title = str_replace('((', '', $tmp_title);
                $tmp_title = str_replace('))', '', $tmp_title);
            } else {
                $tmp_val = $val[$i];
                $tmp_title = $val[$i];
            }
            if ($tmp_val == $data) {
                $s_data = $tmp_title;
            }
        };
    }

    return $s_data;

    //echo $comment;

//	$val = splstr($comment, ";");
//	if ($data == '') {return false;} else {return $val[$data + 1];};
}

;

function input_mselect($name, $attr, $data, $comment)
{
    global $sql;
    global $prname;

    // block:template:field_name:[PID]:[SORT]
    //пр. block:news:news_theme:1234
    // ИЛИ
    // cat:PID
    //пр. cat:56
    // ИЛИ
    // variant1;variant2;variant3

    $s = "<select name=\"" . htmlspecialchars($name) . "[]\" multiple $attr>";

    $vals = explode(';', $data); //массив значений

    if (strpos($comment, ":") !== FALSE) //если связанные блоки
    {

        $d = explode(":", $comment);
        $type = $d[0];

        if ($type == "block") {
            $tpl = $d[1];
            $fname = $d[2];

            $pid = "";
            if (intval($d[3]))
                $pid = sprintf("AND parent = %d", intval($d[3]));

            $orderby = 'sort';
            if (strlen($d[4]))
                $orderby = $d[4];


            $q = sprintf("SELECT id, %s AS name FROM prname_b_%s WHERE visible <> 0 %s ORDER BY %s;", $fname, $tpl, $pid, $orderby);
        }

        if ($type == "cat") {
            $q = sprintf("select id, name FROM prname_categories WHERE parent = %d ORDER BY sort;", intval($d[1]));
        }


        $res = $sql->query($q);
        while ($arr = $sql->fetch_assoc($res)) {
            $s .= "<option value=\"" . $arr['id'] . "\" " . ((in_array($arr['id'], $vals)) ? ' selected ' : '') . " $attr>" . htmlspecialchars($arr['name']) . "</option>";
        }

    } else // если просто перечень значений
    {
        $val = explode(';', $comment);
        for ($i = 0; $i < count($val); $i++) {
            $s .= "<option value=\"" . htmlspecialchars($val[$i]) . "\" " . (in_array(htmlspecialchars($val[$i]), $vals) ? ' selected ' : '') . ">" . htmlspecialchars($val[$i]) . "</option>";
        };
    }


    $s .= "</select>";
    return $s;
}

function save_mselect($name)
{
    global ${"$name"};

    $s = '';

    if (!is_array(${"$name"})) {
        $s = ${"$name"};
    } else {
        foreach (${"$name"} as $el) {
            if ($s != '') {
                $s .= ";";
            };
            $s .= $el;
        };
    };

    return $s;
}

function get_mselect($data, $comment = '')
{

    global $sql;
    if (strpos($comment, 'block:') !== false) {
        $IDS = explode(";", $data);
        $d = explode(":", $comment);
        $type = $d[0];
        $tpl = $d[1];
        $fname = $d[2];
        $pid = "";
        if (intval($d[3]))
            $pid = sprintf("AND parent = %d", intval($d[3]));

        $q = sprintf("SELECT id, %s AS name FROM prname_b_%s WHERE visible <> 0 %s ORDER BY sort;", $fname, $tpl, $pid);
        $res = $sql->query($q);
        $ret = array();
        while ($row = $sql->fetch_array($res)) {
            if (in_array($row['id'], $IDS))
                $ret[] = $row['name'];
        }
        $str = implode(',', $ret);
        if (strlen($str) < 40)
            return $str;
        else
            return substr($str, 0, 37) . "...";
    } else {

        $val = splstr($comment, ";");
        if ($data == '') {
            return false;
        } else {
            $d = splstr($data, ";");
            $ret = array();
            foreach ($d as $d1) {
                array_push($ret, $val[$d1 + 1]);
            };
            return $ret;
        };
    }
}

;

function input_mcheckbox($name, $attr, $data, $comment)
{
    global $prname;
    $n = $n3 = $n4 = $frombl = false;
    $comment = strtolower($comment);
    if ((($n = strpos($comment, "allblocks:")) !== false) || (($n3 = strpos($comment, "visblocks:")) !== false) || (($n4 = strpos($comment, "hidblocks:")) !== false)) {
        if ($n3 !== false) {
            $q = ' AND bl.visible>0';
            $n = $n3;
        } elseif ($n4 !== false) {
            $q = ' AND bl.visible<1';
            $n = $n4;
        } else $q = '';
        $frombl = false;
        if (($n2 = strpos($comment, ' ', $n)) == false) {
            $n2 = strlen($comment);
        };
        $n += 10;
        $val2 = substr($comment, $n, $n2 - $n);
        $val2 = 0 + @mysql_result(mysql_query("SELECT id FROM $prname" . "_categories WHERE `key`='" . addslashes($val2) . "'"), 0, 0);
        $rec = (strpos(str_replace(" ", "", $comment), "recursion:yes") !== false);
        $cango = true;
        if ($rec) {
            $tree = tree_create($val3 = $val2, 'AND visible>0');
            $cango = (($r = tree_next($tree)) != false);
            $shift = array();
            $cats = array();
            //$blid = array();
        };
        $val = array();
        $i = 0;
        $blids = array();
        while ($cango) {
            if ($rec) {
                $val2 = $r['id'];
                $shift2 = $r['lev'];
                $catname = $r['name'];
                $cango = (($r = tree_next($tree)) != false);
            } else $cango = false;
            $qq = "SELECT dt.data, bl.id FROM $prname" . "_categories ct, $prname" . "_data dt, $prname" . "_bdatarel dr, $prname" . "_blocks bl WHERE dt.blockid=bl.id AND dt.relkey=dr.key AND bl.parent=ct.id AND ct.id=" . addslashes($val2) . " AND dr.`show`>0$q ORDER BY bl.sort";
            $res = mysql_query($qq);
            if (mysql_num_rows($res) == 0) {
                if ($rec) $frombl = true;
            } else {
                $frombl = true;
                while ($row = mysql_fetch_array($res)) {
                    if (in_array($row['id'], $blids)) continue;
                    $val[++$i] = $row['data'];
                    $blids[$i] = $row['id'];
                    if ($rec) {
                        $shift[$i] = $shift2;
                        $cats[$i] = $catname; /*$blid[$i] = $row['id'];*/
                    };
                };
            };
        };
    };

    if ((($n = strpos($comment, "allcats:")) !== false) || (($n3 = strpos($comment, "viscats:")) !== false) || (($n4 = strpos($comment, "hidcats:")) !== false)) {
        /*if ($n3 !== false) {$q = ' AND visible>0'; $n = $n3;} elseif ($n4 !== false) {$q = ' AND visible<1'; $n = $n4;} else $q = '';
        $frombl = false;
        if (($n2 = strpos($comment, ' ', $n)) == false) {$n2 = strlen($comment);};
        $n += 8;
        $val2 = substr($comment, $n, $n2 - $n);
        $val2 = 0 + @mysql_result(mysql_query("SELECT id FROM $prname"."_categories WHERE `key`='".addslashes($val2)."'"), 0, 0);
        $q = "SELECT name, id FROM $prname"."_categories WHERE parent=$val2 $q ORDER BY `sort`";
        if ($res = mysql_query($q)) {
            $val = array(); $i = 0; $blids = array();
            $frombl = true;
            while ($row = mysql_fetch_array($res)) {
                $val[++$i] = $row['name'];
                $blids[$i] = $row['id'];
            };
        };*/
        if ($n3 !== false) {
            $q = ' AND visible>0';
            $n = $n3;
        } elseif ($n4 !== false) {
            $q = ' AND visible<1';
            $n = $n4;
        } else $q = '';
        $frombl = false;
        if (($n2 = strpos($comment, ' ', $n)) == false) {
            $n2 = strlen($comment);
        };
        $n += 8;
        $val2 = substr($comment, $n, $n2 - $n);
        $val2 = 0 + @mysql_result(mysql_query("SELECT id FROM $prname" . "_categories WHERE `key`='" . addslashes($val2) . "'"), 0, 0);
        //
        $rec = (strpos(str_replace(" ", "", $comment), "recursion:yes") !== false);
        $cango = true;
        if ($rec) {
            $tree = tree_create($val3 = $val2, $q);
            $cango = (($r = tree_next($tree)) != false);
            $shift = array();
            $cats = array();
        };
        $val = array();
        $i = 0;
        $blids = array();
        while ($cango) {
            if ($rec) {
                $val[++$i] = $r['name'];
                $blids[$i] = $r['id'];
                $shift[$i] = $r['lev'];
                $cats[$i] = '';
                $cango = (($r = tree_next($tree)) != false);
                $frombl = true;
            } else {
                $cango = false;
                $qq = "SELECT name, id FROM $prname" . "_categories WHERE parent=$val2 $q ORDER BY `sort`";
                $res = mysql_query($qq);
                if (@mysql_num_rows($res) == 0) {
                    if ($rec) $frombl = true;
                } else {
                    $frombl = true;
                    while ($row = mysql_fetch_array($res)) {
                        $val[++$i] = $row['name'];
                        $blids[$i] = $row['id'];
                    };
                };
            };
        };
    };
    if (!$frombl) $val = splstr($comment, ";");
    $s = '';
    $d = splstr($data, ";");
    $cname = '';
    for ($i = 0; $i < count($val); $i++) {
        if ($rec) {
            $ss = '';
            for ($j = 0; $j < $shift[$i + 1]; $j++) $ss .= '&nbsp;&nbsp;&nbsp;';
            if ($cats[$i + 1] != $cname) {
                $cname = $cats[$i + 1];
                $s .= $ss . $cname . "<br>";
            };
            $s .= $ss;
        };
        $v = $frombl ? $blids[$i + 1] : $i;

        $q = "SELECT template FROM $prname" . "_categories WHERE id = '$v'";
        $str1 = mysql_fetch_row(mysql_query($q));
        if (($str1[0] != 'cat_set') && ($str1[0] != 'cat_gorod')) {
            $s .= "<input type=\"checkbox\" name=\"" . htmlspecialchars($name) . "[]\" value=\"" . $v . "\" " . (in_array($v, $d) ? ' checked ' : '') . "$attr>" . htmlspecialchars($val[$i + 1]) . "<br>";
        } else {
            $s .= "<b>" . htmlspecialchars($val[$i + 1]) . "</b><br>";
        }
    };
    return $s;
}

function save_mcheckbox($name)
{
    global ${"$name"};
    $s = '';
//	echo ${"$name"}[0]; exit;
    if (count(${"$name"}) < 2) {
        $s = ${"$name"}[0];
    } else {
        foreach (${"$name"} as $el) {
            if ($s != '') {
                $s .= ";";
            };
            $s .= $el;
        };
    };
    return $s;
}

function get_mcheckbox($data, $comment = '')
{
    $val = splstr($comment, ";");
    if ($data == '') {
        return false;
    } else {
        $d = splstr($data, ";");
        $ret = array();
        foreach ($d as $d1) {
            array_push($ret, $val[$d1 + 1]);
        };
        return $ret;
    };
}

;

function input_radio($name, $attr, $data, $comment)
{
    global $prname;
    $n = $n3 = $n4 = $frombl = false;
    $comment = strtolower($comment);
    if ((($n = strpos($comment, "allblocks:")) !== false) || (($n3 = strpos($comment, "visblocks:")) !== false) || (($n4 = strpos($comment, "hidblocks:")) !== false)) {
        if ($n3 !== false) {
            $q = ' AND bl.visible>0';
            $n = $n3;
        } elseif ($n4 !== false) {
            $q = ' AND bl.visible<1';
            $n = $n4;
        } else $q = '';
        $frombl = false;
        if (($n2 = strpos($comment, ' ', $n)) == false) {
            $n2 = strlen($comment);
        };
        $n += 10;
        $val2 = substr($comment, $n, $n2 - $n);
        $val2 = 0 + @mysql_result(mysql_query("SELECT id FROM $prname" . "_categories WHERE `key`='" . addslashes($val2) . "'"), 0, 0);
        $rec = (strpos(str_replace(" ", "", $comment), "recursion:yes") !== false);
        $cango = true;
        if ($rec) {
            $tree = tree_create($val3 = $val2, 'AND visible>0');
            $cango = (($r = tree_next($tree)) != false);
            $shift = array();
            $cats = array();
        };
        $val = array();
        $i = 0;
        $blids = array();
        while ($cango) {
            if ($rec) {
                $val2 = $r['id'];
                $shift2 = $r['lev'];
                $catname = $r['name'];
                $cango = (($r = tree_next($tree)) != false);
            } else $cango = false;
            $qq = "SELECT dt.data, bl.id FROM $prname" . "_categories ct, $prname" . "_data dt, $prname" . "_bdatarel dr, $prname" . "_blocks bl WHERE dt.blockid=bl.id AND dt.relkey=dr.key AND bl.parent=ct.id AND ct.id=" . addslashes($val2) . " AND dr.`show`>0$q ORDER BY dr.key";
            $res = mysql_query($qq);
            if (@mysql_num_rows($res) == 0) {
                if ($rec) $frombl = true;
            } else {
                $frombl = true;
                while ($row = mysql_fetch_array($res)) {
                    if (in_array($row['id'], $blids)) continue;
                    $val[++$i] = $row['data'];
                    $blids[$i] = $row['id'];
                    if ($rec) {
                        $shift[$i] = $shift2;
                        $cats[$i] = $catname;
                    };
                };
            };
        };
    };

    if ((($n = strpos($comment, "allcats:")) !== false) || (($n3 = strpos($comment, "viscats:")) !== false) || (($n4 = strpos($comment, "hidcats:")) !== false)) {
        if ($n3 !== false) {
            $q = ' AND visible>0';
            $n = $n3;
        } elseif ($n4 !== false) {
            $q = ' AND visible<1';
            $n = $n4;
        } else $q = '';
        $frombl = false;
        if (($n2 = strpos($comment, ' ', $n)) == false) {
            $n2 = strlen($comment);
        };
        $n += 8;
        $val2 = substr($comment, $n, $n2 - $n);
        $val2 = 0 + @mysql_result(mysql_query("SELECT id FROM $prname" . "_categories WHERE `key`='" . addslashes($val2) . "'"), 0, 0);
        //
        $rec = (strpos(str_replace(" ", "", $comment), "recursion:yes") !== false);
        $cango = true;
        if ($rec) {
            $tree = tree_create($val3 = $val2, $q);
            $cango = (($r = tree_next($tree)) != false);
            $shift = array();
            $cats = array();
        };
        $val = array();
        $i = 0;
        $blids = array();
        while ($cango) {
            if ($rec) {
                $val[++$i] = $r['name'];
                $blids[$i] = $r['id'];
                $shift[$i] = $r['lev'];
                $cats[$i] = '';
                $cango = (($r = tree_next($tree)) != false);
                $frombl = true;
            } else {
                $cango = false;
                $qq = "SELECT name, id FROM $prname" . "_categories WHERE parent=$val2 $q ORDER BY `sort`";
                $res = mysql_query($qq);
                if (@mysql_num_rows($res) == 0) {
                    if ($rec) $frombl = true;
                } else {
                    $frombl = true;
                    while ($row = mysql_fetch_array($res)) {
                        $val[++$i] = $row['name'];
                        $blids[$i] = $row['id'];
                    };
                };
            };
        };
    };
    if (!$frombl) $val = splstr($comment, ";");
    $s = '';

    $d = splstr($data, ";");
    $cname = '';
    for ($i = 0; $i < count($val); $i++) {
        if ($rec) {
            $ss = '';
            for ($j = 0; $j < $shift[$i + 1]; $j++) $ss .= '&nbsp;&nbsp;&nbsp;';
            if ($cats[$i + 1] != $cname) {
                $cname = $cats[$i + 1];
                $s .= $ss . $cname . "<br>";
            };
            $s .= $ss;
        };
        $v = $frombl ? $blids[$i + 1] : $i;
        //$v = $rec ? $blids[$i + 1] : $i;
        $s .= "<input type=\"radio\" name=\"" . htmlspecialchars($name) . "\" value=\"" . $v . "\" " . (($data == $v) ? ' checked ' : '') . "$attr>" . htmlspecialchars($val[$i + 1]) . "<br>";
    };
    return $s;
}

function save_radio($name)
{
    global ${"$name"};
    return ${"$name"};
}

function get_radio($data, $comment = '')
{
    $val = splstr($comment, ";");
    if ($data == '') {
        return false;
    } else {
        return $val[$data + 1];
    };
}

;


function input_items($name, $attr, $data, $comment)
{
    global $prname;
    global $cparent;
    global $parent;
    global $filt;

    $tbl = reset(explode('&', $comment));
	$q = "SELECT count(id) FROM $prname"."_b_".$tbl." WHERE blockparent = '$parent'";

    $str1 = mysql_fetch_row(mysql_query($q));
    $c_blocks = $str1[0];
    $attrs = array(
        'name' => 'itemframe',
        'frameborder' => 'no',
        'id' => 'itemframe' . $tbl,
        'width' => '100%',
        'height' => '1px',
        'src' => 'item_edit.php?parent=' . $cparent.'&blockparent=' . $parent.'&blockparenttpl=' . $_GET['btmplate'].'&template=' . $comment,

    );
    $s = '<iframe ';
    foreach($attrs as $key=>$val){
        $s .= sprintf('%s="%s"', $key, addslashes($val));
    }
    $s .= '></iframe>';
	return $s;
}

function save_items($name)
{
    global ${"$name"};
    return ${"$name"};
}

function get_items($data, $comment = '')
{
    $val = splstr($comment, ";");
    if ($data == '') {
        return false;
    } else {
        return $val[$data + 1];
    };
}

;


function input_int($name, $attr, $data, $comment = '')
{
    return "<input name=\"" . htmlspecialchars($name) . "\" id=\"" . htmlspecialchars($name) . "text\" type=\"text\" $attr value=\"" . htmlspecialchars($data) . "\">";
}

function save_int($name)
{
    global ${"$name"};
    ${"$name"} = str_replace(' ', '', ${"$name"});
    ${"$name"} = 0 + ${"$name"};
    return stripslashes(${"$name"});
}

;

function get_int($data, $comment = '')
{
    return $data;
}

;


function input_double($name, $attr, $data, $comment = '')
{
    return "<input name=\"" . htmlspecialchars($name) . "\" id=\"" . htmlspecialchars($name) . "text\" type=\"text\" $attr value=\"" . htmlspecialchars($data) . "\">";
}

function save_double($name)
{
    global ${"$name"};
    ${"$name"} = str_replace(' ', '', ${"$name"});
    ${"$name"} = str_replace(',', '.', ${"$name"});
    ${"$name"} = 0 + ${"$name"};

    return stripslashes(${"$name"});
}

;

function get_double($data, $comment = '')
{
    return $data;
}

;


function input_meta($name, $attr, $data, $comment = '')
{

    $arr_data = explode('|---|', $data);
    $data_1 = $arr_data[0];
    $data_2 = $arr_data[1];
    $data_3 = $arr_data[2];

    return "
		<input type=\"hidden\" name=\"" . htmlspecialchars($name) . "\" id=\"" . htmlspecialchars($name) . "text\"  $attr value=\"" . htmlspecialchars($data) . "\">
		<table width=\"70%\" border=\"0\">
		<tr><td style=\"width:50px\">Title:</td><td style=\"text-align:left\"><input name=\"" . htmlspecialchars($name) . "meta_1\" id=\"" . htmlspecialchars($name) . "meta_1\" type=\"text\" $attr value=\"" . htmlspecialchars($data_1) . "\" ></td></tr>
		<tr><td style=\"width:50px\">Keywords:</td><td style=\"text-align:left\"><input name=\"" . htmlspecialchars($name) . "meta_2\" id=\"" . htmlspecialchars($name) . "meta_2\" type=\"text\" $attr value=\"" . htmlspecialchars($data_2) . "\"></td></tr>
		<tr><td style=\"width:50px\">Description:</td><td style=\"text-align:left\"><input name=\"" . htmlspecialchars($name) . "meta_3\" id=\"" . htmlspecialchars($name) . "meta_3\" type=\"text\" $attr value=\"" . htmlspecialchars($data_3) . "\"></td></tr>
		</table>

	";
}

function save_meta($name)
{
    global ${"$name"};

    global ${"$name" . 'meta_1'};
    global ${"$name" . 'meta_2'};
    global ${"$name" . 'meta_3'};


    ${"$name"} = trim(${"$name" . 'meta_1'}) . '|---|' . trim(${"$name" . 'meta_2'}) . '|---|' . trim(${"$name" . 'meta_3'});


    return stripslashes(${"$name"});
}

;

function get_meta($data, $comment = '')
{
    return $data;
}

;


function input_alt_url($name, $attr, $data, $comment = '') {
    if (!$comment) $comment = 'title';
    $s = "<input name=\"".htmlspecialchars($name)."\" id=\"".htmlspecialchars($name)."text\" type=\"text\" $attr value=\"".htmlspecialchars($data)."\">";
    $resultFieldId = htmlspecialchars($name)."text";
    $s .= "<div class=\"generate-alturl-button\" id=\"".htmlspecialchars($name)."text_button\" style=\"display:none;\" title=\"Сгенерировать\"></div>";
    $s .= "
<script>
var arInputs = document.querySelectorAll('input[value=\"".$comment."\"]');
var urlButton = document.getElementById('".htmlspecialchars($name)."text_button');
var findedInput = false;
for (var i = 0; i < arInputs.length; i++) {
    if (arInputs[i].hasAttribute('name') && arInputs[i].getAttribute('name') == 'dat[]') {
        if (findedInput = arInputs[i].parentNode.querySelector('input:not([type=\"hidden\"])'))
            break;
    }
}
if (findedInput && findedInput.hasAttribute('id')) {
    var textFieldId = findedInput.getAttribute('id');
    ".(
    (!$_GET['blockid']) ?
    ("
    var tfi = document.getElementById(textFieldId);
    tfi.setAttribute(\"onkeyup\", \"translit(textFieldId, '".$resultFieldId."')\");
    tfi.setAttribute(\"onblur\", \"translit(textFieldId, '".$resultFieldId."')\");
    ") :
    ""
    )."
    urlButton.setAttribute(\"onclick\", \"translit(textFieldId, '".$resultFieldId."')\");
    urlButton.style.display = \"inline-block\";
}
</script>";

	return $s;
}

function save_alt_url($name) {
	global ${"$name"};
	return stripslashes(${"$name"});
};

function get_alt_url($data, $comment = '') {
	return $data;
};



?>