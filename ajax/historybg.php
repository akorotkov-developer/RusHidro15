<?
include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/history.php";
$sql = new Sql();
$sql->connect();
$history = new history();

$html = $history->load();

echo $html;
?>