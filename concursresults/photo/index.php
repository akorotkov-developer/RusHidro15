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

        $query = "Select *, max(vote_count) From vote_tablenew WHERE sectioncolumn = ' photo ' group by work_name ";

        //$query = "SELECT *, MAX(vote_count) FROM vote_table GROUP BY work_name";
        $result = $mysqli->query($query);

        /* ассоциативный массив */
        $arrItems = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $arrItems[] = $row;
        }

        function cmp_function($a, $b){
            return ($a['max(vote_count)'] < $b['max(vote_count)']);
        }
        uasort($arrItems, 'cmp_function');








        if ($_GET["tst"] == "tst") {
/*            //Заполним таблицу новыми данными
            $query = "TRUNCATE TABLE vote_tablenew";
            $result = $mysqli->query($query);

            foreach ($arrItems as $item) {
                $query = "INSERT INTO vote_tablenew (vote_id, vote_ipadress, vote_count, work_name, sectioncolumn) VALUES 
                            ('" . $item['vote_id'] . "','" . $item['vote_ipadress'] . "','" . $item['max(vote_count)'] . "','" . $item['work_name'] . "','" . $item['sectioncolumn'] . "')";

                $result = $mysqli->query($query);
            }

            $query = "SELECT * FROM vote_tablenew";
            //$query = "SELECT *, MAX(vote_count) FROM vote_table GROUP BY work_name";
            $result = $mysqli->query($query);


            $arrItems = array();
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $arrItems[] = $row;
            }*/
        }









        $content = "<div class=\"results\">";
        foreach ($arrItems as $item) {
            $content .= "<b>" . $item["work_name"] . "</b> - голосов " . $item["max(vote_count)"] . "<br>";
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