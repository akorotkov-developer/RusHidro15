<?php
//error_reporting(E_ALL);
foreach ($_REQUEST as $k => $v) $$k = $v;
foreach ($_GET as $k => $v) $$k = $v;
foreach ($_POST as $k => $v) $$k = $v;
// прошиваем локали, чтобы у нас всё работало с case-sensitivity
setlocale(LC_CTYPE, array("ru_RU.CP1251", "ru_SU.CP1251", "ru_RU.KOI8-r", "ru_RU", "russian", "ru_SU", "ru"));
session_start();

//	session_register("admin_name");
//	session_register("admin_password");
//    session_register("code");
//    session_register("lang");
#########################

//логирование адм.действий
function adminlog($bid=0,$btpl='',$cid=0,$ctpl='',$action='',$info='')
{
    global $prname;
    global $config;
    global $sql;

    $user = $_SESSION['admin_name'];

    if (intval($bid) && strlen($btpl)) {
        $q = sprintf(" SELECT * from prname_b_%s WHERE id = %s ", $btpl, $bid);
        $brow = $sql->fetch_assoc($sql->query($q));
        if ($brow) {
            if (isset($brow['title'])) {
                $info = All::smart_substr($brow['title'],50);
            } else {
                $info = 'в разделе ' . All::get_name($cid);
            }
        }
    } else {
        if (intval($cid)) {
            $info = All::get_name($cid);
        }
    }

    $q = sprintf("INSERT INTO %s_adminlog SET dt='%s', user='%s', cid=%d, bid=%d, ctpl='%s', btpl='%s', `action`='%s', `info`='%s', ip='%s';",
        $prname, date("Y-m-d H:i:s"), $user, intval($cid), intval($bid), $ctpl, $btpl, mysql_escape_string($action), mysql_escape_string($info), $_SERVER['REMOTE_ADDR']);
    mysql_query($q);
}

function admin_print_map($parent = 0, $user_id)
{
    global $prname;
    global $key;


    function admin_print_map_cat($main, $mid, $user_id)
    {
        global $prname;
        global $text_map;
        global $main_key;

        if ($main == $mid) {
            $text_map = '';
        }

        $q = "SELECT `key` FROM $prname" . "_categories WHERE id = '$mid'";
        $str1 = mysql_fetch_row(mysql_query($q));
        $mkey = $str1[0];

        $q = "SELECT id, name, `key`, parent, template, sort FROM $prname" . "_categories WHERE parent = '$main'  ORDER BY sort";
        $res = mysql_query($q);
        $last_main = $main;

        if (mysql_num_rows($res) > 0) {
            $text_map .= '<ul>';
            while ($str = mysql_fetch_row($res)) {
                $id = $str[0];
                $name = htmlspecialchars($str[1]);
                $main = $id;

                $tmp_url = '';
                $tmp_url .= "";
                $text_map .= '<li><label for="cat_' . $id . '">' . $name . '</label><input type="checkbox" id="cat_' . $id . '" name="cat_' . $id . '" value="1" ' . checked_cid($id, $user_id) . '></li>';
                admin_print_map_cat($id, $mid, $user_id);
            }
            $text_map .= '</ul>';
        }

        return $text_map;
    }

    $q = "SELECT id, name, `key` FROM $prname" . "_categories WHERE parent = '$parent' ORDER BY sort";
    $res = mysql_query($q);

    $text .= '<ul>';
    while ($str = mysql_fetch_row($res)) {
        $mid = $str[0];
        $mname = htmlspecialchars($str[1]);
        $mkey = ($str[2]);

        $text .= '<li><label for="cat_' . $mid . '"><b>' . $mname . '</a></b></label><input type="checkbox" id="cat_' . $mid . '" name="cat_' . $mid . '" value="1" ' . checked_cid($mid, $user_id) . '></li>';
        $text_map = '';
        $text .= admin_print_map_cat($mid, $mid, $user_id);
    }
    $text .= '</ul>';


    return $text;
}

function checked_cid($cid, $user_id)
{
    global $prname;

    $flag = 0;
    $q = "SELECT count(id) FROM $prname" . "_sadmin_resctricted  WHERE admin_id = '$user_id' AND cat_id = '$cid'";
//			echo $q;
    $str1 = mysql_fetch_row(mysql_query($q));
    if ($str1[0] > 0) {
        return 'checked';
    } else {
        return '';
    }
}

function user_check_add($admin_id)
{
    global $array_admin_add;
    if (count($array_admin_add) > 0) {
        foreach ($array_admin_add as $admin) {
            if ($admin == $admin_id) return true;
        }
    }

    return false;
}

########################


if ($logout) {
    $admin_name = '';
    $admin_password = '';
};
srand((double)microtime() * 1000000);
$stree = array();

//
//	function user_check_add($admin_id)		{
//		global $array_admin_add;
//		if (count($array_admin_add) > 0)	{
//			foreach ($array_admin_add as $admin)	{
//				if ($admin == $admin_id) return true;
//			}
//		}
//
//		return false;
//	}
//
function module_prapare($module, $key)
{
    if (!is_file('inc/modules/' . $module . '.php') || !is_file('templates/' . $key . '/' . $key . '.html'))
        generate_module($module, $key);
    return true;
}

function generate_module($module, $key)
{
    if ($module != 'temp1' && $module != 'temp2' && $module != 'temp3' && $module != 'temp4') {
        $dirName = 'inc/modules/';
        $content = str_replace('temp2', $key, file_get_contents("inc/modules/temp2.php"));
        file::greate_file($module . '.php', $dirName, $content);
    }
    $dirName = 'templates/' . $key . '/';
    switch ($module) {
        case 'temp1':
            $content = str_replace('{module}', $key, file_get_contents("templates/temps/list.html"));// Листинг1
            file::greate_file($key . '.html', $dirName, $content);
            break;
        case 'temp2':
            $content = str_replace('{module}', $key, file_get_contents("templates/temps/list.html"));// Листинг1
            file::greate_file($key . '.html', $dirName, $content);

            $content2 = str_replace('{module}', $key, file_get_contents("templates/temps/one.html"));// one
            file::greate_file($key . '_one.html', $dirName, $content2);
            break;
        case 'temp3':
            $content = str_replace('{module}', $key, file_get_contents("templates/temps/list.html"));// Листинг1
            file::greate_file($key . '.html', $dirName, $content);
            break;
        default:
            $content = str_replace('{module}', $key, file_get_contents("templates/temps/list.html"));// Листинг1
            file::greate_file($key . '.html', $dirName, $content);

            $content2 = str_replace('{module}', $key, file_get_contents("templates/temps/one.html"));// one
            file::greate_file($key . '_one.html', $dirName, $content2);
            break;
    }


}

