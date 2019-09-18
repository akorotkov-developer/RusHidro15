<?php
//error_reporting(E_ALL);
include_once "../includes.php";
include_once "../inc/libs/caching.php";

global $sql;
$sql = new Sql();
$sql->connect();


if (!strstr($_SERVER['SCRIPT_NAME'], 'admin_login.php') && !strstr($_SERVER['SCRIPT_NAME'], '/admin/index.php')) {
	kick_unauth();
}

//


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HEAD>
    <TITLE><?php echo $config['admin_version']; ?> - Система администрирования сайта - СофтМажор
        - <?= strip_tags($admin_name) ?></TITLE>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <META NAME="ROBOTS" CONTENT="NOINDEX"/>
<LINK href="styles/main.css?v=3" rel="stylesheet" type="text/css">
<LINK href="styles/styles.css" rel="stylesheet" type="text/css">
    <LINK href="styles/moderniz.css" rel="stylesheet" type="text/css">
<script src="js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script src="js/jquery-ui-1.11.4.custom/datepicker-ru.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="elfinder/js/elfinder.min.js"></script>
<script src="elfinder/js/i18n/elfinder.ru.js" charset="utf-8"></script>
<script>
	_jQuery = jQuery.noConflict(true);
</script>
<link rel="stylesheet" href="js/jquery-ui-1.11.4.custom/jquery-ui.min.css"/>
<link rel="stylesheet" href="js/jquery-ui-1.11.4.custom/jquery-ui.structure.min.css"/>
<link rel="stylesheet" href="js/jquery-ui-1.11.4.custom/jquery-ui.theme.min.css"/>

<link rel="stylesheet" type="text/css" href="elfinder/css/elfinder.min.css">
<link rel="stylesheet" type="text/css" href="elfinder/css/theme.css">


<script>
bpressed = '';


// ---------------------------------------
// Центральная функция определения.
// ---------------------------------------
        function $(id) {
   return document.getElementById(id);
}

        function $action(param) {
	$('actions').value=param;
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
  if($(id).style.display !== 'block') processing(id,1)
}
        function contexthide(id) {
  if($(id).style.display == 'block')processing(id,2)
}
// ---------------------------------------
// Видимость.
// ---------------------------------------
// Старт видимости.
// Переменные 
var changeTime = 2;  // Скорость перемен.
        function processing(id, v) {
            if (v == 1) {
   if ($(id).filters)$(id).style.filter = 'alpha(opacity=0)';else $(id).style.opacity = 0;
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
    if ($(id).filters) $(id).style.filter = 'alpha(opacity=' + (100-c) + ')'
    else $(id).style.opacity = (100-c) / 100;
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
  if(sy && sx){
  $('data1text').value= sx;
  $('data2text').value= sy;
  $('layer2').style.left=(sx-18)+'px';
  $('layer2').style.top=(sy-15)+'px';
  }
  return true;
}
/*
Всплывающая подсказка - l+id предка

*/
        function out(id) {
  eval("divInv"+id+" = setInterval('if($(\\'l"+id+"\\').style.display==\\'block\\'){contexthide(\\'l"+id+"\\');}',800)");
}
        function over(id) {
  eval("clearInterval(divInv"+id+");context('l"+id+"');");
}

        function gettitle_layer12() {
  var o = $('layerparent').getElementsByTagName('div');
  var vbb = 0;
  for ( var i = 0;i < o.length; i++)
  
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
			    eval("divInv"+o[i].id+"='';")
			}
}
// ---------------------------------------
// Карта
// ---------------------------------------
//-->
</script>
<script language="javascript" type="text/javascript" src="js/popcalendar.js"></script>	
<script language="javascript" type="text/javascript" src="js/admin.js?v=2"></script>	
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>	
<script language="javascript" type="text/javascript" src="js/tree.js"></script>	


</HEAD>


<BODY <?=$bodyinc22?> >


<? if (in_array($_SERVER['REMOTE_ADDR'], $config['admin_IP']) and !strstr($_SERVER['HTTP_HOST'], "smhost")) {
    ; ?>
    <div style="padding: 2px 0; text-align: center; color: #f00; font-size: 13px; font-weight: bold;">Внимание! Это
        боевой сайт!
    </div>
<? } ?>


<script type="text/javascript">
<!--

var iframeAddr = '/admin/ajax/index.php?a=<?php echo rand(1, 999).time(); ?>';

<?php
	if ($config['admin_preload_tree'])	{
?>
var iframeYes = true;
<?php
	}		else	{
?>
var iframeYes = false;
<?php
	}
?>


window.name = 'parent_window';


var timeout;

