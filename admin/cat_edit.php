<?php

require_once "../includes.php";
require_once "../inc/libs/caching.php";


global $sql;
$sql = new Sql();
$sql->connect();

kick_unauth();

if (!enabled_cat($_REQUEST['parent'])) {
    header("location:index.php");
    die();
}

//����� ����
clear_cache(0);

$parenttr = substr($parent2, 2);
if ($parent == 0) {
    $parent = intval($parenttr);
}


if ($blocks > 0) {
    header("location:block.php?cparent=$parent");
    exit;
};

/*

if ($del > 0) {
	$res = mysql_query("SELECT template FROM $prname"."_categories WHERE id=$parent");
	$row = mysql_fetch_array($res);
	$res = mysql_query("SELECT candel FROM $prname"."_ctemplates WHERE `key`='".$row['template']."'");
	$rows = mysql_fetch_array($res);
	if (($rows['candel'] == 1) || $user_is_super) {
		delete_category($parent);
//		print "delete from `$prname"."_c_$row[template]` where parent='$parent'";
		mysql_query("delete from `$prname"."_c_$row[template]` where parent='$parent'");
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
	$res = mysql_query("SELECT template FROM $prname"."_categories WHERE id=$parent");
	$row = mysql_fetch_array($res);
	$res = mysql_query("SELECT canhide FROM $prname"."_ctemplates WHERE `key`='".$row['template']."'");
	@$row2 = mysql_fetch_array($res);
	if (($row2['canhide'] == 1) || $user_is_super) {
		mysql_query("UPDATE $prname"."_categories SET visible=1-visible WHERE id=$parent");
		header("location:index.php");
	} else {
		header("location:index.php?error=2");
	};
	exit;
};

if (($copy > 0) || ($move > 0)) {
	$res1 = mysql_query("SELECT template, parent FROM $prname"."_categories WHERE id=$parent");
	$row1 = mysql_fetch_array($res1);
	$res = mysql_query("SELECT can".(($copy > 0)?'copy':'move')."to AS cando FROM $prname"."_ctemplates WHERE `key`='".$row1['template']."'");
	@$row = mysql_fetch_array($res);
	if ($row1['parent'] > 0) {
		$res1 = mysql_query("SELECT template FROM $prname"."_categories WHERE id=".$row1['parent']);
		$row1 = mysql_fetch_array($res);
		$res2 = mysql_query("SELECT can".(($copy > 0)?'copy':'move')."from AS cando FROM $prname"."_ctemplates WHERE `key`='".$row1['template']."'");
		if (@$row2 = mysql_fetch_array($res2)) {
			if ($row2['cando'] < 1) {$row['cando'] = 0;};
		};
	};
	if (($row['cando'] == 1) || $user_is_super) {
		$t = true;
		$to = $copy + $move;
		$p = $to;
		$parent = $parent2;
		do {
			$res = mysql_query("SELECT parent, id FROM $prname"."_categories WHERE id=$p");
			$row = mysql_fetch_array($res);
			if ($row['id'] == $parent) $t = false;
		} while (($p = $row['parent']) > 0);
		if ($t) {
			$res1 = mysql_query("SELECT parent, id FROM $prname"."_categories WHERE id=$parent");
			$row1 = mysql_fetch_array($res1);
			$res2 = mysql_query("SELECT parent, id, sort FROM $prname"."_categories WHERE id=$to");
			$row2 = mysql_fetch_array($res2);
			if ($copymove == 0) 
			{
				mysql_query("UPDATE $prname"."_categories SET sort=sort+1 WHERE sort>=".$row2['sort']." AND parent=".$row2['parent']);
				$sort = $row2['sort'];
				$par = $row2['parent'];
			} elseif ($copymove == 1) {
				mysql_query("UPDATE $prname"."_categories SET sort=sort+1 WHERE sort>".$row2['sort']." AND parent=".$row2['parent']);
				$sort = $row2['sort'] + 1;
				$par = $row2['parent'];
			} else {
				$res = mysql_query("SELECT MAX(sort) AS msort FROM $prname"."_categories WHERE parent=".$row2['id']);
				$sort = 1 + mysql_result($res, 0, 0);
				$par = $row2['id'];
			};
			if ($move > 0) {
				mysql_query("UPDATE $prname"."_categories SET parent=$par, sort=$sort WHERE id=$parent");
				
			} else {
				$parent = copy_category($parent, $par, $sort);
			};
			header("location:index.php");
			exit;
		} else {
			?>
			<script language="JavaScript" type="text/javascript">
				alert('<?=($copy>0)?'�����������':'�������'?> � ���� �� �������� ����� <?=($copy>0)?'����������':'����������'?>.');
				location.href = 'index.php';
			</script>
			<?php
			exit;
		};
	} else {
		header("location:index.php?error=".(($copy > 0)?3:4));
		exit;
	};
};

if (($toup > 0) || ($todown > 0)) {
	$res = mysql_query("SELECT sort,parent FROM $prname"."_categories WHERE id=$parent");
	$row = mysql_fetch_array($res);
	if ($toup > 0) {
		$res2 = mysql_query("SELECT id,sort FROM $prname"."_categories WHERE sort<".$row['sort']." AND parent=".$row['parent']." ORDER BY sort DESC LIMIT 0,1");
	} else {
		$res2 = mysql_query("SELECT id,sort FROM $prname"."_categories WHERE sort>".$row['sort']." AND parent=".$row['parent']." ORDER BY sort ASC LIMIT 0,1");
	};
	if ($row2 = mysql_fetch_array($res2)) {
		mysql_query("UPDATE $prname"."_categories SET sort=".$row2['sort']." WHERE id=$parent");
		mysql_query("UPDATE $prname"."_categories SET sort=".$row['sort']." WHERE id=".$row2['id']);
	};
	header("location:index.php");
	exit;
};

*/