function splstr($ss, $delim)
{
    $s = array();
    $inq = false;
    $n = 1;
    for ($i = 0; $i < strlen($ss); $i++) {
        $c = substr($ss, $i, 1);
        if ($i == (strlen($ss) - 1)) {
            $c2 = '';
        } else {
            $c2 = substr($ss, $i + 1, 1);
        };
        if (($c == $delim) && (!$inq)) {
            $n++;
        } elseif (($c == '"') && ($c2 != '"')) {
            $inq = !$inq;
        } elseif (($c == '"') && ($c2 == '"')) {
            $i++;
            $s[$n] .= $c;
        } else {
            $s[$n] .= $c;
        };
    };
    return $s;
}

;

function addcats($id, $lev, $prefix, $addq, $pars, $hidestructure)
{
    global ${$prefix . 'stree'};
    global $prname;

    $q = "SELECT c.*, t.hidestructure FROM $prname" . "_categories c, $prname" . "_ctemplates t WHERE c.parent=$id AND c.template=t.key AND c.id>0 $addq ORDER BY c.sort";
    $res = mysql_query($q);
    while ($row = mysql_fetch_array($res)) {
        $row['lev'] = $lev + 1;

        // old if (is_array($pars)) $can = in_array($row['id'], $pars) || ($row['lev'] >= count($pars)); else $can = true;

        //if (is_array($pars)) $can = in_array($row['id'], $pars) || ($row['lev'] >= count($pars)); else $can = true;
        /*
        $can = false;
        if (is_array($pars))	{
            //$can = true;
            if (in_array($row['id'], $pars))	{
                $can = true;
            }	else	{
                //$can = false;
            }//in_array($row['id'], $pars) || ($row['lev'] >= count($pars));
        } else {
            $can = true;
        }
        */
        //echo $can;
        $can = true;


        if ($can) {
            if ($hidestructure === false) $can = true;
            elseif
            (($hidestructure === true) || ($row['lev'] >= @count($pars))
            )
                $can = $row['hidestructure'] < 1;
            else $can = true;


            global $hsa;
            //$pars = $hsa;
            if (is_array($pars)) {
                foreach ($pars as $one_arr) {
                    if (($row['id']) == $one_arr) {
                        $can = true;
                        break;
                    } else {
                        $can = false;
                    }
                }
            }

            //echo $can;
            if (is_array($hsa)) {
                foreach ($hsa as $one_arr) {
                    //echo $one_arr. " ";
                    if ($can == false) {
                        //echo 'f';
                        if (($row['id']) == $one_arr) {
                            $can = true;
                            break;
                        } else {
                            $can = false;
                        }
                    }
                }
            }


            if (!$can) $row['catcount'] = mysql_result(mysql_query("SELECT COUNT(id) FROM $prname" . "_categories WHERE parent=" . $row['id']), 0, 0);
            array_push(${$prefix . 'stree'}, $row);
            if ($can) addcats($row['id'], $lev + 1, $prefix, $addq, $pars, $hidestructure);
        };
    };
}

;
function tree_create($id = false, $addq = '', $hidestructure = false)
{
    do {
        $prefix = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . time();
    } while (isset(${$prefix . 'stree'}));
    if ($id === false) $id = 0;
    global ${$prefix . 'stree'};
    global $prname;
    ${$prefix . 'stree'} = array();
    ${$prefix . 'stree'}[0] = 0;
    $pars = false;


    if ($hidestructure && ($hidestructure !== true)) {

        $pars = array($id2 = $hidestructure);
        //echo $id2;
        while ($id2 > 0) {
            array_push($pars, $id2 = mysql_result(mysql_query("SELECT parent FROM $prname" . "_categories WHERE id=$id2"), 0, 0));
        };
        //unset($pars);
    };
    addcats($id, 0, $prefix, $addq, $pars, $hidestructure);

    return $prefix;
}

;

function tree_next($prefix)
{
    global ${$prefix . 'stree'};
    return (${$prefix . 'stree'}[0] < (count(${$prefix . 'stree'}) - 1)) ? ${$prefix . 'stree'}[++${$prefix . 'stree'}[0]] : false;
}

;

function tree_prev($prefix)
{
    global ${$prefix . 'stree'};
    return (${$prefix . 'stree'}[0] > 0) ? ${$prefix . 'stree'}[${$prefix . 'stree'}[0]-- - 1] : false;
}

;

function tree_tofirst($prefix)
{
    global ${$prefix . 'stree'};
    ${$prefix . 'stree'}[0] = 0;
}

;

function tree_itemno($n, $prefix)
{
    global ${$prefix . 'stree'};
    return ${$prefix . 'stree'}[$n + 1];
}

;

function tree_tolast($prefix)
{
    global ${$prefix . 'stree'};
    ${$prefix . 'stree'}[0] = count(${$prefix . 'stree'} - 2);
}

;

function tree_pos($prefix)
{
    global ${$prefix . 'stree'};
    return ${$prefix . 'stree'}[0];
}

;

function tree_count($prefix)
{
    global ${$prefix . 'stree'};
    return count(${$prefix . 'stree'}) - 1;
}

;

function tree_path($id)
{
    global $prname;
    $res = mysql_query("SELECT * FROM $prname" . "_categories WHERE id=$id");
    if ($row = mysql_fetch_array($res)) $s = " / " . strip_tags($row['name']);
    while ($row['parent'] > 0) {
        $res = mysql_query("SELECT * FROM $prname" . "_categories WHERE id=" . $row['parent']);
        $row = mysql_fetch_array($res);
        $s = " / " . strip_tags($row['name']) . $s;
    };
    $s = "корень" . $s;
    return $s;
}

;

