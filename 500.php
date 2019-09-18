<?php
include "inc/var.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Внутренняя ошибка сервера</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
    <link href="<?php echo $config['server_url']; ?>errors/error.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<table class="parent" cellpadding="0" cellspacing="0">
    <tr>
        <td class="pd">&nbsp;</td>
        <td class="content">
            <h1 class="e500">
                Ошибка 500!
            </h1>
            <div class="brd">
                <p>
                    Скорее всего ошибка произошла по нашей вине. Попробуйте зайти на сайт через некоторое время.
                </p>
                <p>
                    Если вас не затруднит &mdash; <a href="mailto:id@softmajor.ru">сообщите нам</a> об этой ошибке.
                </p>
            </div>
            <ul>
                <li><a href="<?php echo $config['server_url']; ?>">Перейти на главную страницу</a></li>
            </ul>
            <p>
                &nbsp;
            </p>
            <p>
                &nbsp;
            </p>
            <p>
                &nbsp;
            </p>
            <img class="block" src="<?php echo $config['server_url']; ?>errors/e.gif" width="500" height="1" alt=""
                 title=""/>
            <div class="sm">
                <a target="_blank" href="http://softmajor.ru"><img align="middle"
                                                                   src="<?php echo $config['server_url']; ?>errors/sm-logo.gif"
                                                                   width="100" height="10" alt="" title=""/></a>
            </div>
        </td>
        <td class="pd">&nbsp;</td>
    </tr>
</table>
</body>
</html>