if (isset($add)) {

    $cat_name = trim($cat_name);
    if (!strlen(trim($cat_key))) {
        $cat_key = strtolower(all::translit(trim($cat_name)));
    } else {
        $cat_key = strtolower(all::translit(trim($cat_key)));
    }
    if ($id == 1) $cat_key = 'index';

    $res = mysql_query("SELECT template FROM $prname" . "_categories WHERE id='$parent'");
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
													`alt_url`='" . trim($cat_alt_url) . "',
													template='$template',
													templateinc='$templateinc' WHERE id='$parent'";
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
// ����� - ��������� �� tablename_fieldname
                $sql->query("UPDATE prname_c_" . addslashes($cr['template']) . " SET `" . addslashes($rowd['key']) . "` = '" . addslashes($data) . "' WHERE `parent`='$id'");
            };
            //echo $id;

            check_update_cat_double($cat_key, $parent);
            adminlog(0, '', $id,'','change_category');

            //������������� ������
            $tree = new tree();
            $tree->MakeTree();


            header("location:cat_edit.php?id=1&parent=$id");
            exit;
        } else $error = 5;
    } else {

        if (($row['canaddcat'] == '1') || $user_is_super) {
            $sort = 1 + mysql_result(mysql_query("SELECT MAX(sort) AS msort FROM $prname" . "_categories WHERE parent='$parent'"), 0, 0);
            $q = "INSERT INTO $prname" . "_categories (name, `key`, `alt_url`, sort, visible, parent, template, templateinc) VALUES
('$cat_name', '$cat_key', '" . trim($cat_alt_url) . "', $sort, 0, '$parent', '$template', '$templateinc')";
            mysql_query($q);
            $id = mysql_insert_id();

            mysql_query("INSERT INTO prname_c_{$row['template']} set `parent`='$cparent',`id`='$id'");

            check_update_cat_double($cat_key, $id, $parent);

            //������������� ������
            $tree = new tree();
            $tree->MakeTree();
            
            adminlog(0, '', $id,'','add_category');

            header("location:cat_edit.php?id=1&parent=$id");
            exit;
        } else $error = 6;
    };
    header("location:index.php?error=$error");
    exit;
};

$res = mysql_query("SELECT template FROM $prname" . "_categories WHERE id='$parent'");
$row = mysql_fetch_array($res);
$res = mysql_query("SELECT canaddcat, canedit, caneditname, canaddbl FROM $prname" . "_ctemplates WHERE `key`='" . $row['template'] . "'");
$row = mysql_fetch_array($res);
if (($row['canaddcat'] != '1') && (!$user_is_super) && ($id < 1)) {
    header("location:index.php?error=6");
    exit;
};


$bodyinc = ' onload="javascript:document.form1.onsubmit=\'\';document.form1.add.disabled=false;"';

include "templates/includes/new_top.php";

if (intval($parent) < 1) {
    $parent = 0;
};
if ($id > 0) {
    $id = $parent;
};
if ($id > 0) {
    $cr = mysql_fetch_array(mysql_query("SELECT * FROM $prname" . "_categories WHERE id=$id"));
};


$page = new stdClass();
$page->id = $id;
$page->parent = $parent;