function tree_item($os = 0, $prefix)
{
    global ${$prefix . 'stree'};
    $pos = ${$prefix . 'stree'}[0] + $os;
    if (($pos >= (count(${$prefix . 'stree'}) - 1)) || ($p < 0)) {
        return false;
    } else return ${$prefix . 'stree'}[$pos + 1];
}

;

function get_var($s)
{
    global $_POST;
    global $HTTP_GET_VARS;
    if (isset($_POST[$s])) {
        global ${$s};
        ${$s} = $_POST[$s];
        return true;
    } elseif (isset($HTTP_GET_VARS[$s])) {
        global ${$s};
        ${$s} = $HTTP_GET_VARS[$s];
        return true;
    } else {
        return false;
    };
}

function check_access_to_site()
{
    global $config;
    global $prname;
    
    $q = sprintf(" SELECT value FROM %s_settings WHERE name = 'auth_ip_lock_site' LIMIT 1 ", $prname);
    $res = mysql_query($q);
    if($row = mysql_fetch_assoc($res)) {
        $auth_ip_lock_site = $row['value'];
        $ips_lock_site = array();
        if (strlen($auth_ip_lock_site)) {
            $ips_lock_site_tmp = explode(",", $auth_ip_lock_site);
            if ($ips_lock_site_tmp) {
                foreach ($ips_lock_site_tmp as $ips_lock_siteV) {
                    if (trim($ips_lock_siteV))
                        $ips_lock_site[] = trim($ips_lock_siteV);
                }
            }
            unset($ips_lock_site_tmp);
        }
        if (!empty($ips_lock_site) && in_array($_SERVER['REMOTE_ADDR'], $ips_lock_site)) {
            include($config['DOCUMENT_ROOT'] . '403.php');
            die();
        }
    }
}

function kick_unauth()
{
    global $config;
    global $prname;
    global $user_is_super;
    global $user_is_moder;
    global $enabled_ids;
    
    $q = sprintf(" SELECT name, value FROM %s_settings WHERE name IN ('auth_count', 'auth_email', 'auth_email_send', 'auth_lock', 'auth_lock_period', 'auth_ip', 'auth_ip_lock') ", $prname);
    $res = mysql_query($q);
    while($row = mysql_fetch_assoc($res)) {
        $settings[$row['name']] = $row['value'];
    }
    
    
    // блокировать ip
    $ips_lock = array();
    if (strlen($settings['auth_ip_lock'])) {
        $ips_lock_tmp = explode(",", $settings['auth_ip_lock']);
        if ($ips_lock_tmp) {
            foreach ($ips_lock_tmp as $ips_lockV) {
                if (trim($ips_lockV))
                    $ips_lock[] = trim($ips_lockV);
            }
        }
        unset($ips_lock_tmp);
    }
    if (!empty($ips_lock) && in_array($_SERVER['REMOTE_ADDR'], $ips_lock)) {
        include($config['DOCUMENT_ROOT'] . '403.php');
        die();
    }
    

    // не блокировать ip
    $ips = array();
    if (strlen($settings['auth_ip'])) {
        $ips_tmp = explode(",", $settings['auth_ip']);
        if ($ips_tmp) {
            foreach ($ips_tmp as $ipsV) {
                if (trim($ipsV))
                    $ips[] = trim($ipsV);
            }
        }
        unset($ips_tmp);
    }
    if (!empty($ips) && in_array($_SERVER['REMOTE_ADDR'], $ips)) {
        $settings['auth_lock'] = 0;
    }
    
    
    $settings['auth_count'] = (int)$settings['auth_count'];
    if (!$settings['auth_count']) $settings['auth_count'] = 3;

    $settings['auth_lock_period'] = (int)$settings['auth_lock_period'];
    if (!$settings['auth_lock_period']) $settings['auth_lock_period'] = 15;

    $dt = date("Y-m-d H:i:s", time() - 60 * $settings['auth_lock_period']);
    $dt_check = date("Y-m-d H:i:s", time() - 60 * 1);

    $q = sprintf(" SELECT COUNT(id) AS count FROM %s_adminlog WHERE ip = '%s' AND dt > '%s' AND action = 'fail_auth' ", $prname, $_SERVER['REMOTE_ADDR'], $dt_check);
    $crow = mysql_fetch_assoc(mysql_query($q));

    if ($crow['count'] >= $settings['auth_count']) {

        $q = sprintf(" SELECT id FROM %s_adminlog WHERE ip = '%s' AND dt > '%s' AND action = 'limit_auth' LIMIT 1 ", $prname, $_SERVER['REMOTE_ADDR'], $dt_check);
        $lrow = mysql_fetch_assoc(mysql_query($q));

        if (!(int)$lrow['id']) {
            adminlog(0,'',0,'','limit_auth');
            
            if ((int)$settings['auth_email_send']) {

                $emails = array();

                if (strlen($settings['auth_email'])) {
                    $emails_tmp = explode(",", $settings['auth_email']);
                    if ($emails_tmp) {
                        foreach ($emails_tmp as $emailsV) {
                            if (trim($emailsV))
                                $emails[] = trim($emailsV);
                        }
                    }
                    unset($emails_tmp);
                }
                
                if (empty($emails)) {
                    $adminQ = sprintf(" SELECT admin_email FROM %s_sadmin WHERE `admin_name` = 'admin' LIMIT 1 ", $prname);
                    $adminRow = mysql_fetch_assoc(mysql_query($adminQ));
                    if (trim($adminRow['admin_email']))
                        $emails[] = trim($adminRow['admin_email']);
                }

                global $all;
                $all = new All();
                
                $auth_count_str = $all->declOfNum((int)$settings['auth_count'], array('неудачная попытка', 'неудачные попытки', 'неудачных попыток'));

                $subject = "Неудачная попытка авторизации";
                $message = sprintf('
                    <p>%s входа в систему администрирования сайта %s%s.</p>
                    <ul>
                        <li>Дата и время: %s</li>
                        <li>IP: %s</li>
                        <li>User-agent: %s</li>
                        <li>Логин: %s</li>
                    </ul>
                    <p>Отправитель письма: %s</p>
                ', $auth_count_str, $_SERVER['SERVER_NAME'], (((int)$settings['auth_lock']) ? ', IP пользователя заблокирован' : ''), 
                date("d.m.Y H:i:s"), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], htmlspecialchars($_POST['authentification_name']), $_SERVER['SERVER_NAME']);

                if ($emails) {
                    foreach ($emails as $e) {
                        $all->send_mail_from($e, 'noreply@' . $_SERVER['SERVER_NAME'], $subject, $message);
                    }
                }
                
            }
        }

    }
    
    if ((int)$settings['auth_lock']) {
        $q = sprintf(" SELECT id FROM %s_adminlog WHERE ip = '%s' AND dt > '%s' AND action = 'limit_auth' LIMIT 1 ", $prname, $_SERVER['REMOTE_ADDR'], $dt);
        $crow = mysql_fetch_assoc(mysql_query($q));
        if ($crow['id']) {
            include($config['DOCUMENT_ROOT'] . '403.php');
            die();
        }
    }
    

    $admin_name = strlen($_SESSION['admin_name']) > 0 ? $_SESSION['admin_name'] : stripslashes($_POST['authentification_name']);
    $admin_password = strlen($_SESSION['admin_password']) > 0 ? $_SESSION['admin_password'] : stripslashes($_POST['authentification_password']);
    $q = "SELECT admin_name, admin_password, enabled_cid, $prname" . "_rt.* FROM $prname" . "_sadmin, $prname" . "_rt WHERE aid = $prname" . "_sadmin.admin_id AND enabled='1' AND admin_name='" . addslashes($admin_name) . "' AND admin_password='" . md5(base64_encode('SoftMajor AP ' . $admin_password)) . "'";

    if (!@$str = mysql_fetch_array(mysql_query($q))) {
        if ($_POST['authentification_name'])
        {
            $addinfo=sprintf("Логин: %s",$_POST['authentification_name']);
            adminlog(0,'',0,'','fail_auth',$addinfo);
        }
        
        $url = $_SERVER['REQUEST_URI'];

        preg_match_all('/actions=(.*)&/Ui', $url, $uarr, PREG_PATTERN_ORDER);
        if (count($uarr[1]) > 0)	{
            foreach ($uarr[1] as $ukey => $uval) {
                if ($uval != 'edit') {
                    $url = str_replace($uarr[0][$ukey], '', $url);
                }
            }
        }
        unset($uarr);

        $_SESSION['admin_redirect_url'] = $url;
        header("Location:" . $config['admin_login_page']);
        exit;
    }

    //вытаскиваем права этого юзера
    $enabled_ids = explode(';', $str['enabled_cid']);
    $user_is_moder = user_is('is_moder');


    $_SESSION['admin_password'] = $admin_password;
    $_SESSION['admin_name'] = $admin_name;

    if (isset($_POST['authentification_name']))
    {
        adminlog(0,'',0,'','success_auth');
    }
    
    //print_r($_SESSION);

    $user_is_super = (user_is('super') == '1');
}

