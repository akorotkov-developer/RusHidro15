<?php
include "../includes.php";
$sql = new sql();
$sql->connect();
/*Для создания блока нам необходимо следующее.
1. Права на добавление блока.
2. Шаблон блока.
2. Родитель (Каталог или блок.
Дырка по умолчанию $GET 
*/
kick_unauth();

if (isset($_GET['packet']) && $_GET['packet'] === 'true' && $_POST) {
	if ($_GET['edit'] === 'true'){
		foreach ($_POST['packet_field'] as $field => $val){
			$val = $sql->escape_string($val);
			$sql->query("UPDATE prname_b_{$_GET['template']} SET `{$field}` = '{$val}' WHERE id = '{$_POST['id']}'");
		}
		exit;
	}
	// пришли данные с формы пакетной загрузки файлов.
	require_once '../inc/libs/all.php';
	$data = array('blockparent' => $_GET['blockparent']);
	foreach ($_POST['packet_field'] as $field => $val) {
        $data[$field] = $val;
	}
    foreach ($_FILES as $field => $val){
        if ($_FILES[$field]['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
            $file = rand(1000, 9999) . time() . '.' . $ext;
            move_uploaded_file($_FILES[$field]['tmp_name'], '../images/0/' . $file);
            $data[$field] = $file;
        }
    }
    $all = new All();
    $id = $all->insert_block($_GET['template'],'2194', $data);
    $sql->query("UPDATE prname_b_{$_GET['template']} SET blockparent = '{$data['blockparent']}', sort = '{$data['sort']}' WHERE id = '{$id}'");
    resize_image($file, '40x40', 'packet');
    echo json_encode(array('img' => 'images/packet/' . $file, 'id' => $id));
    exit;
}

class insert_b_admin
{
    var $parent; // Родитель блок или каталог.
    var $template; // Шаблон блока.

    function insert_b_admin($btmplate = '')
    {
        global $sql;
        // Проверка привилегий.
        $this->template = $btmplate;
        $user_priv = $sql->fetch_assoc($sql->query("SELECT * FROM prname_btemplates WHERE `key`='$btmplate'"));
        $btemplates_fields = $sql->query("describe prname_btemplates");
        $i = 0;
        while ($bdfarr = $sql->fetch_assoc($btemplates_fields)) $this->{$bdfarr[Field]} = user_is('super') ? 1 : $user_priv[$bdfarr[Field]]; // Массив с названиями полей.
        $this->superadmin = user_is('super');
    } // ПРоверка пользователя.

    function get_filds()
    {
        global $sql;
        // Выборка списка полей для шаблона $this->template.
        $q = $sql->query("select p2.* from prname_btemplates p1, prname_bdatarel p2 where p1.key = '$this->template' and p2.templid=p1.id order by p2.sort");
        $bdatarel_fields = $sql->query("describe prname_bdatarel");
        $i = 0;
        while ($bfarr = $sql->fetch_assoc($bdatarel_fields)) $this->block_fields[$i++] = $bfarr[Field]; // Массив с названиями полей.
        $i = 0;
        while ($f_arr = $sql->fetch_assoc($q)) {
            $this->items[$i] = new stdClass();
            for ($ii = 0; $ii < count($this->block_fields); $ii++) $this->items[$i]->{$this->block_fields[$ii]} = $f_arr[$this->block_fields[$ii]];
            $i++;
        }
// -----------------------------------------------------------------------------------------------------------
// Проверка полей каталога и вложенные шаблоны.
        $this->catdata = $sql->fetch_assoc($sql->query("select p2.* from prname_categories p1,prname_ctemplates  p2 where p2.key=p1.template and p1.id='$this->parent'"));
    } // Получение списка полей.

    function bhide($bid)
    {
        global $sql;
        if ($this->canhide) $sql->query("update prname_b_$this->template set visible=1-visible where `id`='$bid'");
    } // Скрытие блока.

    function save_b()
    {
        global $sql;

        if ($this->canedit) {
            $q = "update prname_b_$this->template set  `visible`= visible";
            for ($i = 0; $i < count($_POST[dat]); $i++) {
                $s = "\$data = save_" . $_POST[dkey][$i] . "('data$i');";
                eval($s);
                $q .= " , " . addslashes($_POST[dat][$i]) . " = '" . mysql_real_escape_string($data) . "' ";
            }
            $sql->query($q . " where `id`='$this->bid' ");
        }

    } // Сохранение блока.

    function mup_block()
    {
        global $sql;

        if ($this->canmove) {
            $q = $sql->fetch_assoc($sql->query("select p1.sort as psort,p2.sort as ssort from prname_b_" . $this->template . " p1,prname_b_" . $this->template . " p2 where p1.id='$this->bid' and p2.id='$_GET[mblockparent]' "));
            $q = "update prname_b_" . trim($this->template) . " set sort=if($q[psort]>$q[ssort],if($_GET[copymove]=0, if(sort>$q[ssort] && sort<$q[psort],sort-1,if(sort=$q[ssort],$q[psort]-1,sort)),if(sort>$q[ssort] && sort<= $q[psort] ,sort-1,if(sort=$q[ssort],$q[psort],sort))),
             if($_GET[copymove]=0, if(sort<$q[ssort] && sort>= $q[psort],sort+1,if(sort=$q[ssort],$q[psort],sort)), if(sort<$q[ssort] && sort> $q[psort] ,sort+1, if(sort=$q[ssort],$q[psort]+1,sort))))";
            $sql->query($q);
        }

    } // Сохранение блока.

    function addnew()
    {
        global $sql;
        if ($this->canadd) {
            $sort = $sql->fetch_row($sql->query("select max(sort) from prname_b_$this->template"), 0, 1) + 1;
            $q = "insert into prname_b_$this->template set `visible`= '1',`sort`= '$sort', `parent` = '" . addslashes($_POST['parent']) . "', `blockparent` = '" . addslashes($_POST['blockparent']) . "'";
            for ($i = 0; $i < count($_POST[dat]); $i++) {
                $s = "\$data = save_" . $_POST[dkey][$i] . "('data$i');";
                eval($s);
                $q .= " , " . stripslashes(($_POST[dat][$i])) . " = '" . mysql_real_escape_string($data) . "' ";
            }
            $sql->query($q);
        }
    }

    function add_b()
    {
        if ($this->canadd) {
            $this->action = "addnew";
            $this->html = "one";
        }
    } // HTML для создания блока

    function get_list()
    {


        global $sql;
        $q = "SELECT id FROM prname_btemplates WHERE `key` = '" . $this->template . "' ";
        $templid = $sql->one_record($q);
        $q = "SELECT * FROM prname_bdatarel WHERE templid = '$templid' AND `show` = 1 ORDER BY sort";
        $res = $sql->query($q);
        $i = 0;
        while ($str = $sql->fetch_array($res)) {
            $this->table_hr[$i] = new stdClass();
            $this->table_hr[$i]->title = $str['name'];
            $this->table_hr[$i]->key = $str['key'];
            $this->table_hr[$i]->datakey = $str['datatkey'];
            $i++;
        }

        // Получаем список блоков.
        // Выборка из таблицы.
        $q = $sql->query("select * from prname_b_$this->template where parent=$this->parent " . ($this->blockparent ? "and blockparent='$this->blockparent'" : '') . " order by sort");
        $i = 0;
        while ($arr = $sql->fetch_assoc($q)) {
            $this->blocklist[$i] = new stdClass();
            $name = '';
            $ikk = 0;
            for ($ii = 0; $ii < count($this->items); $ii++) {
                if ($this->items[$ii]->show) {
                    switch ($this->items[$ii]->datatkey) {
                        case 'date':
                            $name = all::get_date($arr[$this->items[$ii]->key], 1) . '';
                            break;

                        case 'select': {
                            $name = get_select($arr[$this->items[$ii]->key], $this->items[$ii]->comment);
                            break;
                        }

                        case 'mselect': {
                            $name = get_mselect($arr[$this->items[$ii]->key], $this->items[$ii]->comment);
                            break;
                        }

                        default:
                            $name = substr(strip_tags($arr[$this->items[$ii]->key]), 0, 50) . '';
                            break;
                    }
                    $this->blocklist[$i]->table_name[$ikk] = new stdClass();
                    $this->blocklist[$i]->table_name[$ikk]->data = $name;
                    $ikk++;
                }
            }
            $this->blocklist[$i]->name = $name;
            $this->blocklist[$i]->id = $arr[id];
            $this->blocklist[$i]->blockvisible = $arr[visible];
            $this->blocklist[$i]->blocksort = $arr[sort];
            $this->blocklist[$i]->visible = $arr[visible];
            $this->blocklist[$i]->blockparent = $arr['parent'];
            $i++;
        }
        $this->count = $sql->num_rows($q);
    } // Список полей в шаблоне $insert_b->template.

    function get_one()
    {
        global $sql;
        if ($this->canedit) {
            $this->html = "one";
            $this->action = "save";
            $q = $sql->query("select * from prname_b_$this->template where `id`='$this->bid'");
            $i = 0;
            while ($arr = $sql->fetch_assoc($q)) {
                for ($ii = 0; $ii < count($this->items); $ii++) {
                    $this->values[$ii] = new stdClass();
                    for ($iii = 0; $iii < count($this->block_fields); $iii++) {
                        $this->values[$ii]->{$this->block_fields[$iii]} = $this->items[$ii]->{$this->block_fields[$iii]};
                    }
                    $s = '$this->values[$ii]->value = ' . " input_" . $this->items[$ii]->datatkey . '(\'data' . $ii . '\', "' . addslashes($this->items[$ii]->attr) . '", "' . addslashes($arr[$this->items[$ii]->key]) . '", "' . addslashes($this->items[$ii]->comment) . '");';
                    eval($s);
                }
                $i++;
            };
        }
    } // Вывод одного блока на экран.

    function get_values()
    {
        // Получаем значения для поля.
        // Выборка из таблицы.
        for ($ii = 0; $ii < count($this->items); $ii++) {
            $this->values[$ii] = new stdClass();
            for ($iii = 0; $iii < count($this->block_fields); $iii++) {
                $this->values[$ii]->{$this->block_fields[$iii]} = $this->items[$ii]->{$this->block_fields[$iii]};
                $this->values[$ii]->value = $this->items[$ii]->default;
            };
            $s = '$this->values[$ii]->value = ' . " input_" . $this->items[$ii]->datatkey . '(\'data' . $ii . '\', "' . addslashes($this->items[$ii]->attr) . '", "' . addslashes($this->items[$ii]->default) . '", "' . addslashes($this->items[$ii]->comment) . '");';
            eval($s);
        }
    } // Список полей со значениями по умолчанию.

    function get_html()
    {
        $this->html = sprintt($this, 'templates/item_blocks/' . $this->html . '.html');
    } // Вывод на редактирование списка блоков.

    function delete_block($bid, $templ)
    {
        delete_block($bid, $templ);
    }
}


