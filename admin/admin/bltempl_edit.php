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

if ($_POST[oldbkey] !== $_POST[key]) {
    mysql_query("ALTER TABLE `$prname" . "_b_" . $_POST[oldbkey] . "` RENAME `$prname" . "_b_" . $_POST[key] . "`");
    mysql_query("update `$prname" . "_ctemplates` set `blocktypes`= REPLACE(`blocktypes`,'" . $_POST[oldbkey] . "','" . $_POST[key] . "')");
}


if (($parent > 0) && ($vis == 1)) {
    $q = "UPDATE $prname" . "_btemplates SET visible = not visible WHERE id = '$parent'";
    mysql_query($q);
    header("location:s_bltempl.php?parent=$parent");
    exit;
}

if ($del > 0) {
    delete_btemplate($parent);
    header("location:s_bltempl.php");
    exit;
};

if (isset($add) || ($add2 == 1)) {
    $candel = ($candel == 1) ? 1 : 0;
    $canmove = ($canmove == 1) ? 1 : 0;
    $cancopy = ($cancopy == 1) ? 1 : 0;
    $canhide = ($canhide == 1) ? 1 : 0;
    $canedit = ($canedit == 1) ? 1 : 0;
    $canadd = ($canadd == 1) ? 1 : 0;

    if (($parent > 0) && ($vis == 1)) {

        header("location:s_bltempl.php?parent=$parent");
        exit;
    }
    if (($parent > 0) && ($makecopy < 1)) {
        mysql_query("UPDATE $prname" . "_btemplates SET name='$name',
											`key`='$key',
											`candel`='$candel',
											`canedit`='$canedit',
											`canadd`='$canadd',
											`canhide`='$canhide',
											`cancopy`='$cancopy',
											`canmove`='$canmove' WHERE id=$parent");
        mysql_query("DELETE FROM $prname" . "_bdatarel WHERE templid=$parent");
// ================================================================================================================================================================================
// Проверим на наличие всех старых полей.
        $q = mysql_query("describe  $prname" . "_b_$key");
// Удаление столбца.
        while ($arr_f = mysql_fetch_assoc($q)) {
            $_old_field[$arr_f[Field]] = $arr_f[Field];
            $last_fild = $arr_f[Field];
            if (!$_POST[old_name_f][$arr_f[Field]] && $arr_f[Field] !== 'id' && $arr_f[Field] !== 'parent' && $arr_f[Field] !== 'blockparent' && $arr_f[Field] !== 'visible' && $arr_f[Field] !== 'sort')
                mysql_query("ALTER TABLE `$prname" . "_b_$key` DROP `$arr_f[Field]` ");// удляем столбец.
        }
        for ($i = 1; $i <= $nfields; $i++) {
            switch (${"nfield$i"}) {
                case 'html':
                    $ptype = 'longtext';
                    break;
                case 'int':
                    $ptype = 'bigint';
                    break;
                case 'double':
                    $ptype = 'double';
                    break;
                case 'checkbox':
                    $ptype = 'tinyint(1)';
                    break;
                case 'radiio':
                    $ptype = 'tinyint(1)';
                    break;
                case 'date':
                    $ptype = 'date';
                    break;
                case 'file':
                    $ptype = 'varchar(255)';
                    break;
                case 'text':
                    $ptype = 'varchar(255)';
                    break;
                case 'mcheckbox':
                    $ptype = 'varchar(100)';
                    break;
                default:
                    $ptype = 'text';
                    break;
            }
            if (strlen(${"nfieldd$i"}) > 0) $def_v = " DEFAULT '" . ${"nfieldd$i"} . "' NOT NULL";
// ================================================================================================================================================================================
// Создание нового поля.

            //Костыль
            if ($ptype == "text") $def_v = "";
            if ($ptype == "date") $def_v = "";


            if (!$_old_field[${"nfieldk$i"}]) {


                mysql_query("ALTER  TABLE `$prname" . "_b_$key` ADD `" . ${"nfieldk$i"} . "` $ptype $def_v");
            }
// ================================================================================================================================================================================
// Переименование поля.
            mysql_query("ALTER TABLE `$prname" . "_b_$key` CHANGE `" . ${"old_nfieldk$i"} . "` " . ${"nfieldk$i"} . " $ptype $def_v");
// ================================================================================================================================================================================
            mysql_query("INSERT INTO $prname" . "_bdatarel (templid, datatkey, comment, `key`, attr, `default`, `show`, name, readonly, sort) VALUES ($parent, '" . ${"nfield$i"} . "', '" . ${"nfieldc$i"} . "', '" . ${"nfieldk$i"} . "', '" . ${"nfielda$i"} . "', '" . ${"nfieldd$i"} . "', '" . ((${"nfields$i"} > 0) ? 1 : 0) . "', '" . ${"nfieldn$i"} . "', '" . ((${"nfieldr$i"} > 0) ? 1 : 0) . "' ,  '" . ${"nfieldsort$i"} . "') ");
        };

        if ($add2 == 1) {
            header("location:s_bltempl_edit.php?parent=$parent#bottom_page");
            exit;
        }


        header("location:s_bltempl.php?parent=$parent");
        exit;
    } else {
// ================================================================================================================================================================================
// Создание блока.
        $q = "INSERT INTO $prname" . "_btemplates (name, `key`, candel, canmove, cancopy, canhide, canedit, canadd)
			 VALUES ('$name', '$key', $candel, $canmove, $cancopy, $canhide, $canedit, $canadd)";
        mysql_query($q);
        $id = mysql_result(mysql_query("SELECT MAX(id) AS mid FROM $prname" . "_btemplates"), 0, 0);
        for ($i = 1; $i <= $nfields; $i++) {
// ================================================================================================================================================================================
// Поля блока.
            switch (${"nfield$i"}) {
                case 'html':
                    $ptype = 'longtext';
                    break;
                case 'int':
                    $ptype = 'bigint';
                    break;
                case 'double':
                    $ptype = 'double';
                    break;
                case 'checkbox':
                    $ptype = 'tinyint(1)';
                    break;
                case 'radiio':
                    $ptype = 'tinyint(1)';
                    break;
                case 'date':
                    $ptype = 'date';
                    break;
                case 'file':
                    $ptype = 'varchar(255)';
                    break;
                case 'text':
                    $ptype = 'varchar(255)';
                    break;
                case 'mcheckbox':
                    $ptype = 'varchar(100)';
                    break;
                default:
                    $ptype = 'text';
                    break;
            }
            $text_base .= "`" . ${"nfieldk$i"} . "` $ptype ,";
// ================================================================================================================================================================================
            mysql_query("INSERT INTO $prname" . "_bdatarel (templid, datatkey, comment, `key`, attr, `default`, `show`, name, readonly, sort)
					 VALUES ($id, '" . ${"nfield$i"} . "', '" . ${"nfieldc$i"} . "', '" . ${"nfieldk$i"} . "', '" . ${"nfielda$i"} . "', '" . ${"nfieldd$i"} . "', '" . ((${"nfields$i"} > 0) ? 1 : 0) . "', '" . ${"nfieldn$i"} . "', '" . ((${"nfieldr$i"} > 0) ? 1 : 0) . "', '" . ${"nfieldsort$i"} . "')");
        };

// ================================================================================================================================================================================
// Создание таблы.
        if ($key) mysql_query("CREATE TABLE `$prname" . "_b_$key` (`id` int(12) NOT NULL auto_increment,`parent` int(12),`blockparent` int(12),`visible` tinyint(1) DEFAULT '1' NOT NULL,`sort` int(12) ,$text_base PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;");
// ================================================================================================================================================================================

        if ($add2 == 1) {
            header("location:s_bltempl_edit.php?parent=$id#bottom_page");
            exit;
        }


        header("location:s_bltempl.php?parent=$id");
        exit;
    };
};

include "templates/includes/new_top.php";
if (intval($parent) < 1) {
    $parent = 0;
};
$id = $parent;
if ($nfields != '') {
    $name = stripslashes($name);
    $key = stripslashes($key);
    for ($i = 1; $i <= $nfields; $i++) {
        ${"nfield$i"} = stripslashes(${"nfield$i"});
        ${"nfieldc$i"} = stripslashes(${"nfieldc$i"});
        ${"nfieldk$i"} = stripslashes(${"nfieldk$i"});
        ${"nfielda$i"} = stripslashes(${"nfielda$i"});
        ${"nfieldd$i"} = stripslashes(${"nfieldd$i"});
        ${"nfields$i"} = stripslashes(${"nfields$i"});
        ${"nfieldn$i"} = stripslashes(${"nfieldn$i"});
        ${"nfieldr$i"} = stripslashes(${"nfieldr$i"});
        ${"nfieldsort$i"} = stripslashes(${"nfieldsort$i"});
    };

};
if ($id > 0) {
    $q = "SELECT * FROM $prname" . "_btemplates WHERE id=$id";
    $cr = mysql_fetch_array(mysql_query($q));
    if ($nfields == '') {
        if ($makecopy > 0) {
            $cr['name'] = 'Копия ' . $cr['name'];
            $cr['key'] = 'Копия ' . $cr['key'];
        };
        $res = mysql_query("SELECT * FROM $prname" . "_bdatarel WHERE templid=$id ORDER BY sort, `key`");
        $nfields = mysql_num_rows($res);
        $i = 0;
        while ($row = mysql_fetch_array($res)) {
            $i++;
            ${"nfield$i"} = $row['datatkey'];
            ${"nfieldc$i"} = $row['comment'];
            ${"nfieldk$i"} = $row['key'];
            ${"nfielda$i"} = $row['attr'];
            ${"nfieldd$i"} = $row['default'];
            ${"nfields$i"} = $row['show'];
            ${"nfieldn$i"} = $row['name'];
            ${"nfieldr$i"} = $row['readonly'];
            ${"nfieldsort$i"} = $row['sort'];
        };
        $name = $cr['name'];
        $key = $cr['key'];
        $candel = $cr['candel'];
        $canedit = $cr['canedit'];
        $canadd = $cr['canadd'];
        $canhide = $cr['canhide'];
        $canmove = $cr['canmove'];
        $cancopy = $cr['cancopy'];
    };
} else {
    if ($nfields < 1) $nfields = 0;
};
if ($removefield > 0) {
    for ($i = $removefield; $i < $nfields; $i++) {
        ${"nfield$i"} = ${"nfield" . ($i + 1)};
        ${"nfieldc$i"} = ${"nfieldc" . ($i + 1)};
        ${"nfieldk$i"} = ${"nfieldk" . ($i + 1)};
        ${"nfielda$i"} = ${"nfielda" . ($i + 1)};
        ${"nfieldd$i"} = ${"nfieldd" . ($i + 1)};
        ${"nfields$i"} = ${"nfields" . ($i + 1)};
        ${"nfieldn$i"} = ${"nfieldn" . ($i + 1)};
        ${"nfieldr$i"} = ${"nfieldr" . ($i + 1)};
        ${"nfieldsort$i"} = ${"nfieldsort" . ($i + 1)};

    };
    $nfields--;
};

$page = new stdClass();
$page->id = $id;
$page->makecopy = $makecopy;
$page->nfields = $nfields;
$page->parent = $parent;
$page->key = $key;
$page->name = $name;
$page->candel = $candel;
$page->canedit = $canedit;
$page->canadd = $canadd;
$page->cancopy = $cancopy;
$page->canmove = $canmove;
$page->canhide = $canhide;
//$page->  = $;
//$page->  = $;


?>
<?php

$res = mysql_query("SELECT * FROM $prname" . "_datatypes ORDER BY `key`");

$page->num_datatypes = mysql_num_rows($res);

if (mysql_num_rows($res) > 0) {

    $text_base = '';
    for ($i = 1; $i <= $nfields; $i++) {
        $page->nfield[$i] = new stdClass();
        $page->nfield[$i]->i = $i;

        switch (${"nfield$i"}) {
            case 'html':
                $ptype = 'longtext';
                break;
            case 'int':
                $ptype = 'bigint';
                break;
            case 'double':
                $ptype = 'double';
                break;
            case 'checkbox':
                $ptype = 'tinyint(1)';
                break;
            case 'radiio':
                $ptype = 'tinyint(1)';
                break;
            case 'date':
                $ptype = 'date';
                break;
            case 'file':
                $ptype = 'varchar(255)';
                break;
            case 'text':
                $ptype = 'varchar(255)';
                break;
            case 'mcheckbox':
                $ptype = 'varchar(100)';
                break;
            default:
                $ptype = 'text';
                break;
        }
        $text_base .= "`" . trim(${"nfieldk$i"}) . "` $ptype ,";

        $page->nfield[$i]->nfield = ${"nfield$i"};
        $page->nfield[$i]->nfieldn = ${"nfieldn$i"};
        $page->nfield[$i]->nfieldk = ${"nfieldk$i"};
        $page->nfield[$i]->nfieldd = ${"nfieldd$i"};

        $page->nfield[$i]->nfieldc = ${"nfieldc$i"};
        $page->nfield[$i]->nfielda = ${"nfielda$i"};
        $page->nfield[$i]->nfieldsort = ${"nfieldsort$i"};
        $page->nfield[$i]->nfieldr = ${"nfieldr$i"};
        $page->nfield[$i]->nfields = ${"nfields$i"};


        mysql_data_seek($res, 0);
        $j = 0;
        while ($row = mysql_fetch_array($res)) {
            $page->nfield[$i]->row[$j] = new stdClass();
            $page->nfield[$i]->row[$j]->row = $row;
            if (${"nfield$i"} == $row['key']) {
                $page->nfield[$i]->row[$j]->selected = 'selected';
            }
            $j++;
        }

    }

}


?>


<?php
echo sprintt($page, 'templates/tblocks/edit.html');
?>


<?php

if ($key) mysql_query("CREATE TABLE IF NOT EXISTS `$prname" . "_b_$key` (`id` int(12) NOT NULL auto_increment,`parent` int(12),`blockparent` int(12),`visible` tinyint(1) DEFAULT '1' NOT NULL,`sort` int(12) ,$text_base PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;");


include "templates/includes/new_bottom.php";
?>