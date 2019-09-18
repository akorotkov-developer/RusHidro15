<?

require_once "templates/includes/new_top.php";

global $config;
global $sql;
global $all;

$all = new All();


    $page = new stdClass();
    $page->page_url = $_SERVER['PHP_SELF'];
    
    $settings = array('auth_count', 'auth_email', 'auth_email_send', 'auth_lock', 'auth_lock_period', 'auth_ip', 'auth_ip_lock', 'auth_ip_lock_site');
    

    if (isset($_GET['unlock_ip']) && trim($_GET['unlock_ip'])) {
        $q = sprintf(" DELETE FROM prname_adminlog WHERE ip = '%s' AND action = 'limit_auth' ", $sql->escape_string($_GET['unlock_ip']));
        $sql->query($q);
    }


    if ($_POST['settings']) {
        if (!isset($_POST['auth_lock'])) $_POST['auth_lock'] = '0';
        if (!isset($_POST['auth_email_send'])) $_POST['auth_email_send'] = '0';
        foreach ($_POST as $pkey => $pval) {
            if (in_array($pkey, $settings)) {
                $q = sprintf(" UPDATE prname_settings SET `value` = '%s' WHERE `name` = '%s' LIMIT 1 ", $sql->escape_string($pval), $sql->escape_string($pkey));
                $sql->query($q);
            }
        }
    }


    $arS = array();
    if ($settings) {
        foreach ($settings as $s) {
            $arS[] = '"'.$s.'"';
        }
    }
    
    $q = sprintf(" SELECT name, value FROM prname_settings WHERE name IN (%s) ", implode(",", $arS));
    $res = $sql->query($q);
    while($row = $sql->fetch_assoc($res)) {
        $page->{$row['name']} = $row['value'];
    }

    $i = 1;
    while ($i <= 10) {
        $page->auth_counts[$i] = new stdClass();
        $page->auth_counts[$i]->value = $i;
        $page->auth_counts[$i]->selected = false;
        if ($i == $page->auth_count) $page->auth_counts[$i]->selected = true;
        $i++;
    }
    
    
    $page->locked_ips = array();
    if ((int)$page->auth_lock_period) {
        $dt = date("Y-m-d H:i:s", time() - 60 * $page->auth_lock_period);
        $q = sprintf(" SELECT ip FROM prname_adminlog WHERE dt > '%s' AND action = 'limit_auth' GROUP BY ip ", $dt);
        $res = $sql->query($q);
        while ($row = $sql->fetch_assoc($res)) {
            $obj = new stdClass();
            $obj->ip = $row['ip'];
            $page->locked_ips[] = $obj;
        }
    }

    
    $html = sprintt($page, 'templates/protection/protection.html');
    echo $html;

    
include "templates/includes/new_bottom.php";

?>