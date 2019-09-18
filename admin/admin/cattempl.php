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

$res = mysql_query("SELECT * FROM $prname" . "_ctemplates ORDER BY `name`");
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

    $res1 = mysql_query("SELECT name FROM $prname" . "_categories WHERE template='" . addslashes($row['key']) . "' OR templateinc='" . addslashes($row['key']) . "' ORDER BY `sort`");
    if ($res1)
        $page->item[$i]->isp = mysql_num_rows($res1);
    else
        $page->item[$i]->isp = 0;

    if (mysql_result(mysql_query("SELECT COUNT(*) AS cnt FROM $prname" . "_ctemplates WHERE `key`='" . addslashes($row['key']) . "' AND id<>" . $row['id']), 0, 0) > 0) {
        $page->item[$i]->notunic = true;
    }

    if ($parent == $row['id']) {
        $page->item[$i]->checked = 'checked';
    }

    $i++;


}

echo sprintt($page, 'templates/tcats/list.html');


include "templates/includes/new_bottom.php";
?>