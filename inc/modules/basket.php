<?


class basket extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'basket',


        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'basket' => 'inner.html',


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

        if ($_SESSION['ses_basket']) {
            foreach ($_SESSION['ses_basket'] as $item) {
                $goods = all::b_data_all($item->id, 'goods');
                $page->item[$item->id] = $goods;
                $page->item[$item->id]->col = $item->col;
            }
        }

        $html = $this->sprintt($page, $this->_tplDir() . "showbasket.html");
        return $html;
    }

    function add($id, $title, $price, $col)
    {
        if (!$_SESSION['ses_cost']) $_SESSION['ses_cost'] = 0;
        if (!$_SESSION['ses_basket']) $_SESSION['ses_basket'] = array();
        if (!$_SESSION['ses_basket'][$id]) {
            $_SESSION['ses_basket'][$id]->id = $id;
            $_SESSION['ses_basket'][$id]->title = $title;
            $_SESSION['ses_basket'][$id]->price = $price;
            $_SESSION['ses_basket'][$id]->col = $col;
            $_SESSION['ses_cost'] += ($col * $price);
        } else {

            $_SESSION['ses_basket'][$id]->col += $col;
            $_SESSION['ses_cost'] += ($col * $price);
        }

    }

    function delete($id)
    {
        if ($_SESSION['ses_basket']) {
            $price = $_SESSION['ses_basket'][$id]->price;
            $col = $_SESSION['ses_basket'][$id]->col;
            $_SESSION['ses_cost'] -= ($price * $col);
            unset($_SESSION['ses_basket'][$id]);
        }
    }

    function change($id, $col)
    {
        $price = $_SESSION['ses_basket'][$id]->price;
        $costold = $price * $_SESSION['ses_basket'][$id]->col;
        $_SESSION['ses_basket'][$id]->col = $col;
        $_SESSION['ses_cost'] -= $costold;
        $_SESSION['ses_cost'] += ($col * $price);
    }
}

?>