<?
$useragent = $_POST["useragent"];
$vowels = array(";", "(", ")", "/", ",");
$useragent = str_replace($vowels, "", $useragent);

$remote = $_SERVER['REMOTE_ADDR'].$useragent;

echo json_encode($remote);
?>