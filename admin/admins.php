<?php


require_once "../includes.php";
require_once "../inc/libs/caching.php";

global $sql;
$sql = new Sql();
$sql->connect();
kick_unauth();
if (user_is('is_moder')) {
    header("location:admin_login.php");
};

$admin_id = user_is('admin_id');
$superadmin = user_is('super');

include "templates/includes/new_top.php";
if ($superadmin)
    $res = mysql_query("SELECT * FROM $prname" . "_sadmin ORDER BY ($admin_id=admin_id) DESC, `admin_name`");
else
    $res = mysql_query("SELECT * FROM $prname" . "_sadmin WHERE is_moder = 1 ORDER BY ($admin_id=admin_id) DESC, `admin_name`");
?>

    <script>
        //sMenu(1);
    </script>


<?php
$page = new stdClass();
$page->item_num = mysql_num_rows($res);

?>

<?php

$i = 0;
while ($row = mysql_fetch_array($res)) {
    $page->item[$i] = new stdClass();
    $page->item[$i]->admin_id = $row['admin_id'];
    $page->item[$i]->enabled = $row['enabled'];
    $page->item[$i]->admin_name = $row['admin_name'];

    if (intval($parent) < 1) {
        $parent = $row['admin_id'];
    };
    if ($parent == $row['admin_id']) {
        $page->item[$i]->checked = 'checked';
    }
    $i++;
}

$page->parent = $parent;
?>

<?php
echo sprintt($page, 'templates/admin/list.html');
?>


<?php
include "templates/includes/new_bottom.php";
?>