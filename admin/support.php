<?php
/**
 * Created by PhpStorm.
 * User: yarullin
 * Date: 04.04.2016
 * Time: 16:46
 */
ob_start();
require 'templates/includes/new_top.php';

$tpl = 'content.html';
$page = new stdClass();
$page->count = json_decode(exec_query($data = array('action' => 'count')));
if ($_GET['error']) $page->error = true;
if ($_GET['success']) $page->success = true;

$data = array();

switch ($_REQUEST['action']) {
    case 'create':
        $data = array(
            'email' => $_REQUEST['email'],
            'from' => $_REQUEST['name'],
            'subj' => $_REQUEST['subj'] ? 'Re: ' . $_REQUEST['subj'] : '',
            'message' => htmlspecialchars($_REQUEST['text']),
        );
        $data['action'] = 'create';
        $result = exec_query($data);
        if ((int)$result > 0 || $result != 0) {
            header('Location: support.php?error=true');
        } else {
            header('Location: support.php?success=true');
        }
        exit;
    case 'add':
        $data = array(
            'id' => $_REQUEST['id'],
            'message' => htmlspecialchars($_REQUEST['text']),
        );
        $data['action'] = 'add';
        $result = exec_query($data);
        if ((int)$result > 0 || $result != 0) {
            header('Location: support.php?error=true');
        } else {
            header('Location: support.php?success=true&id=' . $_REQUEST['id'] . '&action=view');
        }
        exit;
    case 'new':
        $tpl = 'create.html';
        break;
    case 'view':
        $data = array(
            'action' => 'get',
            'id' => $_REQUEST['id'],
        );
        $result = exec_query($data);
        if ((int)$result > 0) {
            header('Location: support.php?error=true');
            exit;
        } else {
            $result = (array)json_decode($result);
            foreach ($result as $key => $val) {
                $page->{$key} = $val;
            }
        }
        if (is_array($page->msg)) {
            foreach ($page->msg as $msg) {
                if (!empty($msg->attach)) {
                    foreach ($msg->attach as $file)
                        get_attach($file);
                }
                if (is_array($msg->resp)) {
                    foreach ($msg->resp as $resp) {
                        if (!empty($resp->attach)) {
                            foreach ($resp->attach as $file)
                                get_attach($file);
                        }
                    }
                }
            }
        }
        $tpl = 'ticket.html';
        break;
    default:
        $data['action'] = 'list';
        $page->status = $data['status'] = $_GET['status'] ? $_GET['status'] : 'open';
        $result = exec_query($data);
        if (is_numeric($result) && $result > 0) {
            header('Location: support.php?error=true');
            exit;
        } else {
            $result = json_decode($result);
            $page->result = $result;

        }
}

function iconv_recursive(&$data, $from = 'utf-8', $to = 'windows-1251')
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            iconv_recursive($data[$key], $from, $to);
        }
    } elseif (is_object($data)) {
        foreach ((array)$data as $key => $val) {
            iconv_recursive($data->{$key}, $from, $to);
        }
    } elseif (is_string($data)) {
        $data = iconv($from, $to, $data);
    }
}

function get_attach(&$attach)
{
    $lname = sprintf('%d-%d.%s', $attach->attach_id, $attach->ticket_id, pathinfo($attach->file_name, PATHINFO_EXTENSION));
    if (!file_exists('tmp/' . $lname)) {
        $data = array('action' => 'attach', 'id' => $attach->attach_id, 'ticket_id' => $attach->ticket_id);
        $result = exec_query($data);
        if ($result === 'error') {
            return false;
        } else {
            file_put_contents('tmp/' . $lname, $result);
        }
    }
    $attach->file = 'tmp/' . $lname;
}


function normalize_files_array($files)
{

    $normalized_array = array();

    foreach ($files as $index => $file) {

        if (!is_array($file['name'])) {
            $normalized_array[$index][] = $file;
            continue;
        }

        foreach ($file['name'] as $idx => $name) {
            if ($file['error'][$idx] > 0) continue;
            $normalized_array[$index][$idx] = array(
                'name' => $name,
                'type' => $file['type'][$idx],
                'tmp_name' => $file['tmp_name'][$idx],
                'error' => $file['error'][$idx],
                'size' => $file['size'][$idx]
            );
        }

    }

    return $normalized_array;

}

function exec_query(&$data)
{
    $data['sitename'] = $_SERVER['HTTP_HOST'];
    $params = array(
        'key' => '5440637602817D5CAA92BF1A5E50EA91',
        'data' => json_encode($data),
    );
    $files = normalize_files_array($_FILES);
    if (isset($files['attach'])) {
        foreach ($files['attach'] as $i => $file) {
            move_uploaded_file($file['tmp_name'], 'tmp/' . $file['name']);
            $params['attach[' . $i . ']'] = '@' . dirname(__FILE__) . '/tmp/' . $file['name'];
        }
    }


    $params['sign'] = md5(md5($params['data']) . md5($params['key']));
    $url = 'http://support.softmajor.ru/api/api.php?' .
        //$url = 'http://support.smtest/api/api.php?' .
        http_build_query($params);
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
    ));
    return curl_exec($curl);
}

/*
[email] = yarullin@softmajor . ru
[from] = Ruslan
[subj] = Support % 20testing
[message] = SUPPORT % 20API % 20TEST
[sitename] = cms . smtest
*/

echo sprintt($page, 'templates/support/' . $tpl);

require 'templates/includes/new_bottom.php';
ob_flush();