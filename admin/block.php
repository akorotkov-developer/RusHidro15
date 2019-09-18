<?php
require_once "templates/includes/new_top.php";
/*
Префиксы.
news:news_date asc -  Сортировка.
*/
// ===================================================================================================================================
// Область определения переменных.
$actions = $_POST['actions'] ? $_POST['actions'] : $_GET[actions];
//$_GET[actions]; // Действие.
$btmplate = $_POST['btmplate'] ? $_POST['btmplate'] : $_GET[btmplate];
//$_GET[btmplate]; // Шаблон блока.
$cparent = $_POST['cparent'] ? $_POST['cparent'] : $_GET[cparent]; // id каталога.
$parent = $_GET['blockid']; // id блока.


//
$catdata = $sql->fetch_assoc($sql->query("select p2.* from prname_categories p1,prname_ctemplates  p2 where p2.key=p1.template and p1.id='$cparent'"));
$btpl = explode('  ', trim($catdata[blocktypes]));
if ($btmplate == '') $btmplate = $btpl[0];
$error = "";
$__DATA = array();
//echo $btmplate;


//------------------------------------
// все блоки редактирование оптом.


///  блоки во всех папках
function tree_all_select($id = '1', $level = '')
{
    global $control;
    global $config;
    global $sql;
    $q = $sql->query("SELECT p1.*,p2.level as parent_level FROM prname_tree p1,prname_tree p2 where p1.left_key > p2.left_key and p1.right_key < p2.right_key and p2.id='$id' " . ($level !== '' ? " and p1.level<=$level" : "") . " ORDER BY p1.left_key");
    $i = 0;
    while ($b = mysql_fetch_assoc($q)) {
        if (
            $b['level'] == $b[parent_level] + 1
        ) {
            if ($control->cid == $b['id']) $a->item[$b['id']]->link = 'nolink'; else $a->item[$b['id']]->link = 'link';
            $a->item[$b['id']]->name = $b['name'];
            $a->item[$b['id']]->id = $b['id'];
            $a->item[$b['id']]->parent = $b['parent'];
            $a->item[$b['id']]->level = $b['level'];
            $a->item[$b['id']]->url = $config['server_url'] . $b['url'];
            $a->item[$b['id']]->template = $b['template'];
            $a->item[$b['id']]->key = $b['key'];
            $a->item[$b['id']]->i = $i + 1;
            $a->item[$b['id']]->visible = $b['visible'];
            $a->item[$b['id']]->class = '';//$i==0?"Первый раздел всего меню сайта":"Первый раздел ветки";
            $level = $b['level'];
            $last_id = $b[id];
            $c[$b['level']] =& $a->item[$b['id']];
            $allid[$b['id']] = $b['id'];
        } else {
            if ($control->cid == $b[id]) {
                for ($l = 0; $l < $b['level']; $l++) if ($allid[$control->parents[$b['level'] - $l]]) $c[$b['level'] - $l]->link = 'stronglink';
                $c[$b['level'] - 1]->item[$b['id']]->link = 'nolink';
            } else $c[$b['level'] - 1]->item[$b['id']]->link = 'link';
            $c[$b['level'] - 1]->item[$b['id']]->name = $b['name'];
            $c[$b['level'] - 1]->item[$b['id']]->id = $b[id];
            $c[$b['level'] - 1]->item[$b['id']]->parent = $b['parent'];
            $c[$b['level'] - 1]->item[$b['id']]->level = $b['level'];
            $c[$b['level'] - 1]->item[$b['id']]->url = $config['server_url'] . $b['url'];
            $c[$b['level'] - 1]->item[$b['id']]->template = $b['template'];
            $c[$b['level'] - 1]->item[$b['id']]->key = $b['key'];
            $c[$b['level'] - 1]->item[$b['id']]->i = $i + 1;
            $c[$b['level'] - 1]->item[$b['id']]->visible = $b['visible'];
            $c[$b['level'] - 1]->item[$b['id']]->class = "";//"Последний в своей ветке";
            if ($level > $b['level']) $c[$b['level']]->class = "class='open'";
            if ($level == $b['level']) $c[$level]->class = '';//Раздел не имеет вложений он не первый но и не последний
            if ($level < $b['level']) $c[$level]->class = "class='open'";//'"Этот раздел имеет вложение '.$c[$level]->class;
            $level = $b['level'];
            $last_id = $b[id];
            $allid[$b['id']] = $b['id'];
            $c[$b['level']] =& $c[$b['level'] - 1]->item[$b['id']];
        }
        $i++;
    }
    return $a;
}


function rekurs_tree(&$obj)
{
    global $sql;
    global $cat_filtr;

    if (isset($obj->item)) {
        foreach ($obj->item as &$o) {
            $summ += rekurs_tree($o);
        }
    }

    $flag = false;
    $q = "SELECT blocktypes FROM prname_ctemplates WHERE `key`='" . $obj->template . "' ";
    if (strstr($sql->one_record($q), ' ' . $cat_filtr->btmplate)) {
        $flag = true;
    }

    $selected = false;
    if (strstr($cat_filtr->cat_id, ';')) {
        $tmp_arr = explode(';', $cat_filtr->cat_id);
        foreach ($tmp_arr as $one_arr) {
            if ($one_arr == $obj->id) $selected = true;
        }
    }


    $obj->flag = $flag;
    $obj->selected = $selected;

    return $obj->item_cnt;

}


if (is_array($config['admin_all_blocks'])) {

    $cat_filtr->is = false;
    foreach ($config['admin_all_blocks'] as $one_arr) {
        if ($one_arr['template'] == $btmplate) {
            $cat_filtr->is = true;
            $cat_filtr->parent = $one_arr['parent'];
            $cat_filtr->btmplate = $one_arr['template'];
            $cat_filtr->select_size = $one_arr['select_size'];
            $cat_filtr->font_size = $one_arr['font_size'];

            //$tree = new Tree();
            $main_tree = tree_all_select($cat_filtr->parent, 1000);
            $q = "SELECT level FROM prname_tree WHERE id = '" . $cat_filtr->parent . "' ";
            $cat_filtr->level = $sql->one_record($q);
            $cat_filtr->items = ($main_tree);
            $cat_filtr->cat_id = $_POST['f_cat_filtr_catid'] ? $_POST['f_cat_filtr_catid'] : $_GET['f_cat_filtr_catid'];
            rekurs_tree($cat_filtr->items);

            $cat_filtr->unvis_option_block = false;
            if (strstr($cat_filtr->cat_id, ';')) {
                if ($cparent . ';' != $cat_filtr->cat_id) {
                    $cat_filtr->unvis_option_block = true;
                }
            }
        }
    }
}


//---------------------------------////


