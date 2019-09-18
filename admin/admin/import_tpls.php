<?php

require_once "../includes.php";
require_once "../inc/libs/caching.php";

include "../inc/class/file_oper.php";
include "../inc/class/protect.php";
global $sql;
$sql = new Sql();
$sql->connect();
kick_unauth();

if (!user_is('super')) {
    header("location:admin_login.php");
};


if (!$_POST['send']) {
    $out = '
	<form name="FormName" action="" method="POST" enctype="multipart/form-data">
		<input name="send" type="hidden" value="1">
	    Выберите шаблон: <input name="tpl" type="file">
	    <br>
	    <input type="submit" value="Импортировать">
	</form>';
} else {

    /*error_reporting(E_ALL);*/
    $basefile = $_FILES['tpl']['tmp_name']; // Адрес файла базы данных
    $bufer = file_get_contents($basefile);
    $bufer = protect::p_encode($bufer);
    preg_match_all('/<cataloge>(.*)<\/cataloge>/Uis', $bufer, $cataloge, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($cataloge[1]); $i++) {
        preg_match_all('/<html_file>(.*)<\/html_file>/Uis', $cataloge[1][$i], $html, PREG_PATTERN_ORDER);
        for ($f = 0; $f < count($html[1]); $f++) {
            preg_match_all('/<file_name>(.*)<\/file_name>/Uis', $html[1][$f], $f_name, PREG_PATTERN_ORDER);
            preg_match_all('/<file_content>(.*)<\/file_content>/Uis', $html[1][$f], $f_content, PREG_PATTERN_ORDER);
            for ($ff = 0; $ff < count($f_name[1]); $ff++) {
                $f_name[1][$ff]; // Файл html к сохранению.
                $html_patch = explode("/", $f_name[1][$ff]);
                $html_fname = $html_patch[count($html_patch) - 1];
                $html_patch = str_replace($html_patch[count($html_patch) - 1], '', $f_name[1][$ff]);
                $f_content[1][$ff]; // Файл html  к сохранению.
                file::greate_file($html_fname, '../' . $html_patch . "/", $f_content[1][$ff]);
            }
        }
        preg_match_all('/<php_file>(.*)<\/php_file>/Uis', $cataloge[1][$i], $php, PREG_PATTERN_ORDER);
        for ($p = 0; $p < count($php[1]); $p++) {
            preg_match_all('/<file_name>(.*)<\/file_name>/Uis', $php[1][$p], $f_name, PREG_PATTERN_ORDER);
            $f_name[1][$p]; // Файл php к сохранению.
            preg_match_all('/<file_content>(.*)<\/file_content>/Uis', $php[1][$p], $f_content, PREG_PATTERN_ORDER);
            $f_content[1][$p]; // Файл php к сохранению.

            file::greate_file($f_name[1][$p], $config['user_file_moduls'] . "/", $f_content[1][$p]);
        }
    }
    preg_match_all('/<sql>(.*)<\/sql>/Uis', $bufer, $sql, PREG_PATTERN_ORDER);
    for ($s = 0; $s < count($sql[1]); $s++) {
        if (stristr($sql[1][$s], "[cataloge]")) {
            $sql[1][$s] = str_replace("[cataloge]", "", $sql[1][$s]);
//      print $sql[1][$s];
            $sql->query($sql[1][$s]);
            $id = $sql->insert_id();
        } else {
            $sql[1][$s] = str_replace("<!--id//-->", $id, $sql[1][$s]);
            $sql->query($sql[1][$s]);
        }
    }
    $out = '<b>Поздравляю! :) <br>Шаблон ' . (($type == b) ? 'блока' : 'папки') . ' импортирован! :)</b>';

};

include("templates/includes/new_top.php");
//echo '<td bgcolor="#cccccc" style="padding:20px;">';
echo '<H1>Импорт шаблонов</H1>';
echo $out;
//echo '</td>';
include("templates/includes/new_bottom.php");
mysql_close();

?>