<?php

require_once "../includes.php";
require_once "../inc/libs/caching.php";

//построение списка дерева сайта
function add_maplevel(&$obj, $ids, $level = 0)
{
    $mask = '<option value="%s" %s>%s</option>' . "\n";
    $selected = "";
    $prefix = str_repeat("&nbsp;&nbsp;&nbsp;", $level);

    if (in_array($obj->id, $ids))
        $selected = "selected";

    $result = sprintf($mask, $obj->id, $selected, $prefix . $obj->title);
    if ($obj->item)
        foreach ($obj->item as &$item)
            $result .= add_maplevel($item, $ids, $level + 1);
    return $result;
}


global $sql;
$sql = new Sql();
$sql->connect();
kick_unauth();
if (user_is('is_moder')) {
    header("location:admin_login.php");
};
$page = new stdClass();

$user_is_super = user_is('super');


$admin_id = user_is('admin_id');
$parent = intval($_REQUEST['parent']);
$parent2 = "tr$parent";

if (!$parent && !isset($_REQUEST['adduser'])) {
    $parent = $admin_id;
}

/*
if (!user_is('super')) {
	$parent = $admin_id;
	$parent2 = "tr$parent";
};
*/

if ($del > 0) {
    if ($parent != $admin_id) {
        mysql_query("DELETE FROM $prname" . "_sadmin WHERE admin_id=$parent");
        mysql_query("DELETE FROM $prname" . "_rt WHERE aid=$parent");
        header("location:admins.php");
        exit;
    } else //режим удаления модераторов
    {
        mysql_query("DELETE FROM $prname" . "_sadmin WHERE admin_id=$parent AND is_moder = 1");
        mysql_query("DELETE FROM $prname" . "_rt WHERE aid=$parent");
        header("location:admins.php");
        exit;
    }

};

if ($hide > 0) {
    if ($parent != $admin_id) {
        mysql_query("UPDATE $prname" . "_sadmin SET enabled=1-enabled WHERE admin_id=$parent");
        header("location:admins.php?parent=$cparent");
        exit;
    };
};

if (isset($_REQUEST['add'])) {
    if ($parent > 0) {

        $toadmins = 1;//$user_is_super;

        if ($user_is_super) {
            $moder = sprintf(", is_moder = %d", intval($_REQUEST['is_moder']));
        }

        $q = "UPDATE $prname" . "_sadmin SET admin_name='$name', admin_email='$email'";
        if ($password != '') {
            $q .= ", admin_password='" . md5(base64_encode('SoftMajor AP ' . stripslashes($password))) . "'";
            $toadmins = true;
        };

        //права доступа
        if (isset($_POST['enabled_cats']) && count($_POST['enabled_cats'])) {
            $cats = implode(";", $_POST['enabled_cats']);
            $q .= sprintf(", enabled_cid = '%s'", $cats);
        }

        $q .= $moder; //параметр модератора

        $q .= " WHERE admin_id=$parent";
        mysql_query($q);
        if ($admin_id != $parent) {
            mysql_query("UPDATE $prname" . "_rt SET super=" . ($super ? 1 : 0) . " WHERE aid=$parent");
        };
        if ($toadmins) {
            header("location:admins.php?parent=$parent");
        } else {
            header("location:admins_edit.php?parent=$parent&parent2=tr$parent");
        };
        exit;
    } else {

        $moder = 1;
        if ($user_is_super) {
            $moder = sprintf("%d", intval($_REQUEST['is_moder']));
        }

        //права доступа
        $cats = '';
        if (isset($_POST['enabled_cats']) && count($_POST['enabled_cats'])) {
            $cats = implode(";", $_POST['enabled_cats']);
        }


        $q = "INSERT INTO $prname" . "_sadmin (admin_name, admin_password, admin_email, is_moder, enabled_cid, enabled) VALUES";
        $q .= "('$name', '" . md5(base64_encode('SoftMajor AP ' . stripslashes($password))) . "', '$email', $moder, '$cats', 1)";
        mysql_query($q);
        $id = mysql_result(mysql_query("SELECT MAX(admin_id) AS mid FROM $prname" . "_sadmin"), 0, 0);
        mysql_query("INSERT INTO $prname" . "_rt (super, aid) VALUES (" . ($super ? 1 : 0) . ", $id)");
        header("location:admins.php?parent=$id");
        exit;
    };
};

include "templates/includes/new_top.php";
if (intval($parent) < 1) {
    $parent = 0;
};
$id = $parent;
$super = 0;
//if ($id > 0) {

if ($_REQUEST['adduser'])
    $page->adduser = 1;

if (!$user_is_super && $parent != $admin_id) //если это админ редактирует модераторов
{
    $WHERE = "AND is_moder = 1";
    $page->modermode = 1; //режим редактирования модераторов
}

$q = "SELECT * FROM $prname" . "_sadmin WHERE admin_id=$id $WHERE";
$cr = mysql_fetch_array(mysql_query($q));

if ($parent == $admin_id) //если редактирую сам себя
    $super = user_is('super', $parent);
//};

$page->id = $id;
$page->parent = $parent;

$page->cr = $cr;
//формирование списка разделов сайта
$s = '<select name="enabled_cats[]" MULTIPLE size="15" style="width:440px">';
$s .= '<optgroup label="Структура сайта"></optgroup>';
$ids = explode(';', $cr['enabled_cid']);
$items = All::get_node(1, 3, 'formoder');
if ($items && $items->item)
    foreach ($items->item as &$item)
        $s .= add_maplevel($item, $ids, 0);
$s .= "</select>";
$page->enabled_cats = $s;


$page->user_is_super = $user_is_super;
$page->super = $super;
$page->admin_id = $admin_id;

$page->makecopy = $makecopy;

//если модерация отключена
if ($config['admin_is_moder'] != true) {
    unset($page->enabled_cats);
}

?>

<?php
echo sprintt($page, 'templates/admin/edit.html');
?>


<?php
include "templates/includes/new_bottom.php";
?>