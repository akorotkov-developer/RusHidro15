<?
include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/basket.php";
$sql = new Sql();
$sql->connect();
session_start();
$goods = all::b_data_all($_POST['id'], 'goods');
$basket = new basket();
$basket->add($_POST['id'], $goods->title, $goods->price, $_POST['col']);
$col = 0;
foreach ($_SESSION['ses_basket'] as $item) {
    $col += $item->col;
}

$arr = array('col' => $col, 'bcost' => $_SESSION['ses_cost']);
echo json_encode($arr);
?>