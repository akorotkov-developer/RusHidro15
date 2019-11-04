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

$useragent = $_POST['useragent'];
$vowels = array(";", "(", ")", "/", ",");
$useragent = str_replace($vowels, "", $useragent);
$remote = $_SERVER['REMOTE_ADDR'].$useragent;

$isPobediteli = strpos($_POST["urlString"], "/energiya-talanta/pobediteli/");

if ($isPobediteli) {
    $query = "SELECT * FROM vote_table_finalvoting WHERE vote_ipadress = '" . $remote . "'";
} else {
    $query = "SELECT * FROM vote_tablenew WHERE vote_ipadress = '" . $remote . "'";
}
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


/* очищаем результаты выборки */
$result->free();

/* закрываем подключение */
$mysqli->close();

echo json_encode($arrItemsResult);

?>