$insert_b = new insert_b_admin($_GET[template]);
$insert_b->packet = $_GET['packet'] == 'true';
if ($insert_b->packet) {
	$insert_b->packet_field = $_GET['field'] ? $_GET['field'] : 'img';
    $insert_b->packet_sort = $sql->fetch_row($sql->query("select max(sort) from prname_b_{$_GET['template']}"), 0, 1) + 1;
	$insert_b->packet_default = array();
    $defaultArr = array('title' => '', 'sort' => '1');
	if (isset($defaultArr)) {
		foreach ($defaultArr as $key => $value){
			$def = new stdClass();
			$def->key = $key;
			$def->val = rawurldecode($value);
			$insert_b->packet_default[] = $def;
		}
	}
	$insert_b->packet_increment = '';
}
$insert_b->html = 'list';
$insert_b->action = $_GET[action] ? $_GET[action] : 'list';
$insert_b->template = $_GET[template];
$insert_b->name = 'Редактирование блока';
$insert_b->bid = $_GET[blockid];
$insert_b->blockparent = $_GET[blockparent];
$insert_b->parent = '2194';

$insert_b->get_filds();
switch ($insert_b->action) {
    case 'hide':
        $insert_b->bhide($insert_b->bid);
        $insert_b->get_list();
        break;// Скрытие блока.
    case 'add':
        $insert_b->get_values();
        $insert_b->add_b();
        break;// Добавление блока.
    case 'save':
        $insert_b->save_b();
        $insert_b->get_list();
        break; // Удаление блока.
    case 'addnew':
        $insert_b->addnew();
        $insert_b->get_list();
        break; // html нового блока.
    case 'edit':
        $insert_b->get_one();
        $insert_b->get_list();
        break; // вывод на редактирование.
    case 'del':
        $insert_b->delete_block($insert_b->bid, $insert_b->template);
        $insert_b->get_list();
        break; // Удаление блока.
    case 'mup':
        $insert_b->mup_block();
        $insert_b->get_list();
        break;// ПЕРЕиещение блока вверх.
    case 'mdown':
        $insert_b->mup_block();
        $insert_b->get_list();
        break;// Перемещение блока вниз.
    case 'list':
        $insert_b->get_list();
        break;// Вывод всех вложенных блоков.
}
//print_r($insert_b);
// Список полей в шаблоне $insert_b->template.
//$insert_b->get_one(); // Вывод одного блока на экран.
//$insert_b->get_values(); // Список полей со значениями по умолчанию.
$insert_b->get_html(); // Сгенерированный HTML код.

print_r($insert_b->html);
$sql->close();
?>