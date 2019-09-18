<?
session_start();
unset($_SESSION['ses_basket']);
unset($_SESSION['ses_cost']);
include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/basket.php";
$url = $config['server_url'] . 'basket';
header("Location:" . $url);


?>