//--------------------------------------------------
//	ЗАГРУЗКА CSV
//проверка наличия возможности загрузки из csv
if (is_array($config['admin_csv_import'])) {
    $page->is_csv = false;
    foreach ($config['admin_csv_import'] as $one_arr) {
        if ($one_arr['template'] == $btmplate) {
            $csv->is = true;
            $csv->regime = $one_arr['default_regime'];
            $csv->merge_compare_field = $one_arr['merge_compare_field'];
        }
    }
}
//загрузка файла из csv
if ($_POST['import_csv_true']) {
    $csv->upload = 1;
    include_once("../libs/csv.php");

    $csv_error = 0;

    if ($_FILES['csv_file']['error'] == 0) {

        //типы полей берем
        $q = "SELECT pr2.key, pr2.datatkey, pr1.id FROM prname_btemplates pr1, prname_bdatarel pr2 WHERE pr1.key = '$btmplate' AND pr2.templid = pr1.id ";
        $res = $sql->query($q);
        $csv_btemplate_field = array();
        while ($str = $sql->fetch_array($res)) {
            $csv_btemplate_field[$str['key']] = $str['datatkey'];
        }

        // чтение файла
        $f = fopen($_FILES['csv_file']['tmp_name'], "r");
        $csv_block = array();
        for ($i = 0; $data = fgetcsv_func($f, 1000, ";"); $i++) {
            //определяем поля из выгрузки
            if ($i == 0) {
                $csv_keys = array();
                $num = count($data);
                for ($c = 0; $c < $num; $c++) {
                    $csv_keys[$c] = $data[$c];
                    $csv_keys[$c] = str_replace('#', '', $csv_keys[$c]);
                    $csv_keys[$c] = trim($csv_keys[$c]);
                }
            }

            if ($i > 0) {
                $num = count($data);
                for ($c = 0; $c < $num; $c++) {
                    switch ($csv_btemplate_field[$csv_keys[$c]]) {
                        case 'double': {
                            $data[$c] = str_replace(' ', '', $data[$c]);
                            $data[$c] = str_replace(',', '.', $data[$c]);
                            break;
                        }
                        case 'int': {
                            $data[$c] = str_replace(' ', '', $data[$c]);
                            $data[$c] = str_replace(',', '.', $data[$c]);
                            break;
                        }
                    }
                    $csv_block[$i][$csv_keys[$c]] = $data[$c];
                }
            }
        }


        if ($_POST['csv_regime'] == 'delete') {
            $q = "SELECT id FROM prname_b_" . $btmplate . " WHERE parent = '$cparent' ";
            $res = $sql->query($q);
            while ($str = $sql->fetch_array($res)) {
                delete_block($str['id'], $btmplate);
            }
        }

        if ($_POST['csv_regime'] == 'merge') {
            $q = "UPDATE prname_b_" . $btmplate . " SET visible = 0 WHERE parent = '$cparent' ";
            $sql->query($q);
        }

        //для режим сравнения-обновления
        $csv->merge_compare_field_arr = explode(',', $csv->merge_compare_field);
        if (is_array($csv->merge_compare_field_arr) > 0) {
            foreach ($csv->merge_compare_field_arr as $key => $value) {
                $csv->merge_compare_field_arr[$key] = trim($csv->merge_compare_field_arr[$key]);
            }
        }


        foreach ($csv_block as $one_arr) {
            if ($_POST['csv_regime'] == 'merge') {
                $q = "SELECT id FROM prname_b_" . $btmplate . " WHERE parent = '$cparent' ";
                if (is_array($csv->merge_compare_field_arr) > 0) {
                    foreach ($csv->merge_compare_field_arr as $key => $value) {
                        $q .= " AND `" . $csv->merge_compare_field_arr[$key] . "` = '" . $sql->escape_string($one_arr[$csv->merge_compare_field_arr[$key]]) . "' ";
                    }
                }
                $tmp_id = $sql->one_record($q);
                if ($tmp_id > 0) {
                    foreach ($one_arr as $key => $value) {
                        //echo $key." ".$value."<BR>";
                        all::update_block($tmp_id, $btmplate, $value, $key);
                    }
                    $q = "UPDATE prname_b_$btmplate SET visible = 1  WHERE parent = '$cparent' AND id = '$tmp_id' ";
                    $sql->query($q);
                } else {
                    all:: insert_block($btmplate, $cparent, $one_arr, 1);
                }


            }
            if ($_POST['csv_regime'] == 'delete') {
                all:: insert_block($btmplate, $cparent, $one_arr, 1);
            }

            if ($_POST['csv_regime'] == 'add') {
                all:: insert_block($btmplate, $cparent, $one_arr, 1);
            }

        }

    } else {
        $csv->errors[$csv_error]->error = 'Не выбран файл';
        $csv_error++;
    }


}


///--------------------------------------


//--------------------------------------
//объект для фильтров
$filter = new stdClass();
$filter->preurl = 'block.php?cparent=' . $cparent;
if ($btmplate) $filter->preurl .= '&btmplate=' . $btmplate;
if ($parent) $filter->preurl .= '&parent=' . $parent;

$filter->add_q = '';

$filter->urlfilter = '';

//кол-во блоков на странице
$filter->limit_arr[0] = new stdClass();
$filter->limit_arr[1] = new stdClass();
$filter->limit_arr[2] = new stdClass();
$filter->limit_arr[3] = new stdClass();

$filter->limit_arr[0]->limit = '20';
$filter->limit_arr[1]->limit = '50';
$filter->limit_arr[2]->limit = '100';
$filter->limit_arr[3]->limit = '250';

//лимит и постраничка
$filter->limit = $_POST['f_limit'] ? $_POST['f_limit'] : $_GET['f_limit'];
if ((0 + $filter->limit) == 0) $filter->limit = 20;

$filter->page = $_POST['f_page'] ? $_POST['f_page'] : $_GET['f_page'];
if ((0 + $filter->page) == 0) $filter->page = 0;
//------
$filter->urlfilter .= '&f_limit=' . $filter->limit . '&f_page=' . $filter->page;


//форма фильтра
//фильтр папок
$filter->cat_filtr_catid = $_POST['f_cat_filtr_catid'] ? $_POST['f_cat_filtr_catid'] : $_GET['f_cat_filtr_catid'];
$filter->urlfilter .= '&f_cat_filtr_catid=' . $filter->cat_filtr_catid;

//print_r($_POST);
$filter->vis = $_POST['f_vis'] ? $_POST['f_vis'] : $_GET['f_vis'];
//echo $filter->vis;
//die();
if ($filter->vis == '') $filter->vis = -1;
if (((0 + $filter->vis) == -10)) $filter->vis = 0;

$filter->urlfilter .= '&f_vis=' . $filter->vis;
if ($filter->vis != '-1') {
    $filter->add_q .= " AND visible = '" . $filter->vis . "' ";
}

//echo $filter->vis;


$filter->s_openparam = $_POST['f_openparam'] ? $_POST['f_openparam'] : $_GET['f_openparam'];
if ((0 + $filter->s_openparam) == 0) $filter->s_openparam = 0;
$filter->urlfilter .= '&f_openparam=' . $filter->s_openparam;