;

function user_is($s, $userid = 0)
{
    global $prname;
    if ($userid == 0) {
        $qq = "admin_name='" . $_SESSION['admin_name'] . "' AND admin_password='" . md5(base64_encode('SoftMajor AP ' . $_SESSION['admin_password'])) . "'";
    } else {
        $qq = "admin_id=$userid";
    };
    $q = "SELECT `$s` FROM $prname" . "_sadmin, $prname" . "_rt WHERE aid = $prname" . "_sadmin.admin_id AND $qq";

    $res = mysql_query($q);
    $row = mysql_fetch_array($res);
    return $row[$s];
}

;

function enabled_cat($id)
{
    global $user_is_moder;
    global $enabled_ids;


    if (!$user_is_moder) return true;
    return in_array($id, $enabled_ids);
}


function save_uploaded($source)
{
    global $blocks_blocks;
    global $blocks_categories;
    global $blocks_sections;
    $ret = false;
    global $config;
    global ${$source};
    global ${$source . "_name"};
    if (is_file(${$source})) {
        $name = explode('.', ${$source . "_name"});
        $name = get_random_name() . "." . $name[count($name) - 1];
        copy(${$source}, $config['upload_dir'] . $name);
        $ret = $name;
    };
    return $ret;
}

function save_uploaded_image($source, $small)
{
    global $blocks_blocks;
    global $blocks_categories;
    global $blocks_sections;

    $ret = false;
//		if ((get_var($source)) && (get_var($source.'_name'))) {
    global ${$source};
    global ${$source . "_name"};
    global $config;
    $file = ${$source};
    $file_name = ${$source . "_name"};
    if ($s = @getimagesize($file)) {
        $name = get_random_name() . ".jpg";
        if (($s[2] == 2) || ($s[2] == 3)) {
            if ($s[2] == 2) {
                $im = @imagecreatefromjpeg($file);
            } else {
                $im = @imagecreatefrompng($file);
            };
            if ($im) {
                $sn = shrink_dimensions($s);
                switch ($small) {
                    case 0:
                        imagejpeg($im, $config['upload_dir'] . $name);
                        break;
                    case 1:
                        $im_small = imagecreate($sn[0], $sn[1]);
                        imagecopyresized($im_small, $im, 0, 0, 0, 0, $sn[0], $sn[1], $s[0], $s[1]);
                        imagejpeg($im_small, $config['upload_dir'] . "small_" . $name);
                        break;
                    case 2:
                        $im_small = imagecreate($sn[0], $sn[1]);
                        imagecopyresized($im_small, $im, 0, 0, 0, 0, $sn[0], $sn[1], $s[0], $s[1]);
                        imagejpeg($im, $config['upload_dir'] . $name);
                        imagejpeg($im_small, $config['upload_dir'] . "small_" . $name);
                        break;
                };
                $ret = $name;
            };
        } elseif (($s[2] == 1) && ($small == 0)) {
            copy($file, $config['upload_dir'] . $name);
            $ret = $name;
        };
    };
//		};
    return $ret;
}

function get_random_name()
{
    return time() . (rand(0, 32767) * rand(0, 32767));
}

;

