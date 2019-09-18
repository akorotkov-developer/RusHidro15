<?

/*
###############################################################################
#  (c)SOFTMAJOR 2011
###############################################################################
#  tovsuhov@softmajor.ru
################################################################################
Метамодуль - класс-прототип для остальных модулей сайта.
В нем реализуется базовый сервисный функционал по взаимодействию с CMS
###############################################################################
*/

class metamodule
{
    protected $moduleName = "";
    protected $defaultWrapper = "inner.html"; //дефолтный базовый шаблон - inner.html
    protected $moduleWrappers = array();
    protected $cTemplates = array(); // массив шаблонов папок, которые обрабатываются данным модулем
    public $html = array('text' => '');

    function __construct()
    {
        $this->moduleName = get_class($this);
    }

    function __destruct()
    {
    }

    //получить список врапперов этого модуля
    /*
   		'index'=>array(module=>'text', html=>'index.html'),
		'textmain'=>array(module=>'text', html=>'text.html'),		
		'news'=>array(module=>'news', html=>'text.html'),		

    */
    function _getWrappers()
    {
        $wrappers = array();

        if (is_array($this->cTemplates) && count($this->cTemplates))
            foreach ($this->cTemplates as $tpl)
                if (isset($this->moduleWrappers[$tpl]))
                    $wrappers[$tpl] = array('module' => $this->moduleName, 'html' => $this->moduleWrappers[$tpl]);
                else
                    $wrappers[$tpl] = array('module' => $this->moduleName, 'html' => $this->defaultWrapper);

        return $wrappers;
    }

    //сохранить настройки врапперов в виде PHP-кода module_wraps.php
    function _saveWrappers()
    {
        $wrappers = $this->_getWrappers();
        $fname = sprintf("%s/../modules/wrappers/%s_wraps.php", dirname(__FILE__), $this->moduleName);
        $phptext = "<?\n";
        $phptext .= '
/* здесь уже доступен $control->template, $control->oper и остальные параметры URL, 
поэтому при необходимости можно динамически менять враппер для модуля
(например для новостей указать разные базовые шаблон у списка новостей и у конкретной новости) :

    global $control;

    if ($control->template == "news" && $control->oper !== "view")
        враппер 1
    else
        враппер 2

*/

';
        $phptext .= sprintf('$%s_wrappers = array (', $this->moduleName);
        $phptext .= "\n";
        foreach ($wrappers as $ctpl => $wrap) {
            $phptext .= sprintf("'%s' => array ('module'=> '%s', 'html'=>'%s'),\n", $ctpl, $wrap['module'], $wrap['html']);
        }
        $phptext .= ");\n";

        $phptext .= sprintf('$wrappers = array_merge($wrappers, $%s_wrappers);', $this->moduleName);
        $phptext .= "\n?>";

        $fp = fopen($fname, "w+");
        fwrite($fp, $phptext, strlen($phptext));
        fclose($fp);

        //заодно создаем папку для шаблона модуля
        $this->_checkTemplateDir();

    }

    //проверить и при необходимости создать каталог для шаблонов этого модуля
    function _checkTemplateDir()
    {
        if (isset($GLOBALS['config']['DOCUMENT_ROOT']))
            $dir = sprintf("%stemplates/%s", $GLOBALS['config']['DOCUMENT_ROOT'], $this->moduleName);
        else
            $dir = sprintf("%s/../../templates/%s", dirname(__FILE__), $this->moduleName);
        if (!is_dir($dir))
            mkdir($dir, 0777);
    }

    //вернуть путь до папки с шаблонами модуля
    function _tplDir()
    {
        if (isset($GLOBALS['config']['DOCUMENT_ROOT']))
            $dir = sprintf("%stemplates/%s", $GLOBALS['config']['DOCUMENT_ROOT'], $this->moduleName);
			if($_SESSION['mobile']) $dir = sprintf("%smobile/%s", $GLOBALS['config']['DOCUMENT_ROOT'], $this->moduleName);
        else
            $dir = sprintf("%s/../../templates/%s", dirname(__FILE__), $this->moduleName);

        return $dir . '/';
    }

    //обработка вызова несуществующего метода
    function __call($name, $params)
    {
        global $config;

        if ($config['metamodule_autocreate'] != 1 || // автодополнение отключено
            $name[0] == '_'
        ) // или первым символом идет "земля", т.е. это служебный метод
        {
            return sprintf("<b>Для объекта класса %s вызван несуществующий метод %s</b>", $this->moduleName, $name);
        } else //добавляем этот метод в исходник модуля
        {

            $fname = sprintf("%sinc/modules/%s.php", $config['DOCUMENT_ROOT'], $this->moduleName);
            $fname_tpl = $this->_tplDir() . $name . ".html";

            if (!is_file($fname_tpl)) //создаем файл шаблона
            {
                $this->_checkTemplateDir(); //при необходимости создаем папку шаблонов модуля

                $tpl = '{reduce:space}
{mask:main}

{* Тут контент*}

{/mask}';
                $fp = fopen($fname_tpl, "w+");
                fwrite($fp, $tpl, strlen($tpl));
                fclose($fp);
            }

            $phptext = '
    function ' . $name . '($arParams=NULL)
    {
        global $control;
        global $config;
        global $sql;

        //попытка загрузить из кэша
' . "        \$_cn = sprintf(\"%s_%s_%s\",get_class(\$this), '" . $name . "',  cache_key()); \n" .
                '        $html = get_cache($_cn);
        if (!is_null($html)) return $html;

// => ТУТ рабочий код метода
// ...
// ...
// ...
// <= ТУТ рабочий код метода

        $html = $this->sprintt($page, $this->_tplDir()."' . $name . '.html");

        //сохраняем кэш
        set_cache($_cn, $html);

        return $html;        
    }

// <#AUTOMETHODS#>
';
            $oldphp = file_get_contents($fname);
            $oldphp = str_replace('// <#AUTOMETHODS#>', $phptext, $oldphp);

            $fp = fopen($fname, "w+");
            fwrite($fp, $oldphp, strlen($oldphp));
            fclose($fp);

            return sprintf("<b>Для объекта класса %s вызван несуществующий метод %s. Он добавлен в класс модуля.</b>", $this->moduleName, $name);

        }
    }

    //аналог статичной функции sprintt
    function sprintt(&$page, $tplfile)
    {
        global $control;

        if (!function_exists('sprintt'))
            return ('<pre>Не подключена библиотека шаблонизации ets.php</pre>');
        /*
		дебаг отключен из-за возможных опасностей
        if ($control->urlparams['D'] == 'EBUG')
        {
           $path = pathinfo($tplfile);
           $file = str_replace('.'.$path['extension'], '', $path['basename']); 


           return '['.$file.']:<PRE>'.print_r($page, true).'</PRE>'.sprintt($page, $tplfile); 
        }
        else
		*/
        return sprintt($page, $tplfile);
    }

    //шаблонизация нативным PHP - альтернатива sprintt (движку ETS)
    function phptpl(&$page, $tplfile)
    {
        global $_GTC;
        $_GTC = $page;

        ob_start();
        include($tplfile);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    function content($params = array())
    {
        return sprintf("<b>Контент для модуля %s не определен.</b>", $this->moduleName);
    }
}

?>