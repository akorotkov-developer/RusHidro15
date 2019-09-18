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

if ($del > 0) {
    delete_datatype($parent);
    header("location:s_datatempl.php");
    exit;
};

if (isset($add)) {
    if (($parent > 0) && ($makecopy < 1)) {
        $q = "UPDATE $prname" . "_datatypes SET name='$name', `key`='$key' WHERE id=$parent";
        mysql_query($q);
        header("location:s_datatempl.php?parent=$parent");
        exit;
    } else {
        $q = "INSERT INTO $prname" . "_datatypes (name, `key`) VALUES";
        $q .= "('$name', '$key')";
        mysql_query($q);
        $id = mysql_result(mysql_query("SELECT MAX(id) AS mid FROM $prname" . "_datatypes"), 0, 0);
        header("location:s_datatempl.php?parent=$id");
        exit;
    };
};

include "templates/includes/new_top.php";
if (intval($parent) < 1) {
    $parent = 0;
};
$id = $parent;
if ($id > 0) {
    $q = "SELECT * FROM $prname" . "_datatypes WHERE id=$id";
    $cr = mysql_fetch_array(mysql_query($q));
    if ($makecopy > 0) {
        $cr['name'] = 'Копия ' . $cr['name'];
        $cr['key'] = 'Копия ' . $cr['key'];
    };
};

$page = new stdClass();
$page->id = $id;
$page->parent = $parent;

$page->cr = $cr;

$page->makecopy = $makecopy;


?>
<?php
echo sprintt($page, 'templates/data/edit.html');
?>


<?php
include "templates/includes/new_bottom.php";
?>