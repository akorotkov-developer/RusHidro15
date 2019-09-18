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
    mysql_query("ALTER TABLE `$prname" . "_c_" . $_POST[oldbkey] . "` RENAME `$prname" . "_c_" . $_POST[key] . "`");
    mysql_query("update `$prname" . "_categories` set `template`= REPLACE(`template`,'" . $_POST[oldbkey] . "','" . $_POST[key] . "')");
}

if (($parent > 0) && ($vis == 1)) {
    $q = "UPDATE $prname" . "_ctemplates SET visible = not visible WHERE id = '$parent'";
    mysql_query($q);
    header("location:s_cattempl.php");
    exit;
}


if ($del > 0) {
    if (delete_ctemplate($parent) == 'inuse') {
        ?>
        <script language="JavaScript" type="text/javascript">
            alert('Выбранный Вами шаблон использован в одной из папок, удаление невозможно.');
            location.href = 's_cattempl.php?parent=<?=$parent?>';
        </script>
        <?php
        exit;
    } else {
        header("location:s_cattempl.php");
        exit;
    };
};

if (isset($add) || ($add2 == 1)) {


    $candel = ($candel == 1) ? 1 : 0;
    $canaddcat = ($canaddcat == 1) ? 1 : 0;
    $canaddbl = ($canaddbl == 1) ? 1 : 0;
    $canmoveto = ($canmoveto == 1) ? 1 : 0;
    $cancopyto = ($cancopyto == 1) ? 1 : 0;
    $canmovefrom = ($canmovefrom == 1) ? 1 : 0;
    $cancopyfrom = ($cancopyfrom == 1) ? 1 : 0;
    $canhide = ($canhide == 1) ? 1 : 0;
    $canedit = ($canedit == 1) ? 1 : 0;
    $caneditname = ($caneditname == 1) ? 1 : 0;
    $hidestr = ($hidestr == 1) ? 1 : 0;

    $s = '';
    if ($blocktypes) {
        foreach ($blocktypes as $bt) {
            $s .= " " . $bt . " ";
        };
    };
    unset($blocktypes);
    $blocktypes = $s;

    $s = '';

    if ($cattypes) {
        foreach ($cattypes as $bt) {
            $s .= " " . $bt . " ";
        };
    };
    unset($cattypes);
    $cattypes = $s;

    if (($parent > 0) && ($makecopy < 1)) {
        $q = "UPDATE $prname" . "_ctemplates SET name='$name',
												candel=$candel,
												canedit=$canedit,
												canaddcat=$canaddcat,
												canaddbl=$canaddbl,
												canmoveto=$canmoveto,
												cancopyto=$cancopyto,
												canmovefrom=$canmovefrom,
												cancopyfrom=$cancopyfrom,
												canhide=$canhide,
												caneditname=$caneditname,
												hidestructure=$hidestr,
												`key`='$key',
												`alias`='$alias',
												abc='$abc',
												filterfield='$filterfield',
												cattypes='$cattypes',
												sortfield='$sortfield',
												blocktypes='$blocktypes' WHERE id=$parent";
        mysql_query($q);
        mysql_query("DELETE FROM $prname" . "_cdatarel WHERE templid=$parent");
// ================================================================================================================================================================================
// Проверим на наличие всех старых полей.


        $q = mysql_query("describe  $prname" . "_c_$key");
        while ($arr_f = mysql_fetch_assoc($q)) {
            $_old_field[$arr_f[Field]] = $arr_f[Field];
            $last_fild = $arr_f[Field];
            if (!$_POST[old_name_f][$arr_f[Field]] && $arr_f[Field] !== 'id' && $arr_f[Field] !== 'parent' && $arr_f[Field] !== 'visible')
                mysql_query("ALTER TABLE `$prname" . "_c_$key` DROP `$arr_f[Field]` ");// удляем столбец.
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

                mysql_query("ALTER  TABLE `$prname" . "_c_$key` ADD `" . ${"nfieldk$i"} . "` $ptype $def_v");
            }

//================================================================================================================================================================================
// Переименование поля.
            mysql_query("ALTER TABLE `$prname" . "_c_$key` CHANGE `" . ${"old_nfieldk$i"} . "` " . ${"nfieldk$i"} . " $ptype $def_v");
            mysql_query("INSERT INTO $prname" . "_cdatarel (templid, datatkey, comment, `key`, attr, `default`, name, readonly, sort) VALUES ($parent, '" . ${"nfield$i"} . "', '" . ${"nfieldc$i"} . "', '" . ${"nfieldk$i"} . "', '" . ${"nfielda$i"} . "', '" . ${"nfieldd$i"} . "', '" . ${"nfieldn$i"} . "', '" . ((${"nfieldr$i"} > 0) ? 1 : 0) . "', '" . ${"nfieldsort$i"} . "')");
        };

        if ($add2 == 1) {
            header("location:s_cattempl_edit.php?parent=$parent#bottom_page");
            exit;
        }
        header("location:s_cattempl.php?parent=$parent");
        exit;
    } else {
        $q = "INSERT INTO $prname" . "_ctemplates (name, candel, canedit, caneditname, canaddcat, canaddbl, canmoveto, cancopyto, canmovefrom, cancopyfrom, hidestructure, blocktypes, cattypes, `key`, canhide, `alias`, abc, filterfield, sortfield) VALUES";
        $q .= "('$name', $candel, $canedit, $caneditname, $canaddcat, $canaddbl, $canmoveto, $cancopyto, $canmovefrom, $cancopyfrom, $hidestr, '$blocktypes', '$cattypes', '$key', $canhide, '$alias', '$abc', '$filterfield', '$sortfield')";
        mysql_query($q);
        $id = mysql_result(mysql_query("SELECT MAX(id) AS mid FROM $prname" . "_ctemplates"), 0, 0);
        $text_base = '';
        for ($i = 1; $i <= $nfields; $i++) {
// ================================================================================================================================================================================
// Поля .
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

            mysql_query("INSERT INTO $prname" . "_cdatarel (templid, datatkey, comment, `key`, attr, `default`, name, readonly, sort) VALUES ($id, '" . ${"nfield$i"} . "', '" . ${"nfieldc$i"} . "', '" . ${"nfieldk$i"} . "', '" . ${"nfielda$i"} . "', '" . ${"nfieldd$i"} . "', '" . ${"nfieldn$i"} . "', '" . ((${"nfieldr$i"} > 0) ? 1 : 0) . "', '" . ${"nfieldsort$i"} . "')");
        };
// ================================================================================================================================================================================
// Создание таблы.
        if ($key) mysql_query("CREATE TABLE IF NOT EXISTS `$prname" . "_c_$key` (`id` int(12) NOT NULL auto_increment,`parent` int(12),`visible` int(1),$text_base PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;");
// ================================================================================================================================================================================

        if ($add2 == 1) {
            header("location:s_cattempl_edit.php?parent=$id#bottom_page");
            exit;
        }


        header("location:s_cattempl.php?parent=$id");
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
    $alias = stripslashes($alias);
    $abc = stripslashes($abc);
    $filterfield = stripslashes($filterfield);
    for ($i = 1; $i <= $nfields; $i++) {
        ${"nfield$i"} = stripslashes(${"nfield$i"});
        ${"nfieldc$i"} = stripslashes(${"nfieldc$i"});
        ${"nfieldk$i"} = stripslashes(${"nfieldk$i"});
        ${"nfielda$i"} = stripslashes(${"nfielda$i"});
        ${"nfieldd$i"} = stripslashes(${"nfieldd$i"});
        ${"nfieldn$i"} = stripslashes(${"nfieldn$i"});
        ${"nfieldr$i"} = stripslashes(${"nfieldr$i"});
        ${"nfieldsort$i"} = stripslashes(${"nfieldsort$i"});
    };
    $s = '';
    if (is_array($blocktypes)) {
        foreach ($blocktypes as $bt) {
            $s .= " " . $bt . " ";
        };
    } else $s .= " " . $blocktypes . " ";
    unset($blocktypes);
    $blocktypes = $s;
    if (is_array($cattypes)) {
        foreach ($cattypes as $bt) {
            $s .= " " . $bt . " ";
        };
    } else $s .= " " . $cattypes . " ";
    unset($cattypes);
    $cattypes = $s;
};
if ($id > 0) {
    $q = "SELECT * FROM $prname" . "_ctemplates WHERE id=$id";
    $cr = mysql_fetch_array(mysql_query($q));
    if ($nfields == '') {
        if ($makecopy > 0) {
            $cr['name'] = 'Копия ' . $cr['name'];
            $cr['key'] = 'Копия ' . $cr['key'];
        };
        $res = mysql_query("SELECT * FROM $prname" . "_cdatarel WHERE templid=$id ORDER BY sort, `key`");
        $nfields = mysql_num_rows($res);
        $i = 0;
        while ($row = mysql_fetch_array($res)) {
            $i++;
            ${"nfield$i"} = $row['datatkey'];
            ${"nfieldc$i"} = $row['comment'];
            ${"nfieldk$i"} = $row['key'];
            ${"nfielda$i"} = $row['attr'];
            ${"nfieldd$i"} = $row['default'];
            ${"nfieldn$i"} = $row['name'];
            ${"nfieldr$i"} = $row['readonly'];
            ${"nfieldsort$i"} = $row['sort'];
        };
        $name = $cr['name'];
        $candel = $cr['candel'];
        $canedit = $cr['canedit'];
        $caneditname = $cr['caneditname'];
        $canaddcat = $cr['canaddcat'];
        $canaddbl = $cr['canaddbl'];
        $canmoveto = $cr['canmoveto'];
        $cancopyto = $cr['cancopyto'];
        $canmovefrom = $cr['canmovefrom'];
        $cancopyfrom = $cr['cancopyfrom'];
        $hidestr = $cr['hidestructure'];
        $canhide = $cr['canhide'];
        $key = $cr['key'];
        $alias = $cr['alias'];
        $blocktypes = $cr['blocktypes'];
        $cattypes = $cr['cattypes'];
        $abc = $cr['abc'];
        $filterfield = $cr['filterfield'];
        $sortfield = $cr['sortfield'];
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
        ${"nfieldn$i"} = ${"nfieldn" . ($i + 1)};
        ${"nfieldr$i"} = ${"nfieldr" . ($i + 1)};
        ${"nfieldsort$i"} = ${"nfieldsort" . ($i + 1)};
    };
    $nfields--;
};


