<?php

class Sql
{
    var $dblink = NULL;
    var $prname = NULL;
    var $dbhost = NULL;

    function connect($confArr = array(), $exit = true)
    {
        global $config;
        global $texts;


        if (count($confArr)) {

            $this->_log('open connection to ' . $confArr['dbhost'] . '...');

            if (isset($confArr['prname']))
                $this->prname = $confArr['prname'];

            if (!$this->dblink = @mysql_connect($confArr['dbhost'], $confArr['dbuser'], $confArr['dbpass'], true)) {
                $this->_log('failed: ' . $texts['sql_connection_error']);

                //режим, когда в случае сбоя мы не умираем а возвращаем false
                if ($exit) {
                    echo $texts['sql_connection_error'];
                    exit;
                } else
                    return false;
            }

            $this->dbhost = $confArr['dbhost'];

            if (!$aaa = @mysql_select_db($confArr['dbname'], $this->dblink)) {
                //режим, когда в случае сбоя мы не умираем а возвращаем false
                if ($exit) {
                    echo $texts['sql_connection_error'];
                    exit;
                } else
                    return false;
            };

            if (isset($confArr['mysql_set_names']))
                mysql_query("SET NAMES '" . $config['mysql_set_names'] . "'", $this->dblink);
            else
                mysql_query("SET NAMES 'utf8'", $this->dblink);

            $this->_log($confArr['dbhost'] . ' connected');
            return true;
        } else //используем дефолтные настройки
        {
            if (!$dblink = @mysql_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], true)) {
                echo $texts['sql_connection_error'];
                $this->_log('failed: ' . $texts['sql_connection_error']);

                exit;
            }

            if (!is_null($this)) {
                $this->dblink = $dblink;
                $this->dbhost = $config['dbhost'];
            }

            if (!$aaa = @mysql_select_db($config['dbname'], $dblink)) {
                echo $texts['sql_db_selection_error'];
                $this->_log('failed: ' . $texts['sql_connection_error']);

                exit;
            };

            if (isset($config['mysql_set_names']))
                mysql_query("SET NAMES '" . $config['mysql_set_names'] . "'", $dblink);
            else
                mysql_query("SET NAMES 'utf8'", $this->dblink);

            $this->_log($config['dbhost'] . ' connected');
            return true;
        }

    }


    /*
    функция переключения SQL-соединения на Master-сервер
    - вызывается при наличии в тексте запроса команд на модификацию данных
    - отрабатывается только при наличии опции $config['multiserver'] == 1
    */
    function connectToMaster()
    {
        global $config;

        if (!intval($config['multiserver'])) return;

        //если вызов в режиме метода объекта
        if (isset($this) && get_class($this) == "Sql") {
            if ($this->dbhost == $config['dbhost']) {
                //мы уже на мастере - выходим
                //$this->_log('already Master DB');
                return;
            }
            $this->_log('reconnect to Master DB: ' . $config['dbhost']);

            $this->close();
            $this->connect();
        } else //в режиме статичного метода
        {
            $this->_log('reconnect to Master DB: ' . $config['dbhost']);

            $this->close();
            $this->connect();
        }

        $this->_log('reconnected.');

    }


    //проверка запроса на наличие признаков модификации данных
    function checkModifyQuery($q)
    {
        $arr = array('UPDATE', 'DELETE', 'INSERT', 'GRANT', 'ALTER', 'DROP');
        $flag = false;
        foreach ($arr as $cmd)
            if (stripos($q, $cmd) !== false) {
                $flag = true;
                $break;
            }
        return $flag;
    }

    function query($q)
    {
        global $prname;

        if (isset($this) && get_class($this) == "Sql" && !is_null($this->prname))
            $q = str_replace('prname', $this->prname, $q);
        else
            $q = str_replace('prname', $prname, $q);

        if (isset($this) && get_class($this) == "Sql" && !is_null($this->dblink)) {
            if ($this->checkModifyQuery($q))
                $this->connectToMaster();
            $res = mysql_query($q, $this->dblink) OR die($q . "<br>" . mysql_error($this->dblink));
        } else {
            if ($this->checkModifyQuery($q))
                $this->connectToMaster();
            $res = mysql_query($q) OR die($q . "<br>" . mysql_error());
        }

        return $res;
    }

    function fetch_row($res, $n = -1)
    {
        $str = mysql_fetch_row($res);
        if ($n == -1) {
            return $str;
        } else {
            return $str[$n];
        }
    }

    function one_record($q)
    {
        if (isset($this) && get_class($this) == "Sql" && !is_null($this->dblink)) {
            if ($this->checkModifyQuery($q))
                $this->connectToMaster();

            return $this->fetch_row($this->query($q), 0);
        } else {
            if ($this->checkModifyQuery($q))
                $this->connectToMaster();
            return $this->fetch_row($this->query($q), 0);
        }
    }

    function fetch_array($res, $key = '')
    {
        $str = mysql_fetch_array($res);
        if ($key == '') {
            return $str;
        } else {
            return $str[$key];
        }
    }

    function fetch_assoc($res, $key = '')
    {
        return mysql_fetch_assoc($res);
    }

    //вернуть массив значений
    function fetch_assoc_arr($res, $idxField = NULL)
    {
        $ret = array();
        while ($row = mysql_fetch_assoc($res)) {
            if (is_null($idxField))
                $ret[] = $row;
            else
                $ret[$row[$idxField]] = $row;
        }

        return $ret;
    }


    function fetch_object($res)
    {
        return mysql_fetch_object($res);
    }

    //вернуть массив объектов
    function fetch_object_arr($res, $idxField = NULL)
    {
        $ret = array();
        while ($obj = mysql_fetch_object($res)) {
            if (is_null($idxField))
                $ret[] = clone($obj);
            else
                $ret[$obj->$idxField] = clone($obj);
        }

        return $ret;
    }


    function num_rows($res)
    {
        return mysql_num_rows($res);
    }

    function insert_id()
    {
        if (isset($this) && get_class($this) == "Sql" && !is_null($this->dblink))
            return mysql_insert_id($this->dblink);
        else
            return mysql_insert_id();
    }

    function escape_string($data)
    {
        return mysql_real_escape_string($data);
    }

    function free_result(&$res)
    {
        return mysql_free_result($res);
    }

    function close()
    {
        if (isset($this) && get_class($this) == "Sql" && !is_null($this->dblink)) {
            mysql_close($this->dblink);
            $this->dblink = NULL;
        } else
            mysql_close();

        $this->_log('close connection.');
    }


    //====== логирование событий ====================
    function _log($text)
    {
        global $config;

        //если SQL-логирование явно не указано
        if (!intval($config['sql_log'])) return;

        $logdir = $config['DOCUMENT_ROOT'] . 'logs/';
        $fname = "sqllog-" . date('Y-m-d') . ".log";

        $fd = @fopen($logdir . $fname, 'a');
        if ($fd) {
            fwrite($fd, "[" . date('H:i:s') . "] " . $text . "\n");
            fclose($fd);
        }
    }

}

?>