<?php
//error_reporting(E_ALL);
include_once "../../includes.php";
include_once "../../inc/libs/caching.php";

global $sql;
$sql = new Sql();
$sql->connect();

if (!strstr($_SERVER['SCRIPT_NAME'], 'admin_login.php')) {
    kick_unauth();
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HEAD>
    <TITLE>Система администрирования сайта - SoftMajor - <?= strip_tags($admin_name) ?></TITLE>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK href="../styles/main.css" rel="stylesheet" type="text/css">
    <LINK href="../styles/styles.css" rel="stylesheet" type="text/css">
    <script>
        bpressed = '';

        // ---------------------------------------
        // Центральная функция определения.
        // ---------------------------------------
        function $(id) {
            return document.getElementById(id);
        }

        function $action(param) {
            $('actions').value = param;
        }

    </SCRIPT>

    <script language="javascript">
        <!--
        // ---------------------------------------
        // Карта
        // ---------------------------------------
        function $(id) {
            return document.getElementById(id);
        }

        // ---------------------------------------
        // Контекстное меню.
        // ---------------------------------------
        function context(id) {
            if ($(id).style.display !== 'block') processing(id, 1)
        }
        function contexthide(id) {
            if ($(id).style.display == 'block')processing(id, 2)
        }
        // ---------------------------------------
        // Видимость.
        // ---------------------------------------
        // Старт видимости.
        // Переменные
        var changeTime = 2;  // Скорость перемен.
        function processing(id, v) {
            if (v == 1) {
                if ($(id).filters)$(id).style.filter = 'alpha(opacity=0)'; else $(id).style.opacity = 0;
                for (var c = 1; c <= 80; c++) window.setTimeout(function (c) {
                    return function () {
                        sets_opacity(c, id);
                    }
                }(c), changeTime * c);
            } else {
                for (var c = 1; c <= 80; c++) window.setTimeout(function (c) {
                    return function () {
                        sets_opacitydown(c, id);
                    }
                }(c), changeTime * c);
            }
            ;
        }

        function sets_opacity(c, id) {
            if (c == 1)$(id).style.display = 'block';
            if ($(id).filters)
                $(id).style.filter = 'alpha(opacity=' + c + ')';
            else
                $(id).style.opacity = c / 100;
        }

        function sets_opacitydown(c, id) {
            if ($(id).filters) $(id).style.filter = 'alpha(opacity=' + (100 - c) + ')'
            else $(id).style.opacity = (100 - c) / 100;
            if (c == 80) {
                $(id).style.display = 'none';
            }
        }

        function fun1(evnt) {
            if (!window.event) {
                if (evnt.target.id == 'layerimg')var sx = evnt.layerX;
            } else {
                if (event.srcElement.id == 'layerimg')var sx = event.offsetX;
            }
            if (!window.event) {
                if (evnt.target.id == 'layerimg')var sy = evnt.layerY;
            } else {
                if (event.srcElement.id == 'layerimg') var sy = event.offsetY;
            }
            if (sy && sx) {
                $('data1text').value = sx;
                $('data2text').value = sy;
                $('layer2').style.left = (sx - 18) + 'px';
                $('layer2').style.top = (sy - 15) + 'px';
            }
            return true;
        }
        /*
         Всплывающая подсказка - l+id предка

         */
        function out(id) {
            eval("divInv" + id + " = setInterval('if($(\\'l" + id + "\\').style.display==\\'block\\'){contexthide(\\'l" + id + "\\');}',800)");
        }
        function over(id) {
            eval("clearInterval(divInv" + id + ");context('l" + id + "');");
        }

        function gettitle_layer12() {
            var o = $('layerparent').getElementsByTagName('div');
            var vbb = 0;
            for (var i = 0; i < o.length; i++)

                if (o[i].className == 'layermarker') {
                    if (window.addEventListener) {
                        o[i].addEventListener('mouseover', function () {
                            over(this.id);
                        }, false);
                    }
                    else o[i].onmouseover = function () {
                        over(this.id);
                    }
                    if (window.addEventListener)o[i].addEventListener('mouseout', function () {
                        out(this.id);
                    }, false);
                    else o[i].onmouseout = function () {
                        out(this.id);
                    }
                    vbb++;
                    eval("divInv" + o[i].id + "='';")
                }
        }
        // ---------------------------------------
        // Карта
        // ---------------------------------------
        //-->
    </script>
    <script language="javascript" type="text/javascript" src="../js/tree.js"></script>


</HEAD>

<BODY>


<div id="tree" style="background: #fff; padding:5px; ">



