<?

class contacts extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'contacts',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'contacts' => 'inner.html',

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

        $page = All::c_data_all($control->cid, $control->template);

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

                $control->all->insert_block('sitemessage', $control->cid, $block, 1);


                $admin = $sql->one_record("SELECT admin_email FROM prname_sadmin WHERE `admin_name`='admin'");
                $data = new stdClass();
                $data->block = $block;

                $message = $this->sprintt($data, $this->_tplDir() . "mail.html");
                $subject = 'Сообщение с сайта ' . $_SERVER['SERVER_NAME'];
                $control->all->send_mail_from($admin, 'noreply@' . $_SERVER['SERVER_NAME'], $subject, $message);


                $url = $control->all->get_url($control->cid) . '_adone';
                $url = str_replace('<!--base_url//-->', $config['server_url'], $url);
                header('Location: ' . $url);
                die();

            } else {
                $page->error = 'Неверно введен код с картинки';
                $page->post = $_POST;
            }

        }


        $html = $this->sprintt($page, $this->_tplDir() . "contacts.html");
        return $html;

    }


// Сюда будут заноситься автодополняемые методы
}

?>