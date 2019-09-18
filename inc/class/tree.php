<?php

class Tree
{

    function BeginTree()
    {
        global $sql;
        global $dtimer;


        //лочим очередь
        $q = sprintf("INSERT INTO prname_tree_lock SET id=1, locktime=NOW(), queue=0 ON DUPLICATE KEY UPDATE queue=queue+1;");
        $sql->query($q);

        $q = sprintf("SELECT queue FROM prname_tree_lock WHERE id = 1 AND locktime > NOW() - INTERVAL 1 MINUTE");
        $cnt = intval($sql->one_record($q));

        if ($cnt == 0) return true;

        return false;

//		$q = "SELECT count(id) FROM prname_tree WHERE writeend = 1 ";
//		if (!$sql->one_record($q))	return true;

//		return false;

    }

    /*	function BeginTree()		{
            global $sql;

            $q = "SELECT count(id) FROM prname_tree WHERE writeend = 1 ";
            if (!$sql->one_record($q))	return true;

        }
    */
    /*
    function MakeTree()
    {
            global $sql;
            global $dtimer;
            if ($this->BeginTree() == false) return;

            //создаем TMP таблицу
            $sql->query("TRUNCATE TABLE `prname_tree_tmp`;");
    /*
            $sql->query("CREATE TABLE IF NOT EXISTS `prname_tree_tmp` (
      `id` bigint(20) NOT NULL DEFAULT '0',
      `parent` bigint(20) NOT NULL DEFAULT '0',
      `level` tinyint(4) NOT NULL DEFAULT '0',
      `sort` bigint(20) NOT NULL DEFAULT '0',
      `visible` tinyint(4) NOT NULL DEFAULT '0',
      `left_key` bigint(20) NOT NULL DEFAULT '0',
      `right_key` bigint(20) NOT NULL DEFAULT '0',
      `name` varchar(255) NOT NULL DEFAULT '',
      `key` varchar(255) NOT NULL DEFAULT '',
      `template` varchar(255) NOT NULL DEFAULT '',
      `url` varchar(255) NOT NULL DEFAULT '',
      `writeend` tinyint(4) NOT NULL DEFAULT '0',
      KEY `id` (`id`),
      KEY `parent` (`parent`),
      KEY `left_key` (`left_key`),
      KEY `right_key` (`right_key`),
      KEY `level` (`level`)
    ) ENGINE=MyISAM DEFAULT CHARSET=cp1251;");
    *

            //$q = "TRUNCATE TABLE prname_tree ";
            //$sql->query($q);

            $q = "INSERT INTO prname_tree_tmp (id, parent, name, level, left_key, right_key, `key`, template, visible, enabled)
				VALUES (1, 0, 'Главная страница', 0, 1, 2, 'main', 'index', 1, 1) ";
            $sql->query($q);


            //собираем коллекцию ID, у которых есть дети
            $q = sprintf("SELECT DISTINCT parent FROM prname_categories;");
            $haschild = $sql->fetch_assoc_arr($sql->query($q), 'parent');

            $this->GetTree(1,0,'', $haschild);

//		if ($dtimer)
//			$dtimer->tick('GetTree(1)');

            //$this->WriteUrlTree();

            $this->EndTree();//завершаем нашу "транзакцию"
//		if ($dtimer)
//			$dtimer->tick('EndTree');
        }*/


    function MakeTree()
    {
        global $sql;
        if ($this->BeginTree() == false) return;

        // запрограммирован новый механизм генерации дерева сайта. теперь работает очень быстро
        $this->alltree();
        $this->get_recurs(0, $this->tree);
        $this->calc_recurs($this->tree[0], $this->tree[0]['left_key']);

        foreach ($this->ops as $opfield => $opops) {
            switch ($opfield) {
                case 'left_key':
                case 'right_key':
                    foreach ($opops as $opval => $opids) {
                        $opids = implode(',', $opids);
                        $sql->query("UPDATE prname_tree SET {$opfield} = {$opfield} + {$opval} WHERE id IN({$opids})");
                    }
                    break;
                case 'insert':
                    foreach ($opops as $item) {
                        $sql->query(
                            "INSERT INTO prname_tree (id, parent, level, sort, visible, enabled, left_key, right_key, name, `key`, template, url, writeend)
						SELECT id, parent, {$item['level']}, sort, visible, enabled, {$item['left_key']}, {$item['right_key']}, name, `key`, template, `key`, 1 FROM prname_categories WHERE id = {$item['id']}"
                        );
                    }
                    break;
                case 'update':
                    $checkurl = '';
                    foreach ($opops as $opid => $opdata) {
                        if (isset($opdata['url']) && count($opdata) == 1 && (!$checkurl || strpos($opdata['url'], $checkurl) === false)) continue;
                        if ($opdata['key'] || $opdata['parent']) {
                            $checkurl = $opdata['url'];
                        }
                        $update = array();
                        foreach ($opdata as $opfield => $opval) {
                            $update[] = "`{$opfield}` = '" . $sql->escape_string($opval) . "'";
                        }
                        $update = implode(',', $update);
                        $sql->query("UPDATE prname_tree SET {$update} WHERE id = {$opid}");
                        $q = sprintf(" UPDATE prname_burl SET `parent_url` = '%s' WHERE `parent` = %s ", $opdata['url'], $opid);
                        $sql->query($q);
                    }
                    break;
            }
        }
        $sql->query("DELETE t.* FROM prname_tree t LEFT JOIN prname_categories c ON c.id = t.id WHERE c.id IS NULL");

        $this->EndTree();//завершаем нашу "транзакцию"
    }