$page = new stdClass();
$page->id = $id;
$page->name = $name;
$page->makecopy = $makecopy;
$page->nfields = $nfields;
$page->parent = $parent;
$page->key = $key;


$page->alias = $alias;
$page->abc = $abc;
$page->filterfield = $filterfield;
$page->sortfield = $sortfield;


$page->candel = $candel;
$page->caneditname = $caneditname;
$page->canedit = $canedit;
$page->canaddcat = $canaddcat;
$page->canaddbl = $canaddbl;
$page->canmoveto = $canmoveto;
$page->cancopyto = $cancopyto;
$page->canmovefrom = $canmovefrom;
$page->cancopyfrom = $cancopyfrom;
$page->canhide = $canhide;
$page->hidestr = $hidestr;

$blocktypes_ = "'" . implode("','", explode('  ', $blocktypes)) . "'";
$res = mysql_query("SELECT * FROM $prname" . "_btemplates WHERE visible = 1 ORDER BY FIELD(`key`, {$blocktypes_}) ASC, `name` ASC");
$i = 0;
while ($row = mysql_fetch_array($res)) {
    $page->tblock[$i] = new stdClass();
    $page->tblock[$i]->row = $row;
    if (strpos($blocktypes, " " . $row['key'] . " ") !== false) {
        $page->tblock[$i]->selected = 'selected';
    };
    $i++;
}

