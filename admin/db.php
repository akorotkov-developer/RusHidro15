<?php

require_once "../includes.php";

global $config;
global $sql;
$sql = new Sql();
$sql->connect();

kick_unauth();

$page = new stdClass();
$page->title = 'Импорт/Экспорт БД';
$state = (int)@file_get_contents('db.tmp');
switch ($state) {
    case 1:
        $page->state = 'Производится Экспорт таблиц из БД';
        break;
    case 2:
        $page->state = 'Производится Импорт SQL дампа в БД';
        $page->script = 1;
        break;
    case 3:
        $page->state = 'Экспорт завершен <a onclick="window.open(\'/admin/db.php\'); this.remove();">Скачать SQL дамп</a>';
        file_put_contents('db.tmp', 4);
        break;
    case 4:
        unlink('db.tmp');
        header('Content-Type: text/sql');
        header('Content-Disposition: attachment; filename=' . $_SESSION['dumpname']);
        echo file_get_contents($_SESSION['dumpname']);
        unlink($_SESSION['dumpname']);
        exit;
        break;
    case 5:
        $page->state = '<span style="color:green">Импорт завершен</span>';
        $page->script = 1;
        unlink('db.tmp');
        break;
    case 6:
        $page->state = '<span style="color:red">Импорт провален!</span>';
        unlink('db.tmp');
        break;
    default:
        $page->state = 'Я свободен';
}

if ($_REQUEST['action'] == 'export') {
    if ($state > 0) {
        $page->error = 2;
    } else {
        file_put_contents('db.tmp', 1);
        $tables = $_REQUEST['tables'];
        if (empty($tables)) $tables = array();
        foreach ($tables as $key => $table) {
            if (empty($table)) unset($tables[$key]);
        }
        $_SESSION['dumpname'] = $dumpname = sprintf('%s-%s-dump.sql', $config['dbname'], date('d.m.Y'));
        if (!empty($tables)) {
            $exec = sprintf('mysqldump --add-drop-table -u%s -p%s %s %s > %s && echo 3 > db.tmp &', $config['dbuser'], $config['dbpass'], $config['dbname'], implode(' ', $tables), $dumpname);
        } else {
            $exec = sprintf('mysqldump --add-drop-table -u%s -p%s %s > %s && echo 3 > db.tmp &', $config['dbuser'], $config['dbpass'], $config['dbname'], $dumpname);
        }
        exec($exec);
        header('Location: /admin/db.php');
        exit;
    }
}

if ($_REQUEST['action'] == 'import') {
    if ($state > 0) {
        $page->error = 2;
    } elseif ($_FILES['sql']['error'] !== 0) {
        $page->error = 3;
    } else {
        file_put_contents('db.tmp', 2);
        exec($s = sprintf('((mysql -u%s -p%s %s < %s && echo 5 > db.tmp) || echo 6 > db.tmp) > /dev/null &', $config['dbuser'], $config['dbpass'], $config['dbname'], $_FILES['sql']['tmp_name']));
        sleep(1); // ожидание для того, чтобы mysql успел запуститься, и ему на вход подалось содержимое sql дампа.
        header('Location: /admin/db.php');
        exit;
    }
}

include "templates/includes/new_top.php";

$res = $sql->query("SHOW TABLES FROM {$config['dbname']}");
$page->table = array();
while ($row = $sql->fetch_row($res)) {
    $item = new stdClass();
    $item->name = $row[0];
    $page->table[] = $item;
}

echo sprintt($page, 'templates/db/index.html');

include "templates/includes/new_bottom.php";
?>