function clickAction() {

if ($('add_button_submit'))		$('add_button_submit').disabled = false;
if ($('add2_button_submit'))		$('add2_button_submit').disabled = false;

	frameFitting();

	if ( document.getElementById('demo_frame').contentWindow.document.body )		{
		var fr = document.getElementById('demo_frame').contentWindow.document.body.getElementsByTagName('a');
		for (var i=0; i<fr.length; i++) {
			fr[i].onclick = function() {
			clearInterval(timeout);
			timeout = setInterval("frameFitting()",100);
			}
		}
	}
}

function frameFitting() {

	document.getElementById('demo_frame').width = '100%';
	if ( document.getElementById('demo_frame').contentWindow.document.body )		{
		document.getElementById('demo_frame').height = document.getElementById('demo_frame').contentWindow.document.body.scrollHeight+'px';
	}


}


//-->
</script>

						
<table cellpadding="0" cellspacing="0" id="menu_open" >
	<tr>

		<td>


<?php
	if ( user_is('admin_id') )	{
?>
			<div id="sub2" class="small" onClick="sMenu(1)" style="display:block" >
				
                <div class="green" style="height:400px" >


                </div>
                <div class="black" style="height:50px" >
                </div>
		
			</div>

                <div id="sub" class="sub_menu" style="display:none"><img onClick="sMenu(0)" class="close"
                                                                         src="img/close_menu.gif" width="18" height="18"
                                                                         id="treeclose" alt="Скрыть"/>

                <div class="green" >
                    <h1 class="border"><span>Структура&nbsp;сайта</span></h1>
                        <div class="tree" id="tree" style="width:<?php echo $config['admin_menu_width']; ?>px" >

                            <iframe src="" id="demo_frame" align="center" scrolling="no" frameborder="0"
                                    marginheight="0" marginwidth="0" vspace="0" hspace="0"></iframe>
						<?php
							if ($config['admin_preload_tree'])	{
						?>
						<script>
							//alert (iframeAddr);
						document.getElementById('demo_frame').src = iframeAddr;
						</script>
						<?php
							}	
						?>

						
                        </div>

				
						<div style="height:20px; width:100%; text-align:right;">
                            <span><a href="#" style="color:#fff"
                                     onClick="changeWidthTree('left'); return false;">&larr;</a></span> &nbsp; <span><a
                                    href="#" style="color:#fff"
                                    onClick="changeWidthTree('right'); return false;">&rarr;</a></span> &nbsp;&nbsp;&nbsp;
                        </div>
						
						<!--  /tree  -->
<?php
		if (user_is('super') )	{
?>	
					
<?php
		}
?>
					<!--вставка пунктов общих для всех админов//-->
					<!--
				        <h1>&nbsp;</h1>
					//-->
				</div><!--  /green  -->


                <div class="black" >
					<!--<h1><a href="_ext_importcsv.php" title="">Импорт&nbsp;CSV</a></h1>-->

					<!--вставка пунктов общих для всех админов//-->
					<!--
				        <h1>&nbsp;</h1>
					//-->
                    <h1><a href="settings.php" title="Настройки">Настройки</a></h1>

<?php
		if (user_is('super') )	{
?>

                    <h1><a href="s_cattempl.php" title="Шаблоны данных">Шаблоны&nbsp;папок</a></h1>
                    <h1><a href="s_bltempl.php" title="Шаблоны модулей">Шаблоны&nbsp;блоков</a></h1>
                    <h1><a href="s_datatempl.php" title="Шаблоны модулей">Типы&nbsp;данных</a></h1>
                    <h1><a href="admins.php">Пользователи</a></h1>
                    <h1><a href="db.php" title="Импорт/Экспорт БД" style="color:green">Импорт/Экспорт БД</a></h1>
                    <h1><a href="protection.php" title="Настройки защиты">Настройки защиты</a></h1>
                    <h1><a href="adminlog.php" title="Журнал событий">Журнал событий</a></h1>

<?php
		}	else if (!user_is('is_moder'))	{
?>
				<?php
					if ($config['admin_is_moder'])		{
				?>
                    <h1><a href="admins.php" title="Модераторы">Модераторы</a></h1>
				<?php
					}
				?>
                    <h1><a href="admins_edit.php" title="Управление доступом">Реквизиты</a></h1>
                    <h1><a href="protection.php" title="Настройки защиты">Настройки защиты</a></h1>
                    <h1><a href="adminlog.php" title="Журнал событий">Журнал событий</a></h1>
<?php
        } else {
		}
?>
                    <h1><a href="help.php" title="Справка">Справка</a></h1>
                    <h1><a href="support.php" title="Тех. поддержка">Техподдержка</a></h1>
                </div><!--  /black  -->

            </div><!--  /sub_menu  -->

<?php

	}