function page_nav($addlnk, $q)
{
    global $blocks_blocks;
    global $blocks_categories;
    global $blocks_sections;
    global $config;
    global $pnum;
    global $begin;
    global $end;
    $result = '';
    $num = $config['items_on_page'];
    $pages_in_view = $config['pages_in_view'];
    if ($pnum < 1) {
        $pnum = 1;
    }
    if ((substr($q, 0, 1) >= '0') && (substr($q, 0, 1) <= '9')) {
        $board_number = $q;
    } else {
        $board_number = mysql_result(mysql_query($q), 0, 0);
    };
    while (((($pnum - 1) * $num) + 1) > $board_number) {
        $pnum--;
    };
    $begin = ($pnum - 1) * $num;
    $end = $num;
    $page_col = floor(($board_number - 1) / ($num)) + 1;
    if ($page_col > 1) {
        $num_of_view = intval(($pnum - 1) / $pages_in_view);
        $scanfrom = $num_of_view * $pages_in_view + 1;
        $scanto = ($num_of_view + 1) * $pages_in_view;
        if ($scanto > $page_col) {
            $scanto = $page_col;
        };
        if ($scanfrom > 1) {
            $result .= "<a href=\"$addlnk&pnum=" . ($scanfrom - 1) . "\">&lt;&lt;</a>&nbsp;&nbsp;";
        };
        for ($i = $scanfrom; $i <= $scanto; $i++) {
            if ($i != $pnum) {
                $result .= "<a href=\"$addlnk&pnum=$i\">$i</a>&nbsp;&nbsp;";
            } else {
                $result .= "<b>$i</b>&nbsp;&nbsp;";
            };
        };
        if ($scanto < $page_col) {
            $result .= "<a href=\"$addlnk&pnum=" . ($scanto + 1) . "\">&gt;&gt;</a>&nbsp;&nbsp;";
        };
    };
    return $result;
}

;

function truncate_name($s)
{
    global $config;
    if (strlen($s) > $config['truncate_length'] + 1) {
        $s = substr($s, 0, $config['truncate_length']) . "..";
    };
    return $s;
}

;


function delete_category($parent)
{
    global $prname;
    global $config;
    global $sql;

    if (!intval($parent)) return;

    $tree = tree_create($parent);
    while ($id = tree_next($tree)) {
        delete_category($id['id']);
        /*
                    $res = $sql->fetch_assoc($sql->query("SELECT blocktypes FROM prname_ctemplates WHERE `key` = '".$id['template']."'"));
                    $btpl = explode(' ', $res['blocktypes']);
                    if ($btpl)
                    foreach ($btpl as $bt)
                    {
                        if ($bt == "") continue;
                        $q = sprintf("SELECT id FROM prname_b_%s WHERE parent=%s", $bt, $id['id']);
                        $res = $sql->query($q);
                        while ($row = $sql->fetch_array($res))
                            delete_block($row['id'], $bt);
                    }

                    $q = "DELETE FROM $prname"."_categories WHERE id=".$id['id'];
                    $sql->query($q);
        */
    };

    $id = $sql->fetch_assoc($sql->query(sprintf("SELECT * FROM prname_categories WHERE id=%s;", $parent)));
    if (!is_array($id)) return;

    $q = mysql_query("describe  $prname" . "_c_" . $id['template']);
    while ($arr_f = mysql_fetch_assoc($q)) {
        //удаление возможных картинок, загруженных в папку
        if ($sql->fetch_row($sql->query("select `key` from prname_cdatarel where `key` ='$arr_f[Field]' and `datatkey`= 'image'"), 0, 1)) {
            $data = $sql->fetch_row($sql->query("select " . $arr_f['Field'] . " from $prname" . "_c_" . $id['template'] . " where  parent=$parent"), 0, 1);
            for ($n = 0; $n < 10; $n++)
                @unlink($config['DOCUMENT_ROOT'] . "images/" . $n . "/" . $data);
        }

        //удаление возможных файлов, загруженных в папку
        if ($sql->fetch_row($sql->query("select `key` from prname_cdatarel where `key` ='$arr_f[Field]' and `datatkey`= 'file'"), 0, 1)) {
            $data = $sql->fetch_row($sql->query("select " . $arr_f['Field'] . " from $prname" . "_c_" . $id['template'] . " where  parent=$parent"), 0, 1);
            @unlink($config['DOCUMENT_ROOT'] . "files/" . $data);
        }


    }


    $res = $sql->fetch_assoc($sql->query("SELECT blocktypes FROM prname_ctemplates WHERE `key` = '" . $id['template'] . "'"));
    $btpl = explode(' ', $res['blocktypes']);
    if ($btpl)
        foreach ($btpl as $bt) {
            if ($bt == "") continue;
            $q = sprintf("SELECT id FROM prname_b_%s WHERE parent=%s", $bt, $id['id']);
            $res = $sql->query($q);
            while ($row = $sql->fetch_array($res))
                delete_block($row['id'], $bt);
        }

    $q = "DELETE FROM prname_categories WHERE id=$parent";
    $sql->query($q);

    $q = sprintf("DELETE FROM prname_c_%s WHERE parent=%s", $id['template'], $parent);
    $sql->query($q);
}

;

function delete_ctemplate($parent, $can_del_inuse = false)
{
    global $prname;
    if ($can_del_inuse) {
        $rowt = true;
    } else {
        $resdc = mysql_query("SELECT `key` FROM $prname" . "_ctemplates WHERE id=$parent");
        $rowdc = mysql_fetch_array($resdc);
        $rowt = (mysql_result(mysql_query("SELECT COUNT(*) AS cnt FROM $prname" . "_categories WHERE template='" . addslashes($rowdc['key']) . "' OR templateinc='" . addslashes($rowdc['key']) . "'"), 0, 0) < 1);
    };
    if ($rowt) {
        mysql_query("DELETE FROM $prname" . "_ctemplates WHERE id=$parent");
        mysql_query("DROP TABLE `$prname" . "_c_" . addslashes($rowdc['key']) . "`");
        mysql_query("DELETE FROM $prname" . "_cdatarel WHERE templid=$parent");
    };
    if (!$rowt) {
        return 'inuse';
    };
}

;

function delete_btemplate($parent)
{
    global $prname;

    $q = mysql_fetch_assoc(mysql_query("select `key` from $prname" . "_btemplates WHERE `id`='$parent'"));
    mysql_query("DROP TABLE `$prname" . "_b_$q[key]` ");
    mysql_query("DELETE FROM $prname" . "_btemplates WHERE id=$parent");
    mysql_query("DELETE FROM $prname" . "_bdatarel WHERE templid=$parent");
}

;

