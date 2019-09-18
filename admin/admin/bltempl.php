<?php

require_once "../includes.php";
require_once "../inc/libs/caching.php";


global $sql;
$sql = new Sql();
$sql->connect();
kick_unauth();
if (!user_is('super')) {
    header("location:admin_login.php");
};


include "templates/includes/new_top.php";
$res = mysql_query("SELECT * FROM $prname" . "_btemplates ORDER BY `key`");

$page = new stdClass();
$page->item_num = mysql_num_rows($res);

?>


<?php
$i = 0;
while ($row = mysql_fetch_array($res)) {
    $page->item[$i] = new stdClass();
    $page->item[$i]->row = $row;

    if (intval($parent) < 1) {
        $parent = $row['id'];
    };

    $res1 = mysql_query("SELECT cat.id as cid, cat.name as cname, bl.parent, bl.`sort` FROM $prname" . "_b_$row[key] bl, $prname" . "_categories cat WHERE bl.parent=cat.id ORDER BY `sort` LIMIT 0,9");
    if ($res1)
        $page->item[$i]->isp = mysql_num_rows($res1);
    else
        $page->item[$i]->isp = 0;

    if (mysql_result(mysql_query("SELECT COUNT(id) AS cnt FROM $prname" . "_btemplates WHERE `key`='" . addslashes($row['key']) . "' AND id<>" . $row['id']), 0, 0) > 0) {
        $page->item[$i]->notunic = true;
    }

    if ($parent == $row['id']) {
        $page->item[$i]->checked = 'checked';
    }

    $i++;
}

$page->parent = $parent;

//var_dump($page);

?>


<?php
echo sprintt($page, 'templates/tblocks/list.html');
?>


<?php
include "templates/includes/new_bottom.php";
?>