<?

require_once "templates/includes/new_top.php";

global $config;
global $sql;
global $all;

$all = new All();

$actions = array(
    'success_auth' => 'Успешная авторизация',
    'fail_auth' => 'Неудачная попытка входа',
    'limit_auth' => 'Превышен лимит попыток входа',
    'add_category' => 'Добавлен раздел',
    'change_category' => 'Изменен раздел',
    'toggle_category' => 'Скрыт/показан раздел',
    'move_category' => 'Перемещен раздел',
    'del_category' => 'Удален раздел',
    'add_block' => 'Создан блок',
    'change_block' => 'Изменен блок',
    'toggle_block' => 'Скрыт/показан блок',
    'del_block' => 'Удален блок'
);


function dopInfo(&$row)
{
    global $config;
    global $sql;
    global $actions;
    global $all;
    
	if ($row['bid']==0 && $row['cid']==0) return;

	if (intval($row['bid']) && strlen($row['btpl']))
	{
        //вынимаем название типа блока		
        $q = sprintf("SELECT name FROM prname_btemplates WHERE `key` = '%s'", $row['btpl']);	
        $row['btype'] = $sql->one_record($q);
            
		$q = sprintf("SELECT id from prname_b_%s WHERE id=%d;", $row['btpl'], $row['bid']);
		$row2=$sql->fetch_assoc($sql->query($q));
		if ($row2)
		{
			$row['link'] = sprintf('%sadmin/block.php?cparent=%d&btmplate=%s&actions=edit&blockid=%d',
				$config['server_url'], $row['cid'], $row['btpl'], $row['bid']);
		}
        else
        {
			$row['link'] = sprintf('%sadmin/block.php?cparent=%d&btmplate=%s',
				$config['server_url'], $row['cid'], $row['btpl']);
        }
	} else {
        if (intval($row['cid']))
        {
            $q = sprintf("SELECT id from prname_categories WHERE id=%d;", $row['cid']);
            $row2=$sql->fetch_assoc($sql->query($q));
            if ($row2)
            {
                $row['link'] = sprintf('%sadmin/cat_edit.php?id=1&blocks=0&del=0&toup=0&todown=0&parent2=tr%d&copy=0&move=0&copymove=0&hide=0&parent=%d',
                    $config['server_url'], $row['cid'], $row['cid']);
            }
        }
    }

}


    $page = new stdClass();
                    
    if ($actions) {
        foreach ($actions as $actkey => $actval) {
            $obj = new stdClass();
            $obj->title = $actval;
            $obj->value = $actkey;
            $obj->checked = false;
            if ($actkey == $_GET['action']) $obj->checked = true;
            $page->actions[] = clone($obj);
        }
    }
                    
            
    $q = " SELECT DISTINCT user FROM prname_adminlog ORDER BY user ASC ";
    $res = $sql->query($q);
    while ($row = $sql->fetch_assoc($res))
    {
        $page->user[$row['user']] = new stdClass();
        $page->user[$row['user']]->user = $row['user'];
        $page->user[$row['user']]->checked = false;
        if ($row['user'] == $_GET['user']) $page->user[$row['user']]->checked = true;
    }
                        
                        
                    
                    
    $WHERE=" WHERE 1=1 ";

    if (strlen($_GET['user']))
    {
        $WHERE .= sprintf(" AND `user` = '%s' ", $_GET['user']);
    }

    if (strlen($_GET['dt_start']))
    {
        $WHERE .= sprintf(" AND `dt` > '%s 00:00:00' ", $all->get_date($_GET['dt_start'], 'stringtomysql'));
        $page->dt_start = htmlspecialchars($_GET['dt_start']);
    }

    if (!$_GET['dt_end']) $_GET['dt_end'] = date("d.m.Y");
    if (strlen($_GET['dt_end']))
    {
        $WHERE .= sprintf(" AND `dt` < '%s 23:59:59' ", $all->get_date($_GET['dt_end'], 'stringtomysql'));
        $page->dt_end = htmlspecialchars($_GET['dt_end']);
    }

    if (strlen($_GET['action']))
    {
        $WHERE .= sprintf(" AND `action` = '%s' ", $_GET['action']);
    }


    $LIMIT = 20;
    $OFFSET = intval($_GET['page']) * $LIMIT;

    $q = sprintf("SELECT SQL_CALC_FOUND_ROWS * FROM prname_adminlog %s ORDER BY dt DESC limit %d, %d", $WHERE, $OFFSET, $LIMIT);
    $res = $sql->query($q);

    $allcnt = $sql->one_record("SELECT Found_Rows();");
    while ($row = $sql->fetch_assoc($res))
    {
        dopInfo($row);
        $row['info'] = htmlspecialchars($row['info']);
        $row['action_title'] = $actions[$row['action']];
        $row['dt_1'] = $all->get_date(substr($row['dt'], 0, 10), 1) . substr($row['dt'], 10);
        unset($obj);
        $obj = new stdClass();
        foreach($row as $f => $v)
            $obj->$f = $v;
        $page->logs[] = clone($obj);
    }


    //постраничка
    if ($allcnt > $LIMIT)
    {
        $pcnt = intval($allcnt /$LIMIT);
            if (($allcnt%$LIMIT) > 0)
                $pcnt++;

        for($i=0;$i<$pcnt; $i++)
        {
            $page->pages[$i] = new stdClass();
            $page->pages[$i]->title = $i+1;
            $page->pages[$i]->num = $i;
            if ($i == intval($_GET['page']))
                $page->pages[$i]->current = 1;
        }
    }						

    $page->currpage = intval($_GET['page']);

    
    $html = sprintt($page, 'templates/adminlog/adminlog.html');
    echo $html;

    
include "templates/includes/new_bottom.php";

?>