function delete_datatype($parent)
{
    global $prname;
    $key = @mysql_result(mysql_query("SELECT key FROM $prname" . "_datatypes WHERE id=$parent"), 0, 0);
    mysql_query("DELETE FROM $prname" . "_bdatarel WHERE datatkey='" . addslashes($key) . "'");
    mysql_query("DELETE FROM $prname" . "_cdatarel WHERE datatkey='" . addslashes($key) . "'");
    mysql_query("DELETE FROM $prname" . "_datatypes WHERE id=$parent");
}

function copy_category($source, $dest, $sortcc = 0)
{
    global $prname;
    $rescc = mysql_query("SELECT * FROM $prname" . "_categories WHERE id=$source");
    if ($rowcc = mysql_fetch_array($rescc)) {
        if ($sortcc == 0) $sortcc = $rowcc['sort'];
        mysql_query("INSERT INTO $prname" . "_categories set `name`='" . addslashes($rowcc['name']) . "', `key`='" . addslashes($rowcc['key']) . "', `template`='" . addslashes($rowcc['template']) . "', `templateinc`='" . addslashes($rowcc['templateinc']) . "', `visible`='" . $rowcc['visible'] . "', `insertblocks`='" . $rowcc['insertblocks'] . "', `parent`='$dest',`sort`='$sortcc'");
        $id = mysql_insert_id();
        // Копирование блоков
        $d = mysql_result(mysql_query("select blocktypes from $prname" . "_ctemplates where `key`='" . addslashes($rowcc['template']) . "'"), 0, 0);
        $s = explode(" ", $d);
        if (count($s) > 0) {
            for ($ids = 0; $ids < count($s); $ids++)
                if (strlen($s[$ids]) > 0)
                    dublicate_block($source, $s[$ids], $id);
        };
        $idcc = mysql_result(mysql_query("SELECT MAX(id) AS mid FROM $prname" . "_categories WHERE parent=$dest AND sort=$sortcc"), 0, 0);
        $rescc = mysql_query("SELECT id FROM $prname" . "_categories WHERE parent=$source ORDER BY sort");
        while ($rowcc = mysql_fetch_array($rescc))
            copy_category($rowcc['id'], $idcc);
        return $idcc;
    } else {
        return false;
    };
}

;

function dublicate_block($parent, $templ, $newparent)
{
    global $prname;
    $v = mysql_query("select * from $prname" . "_b_" . $templ . " where `parent`='$parent'"); // список блоков.
    $vd = mysql_query("select p1.* from $prname" . "_bdatarel p1,  $prname" . "_btemplates p2 where p1.templid =p2.id and p2.key='$templ'");
    while ($fr = mysql_fetch_array($vd))
        $keyses[$fr[key]] = $fr[datatkey];
    $sort = mysql_result(mysql_query("select MAX(sort) from $prname" . "_b_" . $templ . " where `parent`='$parent'"), 0, 0); // список блоков.
    $c = mysql_query("describe  $prname" . "_b_" . $templ);// Список полей.
    while ($r = mysql_fetch_array($v)) {
        $str = "insert into  $prname" . "_b_" . $templ . " set ";
        while ($keys = mysql_fetch_assoc($c)) {
            if ($keyses[$keys[Field]] != 'file' && $keys[Field] !== 'id' && $keys[Field] !== 'sort' && $keys[Field] !== 'parent') {
                $str .= "`" . $keys[Field] . "` = '" . $r[$keys[Field]] . "' ,";
            }
        }
        $str .= "`sort` = '" . ++$sort . "' , `parent`='$newparent'";
    }
    mysql_query($str);
}


function delete_block($parent, $templ)
{
    global $prname;
    global $sql;

    $q = mysql_query("describe  $prname" . "_b_$templ");
    while ($arr_f = mysql_fetch_assoc($q)) {
        //удаление возможных картинок, загруженных в блок
        if ($sql->fetch_row($sql->query("select `key` from prname_bdatarel where `key` ='$arr_f[Field]' and `datatkey`= 'image'"), 0, 1)) {
            $data = $sql->fetch_row($sql->query("select " . $arr_f['Field'] . " from $prname" . "_b_$templ where  id=$parent"), 0, 1);
            @unlink("../images/0/" . $data);
            @unlink("../images/1/" . $data);
            @unlink("../images/2/" . $data);
            @unlink("../images/3/" . $data);
            @unlink("../images/4/" . $data);
            @unlink("../images/5/" . $data);
            @unlink("../images/6/" . $data);
            @unlink("../images/7/" . $data);
            @unlink("../images/8/" . $data);
            @unlink("../images/9/" . $data);
            @unlink("../images/packet/" . $data);
        }

        //удаление возможных файлов, загруженных в блок
        if ($sql->fetch_row($sql->query("select `key` from prname_bdatarel where `key` ='$arr_f[Field]' and `datatkey`= 'file'"), 0, 1)) {
            $data = $sql->fetch_row($sql->query("select " . $arr_f['Field'] . " from $prname" . "_b_$templ where  id=$parent"), 0, 1);
            @unlink("../files/" . $data);
        }

        // Удаление вложенных блоков.
        $templid = $sql->fetch_row($sql->query("select id from $prname"."_btemplates where `key` = '$templ' "), 0, 1);
        if ($comm = $sql->fetch_row($sql->query("select `comment` from prname_bdatarel where `templid` = ".$templid." AND `key` ='" . $arr_f['Field'] . "' and `datatkey`= 'items'"), 0, 1)) {
            $comm = reset(explode('&', $comm));
            $data = $sql->query("select id from $prname" . "_b_$comm where  blockparent=$parent");
            while ($arr_b = $sql->fetch_row($data, 0, 1))
                delete_block($arr_b, $comm);
        }
    }

    // удаление ЧПУ
    $sql->query(sprintf("DELETE FROM prname_burl WHERE bid = %s AND btemplate = '%s' ", $parent, $templ));

    $sql->query("DELETE FROM $prname" . "_b_$templ WHERE id=$parent");
}