    private $tree = array();
    private $alltree = array();
    private $ops = array('left_key' => array(), 'right_key' => array(), 'insert' => array(), 'update' => array());

    function alltree()
    {
        global $sql;
        $res = $sql->query("SELECT c.id, c.parent, c.name, t.id AS tree_id, t.left_key, t.right_key, t.level,
		t.parent AS tparent, t.name AS tname, c.template, t.template AS ttemplate, c.`key`, t.`key` AS tkey,
		c.visible, t.visible AS tvisible, c.enabled, t.enabled AS tenabled, c.sort, t.sort AS tsort, c.key as url, c.alt_url as alt_url, t.url as turl -- поля для проверки
		FROM prname_categories c LEFT JOIN prname_tree t ON t.id = c.id ORDER BY c.parent ASC, c.sort ASC, c.id ASC");
        while ($row = $sql->fetch_assoc($res)) {
            if ($row['tree_id']) {
                // если раздел в дереве уже создан
                if ($row['parent'] != $row['tparent']) {
                    $this->ops['update'][$row['id']]['parent'] = $row['parent'];
                }
                if ($row['name'] != $row['tname']) {
                    $this->ops['update'][$row['id']]['name'] = $row['name'];
                }
                if ($row['template'] != $row['ttemplate']) {
                    $this->ops['update'][$row['id']]['template'] = $row['template'];
                }
                if ($row['key'] != $row['tkey']) {
                    $this->ops['update'][$row['id']]['key'] = $row['key'];
                }
                if ($row['visible'] != $row['tvisible']) {
                    $this->ops['update'][$row['id']]['visible'] = $row['visible'];
                }
                if ($row['enabled'] != $row['tenabled']) {
                    $this->ops['update'][$row['id']]['enabled'] = $row['enabled'];
                }
                if ($row['sort'] != $row['tsort']) {
                    $this->ops['update'][$row['id']]['sort'] = $row['sort'];
                }
                if ($row['parent'] == 0) {
                    $this->ops['update'][$row['id']]['url'] = $row['url'] = '';
                } elseif (!empty($row['url'])) {
                    if ($row['url'] . '/' != $row['turl']) {
                        $this->ops['update'][$row['id']]['url'] = $row['url'] . '/';
                    }
                }
            }
            unset($row['tname'], $row['tparent'], $row['ttemplate'], $row['tkey'], $row['tvisible'], $row['tenabled'], $row['tsort'], $row['turl']);

            $this->alltree[$row['parent']][$row['id']] = $row;
        }
    }

    function get_recurs($pid, &$tree)
    {
        if (isset($this->alltree[$pid]))
            foreach ($this->alltree[$pid] as $row) {
                $row['children'] = array();
                $tree[$pid]['children'][(string)$row['id']] = $row;
                $this->get_recurs($row['id'], $tree[$pid]['children']);
            }
    }

    function calc_recurs(&$tree, $left_key, $parent_url = '')
    {
        $subchilds = 0;
        foreach ($tree['children'] as $id => $params) {
            $left_key_tmp = $left_key + 1;
            if ($params['parent'] == 0) {
                $params['url'] = '';
                if ($params['level'] != 0) {
                    $this->ops['update'][$id]['level'] = $params['level'] = 0;
                }
                if (!isset($params['level'])) $params['level'] = 0;
            } else {
                if (!isset($params['level'])) $params['level'] = $tree['level'] + 1;
                if ($params['level'] != $tree['level'] + 1) {
                    $this->ops['update'][$id]['level'] = $params['level'] = $tree['level'] + 1;
                }
                if (!intval($params['alt_url'])) {
                    if (!empty($params['key'])) {
                        $this->ops['update'][$id]['url'] = ($params['url'] = $parent_url . $params['key'] . '/');
                    } else {
                        $this->ops['update'][$id]['url'] = ($params['url'] = $parent_url . $params['id'] . '/');
                    }
                } elseif (substr($params['url'], -1) !== '/') {
                    $this->ops['update'][$id]['url'] = $params['url'] .= '/';
                }
            }
            //ob_start();
            $subchilds += $childs = $this->calc_recurs($params, $left_key_tmp, $params['url']);
            $right_key_tmp = $left_key_tmp + ($childs * 2) + 1;
            //$html = ob_get_clean();
            $this->show_item($params, $left_key_tmp == $params['left_key'], $left_key_tmp, $right_key_tmp == $params['right_key'], $right_key_tmp/*, $html*/);

            $left_key = $right_key_tmp;
        }
        return count($tree['children']) + $subchilds;
    }

    function show_item(&$item, $left_key_right, $left_key, $right_key_right, $right_key, $html = '')
    {
        if (!$item['tree_id']) {
            $item['left_key'] = $left_key;
            $item['right_key'] = $right_key;
            $this->ops['insert'][] = $item;
        } else {
            if (!$left_key_right) {
                $this->ops['left_key'][$left_key - $item['left_key']][] = $item['id'];
            }
            if (!$right_key_right) {
                $this->ops['right_key'][$right_key - $item['right_key']][] = $item['id'];
            }
        }
        /* визуальное отображение дерева сайта, с пометками необходимых исправлений
        echo
        str_repeat('&nbsp;', $item['level'] * 4) .
            sprintf('<span style="color:%s">%d</span> ? %d{%d: %s [%d]}<span style="color:%s">%d</span> ? %d<br>',
                $left_key_right ? 'green' : 'red',
                $item['left_key'],
                $left_key,
                $item['id'],
                $item['name'],
                $item['parent'],
                $right_key_right ? 'green' : 'red',
                $item['right_key'],
                $right_key
            ) . $html;*/
    }


    static function tree_url($parent = '')
    {
        global $control;
        global $config;
        global $sql;

        $q = $sql->query("select p2.* from prname_tree p1, prname_tree p2 where p2.left_key<=p1.left_key and p2.right_key>=p1.right_key and p1.id='" . ($parent ? $parent : $control->cid) . "' ORDER BY p2.left_key");
        $array = array();
        while ($arr = mysql_fetch_assoc($q)) {
            $array[$arr['id']] = new stdClass();
            $array[$arr['id']]->id = $arr['id'];
            $array[$arr['id']]->name = $arr['name'];
            $array[$arr['id']]->template = $arr['template'];
            $array[$arr['id']]->url = $arr['url'] == '/' ? $config['server_url'] : $config['server_url'] . $arr['url'];
        }

        if (isset($array)) return $array;
    }

    function EndTree()
    {
        global $sql;

        $q = "OPTIMIZE TABLE prname_tree";
        $sql->query($q);

        $q = "TRUNCATE TABLE prname_tree_lock ";
        $sql->query($q);
    }

    function WriteUrlTree()
    {
        global $sql;

        return;

        $q = "SELECT id, `key`, left_key, right_key FROM prname_tree_tmp ORDER BY id";
        $res = $sql->query($q);
        while ($str = $sql->fetch_array($res)) {
            $id = $str['id'];
            $key = $str['key'];
            $left_key = $str['left_key'];
            $right_key = $str['right_key'];

            $url = '';

            $q = "SELECT id, `key`, level FROM prname_tree_tmp WHERE left_key <= '$left_key' AND right_key >= '$right_key' ORDER BY left_key ";
            $res2 = $sql->query($q);
            $i = 0;
            while ($str2 = $sql->fetch_array($res2)) {
                $tmp_id = $str2['id'];
                $tmp_key = $str2['key'];
                if ($i > 1)
                    $url .= '/';
                if ($tmp_key <> '') {
                    $url .= $tmp_key;
                } else {
                    $url .= $tmp_id;
                }
                $i++;
            }
            $url .= '/';
            $url = substr($url, 4);
            $q = "UPDATE prname_tree_tmp SET url = '$url' WHERE id = '$id' ";
            $sql->query($q);
        }

    }

    function GetTree($parent, $old_parent = 0, $purl = '', &$haschild = NULL)
    {
        global $sql;

        $q = "SELECT * FROM prname_categories WHERE parent = '$parent' ORDER BY sort ";
        $res = $sql->query($q);
        if ($sql->num_rows($res) > 0) {
            while ($str = $sql->fetch_assoc($res)) {
                $id = $str['id'];
                $name = $str['name'];
                $sort = $str['sort'];
                $key = $str['key'];
                $url = strlen($key) ? $purl . $key . '/' : $purl . $id . '/';

				if (intval($str['alt_url']))
                    $url = strlen($key) ? $key.'/' : $id.'/';

                $template = $str['template'];
                $visible = $str['visible'];
                $enabled = $str['enabled'];
//				echo " $id $name <br>";

                $q = "SELECT level, left_key, right_key FROM prname_tree_tmp WHERE id = '$parent' ";
                $str1 = $sql->fetch_array($sql->query($q));
                $level = $str1['level'];
                $left_key = $str1['left_key'];
                $right_key = $str1['right_key'];

                $q = "UPDATE prname_tree_tmp SET left_key = left_key + 2, right_key = right_key + 2 WHERE left_key > $right_key ";
                $sql->query($q);

                $q = " UPDATE prname_tree_tmp SET right_key = right_key + 2 WHERE right_key >= '$right_key' AND left_key < '$right_key' ";
                $sql->query($q);

                $q = "INSERT INTO prname_tree_tmp SET left_key = $right_key, right_key = $right_key + 1, level = $level + 1,
				 id = '$id', name = '" . $sql->escape_string($name) . "', parent = '$parent', sort = '$sort', `key` = '$key',
				 `url`='$url', template = '$template', visible = '$visible' , enabled=$enabled";
                $sql->query($q);
                
                $q = sprintf(" UPDATE prname_burl SET `parent_url` = '%s' WHERE `parent` = %s ", $url, $id);
                $sql->query($q);

                if (is_array($haschild) && !isset($haschild[$id])) {
                    //заведомо знаем, что детей у этой ветки нет - не дергаем мускул понапрасну
                    continue;
                }

                $this->GetTree($id, $parent, $url, $haschild);
            }
        }

    }

    static function tree_all($id = '1', $level = '')
    {
        global $control;
        global $config;
        global $sql;
        $q = $sql->query("SELECT p1.*,p2.level as parent_level FROM prname_tree p1,prname_tree p2 where p1.left_key > p2.left_key and p1.right_key < p2.right_key and p2.id='$id'" . ($level !== '' ? " and p1.level<=$level" : "") . " ORDER BY p1.left_key");
        $i = 0;
        $a = new stdClass();
        $b = $c = array();
        $allid = array();
        while ($b = mysql_fetch_assoc($q)) {
            if (
                $b['level'] == $b[parent_level] + 1
            ) {
                $a->item[$b['id']] = new stdClass();
                if ($control->cid == $b[id]) $a->item[$b['id']]->link = 'nolink'; else $a->item[$b['id']]->link = 'link';
                $a->item[$b['id']]->name = $b[name];
                $a->item[$b['id']]->id = $b[id];
                $a->item[$b['id']]->parent = $b['parent'];
                $a->item[$b['id']]->level = $b['level'];
                $a->item[$b['id']]->url = $config['server_url'] . $b['url'];

                $a->item[$b['id']]->template = $b['template'];
                $a->item[$b['id']]->key = $b['key'];
                $a->item[$b['id']]->i = $i + 1;
                $a->item[$b['id']]->visible = $b['visible'];
                $a->item[$b['id']]->enabled = $b['enabled'];
                $a->item[$b['id']]->class = '';//$i==0?"Первый раздел всего меню сайта":"Первый раздел ветки";
                $level = $b['level'];
                $last_id = $b['id'];
                $c[$b['level']] =& $a->item[$b['id']];
                $allid[$b['id']] = $b['id'];
            } else {
                if (!isset($c[$b['level'] - 1]))
                    $c[$b['level'] - 1] = new stdClass();
                $c[$b['level'] - 1]->item[$b['id']] = new stdClass();

                if ($control->cid == $b[id]) {
                    for ($l = 0; $l < $b['level']; $l++)
                        if ($allid[$control->parents[$b['level'] - $l]])
                            $c[$b['level'] - $l]->link = 'stronglink';
                    $c[$b['level'] - 1]->item[$b['id']]->link = 'nolink';
                } else
                    $c[$b['level'] - 1]->item[$b['id']]->link = 'link';

                $c[$b['level'] - 1]->item[$b['id']]->name = $b[name];
                $c[$b['level'] - 1]->item[$b['id']]->id = $b['id'];
                $c[$b['level'] - 1]->item[$b['id']]->parent = $b['parent'];
                $c[$b['level'] - 1]->item[$b['id']]->level = $b['level'];
                $c[$b['level'] - 1]->item[$b['id']]->url = $config['server_url'] . $b['url'];

                $c[$b['level'] - 1]->item[$b['id']]->template = $b['template'];
                $c[$b['level'] - 1]->item[$b['id']]->key = $b['key'];
                $c[$b['level'] - 1]->item[$b['id']]->i = $i + 1;
                $c[$b['level'] - 1]->item[$b['id']]->visible = $b['visible'];
                $c[$b['level'] - 1]->item[$b['id']]->enabled = $b['enabled'];
                $c[$b['level'] - 1]->item[$b['id']]->class = "";//"Последний в своей ветке";
                if ($level > $b['level']) $c[$b['level']]->class = "class='open'";
                if ($level == $b['level']) $c[$level]->class = '';//Раздел не имеет вложений он не первый но и не последний
                if ($level < $b['level']) $c[$level]->class = "class='open'";//'"Этот раздел имеет вложение '.$c[$level]->class;
                $level = $b['level'];
                $last_id = $b['id'];
                $allid[$b['id']] = $b['id'];
                $c[$b['level']] =& $c[$b['level'] - 1]->item[$b['id']];
            }

            $i++;
        }
        tree::delete_hide_tree($a);
        return $a;
    }

    static function delete_hide_tree(&$arr)//Функция удаляет невидимые ветки. Вызывается в tree_all
    {
        if (is_array($arr->item))
            foreach ($arr->item as $item) {
                if ($item->visible == 0) {
                    unset ($arr->item[$item->id]);
                } else {
                    if ($item->item) {
                        self::delete_hide_tree($item);
                    }
                }
            }
    }

    static function GetUrl($id)
    {
        global $sql;

        $q = "SELECT url FROM prname_tree WHERE id = '$id'";
        $row = $sql->fetch_assoc($sql->query($q));
        return substr($row['url'], 0, strlen($row['url']) - 1);

    }

    function GetParents($id)
    {
        global $sql;

        $parents = array();

        $q = "SELECT left_key, right_key FROM prname_tree WHERE id = '$id' ";
        $str = $sql->fetch_array($sql->query($q));
        $left_key = $str['left_key'];
        $right_key = $str['right_key'];

        $q = "SELECT id, `key`, level FROM prname_tree WHERE left_key <= '$left_key' AND right_key >= '$right_key' ORDER BY left_key ";
        $res2 = $sql->query($q);
        $i = 0;
        while ($str2 = $sql->fetch_array($res2)) {
            $parents[$i] = $str2['id'];
            $i++;
        }

        return $parents;
    }

    static function GetNode($id, $depth = 1000000, $type = 'full')
    {
        global $sql;
        global $control;

        $rootID = $id;
        $page = new stdClass();

        $q = "SELECT left_key, right_key, level FROM prname_tree WHERE id = '$id' ";
        $str = $sql->fetch_array($sql->query($q));
        $left_key = $str['left_key'];
        $right_key = $str['right_key'];
        $level = $str['level'];


        $q = "  SELECT pr1.id as id, pr1.name as title, pr1.url as url, pr1.level as level, pr2.id as parent, pr2.name, pr2.level as parentlevel, pr1.template as template FROM prname_tree pr1, prname_tree pr2 WHERE pr1.right_key > $left_key AND pr1.left_key < $right_key AND pr1.level < '" . ($level + $depth + 1) . "' AND pr2.left_key <= pr1.left_key AND pr2.right_key >= pr1.right_key   AND pr1.visible = 1 ORDER BY pr1.left_key, pr1.sort, pr2.level  ";

        if ($type == 'formoder')
            $q = "  SELECT pr1.id as id, pr1.name as title, pr1.url as url, pr1.level as level, pr2.id as parent, pr2.name, pr2.level as parentlevel, pr1.template as template FROM prname_tree pr1, prname_tree pr2 WHERE pr1.right_key > $left_key AND pr1.left_key < $right_key AND pr1.level < '" . ($level + $depth + 1) . "' AND pr2.left_key <= pr1.left_key AND pr2.right_key >= pr1.right_key ORDER BY pr1.left_key, pr1.sort, pr2.level  ";

        $res = $sql->query($q);
        $arr = array();
        while ($str = $sql->fetch_array($res)) {
            $id = $str['id'];
            $title = $str['title'];
            $parent = $str['parent'];
            $level = $str['level'];
            $template = $str['template'];
            $url = $str['url'];

            $url = substr($url, 0, strlen($url) - 1);


            $parentlevel = $str['parentlevel'];
            if (!isset($arr[$id]['parents'])) {
                $arr[$id]['parents'] = array();
            }
            $arr[$id]['id'] = $id;
            $arr[$id]['level'] = $level;
            $arr[$id]['title'] = $title;
            $arr[$id]['template'] = $template;
            $arr[$id]['url'] = $url;
            array_push($arr[$id]['parents'], array('level' => $parentlevel, 'parent' => $parent));
        }

//		print_r($arr);


        if (count($arr) > 0) {

            $contr_parents = $control->parents;
            unset($contr_parents[count($contr_parents) - 1]);

            foreach ($arr as $one_arr) {
                $level = $one_arr['level'];
                $link = 'link';
                if ($one_arr['id'] == $control->cid) {
                    $link = 'nolink';
                    if ($control->bid > 0) {
                        $link = 'stronglink';
                    }
                }

                if (count($contr_parents) > 0) {
                    foreach ($contr_parents as $one_parent) {
                        if ($one_parent == $one_arr['id']) {
                            $link = 'stronglink';
                        }
                    }
                }

                if (count($one_arr['parents']) > 0) {

                    $strok = '';
                    foreach ($one_arr['parents'] as $parent) {
                        if ($type == 'full') {
                            $strok .= 'item' . $parent['level'] . '[' . $parent[parent] . ']->';
                        }
                        if (in_array($type, array('formap', 'formoder'))) {
                            $strok .= 'item[' . $parent[parent] . ']->';
                        }

                    }

                    $_strok = substr($strok, 0, -2);
                    eval('$' . $_strok . ' = new stdClass();');
                    eval("\$" . $strok . "title = '" . $one_arr['title'] . "'; ");
                    eval("\$" . $strok . "url = '<!--base_url//-->" . $one_arr['url'] . "'; ");
                    eval("\$" . $strok . "link = '" . $link . "'; ");
                    eval("\$" . $strok . "level = '" . $level . "'; ");
                    eval("\$" . $strok . "id = '" . $one_arr['id'] . "'; ");
                    eval("\$" . $strok . "template = '" . $one_arr['template'] . "'; ");

                }
            }

            if ($type == 'full') {
                $page->items = $item0;
            }

            if ($type == 'formap') {
                $page->items = $item;
            }

            if ($type == 'formoder') {
                return $item[$rootID];
            }

            return $page->items[1];
        }

    }

    function GetList($id)
    {
        global $sql;
        global $control;

        $q = "  SELECT id as id, name as title, url as url, level as level FROM prname_tree WHERE parent = '$id' AND visible = 1 ORDER BY sort";

        $res = $sql->query($q);
        $arr = array();
        while ($str = $sql->fetch_array($res)) {
            $id = $str['id'];
            $title = $str['title'];
            $level = $str['level'];
            $url = $str['url'];

            $url = substr($url, 0, strlen($url) - 1);


            $arr[$id]['id'] = $id;
            $arr[$id]['level'] = $level;
            $arr[$id]['title'] = $title;
            $arr[$id]['url'] = $url;
        }


        if (count($arr) > 0) {

            $strok = '';
            foreach ($arr as $one_arr) {
                $level = $one_arr['level'];

                $strok = 'item[' . $one_arr['id'] . ']->';


                eval("\$" . $strok . "title = '" . $one_arr['title'] . "'; ");
                eval("\$" . $strok . "url = '<!--base_url//-->" . $one_arr['url'] . "'; ");
//				eval("\$".$strok."link = '".$link . "'; ");
                eval("\$" . $strok . "level = '" . $level . "'; ");

            }

            $page->items = $item;

            return $page->items;
        }

    }

// Новый нестед.
    static function getparents_new($left_key, $right_key)
    {
        global $sql;

        $res2 = $sql->query("SELECT id, `key`, level FROM prname_tree WHERE left_key <= '$left_key' AND right_key >= '$right_key' ORDER BY left_key ");
        $i = 0;
        while ($str2 = $sql->fetch_array($res2)) {
            $parents[$i] = $str2['id'];
            $i++;
        }
        return $parents;
    }


}

?>