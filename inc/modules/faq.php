<?

class faq extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'faq',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'faq' => 'inner.html',

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

        $page = all::c_data_all($control->cid, $control->template);

        $list = new Listing('faq', 'blocks', $control->cid);
        $list->limit = intval($page->cnt) ? $page->cnt : 10;
        $list->page = $control->page;//для постранички
        $list->sortfield = 'date DESC, id';//сортируем по дате
        $list->sortby = 'DESC';
        $list->tmp_url = $list->all->get_url($control->cid); //для постранички
        //$list->no_text_view = 1;

        $list->get_list();
        $list->get_item();
        $page->item = $list->item;

        $page->navigation = $list->navigation;
        $page->url_prev = $list->url_prev;
        $page->url_next = $list->url_next;


        if ($control->oper == 'done')
            $page->sended = 1;

        if ($_POST['fio']) {
            foreach ($_POST as $pkey => $pval) {
                $_POST[$pkey] = htmlspecialchars($pval);
            }

            if ($_POST['captcha'] == $_SESSION['captcha']) {

                $block = array();
                foreach ($_POST as $key => $val) {
                    $block[$key] = stripslashes($val);
                }

                $block['date'] = date('Y-m-d');
                $block['ip'] = $_SERVER['REMOTE_ADDR'];

                all::insert_block('faq', $control->cid, $block, 0);


                $admin = $sql->one_record("SELECT admin_email FROM prname_sadmin WHERE `admin_name`='admin'");
                $data->block = $block;

                $message = $this->sprintt($data, $this->_tplDir() . "mail.html");
                $subject = 'Вопрос с сайта ' . $_SERVER['SERVER_NAME'];

                all::send_mail_from($admin, 'noreply@' . $_SERVER['SERVER_NAME'], $subject, $message);


                $url = All::get_url($control->cid) . '_adone';
                $url = str_replace('<!--base_url//-->', $config['server_url'], $url);
                header('Location: ' . $url);
                die();
            } else {
                $page->error = 'Неверно введен код с картинки';
                $page->post = $_POST;
            }
        }


        $html = $this->sprintt($page, $this->_tplDir() . "faq.html");
        return $html;
    }
}

?>