/*
    function delete_item($parent,$templ) 
	{
		global $prname;
		error_reporting(0);
		$q  = mysql_query("describe  $prname"."_b_$templ");
		while ($arr_f = mysql_fetch_assoc($q))
		{
			if($sql->fetch_row($sql->query("select `key` from prname_bdatarel where `key` ='$arr_f[Field]' and `datatkey`= 'file'"),0,1))
			{
				$data = $sql->fetch_row($sql->query("select $arr_f[Field] from $prname"."_b_$templ where  id=$parent"), 0, 1);
					@unlink("../files/0/" . $data);
					@unlink("../files/1/" . $data);
					@unlink("../files/2/" . $data);
					@unlink("../files/3/" . $data);
					@unlink("../files/4/" . $data);
					@unlink("../files/5/" . $data);
					@unlink("../files/6/" . $data);
					@unlink("../files/7/" . $data);
					@unlink("../files/8/" . $data);
					@unlink("../files/9/" . $data);
			}	
		// Удаление вложенных блоков.
			if($comm = $sql->fetch_row($sql->query("select `comment` from prname_bdatarel where `key` ='$arr_f[Field]' and `datatkey`= 'items'"),0,1))
			{
			  $data = $sql->query("select id from $prname"."_b_$comm where  blockparent=$parent");
			  while ($arr_b = $sql->fetch_row($data,0,1))delete_item($arr_b,$comm);
			}
		}
        $sort = mysql_result(mysql_query("select sort from $prname"."_b_$templ where id=$parent"), 0, 1);
		mysql_query("update  $prname"."_b_$templ set sort = sort-1 WHERE sort>$sort");
		mysql_query("DELETE FROM $prname"."_b_$templ WHERE id=$parent");
	}
	
*/

function resize_image($data, $val, $resized, $patch = '../', $prefix = '')
{
    $oldval = $val;
    if (!$sizes = @getimagesize($patch . "images/" . $prefix . "0/$data")) return false;

    //определяем, хватит ли памяти для обработки этого изображения
    $memoryNeeded = round(($sizes[0] * $sizes[1] * $sizes['bits'] * $sizes['channels'] / 8 + Pow(2, 16)) * 1.65);
    $memoryNeeded = $memoryNeeded * 2;//требуем как минимум двухкратный запас памяти
    if (function_exists('memory_get_usage') && memory_get_usage() + $memoryNeeded > (integer)ini_get('memory_limit') * pow(1024, 2)) {
        return false;
        //не зватает памяти для обработки этого изображения
    }

    $imgtype = strtolower(end(explode('/', $sizes['mime'])));

    if ($imgtype == 'jpg' || $imgtype == 'jpeg') {
        $im = @imagecreatefromjpeg($patch . "images/" . $prefix . "0/$data");
    } elseif ($imgtype == 'png') {
        $im = @imagecreatefrompng($patch . "images/" . $prefix . "0/$data");
    } elseif ($imgtype == 'gif') {
        $im = @imagecreatefromgif($patch . "images/" . $prefix . "0/$data");
    }
    if (!$im) return false;
    
    if (!$sizes = @getimagesize($patch . "images/" . $prefix . "0/$data")) return false;
    image_fix_orientation($im, $sizes, $patch . "images/" . SITE_ID . "/" . $prefix . "0/$data");
    if ($around = (strpos($val, "around") !== false)) $val = str_replace("around", "", $val);

    $sizes_origin = $sizes;

//!!!rightcrop
    if ($rightcrop = (strpos($val, "rightcrop") !== false)) {

        $val = str_replace("rightcrop", "", $val);//режим обрезки  справа
        $crop = 1;
    } else
        if ($crop = (strpos($val, "crop") !== false)) $val = str_replace("crop", "", $val);//режим обрезки

    if ($frame = (strpos($val, "frame") !== false)) $val = str_replace("frame", "", $val);//режим обрезки

    $mimes = explode("x", $val);
    if (!$mimes[2]) $mimes[2] = 70;
    if ($crop) //режим "вписанный фрагмент целого"
    {
        if ($sizes[1] / $mimes[1] > $sizes[0] / $mimes[0]) {
            $sizes[1] = round($sizes[0] * $mimes[1] / $mimes[0]);
        } else {
            $sizes[0] = round($sizes[1] * $mimes[0] / $mimes[1]);
        }
    } else
        if (!$around) {
            if (($mimes[0] / $mimes[1]) > ($sizes[0] / $sizes[1])) {
                $mimes[0] = round($sizes[0] * min($mimes[1], $sizes[1]) / $sizes[1]);
            } else {
                $mimes[1] = round($sizes[1] * min($mimes[0], $sizes[0]) / $sizes[0]);
            };
            $mimes[0] = min($mimes[0], $sizes[0]);
            $mimes[1] = min($mimes[1], $sizes[1]);
        } else {
            if (($mimes[0] / $mimes[1]) < ($sizes[0] / $sizes[1])) {
                $mimes[0] = round($sizes[0] * min($mimes[1], $sizes[1]) / $sizes[1]);
            } else {
                $mimes[1] = round($sizes[1] * min($mimes[0], $sizes[0]) / $sizes[0]);
            };

            $mimes[0] = min($mimes[0], $sizes[0]);
            $mimes[1] = min($mimes[1], $sizes[1]);
        };


    $dstX = 0;
    $dstY = 0;

    if ($frame) {
        $mimes2 = explode("x", $val);
        $im2 = imagecreatetruecolor($mimes2[0], $mimes2[1]);
        //заливаем белым цветом
        $color = 0xFFFFFF;
        imagefill($im2, 0, 0, $color);
        $dstX = $mimes2[0] > $mimes[0] ? intval(($mimes2[0] - $mimes[0]) / 2) : 0;
        $dstY = $mimes2[1] > $mimes[1] ? intval(($mimes2[1] - $mimes[1]) / 2) : 0;

    } else

        $im2 = imagecreatetruecolor($mimes[0], $mimes[1]);

    imagealphablending($im2, false);
    imagesavealpha($im2, true);

//!!!
    if ($rightcrop) //обрезка справа
    {
        imagecopyresampled($im2, $im, 0, 0, round(($sizes_origin[0] - $sizes[0])), 0, $mimes[0], $mimes[1], $sizes[0], $sizes[1]);
    } else {
		if ($sizes_origin[0] > $sizes[0]) {
			imagecopyresampled($im2, $im, 0, 0, round(($sizes_origin[0]-$sizes[0])/2), 0, $mimes[0], $mimes[1], $sizes[0], $sizes[1]);
		} else {
            imagecopyresampled($im2, $im, $dstX, $dstY, 0, 0, $mimes[0], $mimes[1], $sizes[0], $sizes[1]);
        }
    }


    if (!is_dir($patch . "images/" . $prefix . $resized)) mkdir($patch . "images/" . $prefix . $resized);


    if ($wfilename = strstr($val, 'watermark'))
        if (is_file('img/watermark/' . $wfilename . '.png')) {
            //global $config;
            include_once("libs/watermark.php");
            $watermark = new  watermark();
            $main_img_obj = $im2;
            $watermark_img_obj = imagecreatefrompng('img/watermark/' . $wfilename . '.png');

            $im3 = $watermark->create_watermark($main_img_obj, $watermark_img_obj, 50);

            $im2 = $im3;
            //imagejpeg($im3, "files/wmark/".$one_arr['img']);

        }

    if ($imgtype == 'png') {
        $quality = intval(round((100 - $mimes[2]) / 10));
        if ($quality < 0)
            $quality = 0;

        imagepng($im2, $patch . "images/" . $prefix . $resized . "/$data", $quality);
    } else
        imagejpeg($im2, $patch . "images/" . $prefix . $resized . "/$data", $mimes[2]);

    imagedestroy($im2);
    return true;
}

