<?php


require_once "../../includes.php";
require_once "../../inc/libs/caching.php";


global $sql;
$sql = new Sql();
$sql->connect();

kick_unauth();
//
//mysql_query("TRUNCATE TABLE $prname"."__templates");
//mysql_query("TRUNCATE TABLE $prname"."_tree");
//require_once( "../../inc/libs/sql.php");
//require_once("../../inc/class/tree.php");
//$tree = new tree();
//$tree->MakeTree();


session_start();
$hidestructure = $_SESSION['hidestructure'];


//unset($hidestructure);

//session_register("hidestructure");
//session_register("showallstructure");


if (isset($showallstruct)) $showallstructure = $showallstruct;

if (($unhide = intval($unhide)) < 0) {
    $hidestructure = $hidestructure . ' ';
    $hidestructure = str_replace(' ' . abs($unhide) . ' ', ' ', $hidestructure);
    trim($hidestructure);
    $hidestructure = ' ' . $hidestructure;
    //echo $hidestructure;
}


$hsa = explode(' ', $hidestructure);
do {
    if (isset($pos)) $hidestructure = substr($hidestructure, 0, $pos);
    $hs = substr($hidestructure, ($pos = strrpos($hidestructure, ' ')) + 1);
} while (((0 + $hs) > 0) && (!mysql_result(mysql_query("SELECT COUNT(id) FROM $prname" . "_categories WHERE id=$hs"), 0, 0)));


if (($unhide > 0) && (!in_array($unhide, $hsa))) {
    $hidestructure .= " $unhide";
    array_push($hsa, $hs = $unhide);
};


require_once "new_top.php";


$tree = tree_create(0, '', $showallstructure ? false : ($hs ? $hs : true));
//$tree = tree_create(0, '', $showallstructure);

if (intval($parent) < 1) {
    $parent = 0;
};


$_SESSION['hidestructure'] = $hidestructure;

$page = new stdClass();

$page->tree_count = tree_count($tree);
$page->user_is_super = $user_is_super;
$page->id = $id;


if (!$user_is_super) {

    $tids = array(0);
    foreach (${$tree . 'stree'} as $r) {
        $tids[] = (int)$r['id'];
    }
    $tids = implode(',', $tids);
    $res = mysql_query("SELECT * FROM $prname" . "_ctemplates tpl, $prname" . "_categories cats WHERE cats.id IN ({$tids}) AND cats.template=tpl.key ORDER BY cats.id");

};

if ((!$user_is_super) && (mysql_num_rows($res) > 0)) {
    mysql_data_seek($res, 0);
    $scaneditbl = '';
    while ($row = mysql_fetch_array($res)) {
        if ($row['canaddbl'] < 1) $scaneditbl .= ' && (id != ' . $row['id'] . ')';
    };
};
$page->scaneditbl = $scaneditbl;

if ((!$user_is_super) && (mysql_num_rows($res) > 0)) {
    mysql_data_seek($res, 0);
    $scandel = '';
    while ($row = mysql_fetch_array($res)) {
        if ($row['candel'] < 1) $scandel .= ' && (id != ' . $row['id'] . ')';
    };
};
$page->scandel = $scandel;

if ((!$user_is_super) && (mysql_num_rows($res) > 0)) {
    mysql_data_seek($res, 0);
    $scanaddcat = '';
    $scanaddcat .= ' && (id != 0)';
    while ($row = mysql_fetch_array($res)) {
        if ($row['canaddcat'] < 1) $scanaddcat .= ' && (id != ' . $row['id'] . ')';
    };
};
$page->scanaddcat = $scanaddcat;

if ((!$user_is_super) && (mysql_num_rows($res) > 0)) {
    mysql_data_seek($res, 0);
    $scanedit = '';
    while ($row = mysql_fetch_array($res)) {
        if ($row['canedit'] < 1) $scanedit .= ' && (id != ' . $row['id'] . ')';
    };
};
$page->scanedit = $scanedit;

if ((!$user_is_super) && (mysql_num_rows($res) > 0)) {
    mysql_data_seek($res, 0);
    $scanhide = '';
    $scanenabled = '';
    while ($row = mysql_fetch_array($res)) {
        if ($row['canhide'] < 1) {
            $scanhide .= ' && (id != ' . $row['id'] . ')';
            $scanenabled .= ' && (id != ' . $row['id'] . ')';
        }
    };
};
$page->scanhide = $scanhide;
$page->scanenabled = $scanenabled;

