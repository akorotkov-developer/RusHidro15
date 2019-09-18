<?


class user extends metamodule
{
    function __construct()
    {
        parent::__construct();

        //обязательно указываем наши шаблоны папок
        $this->cTemplates = array(
            'registration',
            'remember',

        );
        //здесь настраиваем базовый шаблон для каждого шаблона папки, используемого модулем
        $this->moduleWrappers = array(
            'registration' => 'inner.html',
            'remember' => 'inner.html',

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

        if ($control->template == 'registration')//Если хотим зарегистрироваться
        {
            //------------Ищем папку с пользователями и берем ее id-------------------
            $q = $sql->query("SELECT * FROM prname_categories WHERE template='users'");
            $res = mysql_fetch_array($q);
            $usersdir = $res['id'];
            //------------------------------------------------------------------------

            if ($_POST['name'])//если пришли с формы регистрации
            {

                //-------------Забиваем все в класс page что бы поля сохраняли введеные данные при возникновении ошибок
                $page->name = $_POST['name'];
                $page->email = $_POST['email'];
                $page->phone = $_POST['phone'];
                $page->addres = $_POST['addres'];
                //--------------------------------------------------------------------------------

                if ($_POST['captcha'] == $_SESSION['captcha'])//капча введена правильно, начинаем регистрацию
                {


                    //------------Если нашли папку с пользователями то производим регистрацию------------------
                    if ($usersdir) {
                        //-----ищем пользователя по введеному мылу----------------------------
                        $critery = "`email`='" . $_POST['email'] . "' AND";
                        $user = new Listing('user', 'blocks', $usersdir, $critery);
                        $user->get_list();
                        $user->get_item();
                        //--------------------------------------------------------------------

                        if ($user->item)//Если пользователь с таким мылом уже существует, выводим соответствующее сообщение
                        {
                            $page->send = 4;
                        } else//Пользователя с таким мылом нет, регистрируем
                        {
                            //-------Забиваем массив block для создания блока пользователя----
                            $block['email'] = $_POST['email'];
                            $block['name'] = $_POST['name'];
                            $block['phone'] = $_POST['phone'];
                            $block['password'] = md5($_POST[password]);
                            $block['addres'] = $_POST['addres'];
                            //-----------------------------------------------------------------

                            //---Формируем индивидуальный хэш для подтверждения регистрации и восстановления пароля-
                            $hash = $block['name'] . $block['email'];
                            $block['hash'] = md5($hash);
                            //------------------------------------------------------------

                            $block['accept'] = 0;//регистрация не потверждена
                            //----------------------------------------------------------------

                            all::insert_block('user', $usersdir, $block, 1);///создаем блок пользователя

                            //-------Достаем мыло администратора------------------------------
                            $qadmin = $sql->query("SELECT * FROM prname_sadmin WHERE `admin_name`='admin'");
                            $resadmin = mysql_fetch_array($qadmin);
                            $admin = $resadmin['admin_email'];
                            //----------------------------------------------------------------

                            //-------Формируем письма администратору и пользователю-----------
                            $data->name = $_POST['name'];
                            $data->email = $_POST['email'];
                            $data->phone = $_POST['phone'];
                            $data->addres = $_POST['addres'];
                            $data->password = $_POST['password'];
                            $data->site = $config['server_url'];
                            $data->url = $config['server_url'] . "registration?hash=" . $block['hash'];
                            $adminmessage = $this->sprintt($data, $this->_tplDir() . "regadminmail.html");
                            $usermessage = $this->sprintt($data, $this->_tplDir() . "regusermail.html");
                            $adminsubject = 'Зарегистрирован новый пользователь на сайте ' . $config['site_name'];
                            $usersubject = 'Регистрация на сайте ' . $config['site_name'];
                            //-----------------------------------------------------------------

                            $index = all::c_data_all(1, 'index');

                            all::send_mail_from($admin, $_POST['email'], $subject, $message);
                            all::send_mail_from($_POST['email'], $index->mail, $usersubject, $usermessage);
                            $page->send = 1;//Все впорядке, выводим сообщение о успешной регистрации
                        }
                    } else//Если не нашли папку с пользователями то выводим сообщение об ошибке.
                    {
                        $page->send = 3;
                    }
                } else//капча введена не правильно, выводим соответсвующее сообщение
                {
                    $page->send = 2;
                }


            }

            if ($_GET['hash'])//Если пришли для подтверждения регистрации
            {
                //-------Ищем пользователя по хэшу из письма-------------------------
                $critery = "`hash`='" . $_GET['hash'] . "' AND";
                $user = new Listing('user', 'blocks', $usersdir, $critery);
                $user->get_list();
                $user->get_item();
                //-------------------------------------------------------------------

                if ($user->item)//Если нашли пользователя, то активируем
                {
                    $page->accept = 1;
                    all::update_block($user->item[0]->id, 'user', 1, 'accept');
                    $html = $this->sprintt($page, $this->_tplDir() . "accept.html");
                    return $html;
                } else//Не нашли, выводим ошибку
                {
                    $page->accept = 2;
                    $html = $this->sprintt($page, $this->_tplDir() . "accept.html");
                    return $html;
                }
            }


            $html = $this->sprintt($page, $this->_tplDir() . "registration.html");
            return $html;
        }

        if ($control->template = 'remember')//Если хотим восстановить пароль
        {
            //-----------------Достаем id папки с пользователями-----------------------------
            $qusersdir = $sql->query("SELECT * FROM prname_categories WHERE template='users'");
            $resusersdir = mysql_fetch_array($qusersdir);
            $usersdir = $resusersdir['id'];
            //-------------------------------------------------------------------------------

            if ($_POST['email'])//Если пришли с формы: проверяем есть ли такое мыло, выводим сообщения, отсылаем письмо если надо
            {

                //----------Проверяем есть ли пользователь с таким мылом-------------------------
                $critery = "email='" . $_POST['email'] . "' AND";
                $user = new Listing('user', 'blocks', $usersdir, $critery);
                $user->get_list();
                $user->get_item();
                if ($user->item)//если есть то отправляем письмо с подтверждением
                {

                    //--------------Формируем письмо---------------------------------------------
                    $subject = 'Восстановление пароля для учетной записи на сайте ' . $config['server_url'];
                    $data->url = $config['server_url'] . "remember?hash=" . $user->item[0]->hash;
                    $data->site = $config['server_url'];
                    $message = $this->sprintt($data, $this->_tplDir() . "remmail.html");
                    //---------------------------------------------------------------------------
                    $index = all::c_data_all(1, 'index');
                    all::send_mail_from($_POST['email'], $index->email, $subject, $message);

                    $page->message = 1;//Чтобы форма не появлялась
                    $page->remember = 1;//выводим сообщение об отправке письма с инструкцией
                } else//если нет такого пользователя то выводим соотвествующее сообщение
                {
                    $page->remember = 0;
                    $page->message = 0;
                }
            }

            if ($_GET['hash'])//если прошли по ссылке в письме
            {
                //----------Проверяем есть ли пользователь с таким мылом-------------------------
                $critery = "hash='" . $_GET['hash'] . "' AND";
                $user = new Listing('user', 'blocks', $usersdir, $critery);
                $user->get_list();
                $user->get_item();
                if ($user->item)//Если есть то генерим пароль и отправляем на мыло
                {
                    //--------------Формируем письмо---------------------------------------------
                    $subject = 'Новый пароль';
                    $data->password = $this->_generatePassword(8);//генерируем пароль
                    $data->site = $config['server_url'];
                    all::update_block($user->item[0]->id, 'user', md5($data->password), 'password');//Меняем пароль у пользователя
                    $message = $this->sprintt($data, $this->_tplDir() . "remmail.html");
                    //---------------------------------------------------------------------------
                    $index = all::c_data_all(1, 'index');
                    all::send_mail_from($user->item[0]->email, $index->email, $subject, $message);
                    $page->remember = 1;//что бы форма не появлялась
                    $page->message = 2;//Выводим сообщение об отправке сообщения с паролем
                } else {
                    $page->remember = 0;
                    $page->message = 0;
                }
            }
            $html = $this->sprintt($page, $this->_tplDir() . "remember.html");
            return $html;
        }
    }

    function auth($email, $password)
    {
        global $control;
        global $config;
        global $sql;
        //-----------------Достаем id папки с пользователями-----------------------------
        $qusersdir = $sql->query("SELECT * FROM prname_categories WHERE template='users'");
        $resusersdir = mysql_fetch_array($qusersdir);
        $usersdir = $resusersdir['id'];
        //-------------------------------------------------------------------------------
        $critery = "`email`='" . $email . "' and `password`='" . md5($password) . "' and";
        $user = new Listing('user', 'blocks', $usersdir, $critery);
        $user->get_list();
        $user->get_item();
        if ($user->item) {
            return $user;
        } else {
            return false;
        }
    }

    function _generatePassword($number)
    {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'R', 'S',
            'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0');
        // Генерируем пароль
        $pass = "";
        for ($i = 0; $i < $number; $i++) {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

}

?>