$q = "SELECT id FROM prname_btemplates WHERE `key` = '" . $btmplate . "' ";
$templid = $sql->one_record($q);
$q = "SELECT * FROM prname_bdatarel WHERE templid = '$templid' AND `show` = 1 ORDER BY sort";
$res = $sql->query($q);
$i = 0;
while ($str = $sql->fetch_array($res)) {
    $filter->s_field[$i] = new stdClass();
    $filter->s_field[$i]->title = $str['name'];
    $filter->s_field[$i]->key = $str['key'];
    $filter->s_field[$i]->attr = $str['attr'];
    $filter->s_field[$i]->datakey = $str['datatkey'];
    $filter->s_field[$i]->comment = $str['comment'];


    if ($filter->s_field[$i]->datakey == 'int') {
        $filter->s_field[$i]->value_1 = $_POST['f_sea_' . $str['key'] . '_1'] ? $_POST['f_sea_' . $str['key'] . '_1'] : $_GET['f_sea_' . $str['key'] . '_1'];
        $filter->s_field[$i]->value_2 = $_POST['f_sea_' . $str['key'] . '_2'] ? $_POST['f_sea_' . $str['key'] . '_2'] : $_GET['f_sea_' . $str['key'] . '_2'];

        $q = "SELECT min(`" . $filter->s_field[$i]->key . "`) FROM prname_b_" . $btmplate . " WHERE " . ($cat_filtr->is ? " parent > 0 " : " parent = '$cparent' ") . " 	 ";
        $filter->s_field[$i]->value_min = $sql->one_record($q);
        $q = "SELECT max(`" . $filter->s_field[$i]->key . "`) FROM prname_b_" . $btmplate . " WHERE  " . ($cat_filtr->is ? " parent > 0 " : " parent = '$cparent' ") . " 	 ";
        $filter->s_field[$i]->value_max = $sql->one_record($q);

        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '_1' . '=' . $filter->s_field[$i]->value_1;
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '_2' . '=' . $filter->s_field[$i]->value_2;

        if ($filter->s_field[$i]->value_1 != '') {
            $filter->add_q .= " AND `" . $str['key'] . "` >= '" . $sql->escape_string($filter->s_field[$i]->value_1) . "' ";
        }
        if ($filter->s_field[$i]->value_2 != '') {
            $filter->add_q .= " AND `" . $str['key'] . "` <= '" . $sql->escape_string($filter->s_field[$i]->value_2) . "' ";
        }
    }

    if ($filter->s_field[$i]->datakey == 'double') {
        $filter->s_field[$i]->value_1 = $_POST['f_sea_' . $str['key'] . '_1'] ? $_POST['f_sea_' . $str['key'] . '_1'] : $_GET['f_sea_' . $str['key'] . '_1'];
        $filter->s_field[$i]->value_2 = $_POST['f_sea_' . $str['key'] . '_2'] ? $_POST['f_sea_' . $str['key'] . '_2'] : $_GET['f_sea_' . $str['key'] . '_2'];

        $q = "SELECT min(`" . $filter->s_field[$i]->key . "`) FROM prname_b_" . $btmplate . " WHERE  " . ($cat_filtr->is ? " parent > 0 " : " parent = '$cparent' ") . " 	 ";
        $filter->s_field[$i]->value_min = $sql->one_record($q);
        $q = "SELECT max(`" . $filter->s_field[$i]->key . "`) FROM prname_b_" . $btmplate . " WHERE  " . ($cat_filtr->is ? " parent > 0 " : " parent = '$cparent' ") . " 	 ";
        $filter->s_field[$i]->value_max = $sql->one_record($q);

        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '_1' . '=' . $filter->s_field[$i]->value_1;
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '_2' . '=' . $filter->s_field[$i]->value_2;

        if ($filter->s_field[$i]->value_1 != '') {
            $filter->add_q .= " AND `" . $str['key'] . "` >= '" . $sql->escape_string($filter->s_field[$i]->value_1) . "' ";
        }
        if ($filter->s_field[$i]->value_2 != '') {
            $filter->add_q .= " AND `" . $str['key'] . "` <= '" . $sql->escape_string($filter->s_field[$i]->value_2) . "' ";
        }
    }

    if ($filter->s_field[$i]->datakey == 'date') {
        $filter->s_field[$i]->value_1 = $_POST['f_sea_' . $str['key'] . '_1'] ? $_POST['f_sea_' . $str['key'] . '_1'] : $_GET['f_sea_' . $str['key'] . '_1'];
        $filter->s_field[$i]->value_2 = $_POST['f_sea_' . $str['key'] . '_2'] ? $_POST['f_sea_' . $str['key'] . '_2'] : $_GET['f_sea_' . $str['key'] . '_2'];
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '_1' . '=' . $filter->s_field[$i]->value_1;
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '_2' . '=' . $filter->s_field[$i]->value_2;

        if ($filter->s_field[$i]->value_1 != '') {
            $filter->add_q .= " AND `" . $str['key'] . "` >= '" . $sql->escape_string(all::get_date($filter->s_field[$i]->value_1, 'stringtomysql')) . "' ";
        }
        if ($filter->s_field[$i]->value_2 != '') {
            $filter->add_q .= " AND `" . $str['key'] . "` <= '" . $sql->escape_string(all::get_date($filter->s_field[$i]->value_2, 'stringtomysql')) . "' ";
        }


        // года
        $q = "SELECT DISTINCT (LEFT(`" . $filter->s_field[$i]->key . "`, 4) ) as year  FROM prname_b_" . $btmplate . " WHERE 	 " . ($cat_filtr->is ? " parent > 0 " : " parent = '$cparent' ") . " 	  ORDER BY `" . $filter->s_field[$i]->key . "` DESC";

        $res2 = $sql->query($q);
        $j = -1;
        while ($str2 = $sql->fetch_array($res2)) {
            $j++;
            $filter->s_field[$i]->year[$j] = new stdClass();
            $filter->s_field[$i]->year[$j]->year = $str2['year'];

            //месяца
            $q = "SELECT DISTINCT (LEFT(`" . $filter->s_field[$i]->key . "`, 7) ) as month  FROM prname_b_" . $btmplate . " WHERE 	" . ($cat_filtr->is ? " parent > 0 " : " parent = '$cparent' ") . " AND LEFT(`" . $filter->s_field[$i]->key . "`, 4)='" . $str2['year'] . "' ORDER BY `" . $filter->s_field[$i]->key . "` DESC";

            $res3 = $sql->query($q);
            $jj = -1;
            while ($str3 = $sql->fetch_array($res3)) {
                $jj++;
                $filter->s_field[$i]->year[$j]->month[$jj] = new stdClass();
                $filter->s_field[$i]->year[$j]->month[$jj]->month = $str3['month'];

                $filter->s_field[$i]->year[$j]->month[$jj]->dayleft = all:: get_date($str3['month'] . '-01', 'mysqltostring');

                $filter->s_field[$i]->year[$j]->month[$jj]->dayright = all:: get_date(date("Y-m-d", mktime(0, 0, 0, date("m", strtotime($filter->s_field[$i]->year[$j]->month[$jj]->dayleft)) + 1, date("d", strtotime($filter->s_field[$i]->year[$j]->month[$jj]->dayleft)) - 1, date("Y", strtotime($filter->s_field[$i]->year[$j]->month[$jj]->dayleft)))), 'mysqltostring');;

                $filter->s_field[$i]->year[$j]->month[$jj]->title = $admin_ar_m[0 + substr($str3['month'], 5, 2)];// . '`' . substr($str2['month'], 2, 2);
            }

        }

        //print_r($filter);


    }

    if ($filter->s_field[$i]->datakey == 'text') {
        $filter->s_field[$i]->value = $_POST['f_sea_' . $str['key']] ? $_POST['f_sea_' . $str['key']] : $_GET['f_sea_' . $str['key']];
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '=' . urlencode($filter->s_field[$i]->value);
        if ($filter->s_field[$i]->value != '') {
            $filter->add_q .= " AND binary upper(`" . $str['key'] . "`) LIKE binary upper('%" . $sql->escape_string($filter->s_field[$i]->value) . "%') ";
        }
    }

    if ($filter->s_field[$i]->datakey == 'textarea') {
        $filter->s_field[$i]->value = $_POST['f_sea_' . $str['key']] ? $_POST['f_sea_' . $str['key']] : $_GET['f_sea_' . $str['key']];
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '=' . urlencode($filter->s_field[$i]->value);
        if ($filter->s_field[$i]->value != '') {
            $filter->add_q .= " AND binary upper(`" . $str['key'] . "`) LIKE binary upper('%" . $sql->escape_string($filter->s_field[$i]->value) . "%') ";
        }
    }

    if ($filter->s_field[$i]->datakey == 'html') {
        $filter->s_field[$i]->value = $_POST['f_sea_' . $str['key']] ? $_POST['f_sea_' . $str['key']] : $_GET['f_sea_' . $str['key']];
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '=' . urlencode($filter->s_field[$i]->value);
        if ($filter->s_field[$i]->value != '') {
            $filter->add_q .= " AND binary upper(`" . $str['key'] . "`) LIKE binary upper('%" . $sql->escape_string($filter->s_field[$i]->value) . "%') ";
        }
    }

    if ($filter->s_field[$i]->datakey == 'select') {
        $filter->s_field[$i]->value = all:: get_var('f_sea_' . $str['key']);

        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '=' . urlencode($filter->s_field[$i]->value);
        if ($filter->s_field[$i]->value > 0) {
            $filter->add_q .= " AND `" . $str['key'] . "` = '" . $sql->escape_string($filter->s_field[$i]->value) . "' ";
        }
        //echo $filter->add_q;
        $filter->s_field[$i]->html_form = input_select('f_sea_' . $str['key'], $filter->s_field[$i]->attr, $filter->s_field[$i]->value, $filter->s_field[$i]->comment);
    }

    if ($filter->s_field[$i]->datakey == 'checkbox') {
        $filter->s_field[$i]->value = all:: get_var('f_sea_' . $str['key']);
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '=' . urlencode($filter->s_field[$i]->value);
        if ($filter->s_field[$i]->value > 0) {
            $filter->add_q .= " AND `" . $str['key'] . "` = '" . $sql->escape_string($filter->s_field[$i]->value) . "' ";
        }
        //echo $filter->add_q;
        $filter->s_field[$i]->html_form = input_checkbox('f_sea_' . $str['key'], $filter->s_field[$i]->attr, $filter->s_field[$i]->value, $filter->s_field[$i]->comment);
    }


    if ($filter->s_field[$i]->datakey == 'idnumber') {
        $filter->s_field[$i]->value = all:: get_var('f_sea_' . $str['key']);
        $filter->urlfilter .= '&' . 'f_sea_' . $str['key'] . '=' . urlencode($filter->s_field[$i]->value);
        if ($filter->s_field[$i]->value > 0) {
            $filter->add_q .= " AND `id` = '" . $sql->escape_string($filter->s_field[$i]->value) . "' ";
        }
    }


//	$filter->s_field[$i]->value = $_POST['f_limit']?$_POST['f_limit']:$_GET['f_limit'];

    $filter->s_field[$i]->value = stripslashes($filter->s_field[$i]->value);

    //$filter->s_field[$i]->value = strip_tags();

    $i++;
}