$page->path = tree_path($parent);

$page->user_is_super = $user_is_super;
$page->row = $row;
$page->cr = $cr;


$res = mysql_query("SELECT templateinc, template FROM $prname" . "_categories WHERE id='$parent'");
@$row1 = mysql_fetch_array($res);
if (($id > 0) && (!$user_is_super)) {

    $page->disabled_select = @mysql_result(mysql_query("SELECT name FROM $prname" . "_ctemplates WHERE `key`='" . addslashes($cr['template']) . "'"), 0, 0);


} else {
    $page->change_template = true;
    if ($row1['template'] != '')
        $row1['templateinc'] = @mysql_result(mysql_query("SELECT cattypes FROM $prname" . "_ctemplates WHERE `key`='" . $row1['template'] . "'"), 0, 0);


    $res = mysql_query("SELECT * FROM $prname" . "_ctemplates ORDER BY `name`");
    $i = 0;
    while ($row = mysql_fetch_array($res)) {
        $page->mtempl[$i] = new stdClass();
        $page->mtempl[$i]->row = $row;
        $page->mtempl[$i]->cr = $cr;
        $page->mtempl[$i]->user_is_super = $user_is_super;

        if ((strpos($row1['templateinc'], ' ' . $row['key'] . ' ') !== false) || $user_is_super) {
            if ($cr['template'] == '') {
                $cr['template'] = $row['key'];
            };
        } else {
            unset($page->mtempl[$i]);
        }
        $i++;
    };
};


if (!$user_is_super) {
    if ($id < 1) {
        $res = mysql_query("SELECT templateinc FROM $prname" . "_categories WHERE id='$parent'");
        $row = mysql_fetch_array($res);
        $cr['templateinc'] = $row['templateinc'];
    };
};

?>


<?php
if (($row['canedit'] == '1') || $user_is_super || ($id < 1)) {
    $page->b_save = true;
}
?>


<?php if (($row['canedit'] == '1') || $user_is_super || ($id < 1)) { ?>
    <?php if ($id > 0) {

        $page->edit_fields = true;
        //echo $page->edit_fields;
        //include "../inc/datafunc.php";
        ?>

        <?php

        $templid = mysql_result(mysql_query("SELECT id FROM $prname" . "_ctemplates WHERE `key`='" . addslashes($cr['template']) . "'"), 0, 0);
        $resd = mysql_query("SELECT * FROM $prname" . "_cdatarel WHERE templid=" . $templid . " ORDER BY sort, `key`");
        $i = 0;
        $ddft = '';

        $res_data = $sql->fetch_assoc($sql->query("select * from `prname_c_" . addslashes($cr['template']) . "` where `parent`= '$id'"), 0, 1);

        while ($rowd = mysql_fetch_array($resd)) {
            $i++;
            $page->field[$i] = new stdClass();

            $page->field[$i]->i = $i;
            $page->field[$i]->rowd = $rowd;
            $page->field[$i]->user_is_super = $user_is_super;
// ================================================================================================================================================================================
// ����� - �������� �������� �� �����	
            if (!isset($res_data[$rowd['key']])) {
                eval('$data = $rowd["default"];');
            } else $data = $res_data[$rowd['key']];
            $key_retro = addslashes($rowd['key']);
            $ddft .= " ," . addslashes($rowd['key']) . " = '$data'";

            if ((!$user_is_super) && ($rowd['readonly'] > 0)) {
                $page->field[$i]->readonly = true;
            }

            $s = "\$result =  input_" . $rowd['datatkey'] . '(\'data' . $i . '\', $rowd[\'attr\'], $data, $rowd[\'comment\']);';
            eval($s);

            $page->field[$i]->s = $result;
            //echo $page->field[$i]->s;
            ?>

        <?php };
        $pref = explode('_', $key_retro);
        //print $cr['template'];
        if (!$res_data[id]) {
            $sql->query("INSERT INTO prname_c_$cr[template] set `parent`='$parent' $ddft");

            //������������� ������
            $tree = new tree();
            $tree->MakeTree();

        }
        ?>

    <?php }; ?>
<?php }; ?>

<?php


$q = "SELECT `url` FROM prname_tree WHERE `id` = '$page->id' ";
$qrow = $sql->fetch_assoc($sql->query($q));
$page->urlforid = $qrow['url'];

$_GTC = clone($page);
include('templates/cats/edit.php');

//echo sprintt($page, 'templates/cats/edit.html');

?>

<?php
include "templates/includes/new_bottom.php";
?>