$cattypes_ = "'" . implode("','", explode('  ', $cattypes)) . "'";
$res = mysql_query("SELECT * FROM $prname" . "_ctemplates WHERE visible = 1 ORDER BY FIELD(`key`, {$cattypes_}) ASC, `name` ASC");
$i = 0;
while ($row = mysql_fetch_array($res)) {
    $page->tcats[$i] = new stdClass();
    $page->tcats[$i]->row = $row;
    if (strpos($cattypes, " " . $row['key'] . " ") !== false) {
        $page->tcats[$i]->selected = 'selected';
    };

    $i++;
}


?>


<?php
$res = mysql_query("SELECT * FROM $prname" . "_datatypes ORDER BY `key`");
$page->num_datatypes = mysql_num_rows($res);


if (mysql_num_rows($res) > 0) {
    if ($nfields > 0) {

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
            $text_base .= "`" . ${"nfieldk$i"} . "` $ptype ,";

            $page->nfield[$i]->nfield = ${"nfield$i"};
            $page->nfield[$i]->nfieldn = ${"nfieldn$i"};
            $page->nfield[$i]->nfieldk = ${"nfieldk$i"};

            $page->nfield[$i]->nfieldd = ${"nfieldd$i"};
            $page->nfield[$i]->nfieldc = ${"nfieldc$i"};
            $page->nfield[$i]->nfielda = ${"nfielda$i"};
            $page->nfield[$i]->nfieldsort = ${"nfieldsort$i"};
            $page->nfield[$i]->nfieldr = ${"nfieldr$i"};
            //$page->nfield[$i]->nfields = ${"nfields$i"};

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


        };
    };
}
?>
<?php
echo sprintt($page, 'templates/tcats/edit.html');
?>


<?php
if ($key) mysql_query("CREATE TABLE IF NOT EXISTS `$prname" . "_c_$key` (`id` int(12) NOT NULL auto_increment,`parent` int(12),`visible` int(1),$text_base PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;");


include "templates/includes/new_bottom.php";
?>