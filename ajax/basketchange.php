<?
include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/basket.php";
$sql = new Sql();
$sql->connect();
session_start();
if ($_SESSION['ses_basket'][$_POST['id']]) {
    $basket = new basket();
    $basket->change($_POST['id'], $_POST['col']);
    $col = 0;
    foreach ($_SESSION['ses_basket'] as $item) {
        $col += $item->col;
    }

    $arr = array('col' => $col, 'bcost' => $_SESSION['ses_cost']);
    echo json_encode($arr);
}
?>