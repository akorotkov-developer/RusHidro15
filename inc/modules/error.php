<?

/*
###############################################################################
#  (c)SOFTMAJOR 2011-2015
###############################################################################
#  ЗДЕСЬ РАЗМЕСТИТЬ КОНТАКТНЫЕ ДАННЫЕ АВТОРА МОДУЛЯ
################################################################################
   ЗДЕСЬ РАЗМЕСТИТЬ КРАТКОЕ ОПИСАНИЕ ЕГО НАЗНАЧЕНИЯ И Т.П.
*/

class error extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'error',
        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'error' => 'error.html',
        );
    }

    function __destruct()
    {
    }

    //базовый метод сайт-модуля
    function content($arParams = array())
    {
        global $control;
        global $config;
        global $sql;

        header('HTTP/1.0 404 Not Found');

        $control->name = 'Ошибка 404';

        //раскомментировать при доработке
        return $this->sprintt($page, $this->_tplDir().'content.html');
    }

// Сюда будут заноситься автодополняемые методы
// <#AUTOMETHODS#>

}
