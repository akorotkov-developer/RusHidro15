<?php
#error_reporting(E_ALL);

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
$res = mysql_query("SELECT * FROM $prname" . "_datatypes ORDER BY `key`");
$page = new stdClass();
$page->item_num = mysql_num_rows($res);

?>

<?php
$i = 0;
while ($row = mysql_fetch_array($res)) {
    $page->item[$i] = new stdClass();
    $page->item[$i]->row = $row;
    $res1 = mysql_query("SELECT bt.name, bt.`key` FROM $prname" . "_bdatarel, $prname" . "_btemplates bt WHERE templid=bt.id AND datatkey='" . addslashes($row['key']) . "'");

    $page->item[$i]->n1 = 0 + mysql_num_rows($res1);


    $res2 = mysql_query("SELECT ct.name, ct.`key` FROM $prname" . "_cdatarel, $prname" . "_ctemplates ct WHERE templid=ct.id AND datatkey='" . addslashes($row['key']) . "'");
    $page->item[$i]->n2 = 0 + mysql_num_rows($res2);

    $page->item[$i]->isp = $page->item[$i]->n1 + $page->item[$i]->n2;

    if (intval($parent) < 1) {
        $parent = $row['id'];
    };

    if (mysql_result(mysql_query("SELECT COUNT(*) FROM $prname" . "_datatypes WHERE `key`='" . addslashes($row['key']) . "' AND id<>" . $row['id']), 0, 0) > 0) {
        $page->item[$i]->notunic = true;
    }

    if ($parent == $row['id']) {
        $page->item[$i]->checked = 'checked';
    }


    $i++;
}

?>

<?php
echo sprintt($page, 'templates/data/list.html');
?>


<?php
include "templates/includes/new_bottom.php";
?>