;



    function update_images($fn, $resized)
    {
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;

                case 6:
                    $image = imagerotate($image, -90, 0);
                    $w = $sizes[0];
                    $sizes[0] = $sizes[1];
                    $sizes[1] = $w;
                    break;

                case 8:
                    $image = imagerotate($image, 90, 0);
                    $w = $sizes[0];
                    $sizes[0] = $sizes[1];
                    $sizes[1] = $w;
                    break;
            }
        }
    }


function image_fix_orientation(&$image, &$sizes, $filename)
{
    if (function_exists('exif_read_data')) $exif = @exif_read_data($filename);

    global $prname;
    if ($resized < 1) return false;
    $q = "SELECT * FROM $prname" . "_data WHERE data='$fn'";
    $res = mysql_query($q);
    $row = mysql_fetch_array($res);
    $q = "SELECT comment FROM $prname" . "_" . (($row['blockid'] > 0) ? 'b' : 'c') . "datarel WHERE `key`='" . addslashes($row['relkey']) . "'";
    $res2 = mysql_query($q);
    if (($comment = @mysql_result($res2, 0, 0)) === false) return false;
    if (($n = strpos(strtolower($comment), 'resize:')) === false) return false;
    if (($n2 = strpos($comment, ' ', $n)) == false) $n2 = strlen($comment);
    $n += 7;
    $val2 = substr($comment, $n, $n2 - $n);
    $sz = explode(",", $val2);
    if ($resized > count($sz)) return false;

    return resize_image($fn, $sz[$resized - 1], $resized);
}

;

function btemplate_num_fields($s)
{
    global $prname;
    $res = mysql_query("SELECT COUNT(r.id) FROM $prname" . "_bdatarel r, $prname" . "_btemplates t WHERE t.key='" . addslashes($s) . "' AND t.id=r.templid");
    return mysql_result($res, 0, 0);
}

;

function stylizetables($s)
{
    $s = str_replace('<TABLE', '<TABLE class="cont"', $s);
    $s = str_replace('<table', '<table class="cont"', $s);
    return $s;
}

;


function ctemplate_num_fields($s)
{
    global $prname;
    $res = mysql_query("SELECT COUNT(r.id) FROM $prname" . "_cdatarel r, $prname" . "_ctemplates t WHERE t.key='" . addslashes($s) . "' AND t.id=r.templid");
    return mysql_result($res, 0, 0);
}

;

$prerror = array();
$prerror[1] = 'Невозможно удалить выбранную папку';
$prerror[2] = 'Невозможно скрыть или показать выбранную папку';
$prerror[3] = 'Невозможно скопировать выбранную папку';
$prerror[4] = 'Невозможно переместить выбранную папку';
$prerror[5] = 'Невозможно редактировать выбранную папку';
$prerror[6] = 'Невозможно добавлять папки в выбранную папку';
$prerror[101] = 'Невозможно удалить выбранный блок';
$prerror[102] = 'Невозможно скрыть или показать выбранный блок';
$prerror[103] = 'Невозможно скопировать выбранный блок';
$prerror[104] = 'Невозможно переместить выбранный блок';
$prerror[105] = 'Невозможно редактировать выбранную папку';
$prerror[106] = 'Невозможно добавлять папки в выбранную папку';

$months = array('01' => 'января',
    '02' => 'февраля',
    '03' => 'марта',
    '04' => 'апреля',
    '05' => 'мая',
    '06' => 'июня',
    '07' => 'июля',
    '08' => 'августа',
    '09' => 'сентября',
    '10' => 'октября',
    '11' => 'ноября',
    '12' => 'декабря');

function getpath($page, $id)
{
}

;

function optimize_tables()
{
    global $prname;
    mysql_query("OPTIMIZE TABLE " . $prname . "_blocks");
    mysql_query("OPTIMIZE TABLE " . $prname . "_categories");
    mysql_query("OPTIMIZE TABLE " . $prname . "_data");
}

;
function formatprice($prc)
{
    global $dollar;
    $prc = (0 + $prc) * $dollar;
    $prc = round($prc, 2);
    $prc = str_replace('.', ',', $prc);
    return $prc;
}

;

function check_update_cat_double($cat_key, $CID, $parentCID = 0)
{
    global $sql;
    
    if (!strlen($cat_key) || !(int)$CID) return;
    if (!(int)$parentCID) {
        $parentCID = $sql->one_record(sprintf("SELECT parent FROM prname_categories WHERE id = '%s' ", $CID));
    }
    
    $doubleID = $sql->one_record(sprintf("SELECT id FROM prname_categories WHERE `key` = '%s' AND `parent` = %s AND `id` != %s LIMIT 1 ", 
        $cat_key, $parentCID, $CID));
    if (intval($doubleID)) {
        $cat_key = $cat_key . '-' . $CID;
        $sql->query(sprintf("UPDATE prname_categories SET `key` = '%s' WHERE `id` = %s ", $cat_key, $CID));
    }
}

?>