if ((!$user_is_super) && (mysql_num_rows($res) > 0)) {
    mysql_data_seek($res, 0);
    $scancopy = '';
    while ($row = mysql_fetch_array($res)) {
        $res2 = mysql_query("SELECT tpl.cancopyto FROM $prname" . "_ctemplates tpl, $prname" . "_categories cats WHERE cats.template=tpl.key AND cats.id=" . $row['id']);
        $row2 = mysql_fetch_array($res2);
        if ($row2['cancopyto'] < 1) $scancopy .= ' && (id != ' . $row['id'] . ')';
    };
};
$page->scancopy = $scancopy;
if (tree_count($tree) < 2) {
    $scancopy_22 = ' && false';
};
$page->scancopy_22 = $scancopy_22;


if ((!$user_is_super) && (mysql_num_rows($res) > 0)) {
    mysql_data_seek($res, 0);
    $scanmove = '';
    while ($row = mysql_fetch_array($res)) {
        $res2 = mysql_query("SELECT tpl.canmoveto FROM $prname" . "_ctemplates tpl, $prname" . "_categories cats WHERE cats.template=tpl.key AND cats.id=" . $row['id']);
        $row2 = mysql_fetch_array($res2);
        if ($row2['canmoveto'] < 1) $scanmove .= ' && (id != ' . $row['id'] . ')';
    };
};
$page->scanmove = $scanmove;
if (tree_count($tree) < 2) {
    $scanmove_22 = ' && false';
};
$page->scanmove_22 = $scanmove_22;


$page->error = $error;
$page->prerror = $prerror[$error];

$page->parent = $parent;

$page->user_is = user_is('super');
?>

<?php

$l = array();
$td = 0;
$count = 0;

$ii = 0;
while ($r = tree_next($tree)) {

    //права
    if (!enabled_cat($r['id'])) {
        $r['disabled'] = 1;
    }


    $page->item[$ii] = new stdClass();
    $page->item[$ii]->r = $r;
    $page->item[$ii]->td = $td;

    $count++;

    $page->item[$ii]->count = $count;


    $l[$r['lev']] = 'closed';
    //for ($i = 0; $i < (tree_count($tree) - tree_pos($tree)); $i++) {
    $i = 0;
    while ($r2 = tree_item($i++, $tree))
        if ($r2['parent'] == $r['parent']) $l[$r['lev']] = 'opened';

    for ($i = 1; $i <= $r['lev']; $i++) {

        $td++;
        $page->item[$ii]->item2[$i] = new stdClass();
        $page->item[$ii]->item2[$i]->td = $td;
        $r2 = tree_item(0, $tree);
        $show_plus = (!$showallstructure) && ($r['hidestructure'] > 0) && ($i == $r['lev']) && (($r['catcount'] > 0) || ($hs == $r['id']) || ($r2['lev'] > $r['lev']));
        $show_minus = $show_plus && (in_array($r['id'], $hsa));
        $show_plus = $show_plus && (!in_array($r['id'], $hsa));

        $page->item[$ii]->item2[$i]->show_plus = $show_plus;
        $page->item[$ii]->item2[$i]->show_minus = $show_minus;
        $page->item[$ii]->item2[$i]->r = $r;

        if ($i < $r['lev']) {
            if ($l[$i] == 'closed') {
                $page->item[$ii]->item2[$i]->l = 'spacer';
            } else {
                $page->item[$ii]->item2[$i]->l = 'm4';
            }
        } else {
            if ($l[$i] == 'closed') {
                $page->item[$ii]->item2[$i]->l = 'm2';
            } else {
                $page->item[$ii]->item2[$i]->l = 'm1';
            }
        };

        if ($show_plus || $show_minus) {
            $page->item[$ii]->item2[$i]->show_plus_minus = true;
        }

    }

    if (($r2['lev'] > $r['lev']) || ($r['catcount'] > 0)) {
        $page->item[$ii]->r2_lev = 'ico-part';
        $cl = 't1';
    } else {
        $page->item[$ii]->r2_lev = 'ico-page';
        $cl = 't2';
        if (!$r['insertblocks']) {
            $page->item[$ii]->r2_lev .= '-e';
        };
    };
    $page->item[$ii]->cl = $cl;
    $page->item[$ii]->user_is_super = $user_is_super;
    $page->item[$ii]->parent = $parent;

    $page->item[$ii]->colspan = 100 - $r['lev'];

    //echo $page->item[$ii]->r[visible];
    $ii++;
};
?>

<?php

$text = sprintt($page, '../templates/cats/list.html');
$text = str_replace('<!--base_url//-->', '../', $text);
echo $text;
?>


<?php
//include "templates/includes/bottom.php";
include "new_bottom.php";
?>
