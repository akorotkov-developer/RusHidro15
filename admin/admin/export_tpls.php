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


function get_export_c($id)
{
    global $sql;
    global $config;
    global $wrappers;
    $i = 0;
    do {
        $cata = $sql->fetch_assoc($sql->query("SELECT * FROM `prname_ctemplates` WHERE `id`='" . $id . "'"));
        $cat_name = $cata[name];
        $file = $wrappers[$cata[key]][module] . "." . "php";
        if ($file_bufer = file_get_contents($config['user_file_moduls'] . $file)) {
            $bufer .= "\r\n<cataloge>\r\n";
            preg_match_all('/sprintt\((.*)\);/Uis', $file_bufer, $html, PREG_PATTERN_ORDER);
            for ($h = 0; $h < count($html[1]); $h++) {
                $f_patch = explode(",", $html[1][$h]);
                $f_patch = trim(str_replace("'", '', $f_patch[1]));
                $bufer .= "<html_file>\r\n";
                $bufer .= "<file_name>$f_patch</file_name>\r\n";
                $html_bufer = file_get_contents("../$f_patch");
                $bufer .= "<file_content> $html_bufer</file_content>\r\n</html_file>";
            }
            $bufer .= "\r\n<php_file>\r\n<file_name>$file</file_name>\r\n<file_content>$file_bufer</file_content>\r\n</php_file>";
            $bufer .= "\r\n</cataloge>";
        }
        $ttr = $sql->query("SELECT * FROM `prname_ctemplates` WHERE `id`='" . $id . "'");
        while ($row = $sql->fetch_array($ttr)) {
            $template = $row[key];
            $bufer .= "\r\n<sql>[cataloge]INSERT INTO `prname_ctemplates` set  `name` = 'Копия - $row[name]',`candel`='$row[candel]',`canedit`='$row[canedit]',`canaddcat`='$row[canaddcat]', `canaddbl` = '$row[canaddbl]', `canmoveto` = '$row[canmoveto]', `cancopyto` = '$row[cancopyto]', `canmovefrom` = '$row[canmovefrom]',`cancopyfrom`='$row[cancopyfrom]',`blocktypes` = '$row[blocktypes] ',`key` = '$row[key]',`canhide` = '$row[canhide]',`alias` = '$row[alias]', `abc` = '$row[abc]',`filterfield` = '$row[filterfield]',`caneditname`='$row[caneditname]',`hidestructure` = '$row[hidestructure]',`cattypes` = '$row[cattypes] ',`sortfield` = '$row[sortfield]',`abccat` = '$row[abccat]',`filterfieldcat` = '$row[filterfieldcat]'</sql>";
        }
        $ttr2 = $sql->query("SELECT * FROM `prname_cdatarel` WHERE `templid`='" . $id . "'");
        while ($row = $sql->fetch_array($ttr2)) {
            $bufer .= "\r\n<sql>INSERT INTO `prname_cdatarel` VALUES ('','<!--id//-->', '$row[datatkey]','$row[comment]','$row[key]','$row[attr][$i]', '$row[default]', '$row[name]', '$row[readonly]', '$row[sort]')</sql>";
        }
        $i++;
    } while ($i < count($POST_VARS[shxrt]));
    $bufer = protect::p_code($bufer);
    file::giveFile($bufer, "Каталог - $cat_name - $config[site_name].tpl");
}

function get_export_b($bid)
{
    global $sql;
    global $config;
    global $wrappers;
    $damp = '';

    $er = '';
    $er2 = '';
    $arre = $bid;
    $delall = "`id` = '$arre' $er";
    $delall2 = "`templid` = '$arre' $er2";
    $ttr = $sql->query("SELECT * FROM `prname_btemplates` WHERE `id`='" . $bid . "'");
    $row = $sql->fetch_assoc($ttr);
    $damp .= "\r\n<sql>[cataloge]INSERT INTO `prname_btemplates` set `key` = '$row[key]',`name`= 'Копия - $row[name]',`canadd` = '$row[canadd]',`candel` = '$row[candel]', `canedit` = '$row[canedit]', `cancopy` = '$row[cancopy]', `canmove` = '$row[canmove]', `canhide` = '$row[canhide]',`visible` ='$row[visible]'</sql>";
    $ttr2 = $sql->query("SELECT * FROM `prname_bdatarel` WHERE `templid` = '$row[id]'");
    while ($rows = $sql->fetch_array($ttr2)) {
        $damp .= "\r\n<sql>INSERT INTO `prname_bdatarel` VALUES ('','$rows[datatkey]', '<!--id//-->','$rows[comment]','$rows[key]','$rows[attr][$i]', '$rows[default]', '$rows[show]', '$rows[name]', '$rows[readonly]', '$rows[sort]')</sql>";
    }
    $damp = protect::p_code($damp);
    file::giveFile($damp, "Блок - $row[name] - $config[site_name].tpl");
}


if (!$_GET['tpl']) {
    $back = '<p><a href="?">&larr; Назад</a></p>';
    $q = mysql_query("Select * From $prname" . "_ctemplates");
    $out = '<option selected>Выберите шаблон</option><optgroup label="Каталоги"style="font-weight: bold; border-style: solid; border-width: 1px; background-color: #C9E0F8">';
    while ($b = mysql_fetch_assoc($q)) $out .= '<option value="c' . $b[id] . '">' . $b[name] . '</option>' . "\n";

    $q = mysql_query("Select * From $prname" . "_btemplates");
    $out .= '<optgroup label="Блоки"style="font-weight: bold; border-style: solid; border-width: 1px; background-color: #FFFF00">';
    while ($b = mysql_fetch_assoc($q)) $out .= '<option value="b' . $b[id] . '">' . $b[name] . '</option>' . "\n";
    if ($out) $out = '<select name="tpl" onchange="this.form.submit()">' . $out . '</select>';
    $out = '
	  	<form action="" method="GET">
		
	  	' . $out . '
	  	</form>';
} else {

    if (substr($_GET[tpl], 0, 1) == 'c') get_export_c(substr($_GET[tpl], 1));
    if (substr($_GET[tpl], 0, 1) == 'b') get_export_b(substr($_GET[tpl], 1));
};


include("templates/includes/new_top.php");
//echo '<td bgcolor="#cccccc" style="padding:20px;">';
echo '<H1>Экспорт шаблонов</H1>';
echo $out;
//echo '</td>';
include("templates/includes/new_bottom.php");

//mysql_close();

?>