//echo $filter->add_q;


//--------------------------------

class blockedit
{
    var $parent; // Родитель блок или каталог.
    var $template; // Шаблон блока.
    var $btmplate; // Шаблон блока.
    var $cparent; // Шаблон блока.
    var $limit = 20; // Шаблон блока.


    function blockedit($cparent, $btmplate = '')
    {
        global $sql;

        //регистрируем триггеры
        // Пример:
        //              событие   шаблон блока   имя функции-триггера
        // $this->trigger['save_b']['zakaz'] = 'trigger_save_zakaz';
        $this->trigger['save_b']['block_news'] = 'trigger_save_news';
    
    
    $q = "SELECT a.key FROM prname_btemplates a JOIN prname_bdatarel b ON b.templid = a.id WHERE b.key = 'alt_url' ";
    $res = $sql->query($q);
    while ($row = $sql->fetch_assoc($res)) {
        $this->trigger['save_b_url'][$row['key']] = 'trigger_save_url';
    }


// -----------------------------------------------------------------------------------------------------------
// Проверка полей каталога и вложенные шаблоны.
        $this->catdata = $sql->fetch_assoc($sql->query("select p2.* from prname_categories p1,prname_ctemplates  p2 where p2.key=p1.template and p1.id='$cparent'"));
// -----------------------------------------------------------------------------------------------------------
// Проверка шаблонов блоков.
        /*$insertblocks = $sql->fetch_assoc($sql->query("select insertblocks , id  from prname_categories where id='$cparent'"),0,1);
        $insertblock = $insertblocks[insertblocks];
       // $cparent = $insertblocks[id];
        $dinsert = explode(" ",$insertblock);
       for ($i = 1; $i < count($dinsert); $i++) {
           $binsert = explode(":",trim($dinsert[$i]));
           $this->insertedblocks[$binsert[0]] = new stdClass();
           $this->insertedblocks[$binsert[0]]->btempl = $binsert[0];
           $this->insertedblocks[$binsert[0]]->num = $binsert[1];
        }*/
// Массив с блоками.
        $btpl = explode('  ', trim($this->catdata['blocktypes']));
        $tplsort = $btpl; // порядок сортировки шаблонов
        $tplsort = "'" . implode("','", $tplsort) . "'";
        $q = $sql->query("select `name`, `key` from prname_btemplates order by field(`key`, {$tplsort}) DESC, `id` asc");
        $i = 0;
        $this->blocks = array();
        while ($arr = $sql->fetch_assoc($q)) {
            /*	 Имеющие вложения в каталоге.*/
            //if($this->insertedblocks[$arr[key]])$this->insertedblocks[$arr[key]]->btemplname = $arr[name];

            /*	 Все блоки.*/
            $this->blocks[$i] = new stdClass();
            $this->blocks[$i]->btemplname = $arr['name'];
            $this->blocks[$i]->btempl = $arr['key'];
            $this->blocks[$i]->count = $sql->one_record("SELECT COUNT(*) FROM prname_b_{$arr['key']} WHERE parent = '{$cparent}'");
            $this->blocks[$i]->super_only = !in_array($arr['key'], $btpl);
            $i++;
        }

        $this->btmplate = $btmplate ? $btmplate : $this->btmplate = $this->blocks[0]->btempl;
// -----------------------------------------------------------------------------------------------------------
// Проверка привилегий.
        $this->cparent = $cparent;
        $this->parent = $cparent;
        $this->html = 'list';
        $user_priv = $sql->fetch_assoc($sql->query("SELECT * FROM prname_btemplates WHERE `key`='$this->btmplate'"));
        $btemplates_fields = $sql->query("describe prname_btemplates");
        $i = 0;
        while ($bdfarr = $sql->fetch_assoc($btemplates_fields)) $this->{$bdfarr[Field]} = user_is('super') && $bdfarr[Field] !== name && $bdfarr[Field] !== visible && $bdfarr[Field] !== id && $bdfarr[Field] !== key ? 1 : $user_priv[$bdfarr[Field]]; // Массив с названиями полей.
        $this->superadmin = user_is('super');
    } // ПРоверка пользователя.

    function b_filds()
    {
        global $sql;
// -----------------------------------------------------------------------------------------------------------
// Выборка списка полей для шаблона $this->btmplate.
        $q = $sql->query("select p2.* from prname_btemplates p1, prname_bdatarel p2 where p1.key = '$this->btmplate' and p2.templid=p1.id order by p2.sort");
        $bdatarel_fields = $sql->query("describe prname_bdatarel");
        $i = 0;
        while ($bfarr = $sql->fetch_assoc($bdatarel_fields)) $this->block_fields[$i++] = $bfarr[Field]; // Массив с названиями полей.
        $i = 0;
        while ($f_arr = $sql->fetch_assoc($q)) {
            if (!isset($this->blocks_field[$i])) {
                $this->blocks_field[$i] = new stdClass();
            }
            for ($ii = 0; $ii < count($this->block_fields); $ii++) {
                if (!isset($this->items[$i]))
                    $this->items[$i] = new stdClass();
                $this->items[$i]->{$this->block_fields[$ii]} = $f_arr[$this->block_fields[$ii]];
            }
            $this->blocks_field[$i]->name = $f_arr[name];
            $this->blocks_field[$i++]->filterkey = $f_arr[key];
        }

    } // Получение списка полей.