?>

    	</td>
    </tr>
</table>


<!--  /menu_open  -->

<div class="head">
<?php
	if ( user_is('admin_id') )	{
?>
	 <div class="navbar">
    	<span class="user">
        	<img src="img/ico_user.gif" width="9" height="11" alt="" />
            <?php 
				$q = "SELECT admin_name FROM $prname"."_sadmin WHERE admin_id = '".user_is('admin_id')."' ";
				$str1 = mysql_fetch_row(mysql_query($q));
				echo $str1[0];
			?>
        </span>
        <span class="exit">
        	<a href="admin_login.php" title="Выход">Выход</a>
            <a href="admin_login.php" title="Выход"><img src="img/ico_exit.gif" width="15" height="11" alt="Выход" /></a>
        </span>
	</div>
<?php
	}
?>
<style type="text/css"> 
h2.logo:hover a { text-decoration: underline !important; }
</style>
	<table cellpadding="0" cellspacing="0">
    	<tr>

            <td class="logo"><a href="/admin/index.php"><img src="img/logo.png" width="171" height="69"
                                                             alt="СофтМажор"/></a></td>
            <td>
            	<h1>Управление сайтом <?php echo $config['admin_version']; ?></h1>
    			<h2 class="logo"><a href="http://<?= $_SERVER['HTTP_HOST']; ?>" target="_blank" style="color:inherit;text-decoration:none;"><?php 

				$s_n = (str_replace('http://', '', $config['server_url'])); 
				if ($s_n[strlen($s_n)-1] == '/')	$s_n = substr($s_n, 0, strlen($s_n)-1);
				echo $s_n;
				
				?></a></h2>
    			<div class="clear"></div>
			</td>
        </tr>

	</table>
    
   <div class="path" style="width:90% !important;">

<?php

        function search_script_name($script_name, $script)
        {
		if (strstr($script_name, $script))	return true;
	}

	if ( search_script_name($_SERVER['SCRIPT_NAME'], 'cat_edit') )		{
		$one_path = 'Структура&nbsp;сайта';
	}
	if ( search_script_name($_SERVER['SCRIPT_NAME'], 'block') )		{
		$one_path = 'Структура&nbsp;сайта';
	}
	if ( search_script_name($_SERVER['SCRIPT_NAME'], 's_cattempl') )		{
		$one_path = 'Шаблоны&nbsp;папок';
	}
	if ( search_script_name($_SERVER['SCRIPT_NAME'], 's_bltempl') )		{
		$one_path = 'Шаблоны&nbsp;блоков';
	}
	if ( search_script_name($_SERVER['SCRIPT_NAME'], 's_datatempl') )		{
		$one_path = 'Типы&nbsp;данных';
	}
	if ( search_script_name($_SERVER['SCRIPT_NAME'], 'admins') )		{
		$one_path = 'Пользователи';
	}
        if (search_script_name($_SERVER['SCRIPT_NAME'], 'support')) {
            $one_path = 'Техподдержка';
        }
//phpinfo();

?>

<?php 
		if ($one_path != '')	{
			echo '
        <div class="first">
			<img src="img/bg_path_left.gif" width="6" height="23" alt="" /><span>'.$one_path.'</span><img src="img/bg_path2.gif" width="12" height="23" alt="" />
        </div>
			';
		}
?>


<?php
	if (
		( search_script_name($_SERVER['SCRIPT_NAME'], 'cat_edit') )
			|| 
		( search_script_name($_SERVER['SCRIPT_NAME'], 'block') )
        ) {
		if ($cparent)	{
			$path_arr = explode('/', tree_path($cparent));
		}	else	{
                if ($parent) {
                    $path_arr = explode('/', tree_path($parent));
		}
            }

		if (count($path_arr) > 1)		{
			$i = 0;
			foreach ($path_arr as $one_arr)		{
				if ($i > 0)		{
					echo '
        <div class="last">

			<img src="img/bg_path.gif" width="12" height="23" alt="" /><span>'.$one_arr.'</span><img src="img/bg_path2.gif" width="12" height="23" alt="" />
        </div>

					';
				}
				$i++;
			}
		}
	}

?>

        
        <span class="clear"></span>
    </div>
</div>


<script>
if (document.getElementById('demo_frame'))		{
	onload = clickAction;
	var id_iframe = setInterval("clickAction()", 1000);
}
</script>


<table class="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
    	<td class="cont">
			<div id="div_cont" style="">



