<html>
<body>
<style>
    a {
        color: #2b2b2b;
        line-height: 30px;
    }
    .findip {
        cursor: pointer;
    }
</style>
<script
    src="https://code.jquery.com/jquery-3.4.1.js"
    integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
    crossorigin="anonymous"></script>
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

if ($_GET["work_name"]) {
    ?>
    <script>
        $( document ).ready(function() {

            $("body").on("click", ".findip", function () {

                $.post(
                    "/concursresults/video/ajax.php",
                    {
                        curIP: $(this).attr('data-ip'),
                    },
                    function (data) {
                        //var arrParams = JSON.parse(data);
                        $('#ipinfo').html(data);
                    }
                );
            });

        });
    </script>
    <?
    echo '<a href="/concursresults/video/ipinfo.php" style="font-size: 24px; color: #447700; font-weight: bold; margin-bottom: 40px">< вернуться назад</a><br><br><br><br>';

    echo "<h3>Распределение голосов для " . $_GET["work_name"] . "</h3>";

    $query = "SELECT * FROM vote_tablenew WHERE work_name LIKE '%" . $_GET["work_name"] . "%'";
    $result = $mysqli->query($query);

    $arrItems = array();
    $arrIpAdress = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $arrItems[] = $row;
    }

    //Получаем IP Адресс
    foreach ($arrItems as $item) {
        $regexp = '/[0-9.]+/u';
        $result = preg_match($regexp, $item['vote_ipadress'], $match);
        if ($result > 0) {
            $arrIpAdress[] = $match[0];
        }
    }

    $tempArIpAdress = array_unique($arrIpAdress);

    //Полчим по сколько голосов с каждого IP
    $array = array_count_values($arrIpAdress);

    function cmp_function($a, $b)
    {
        return ($a < $b);
    }

    uasort($array, 'cmp_function');

    ?>
    <table style="width:100%">
        <tr>
            <td>
                <?
                foreach ($array as $key => $item) {
                    if ($item > 5) {
                        echo "<a class='findip' data-ip='" . $key . "'>" . $key . "</a> : <b>" . $item . "</b><br>";
                    }
                }
                ?>
            </td>
            <td id="ipinfo" style="font-size: 29px; color: chocolate; vertical-align: top;">

            </td>
        </tr>
    </table>
    <?
} else {

    $query = "Select *, max(vote_count) From vote_tablenew WHERE sectioncolumn = ' video ' group by work_name ";

    //$query = "SELECT *, MAX(vote_count) FROM vote_table GROUP BY work_name";
    $result = $mysqli->query($query);

    /* ассоциативный массив */
    $arrItems = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $arrItems[] = $row;
    }

    function cmp_function($a, $b)
    {
        return ($a['max(vote_count)'] < $b['max(vote_count)']);
    }

    uasort($arrItems, 'cmp_function');

    foreach ($arrItems as $item) {
        echo "<a href='?work_name=" . $item["work_name"] . "'>" . $item["work_name"] . "</a> - <b>" . $item["max(vote_count)"] . " голосов</b><br>";
    }
}


/* очищаем результаты выборки */
$result->free();

/* закрываем подключение */
$mysqli->close();
?>

</body>
</html>