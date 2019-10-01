<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    </head>
    <body>
        <?
        $ip = "localhost";
        $db = "rushydro15";
        $login = "rushydro15";
        $pass = "zW4Nqacn6S";


        $mysqli = new mysqli($ip, $login, $pass, $db);
        $mysqli->set_charset("utf8");

        /* проверка подключения */
        if ($mysqli->connect_errno) {
            printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
            exit();
        }

        $query = "SELECT * FROM vote_table WHERE sectioncolumn = ' photo '";
        $result = $mysqli->query($query);

        /* ассоциативный массив */
        $arrItems = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $arrItems[] = $row;
        }

        function array_unique_key($array, $key) {
            $tmp = $key_array = array();
            $i = 0;

            foreach($array as $val) {
                if (!in_array($val[$key], $key_array)) {
                    $key_array[$i] = $val[$key];
                    $tmp[$i] = $val;
                }
                $i++;
            }
            return $tmp;
        }


        $arrItemsResult = array_unique_key($arrItems, 'work_name');
        $arrWorks = array();
        //Ищем максимальное значение для каждого элемента массива
        $i = 0;
        foreach ($arrItemsResult as $key => $item) {
            $max = (int)$item["vote_count"];
            foreach ($arrItems as $work) {
                if ($work["work_name"] == $item["work_name"] and $work["vote_count"] > $max) {
                    $max = $work["vote_count"];
                }
            }
            $arrWorks[$i] = $item;
            $arrWorks[$i]["vote_count"] = $max;
            $i++;
        }

        function cmp_function($a, $b){
            return ($a['vote_count'] < $b['vote_count']);
        }
        uasort($arrWorks, 'cmp_function');

        $content = "<div class=\"results\">";
        foreach ($arrWorks as $item) {
            $content .= "<b>" . $item["work_name"] . "</b> - голосов " . $item["vote_count"] . "<br>";
        }
        $content .=  "</div>";

        /* очищаем результаты выборки */
        $result->free();

        /* закрываем подключение */
        $mysqli->close();

        ?>
        <div class="result_photo">
            <!--begin-photo-->
                <?=$content;?>
            <!--end-photo-->
        </div>
    </body>
</html>