<?php
require_once "../../includes.php";
require_once "../../inc/libs/caching.php";


global $sql;
$sql = new Sql();
$sql->connect();

kick_unauth();

//сброс кеша
clear_cache(0);
if ($parent == 0) {
    $parenttr = substr($parent2, 2);
    $parent = $parenttr;
}
if ($del > 0) {
    $res = mysql_query("SELECT template FROM $prname" . "_categories WHERE id=$parent");
    $row = mysql_fetch_array($res);
    $res = mysql_query("SELECT candel FROM $prname" . "_ctemplates WHERE `key`='" . $row['template'] . "'");
    $rows = mysql_fetch_array($res);
    if (($rows['candel'] == 1) || $user_is_super) {
        adminlog(0, '', $parent,'','del_category');
        delete_category($parent);
//		print "delete from `$prname"."_c_$row[template]` where parent='$parent'";
        mysql_query("delete from `$prname" . "_c_$row[template]` where parent='$parent'");

        //перестраиваем дерево
        $tree = new tree();
        $tree->MakeTree();

        header("location:index.php");
    } else {
        header("location:index.php?error=1");
    };
    exit;
};

if ($blocks > 0) {
    header("location:block.php?cparent=$parent");
    exit;
};

if ($hide > 0) {
    $res = mysql_query("SELECT template FROM $prname" . "_categories WHERE id=$parent");
    $row = mysql_fetch_array($res);
    $res = mysql_query("SELECT canhide FROM $prname" . "_ctemplates WHERE `key`='" . $row['template'] . "'");
    @$row2 = mysql_fetch_array($res);
    if (($row2['canhide'] == 1) || $user_is_super) {
        mysql_query("UPDATE $prname" . "_categories SET visible=1-visible WHERE `enabled` = 1 AND id=$parent"); //скрывать/открывать можно только разрешенные папки
        adminlog(0, '', $parent,'','toggle_category');
                
        //перестраиваем дерево
        $tree = new tree();
        $tree->MakeTree();

        header("location:index.php");
    } else {
        header("location:index.php?error=2");
    };
    exit;
};

if ($enabled > 0) {
    $res = mysql_query("SELECT template FROM $prname" . "_categories WHERE id=$parent");
    $row = mysql_fetch_array($res);
    $res = mysql_query("SELECT canhide FROM $prname" . "_ctemplates WHERE `key`='" . $row['template'] . "'");
    @$row2 = mysql_fetch_array($res);
    if (($row2['canhide'] == 1) || $user_is_super) {
        mysql_query("UPDATE $prname" . "_categories SET visible=1-enabled, enabled=1-enabled WHERE id=$parent");

        //перестраиваем дерево
        $tree = new tree();
        $tree->MakeTree();

        header("location:index.php");
    } else {
        header("location:index.php?error=2");
    };
    exit;
};


if (($copy > 0) || ($move > 0)) {
    $res1 = mysql_query("SELECT template, parent FROM $prname" . "_categories WHERE id=$parent");
    $row1 = mysql_fetch_array($res1);
    $res = mysql_query("SELECT can" . (($copy > 0) ? 'copy' : 'move') . "to AS cando FROM $prname" . "_ctemplates WHERE `key`='" . $row1['template'] . "'");
    @$row = mysql_fetch_array($res);
    if ($row1['parent'] > 0) {
        $res1 = mysql_query("SELECT template FROM $prname" . "_categories WHERE id=" . $row1['parent']);
        $row1 = mysql_fetch_array($res);
        $res2 = mysql_query("SELECT can" . (($copy > 0) ? 'copy' : 'move') . "from AS cando FROM $prname" . "_ctemplates WHERE `key`='" . $row1['template'] . "'");
        if (@$row2 = mysql_fetch_array($res2)) {
            if ($row2['cando'] < 1) {
                $row['cando'] = 0;
            };
        };
    };
    if (($row['cando'] == 1) || $user_is_super) {
        $t = true;
        $to = $copy + $move;
        $p = $to;
        $parent = $parent2;
        do {
            $res = mysql_query("SELECT parent, id FROM $prname" . "_categories WHERE id=$p");
            $row = mysql_fetch_array($res);
            if ($row['id'] == $parent) $t = false;
        } while (($p = $row['parent']) > 0);
        if ($t) {
            $res1 = mysql_query("SELECT parent, id FROM $prname" . "_categories WHERE id=$parent");
            $row1 = mysql_fetch_array($res1);
            $res2 = mysql_query("SELECT parent, id, sort FROM $prname" . "_categories WHERE id=$to");
            $row2 = mysql_fetch_array($res2);
            if ($copymove == 0) {
                mysql_query("UPDATE $prname" . "_categories SET sort=sort+1 WHERE sort>=" . $row2['sort'] . " AND parent=" . $row2['parent']);
                $sort = $row2['sort'];
                $par = $row2['parent'];
            } elseif ($copymove == 1) {
                mysql_query("UPDATE $prname" . "_categories SET sort=sort+1 WHERE sort>" . $row2['sort'] . " AND parent=" . $row2['parent']);
                $sort = $row2['sort'] + 1;
                $par = $row2['parent'];
            } else {
                $res = mysql_query("SELECT MAX(sort) AS msort FROM $prname" . "_categories WHERE parent=" . $row2['id']);
                $sort = 1 + mysql_result($res, 0, 0);
                $par = $row2['id'];
            };
            if ($move > 0) {
                mysql_query("UPDATE $prname" . "_categories SET parent=$par, sort=$sort WHERE id=$parent");
                adminlog(0, '', $parent,'','move_category');
            } else {
                $parent = copy_category($parent, $par, $sort);
            };

            //перестраиваем дерево
            $tree = new tree();
            $tree->MakeTree();

            header("location:index.php");
            exit;
        } else {
            ?>
            <script language="JavaScript" type="text/javascript">
                alert('<?=($copy > 0) ? 'Копирование' : 'Перенос'?> в одну из дочерних папок <?=($copy > 0) ? 'невозможно' : 'невозможен'?>.');
                location.href = 'index.php';
            </script>
            <?php
            exit;
        };
    } else {
        header("location:index.php?error=" . (($copy > 0) ? 3 : 4));
        exit;
    };
};