    //триггеры
    function trigger_save_news($tpl, $block)
    {
        global $sql;
        global $config;

        if (!$block['block_news_001']) return;

        $send_date = date('d.m.Y');

        $email_sub = $send_date . ' - новости с сайта ' . $config['server_url'];

        // убираем картинки
        $email_text = preg_replace("/<img[^>]*>/i", '', $block['block_news_03']);
        $email_text = '
						<html>
							<head>
								<title>Тазовский район</title>
								<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
							</head>
							<body>
					<p><b>' . $send_date . '</b></p>
					<h2>' . $block['block_news_015'] . '</h2>
					' . $email_text . '
					<p>Новости с сайта <a href="http://tasu.ru">tasu.ru</a></p>
					<p>Для того, чтобы отказаться от подписки, нажмите на <a href="http://tasu.ru/subs/_uncheck_p%s%">ссылку</a>.</p>
							</body>
						</html>
					';


        $q = "SELECT block_podp_04 FROM prname_b_block_podp WHERE parent = 885 AND visible = 1 ";
        $send_res = $sql->query($q);
        while ($send_str = mysql_fetch_row($send_res)) {
            $to_email = $send_str[0];
            $pkey = md5($to_email);
            $em_text = str_replace('%s%', $pkey, $email_text);

            $q = "INSERT INTO prname_subscribe (date, subject, body, email, send)
								VALUES (
									NOW(),
									'" . mysql_real_escape_string($email_sub) . "',
									'" . mysql_real_escape_string($em_text) . "',
									'" . mysql_real_escape_string($to_email) . "',
									0
								)
						";
            $sql->query($q);

//						$result=$mail->send(array("<$from_email>"));
        }

    }
    
    
	// сохранение ЧПУ для блоков
    function trigger_save_url($tpl, $block)
    {
        global $sql;
        
        $field = $sql->one_record(sprintf(" SELECT a.comment FROM prname_bdatarel a JOIN prname_btemplates b ON b.id = a.templid 
            WHERE a.key = 'alt_url' AND b.key = '%s' ", $tpl));
        if (!$field) $field = 'title';
        
		$row = $sql->fetch_assoc($sql->query(sprintf("SELECT a.alt_url, a.parent, a.%s AS title, b.url AS parent_url FROM prname_b_%s a JOIN prname_tree b ON b.id = a.parent WHERE a.id = %s LIMIT 1 ", $field, $tpl, $block['id'])));
		$url_id = intval($sql->one_record(sprintf("SELECT id FROM prname_burl WHERE bid = %s AND btemplate = '%s' LIMIT 1 ", $block['id'], $tpl)));

		if (!strlen(trim($row['alt_url']))) {
            $row['alt_url'] = strtolower(all::translit(trim($row['title'])));
        } else {
            $row['alt_url'] = strtolower(all::translit(trim($row['alt_url'])));
        }
        
        $doubleID = $sql->one_record(sprintf("SELECT id FROM prname_burl WHERE url = '%s' AND parent_url = '%s' AND id != %s LIMIT 1 ", 
            $row['alt_url'], $row['parent_url'], $url_id));
        if (intval($doubleID)) {
            $row['alt_url'] = $row['alt_url'] . '-' . $block['id'];
        }
        
        $sql->query(sprintf("UPDATE prname_b_%s SET alt_url = '%s' WHERE id = %s ", $tpl, $row['alt_url'], $block['id']));
        
        if ($url_id) {
            $sql->query(sprintf("UPDATE prname_burl SET parent = %s, url = '%s', parent_url = '%s' WHERE id = %s", $row['parent'], $row['alt_url'], $row['parent_url'], $url_id));
        } else {
            $sql->query(sprintf("INSERT INTO prname_burl SET bid = %s, parent = %s, btemplate = '%s', url = '%s', parent_url = '%s' ", $block['id'], $row['parent'], $tpl, $row['alt_url'], $row['parent_url']));
        }
    }
    


    function bhide($id)
    {
        global $sql;
        if ($this->canhide) {
            $sql->query("update prname_b_$this->btmplate set visible=1-visible where `id`='$id'");
            adminlog($id, $this->btmplate, $this->cparent,'','toggle_block');
        }
    } // Скрытие блока.


    function groupvis()
    {
        global $sql;
        if (count($_POST['massblock']) > 0) {
            foreach ($_POST['massblock'] as $one_id) {
                //print_r($one_id);
                $q = "UPDATE prname_b_" . $this->btmplate . " SET visible = 1 WHERE id = '" . $sql->escape_string($one_id) . "' ";
                $sql->query($q);
                adminlog($one_id, $this->btmplate, $this->cparent,'','toggle_block');
            }
        }
    }

    function grouphid()
    {
        global $sql;
        if (count($_POST['massblock']) > 0) {
            foreach ($_POST['massblock'] as $one_id) {
                //print_r($one_id);
                $q = "UPDATE prname_b_" . $this->btmplate . " SET visible = 0 WHERE id = '" . $sql->escape_string($one_id) . "' ";
                $sql->query($q);
                adminlog($one_id, $this->btmplate, $this->cparent,'','toggle_block');
            }
        }
    }


    function save_b()
    {
        global $sql;
        global $error;
        $error = "";
        if ($this->canedit) {
            $upd = array();

            $q = "update prname_b_$this->btmplate set  `visible`= visible";
            for ($i = 0; $i < count($_POST[dat]); $i++) {
                if ($_POST[dat][$i] == "set_block_parent_catalog") {
                    if ($_POST['set_block_parent_catalog'] > 0)
                        $q .= " , `parent` = '" . $_POST['set_block_parent_catalog'] . "' ";
                } else {
                    $s = "\$data = save_" . $_POST[dkey][$i] . "('data$i');";
                    eval($s);

                    $q .= " , `" . addslashes($_POST[dat][$i]) . "` = '" . mysql_real_escape_string(stripslashes($data)) . "' ";

                    $upd[addslashes($_POST[dat][$i])] = stripslashes($data); //отдельно сохраняем измененные значения для триггера
                }
            }

            if (!strlen($error)) {
				$sql->query($q." where `id`='$this->id' ");
                adminlog($this->id, $this->btmplate, $this->cparent,'','change_block');
                $upd['id'] = $this->id;
				
                //проверяем наличие триггера
                if (isset($this->trigger['save_b'][$this->btmplate]) && method_exists($this, $this->trigger['save_b'][$this->btmplate])) {
                    $this->{$this->trigger['save_b'][$this->btmplate]}($this->btmplate, $upd); //вызов триггера
                }

            	if (isset($this->trigger['save_b_url'][$this->btmplate]) && method_exists($this, $this->trigger['save_b_url'][$this->btmplate]))
            	{
                	$this->{$this->trigger['save_b_url'][$this->btmplate]}($this->btmplate, $upd); //вызов триггера
            	}
            }

            return $error;
        }


    } // Сохранение блока.

    function mblock()
    {

        global $sql;
        if ($this->canmove) {
            $q = $sql->fetch_assoc($sql->query("select p1.sort as psort,p2.sort as ssort from prname_b_" . $this->btmplate . " p1,prname_b_" . $this->btmplate . " p2 where p1.id='$this->id' and p2.id='$_GET[mcparent]' "));
            $sql->query("update prname_b_" . trim($this->btmplate) . " set sort=if($q[psort]>$q[ssort],if($_GET[copymove]=0, if(sort>$q[ssort] && sort<$q[psort],sort-1,if(sort=$q[ssort],$q[psort]-1,sort)),if(sort>$q[ssort] && sort<= $q[psort] ,sort-1,if(sort=$q[ssort],$q[psort],sort))),if($_GET[copymove]=0, if(sort<$q[ssort] && sort>= $q[psort],sort+1,if(sort=$q[ssort],$q[psort],sort)), if(sort<$q[ssort] && sort> $q[psort] ,sort+1, if(sort=$q[ssort],$q[psort]+1,sort))))");
        }
    }

// Сохранение блока.
    function addnew()
    {
        global $error;
        global $sql;
        global $__DATA;
        $error = "";
        if ($this->canadd) {
            $sort = $sql->fetch_row($sql->query("select max(sort) from prname_b_$this->btmplate"), 0, 1) + 1;
            $q = "insert into prname_b_$this->btmplate set `visible`= '1',`sort`= '$sort', `parent` = '" . addslashes($this->cparent) . "'";
            for ($i = 0; $i < count($_POST[dat]); $i++) {
                $s = "\$data = save_" . $_POST[dkey][$i] . "('data$i');";
                eval($s);

                $__DATA['data' . $i] = stripslashes($this->items[$i]->readonly > 0 ? $this->items[$i]->default : addslashes($data));

                $q .= " , `" . addslashes($_POST[dat][$i]) . "` = '" . mysql_real_escape_string(stripslashes($this->items[$i]->readonly > 0 ? $this->items[$i]->default : addslashes($data))) . "' ";
                $upd[addslashes($_POST[dat][$i])] = stripslashes($this->items[$i]->readonly > 0 ? $this->items[$i]->default : addslashes($data)); //отдельно сохраняем измененные значения для триггера
            }

            if (!strlen($error)) {
                $sql->query($q);
                $id = $sql->insert_id();
                adminlog($id, $this->btmplate, $this->cparent,'','add_block');
                
                $upd['id'] = $id;
                $upd['add'] = 1;

                //проверяем наличие триггера
                if (isset($this->trigger['save_b'][$this->btmplate]) && method_exists($this, $this->trigger['save_b'][$this->btmplate])) {
                    $this->{$this->trigger['save_b'][$this->btmplate]}($this->btmplate, $upd); //вызов триггера
                }

            	if (isset($this->trigger['save_b_url'][$this->btmplate]) && method_exists($this, $this->trigger['save_b_url'][$this->btmplate]))
            	{
                	$this->{$this->trigger['save_b_url'][$this->btmplate]}($this->btmplate, $upd); //вызов триггера
            	}
            }
        }

        return $error;

    }

// Добавление нового блока.
    function add_b($error = "")
    {
        if ($this->canadd) {
            $this->action = "addnew";
            $this->html = "one";
            $this->alert = $error;
            $this->name = 'Создание блока';//.$sql->fetch_row($sql->query("select name from prname_btemplates  where `key`='$this->btmplate'"),0,1);
        }

        global $filter;
        global $csv;
        global $cat_filtr;

        $this->filter = $filter;
        $this->csv = $csv;
        $this->cat_filtr = $cat_filtr;

    } // HTML для создания блока

    function get_list()
    {


        global $sql;
        // Получаем список блоков.
        // Выборка из таблицы. 
// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

        $q = "SELECT id FROM prname_btemplates WHERE `key` = '" . $this->btmplate . "' ";
        $templid = $sql->one_record($q);
        $q = "SELECT * FROM prname_bdatarel WHERE templid = '$templid' AND `show` = 1 ORDER BY sort";
        $res = $sql->query($q);
        $i = 0;
        while ($str = $sql->fetch_array($res)) {
            $this->table_hr[$i] = new stdClass();
            $this->table_hr[$i]->title = $str['name'];
            $this->table_hr[$i]->key = $str['key'];
            $this->table_hr[$i]->datakey = $str['datatkey'];
            $this->table_hr[$i]->comment = $str['comment'];
            $i++;
        }


        //print_r($this->table_hr);

        //print_r($this->table_hr);

        if ($_POST['add2'] == 1) {

            global $actions;
            global $btmplate;
            global $cparent;
            global $parent;


            if ($_POST['set_block_parent_catalog'])
                $cparent = $_POST['set_block_parent_catalog'];


            $this->redirect = "block.php?cparent=$cparent&btmplate=$btmplate&actions=edit&blockid=$this->id";
            //header ("location: block.php?cparent=$cparent&btmplate=$btemplate&actions=$actions&blockid='$this->id'");
            //die();
        }


        global $filter;
        global $csv;
        global $cat_filtr;

        $this->filter = $filter;
        $this->csv = $csv;
        $this->cat_filtr = $cat_filtr;


        $p = $filter->page;

        //$this->limit = $_GET[l]?$_GET[l]:$this->limit; // <- Добавить $this->limit
        $this->limit = $filter->limit; // <- Добавить $this->limit


        $this->start = ($start = $p ? ($p * $this->limit) : 0) + 1;
        $this->stop = $start + $this->limit;

        if (strstr($cat_filtr->cat_id, ';')) {
            if ($cat_filtr->cat_id != '0;') {
                $qswe = "select SQL_CALC_FOUND_ROWS  * from prname_b_$this->btmplate where parent in (0 ";
                $tmp_arr = explode(';', $cat_filtr->cat_id);
                foreach ($tmp_arr as $one_arr) {
                    if ($one_arr != '') {
                        $qswe .= ',' . $one_arr;
                    }
                }
                $qswe .= " ) ";
            } else {
                $qswe = "select SQL_CALC_FOUND_ROWS  * from prname_b_$this->btmplate where parent > 0 ";
            }
        } else {
            $qswe = "select SQL_CALC_FOUND_ROWS  * from prname_b_$this->btmplate where parent=$this->parent " . ($this->blockparent ? "and id='$this->blockparent'" : '');
        }
//		print_r($cat_filtr->cat_id);
        $qswe .= ' ' . $filter->add_q . " order by " . ($this->filterkey ? $this->filterkey : " sort") . " $this->filtersortby, sort limit $start, $this->limit";


        if ($this->btmplate) $q = $sql->query($qswe);
        $this->numall = $numall = $sql->one_record("SELECT FOUND_ROWS();");
        $url = "block.php?cparent=" . $this->parent . "&btmplate=" . $this->btmplate;

        if ($numall > $this->limit) {
            $c = ceil($numall / $this->limit);

            for ($ipa = 0; $ipa < $c && $c > 1; $ipa++) {
                $this->page[$ipa]->title = $ipa + 1;
                $this->page[$ipa]->url = $url . "&f_page=" . $ipa; //&f_limit=".$this->limit;
                if ($ipa == $p)
                    $this->page[$ipa]->current = 1;
            }
            $this->page = Listing::pageSplit($this->page, 30, $p);
        }


// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 
        $i = 0;
        while ($arr = $sql->fetch_assoc($q)) {
            if (!isset($this->blocklist[$i]))
                $this->blocklist[$i] = new stdClass();
            $name = '';
            $ikk = 0;
            for ($ii = 0; $ii < count($this->items); $ii++) {
                if ($this->items[$ii]->show) {

                    switch ($this->items[$ii]->datatkey) {
                        case 'date':
                            $name = all::get_date($arr[$this->items[$ii]->key], 1);
                            break;
                        case 'image': {

                            //break;
                            $name = '';
                            if (strlen($arr[$this->items[$ii]->key]) > 1) {
                                $tmp_div_id = 'img' . $arr[$this->items[$ii]->key] . $ii . rand(1, 9999);

                                if (!file_exists("../images/icons/" . $arr[$this->items[$ii]->key])) {
                                    @resize_image($arr[$this->items[$ii]->key], '90x90x50crop ', 'icons');
                                }


                                if (file_exists("../images/icons/" . $arr[$this->items[$ii]->key])) {
                                    $name = '

								<div style="position:relative;">

									
									<img src="img/ico_addimg.gif" width="14" height="14" onMouseOver="$(\'' . $tmp_div_id . '\').style.display=\'block\';" onMouseOut="$(\'' . $tmp_div_id . '\').style.display=\'none\';" />
									

								<div id="' . $tmp_div_id . '" style="z-index:1000; background:#fff; left:25px; top: 0px; display:none; position:absolute; width:91px; height:91px; border:1px solid #cccccc" ><img src="/images/icons/' . $arr[$this->items[$ii]->key] . '" ></div>
								</div>

								</div>
									
										';
                                }
                            }
                            break;
                        }

                        case 'select': {
                            $name = get_select($arr[$this->items[$ii]->key], $this->items[$ii]->comment);
                            break;
                        }

                        case 'mselect': {
                            $name = get_mselect($arr[$this->items[$ii]->key], $this->items[$ii]->comment);
                            break;
                        }

                        case 'idnumber': {
                            $name = input_idnumber($arr[id]);
                            break;
                        }

                        default:
                            $name = mb_substr(strip_tags($arr[$this->items[$ii]->key]), 0, 50, 'UTF-8');
                            break;
                    }
                    if (!isset($this->blocklist[$i]->table_name[$ikk])) {
                        $this->blocklist[$i]->table_name[$ikk] = new stdClass();
                    }
                    $this->blocklist[$i]->table_name[$ikk]->data = $name;
                    $ikk++;
                }
            }

            $this->blocklist[$i]->name = $name;
            $this->blocklist[$i]->id = $arr[id];
            $this->blocklist[$i]->idx = $start + $i + 1;

            $this->blocklist[$i]->blockvisible = $arr[visible];
            $this->blocklist[$i]->blocksort = $arr[sort];
            $this->blocklist[$i]->visible = $arr[visible];
            $this->blocklist[$i]->blockparent = $arr['parent'];

            if ($cat_filtr->unvis_option_block) {

                $this->blocklist[$i]->path_arr = Tree::GetParents($this->blocklist[$i]->blockparent);
                $this->blocklist[$i]->path = '';
                $kk = 0;
                foreach ($this->blocklist[$i]->path_arr as $one_arr) {
                    if ($kk > 0) {
                        $q2 = "SELECT name FROM prname_categories WHERE id = '$one_arr' ";
                        $this->blocklist[$i]->path .= ' / ' . $sql->one_record($q2);
                    }
                    $kk++;
                }

            }
            $i++;
        }
// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 
        $this->count = $this->numall;
// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 
// ===================================================================================================================================================================================================
// Проверка на наличие блоков в каталоге.
        $insertblocks = $sql->fetch_assoc($sql->query("select insertblocks , id  from prname_categories where id='$this->parent'"), 0, 1);
        $insertblock = $insertblocks[insertblocks];
        $cparent = $insertblocks[id];
        $dinsert = explode(" ", $insertblock);
        for ($i = 0; $i < count($dinsert); $i++) {

            $binsert = explode(":", $dinsert[$i]);
            if ($binsert[0] == $this->btmplate) {
                if ($this->count > 0) {
                    $sql->query("update prname_categories set insertblocks = '" . str_replace($dinsert[$i], $binsert[0] . ":" . $this->count, $insertblock) . "' where id='$this->parent'");
                } else $sql->query("update prname_categories set insertblocks = '" . str_replace($insertblock, '', $insertblock) . "' where id='$this->parent'");

                $not = true;
            };
        }
        if (!$not && $this->count > 0) $sql->query("update prname_categories set insertblocks = '" . $insertblock . ' ' . $this->btmplate . ":" . $this->count . "' where id='$this->parent'");
// ===================================================================================================================================================================================================
//  

        if ($this->count == 0) $this->alert = 'Вложенных блоков нет';
    } // Список полей в шаблоне $insert_b->btmplate.

    function get_one($error = "")
    {
        global $sql;
        if ($this->canedit) {
            $this->html = "one";
            $this->action = "save";
            $this->alert = $error;
            $this->name = 'Редактирование  блока'; // - '.$sql->fetch_row($sql->query("select name from prname_btemplates  where `key`='$this->btmplate' "),0,1);
            $q = $sql->query("select * from prname_b_$this->btmplate where `id`='$this->id' ");
            $i = 0;
            while ($arr = $sql->fetch_assoc($q)) {

                for ($ii = 0; $ii < count($this->items); $ii++) {
                    $this->values[$ii] = new stdClass();
                    for ($iii = 0; $iii < count($this->block_fields); $iii++) {
                        $this->values[$ii]->{$this->block_fields[$iii]} = $this->items[$ii]->{$this->block_fields[$iii]};
                    };
                    if ($this->items[$ii]->readonly > 0 && !$this->superadmin) $this->items[$ii]->attr .= " readonly ";
                    $func = 'input_' . $this->items[$ii]->datatkey;
                    $this->values[$ii]->value = $func('data' . $ii, $this->items[$ii]->attr, $arr[$this->items[$ii]->key], $this->items[$ii]->comment);
                }
                $i++;
            };
        }
        //print_r ($this->values);
        global $filter;
        $this->filter = $filter;
        //print_r($filter);


    } // Вывод одного блока на экран.

    function get_values()
    {
        global $__DATA;
        // Получаем значения для поля.
        // Выборка из таблицы.

        for ($ii = 0; $ii < count($this->items); $ii++) {
            $this->values[$ii] = new stdClass();
            for ($iii = 0; $iii < count($this->block_fields); $iii++) {
                $this->values[$ii]->{$this->block_fields[$iii]} = $this->items[$ii]->{$this->block_fields[$iii]};


            };

            if ($_POST) {

                $this->items[$ii]->default = $__DATA['data' . $ii];
            }

            $this->values[$ii]->value = $this->items[$ii]->default;

            $s = '$this->values[$ii]->value = ' . " input_" . $this->items[$ii]->datatkey
                . '(\'data' . $ii . '\', "'
                . addslashes($this->items[$ii]->attr) . '", "'
                . addslashes($this->items[$ii]->default) . '", "'
                . addslashes($this->items[$ii]->comment) . '");';

            eval($s);
        }

    } // Список полей со значениями по умолчанию.

    function get_html()
    {
        $this->html = sprintt($this, 'templates/blocks/' . $this->html . '.html');
    } // Вывод на редактирование списка блоков.

    function delete_block($id, $templ)
    {
        adminlog($id, $templ, $this->cparent,'','del_block');
        delete_block($id, $templ);
    }

    function groupdel()
    {
        if (count($_POST['massblock']) > 0) {
            foreach ($_POST['massblock'] as $one_id) {
                adminlog($one_id, $this->btmplate, $this->cparent,'','del_block');
                delete_block($one_id, $this->btmplate);
                //print_r($one_id);
            }
        }
        //delete_block($id,$templ);
    }

    function copy_block($id, $templ)
    {
        global $sql;
        $sort = $sql->fetch_row($sql->query("select MAX(sort) from prname_b_$templ"), 0, 1) + 1;
        $s = $sql->fetch_array($sql->query("select * from prname_b_$templ where `id`='$id'"));
        $q = $sql->query("SELECT p2.* FROM prname_btemplates p1, prname_bdatarel p2 WHERE p1.key='$templ' and  p2.templid=p1.id");
        $text = "insert into prname_b_$templ set ";
        while ($arr = $sql->fetch_assoc($q)) if ($arr[datatkey] !== 'file') $text .= " `" . $arr[key] . "` ='" . mysql_real_escape_string(stripslashes($s[$arr[key]])) . "' , ";
        $text .= " `parent`='" . $s['parent'] . "'" . ($s['blockparent'] ? ", `blockparent`='" . $s['blockparent'] . "'" : '') . " ,`sort`='" . $sort . "'";
        $sql->query($text);
    }


    //построение списка дерева сайта
    function add_maplevel(&$obj, $ids, $sel, $level = 0)
    {
        $mask = '<option value="%s" %s>%s</option>' . "\n";
        $selected = "";

        $prefix = str_repeat("&nbsp;&nbsp;&nbsp;", $level);

        if (is_array($ids))
            if (!in_array($obj->id, $ids))
                $selected = "disabled";

        if (in_array($obj->id, $sel))
            $selected = "selected";

        $result = sprintf($mask, $obj->id, $selected, $prefix . $obj->title);
        if ($obj->item)
            foreach ($obj->item as &$item)
                $result .= $this->add_maplevel($item, $ids, $sel, $level + 1);
        return $result;
    }


    // добавление поле для перемещения блока в другой каталог
    function add_move_block_item()
    {
        global $sql;
        if ($this->canmove) {

            $q = sprintf("select c.id as id, c.name as name FROM prname_categories as c LEFT JOIN prname_ctemplates as t ON c.template=t.key
					WHERE  t.blocktypes LIKE '%% %s %%' ORDER BY c.sort;", $this->key);

            $s = '<select style="width:440px" size="15"  name="set_block_parent_catalog">';
            $s .= '<optgroup label="Структура сайта"></optgroup>';

            $res = $sql->query($q);
            while ($arr = $sql->fetch_assoc($res))
                $ids[] = $arr['id'];

            $items = All::get_node(1, 3, 'formoder');

            $sel[] = $this->parent;

            if ($items->item)
                foreach ($items->item as &$item)
                    $s .= $this->add_maplevel($item, $ids, $sel, 0);


            $s .= "</select>";


            $item->datatkey = "select";
            $item->key = "set_block_parent_catalog";
            $item->show = 0;
            $item->name = "Каталог блока";
            $item->readonly = 0;
            $item->sort = 99999;
            $item->value = $s;

            $this->values[] = $item;
        }
    }
}

$insert_b = new blockedit($cparent, $btmplate);
$insert_b->action = $actions ? $actions : 'list';

//сброс кеша если не просмотр списка блоков
if ($insert_b->action != 'list')
    clear_cache(0);


//$insert_b->filterkey = $_GET[filterkey]?$_SESSION[sort][$cparent][$insert_b->btmplate][keyby] = $_GET[filterkey]:($_SESSION[sort][$cparent][$insert_b->btmplate][keyby]?$_SESSION[sort][$cparent][$insert_b->btmplate][keyby]:' sort');
//$insert_b->filterkey = $insert_b->filtersortby;
//$insert_b->filtersortby = $_GET[filterby]?$_SESSION[sort][$cparent][$insert_b->btmplate][filterby] = ($_GET[filterby]==1?' asc':' desc'):($_SESSION[sort][$cparent][$insert_b->btmplate][filterby]?$_SESSION[sort][$cparent][$insert_b->btmplate][filterby]:' asc');
//$insert_b->filterby = $insert_b->filtersortby==' asc'?'1':'2';

$insert_b->id = $parent;
$insert_b->b_filds();

$d = explode("|", $insert_b->catdata[sortfield]);
for ($i = 0; $i < count($d); $i++) {
    $f = explode(":", $d[$i]);
    $j = explode(' ', $f[1]);
    $filtr[$f[0]] = $j[0];
    $filtrby[$f[0]] = $j[1];
}
// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 
//$insert_b->filterkey = $filtr[$insert_b->btmplate]?$filtr[$insert_b->btmplate]:'sort'; // Простая сортировка
$insert_b->filterkey = $filtr[$insert_b->btmplate]; // Простая сортировка
// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 
$insert_b->filtersortby = $filtrby[$insert_b->btmplate] ? $filtrby[$insert_b->btmplate] : ' asc';
switch ($insert_b->action) {
    case 'hide':
        $insert_b->bhide($insert_b->id);
        $insert_b->get_list();
        break;// Скрытие блока.
    case 'add':
        $insert_b->get_values();
        $insert_b->add_b();
        break;// Добавление блока.
    case 'save':
        $error = $insert_b->save_b();
        if (!strlen($error))
            $insert_b->get_list();
        else {
            $insert_b->get_one($error);
            $insert_b->get_list();
        }
        break;
    // Удаление блока.
    case 'addnew':
        $error = $insert_b->addnew();
        $insert_b->get_list();
        if (!strlen($error))
            $insert_b->get_list();
        else {
            $insert_b->get_values();
            $insert_b->add_b($error);
        }
        break; // html нового блока.
    case 'edit':
        $insert_b->get_one();
        $insert_b->get_list();
        break; // вывод на редактирование.
    case 'copy':
        $insert_b->copy_block($insert_b->id, $insert_b->btmplate);
        $insert_b->get_list();
        break; // вывод на редактирование.
    case 'del':
        $insert_b->delete_block($insert_b->id, $insert_b->btmplate);
        $insert_b->get_list();
        break; // Удаление блока.
    case 'mup':
        $insert_b->mblock();
        $insert_b->get_list();
        break;// ПЕРЕиещение блока вверх.
    case 'mdown':
        $insert_b->mblock();
        $insert_b->get_list();
        break;// Перемещение блока вниз.
    case 'list':
        $insert_b->get_list();
        break;// Вывод всех вложенных блоков.

    case 'groupvis':
        $insert_b->groupvis();
        $insert_b->get_list();
        break;// Скрытие блока.
    case 'grouphid':
        $insert_b->grouphid();
        $insert_b->get_list();
        break;// Скрытие блока.
    case 'groupdel':
        $insert_b->groupdel();
        $insert_b->get_list();
        break;// Скрытие блока.

}

if ($actions == 'edit')
//if ($insert_b->action == 'save')
    $insert_b->add_move_block_item();


//print_r($insert_b);exit;
// Список полей в шаблоне $insert_b->btmplate.
//$insert_b->get_one(); // Вывод одного блока на экран.
//$insert_b->get_values(); // Список полей со значениями по умолчанию.
$insert_b->blockid = $parent;
$insert_b->patch = tree::tree_url($insert_b->cparent);
$insert_b->get_html(); // Сгенерированный HTML код.

print_r($insert_b->html);
include "templates/includes/new_bottom.php";
?>