if (($toup > 0) || ($todown > 0)) {
    $res = mysql_query("SELECT sort,parent FROM $prname" . "_categories WHERE id=$parent");
    $row = mysql_fetch_array($res);
    if ($toup > 0) {
        $res2 = mysql_query("SELECT id,sort FROM $prname" . "_categories WHERE sort<" . $row['sort'] . " AND parent=" . $row['parent'] . " ORDER BY sort DESC LIMIT 0,1");
    } else {
        $res2 = mysql_query("SELECT id,sort FROM $prname" . "_categories WHERE sort>" . $row['sort'] . " AND parent=" . $row['parent'] . " ORDER BY sort ASC LIMIT 0,1");
    };
    if ($row2 = mysql_fetch_array($res2)) {
        mysql_query("UPDATE $prname" . "_categories SET sort=" . $row2['sort'] . " WHERE id=$parent");
        mysql_query("UPDATE $prname" . "_categories SET sort=" . $row['sort'] . " WHERE id=" . $row2['id']);
        adminlog(0, '', $parent,'','move_category');
    };

    //перестраиваем дерево
    $tree = new tree();
    $tree->MakeTree();

    header("location:index.php");
    exit;
};

if (isset($add)) {
    $res = mysql_query("SELECT template FROM $prname" . "_categories WHERE id=$parent");
    $row = mysql_fetch_array($res);
    $res = mysql_query("SELECT canedit, canaddcat, caneditname FROM $prname" . "_ctemplates WHERE `key`='" . $row['template'] . "'");
    $row = mysql_fetch_array($res);
    //include "../inc/datafunc.php";
    if ($user_is_super) {
        $tinc = $templateinc;
        $templateinc = '';
        if (is_array($tinc)) {
            foreach ($tinc as $t) {
                $templateinc .= " $t ";
            };
        } else $templateinc = $tinc;
    };

    if ($id > 0) {
        if (($row['canedit'] == '1') || $user_is_super) {
            if (($row['caneditname'] == '1') || $user_is_super) $q = "name='$cat_name',"; else $q = '';
            $q = "UPDATE $prname" . "_categories SET $q
													`key`='$cat_key',
													template='$template',
													templateinc='$templateinc' WHERE id=$parent";
            mysql_query($q);
            $cr = mysql_fetch_array(mysql_query("SELECT template FROM $prname" . "_categories WHERE id=$id"));
            $q = "SELECT id FROM $prname" . "_ctemplates WHERE `key`='" . addslashes($cr['template']) . "'";
            $templid = mysql_result(mysql_query($q), 0, 0);
            $resd = mysql_query("SELECT * FROM $prname" . "_cdatarel WHERE templid=" . $templid . " ORDER BY sort, `key`");
            $i = 0;
            while ($rowd = mysql_fetch_array($resd)) {
                $i++;
                if (($row['readonly'] > 0) && (!$user_is_super)) continue;
                $s = "\$data = save_" . $rowd['datatkey'] . "('data$i');";
                eval($s);
//				$q = "UPDATE $prname"."_data SET data='".addslashes($data)."' WHERE relkey='".addslashes($rowd['key'])."' AND catid=$id";
//				mysql_query($q);
// ================================================================================================================================================================================
// Новое - Обновляем по tablename_fieldname
                $sql->query("UPDATE prname_c_" . addslashes($cr['template']) . " SET `" . addslashes($rowd['key']) . "` = '" . addslashes($data) . "' WHERE `parent`='$id'");
            };

            //перестраиваем дерево
            $tree = new tree();
            $tree->MakeTree();

            header("location:index.php");
            exit;
        } else $error = 5;
    } else {

        if (($row['canaddcat'] == '1') || $user_is_super) {
            $sort = 1 + mysql_result(mysql_query("SELECT MAX(sort) AS msort FROM $prname" . "_categories WHERE parent=$parent"), 0, 0);
            $q = "INSERT INTO $prname" . "_categories (name, `key`, sort, visible, parent, template, templateinc) VALUES ('$cat_name', '$cat_key', $sort, 0, $parent, '$template', '$templateinc')";
            mysql_query($q);

            mysql_query("INSERT INTO prname_c_$row[template] set `parent`='$cparent',`id`='$id'");

            $id = mysql_result(mysql_query("SELECT MAX(id) AS mid FROM $prname" . "_categories WHERE parent=$parent"), 0, 0);

            //перестраиваем дерево
            $tree = new tree();
            $tree->MakeTree();

            header("location:cat_edit.php?id=1&parent=$id");
            exit;
        } else $error = 6;
    };
    header("location:index.php?error=$error");
    exit;
};

$res = mysql_query("SELECT template FROM $prname" . "_categories WHERE id=$parent");
$row = mysql_fetch_array($res);
$res = mysql_query("SELECT canaddcat, canedit, caneditname FROM $prname" . "_ctemplates WHERE `key`='" . $row['template'] . "'");
$row = mysql_fetch_array($res);
if (($row['canaddcat'] != '1') && (!$user_is_super) && ($id < 1)) {
    header("location:index.php?error=6");
    exit;
};


$bodyinc = ' onload="javascript:document.form1.onsubmit=\'\';document.form1.add.disabled=false;"';

?>