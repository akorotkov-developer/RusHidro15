<?

include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/voting.php";

$sql = new Sql();
$sql->connect();

$id = $_POST['id'];
$genre = $_POST['genre'];
$votePhoto = $_POST['votephoto'];
$useragent = $_POST['useragent'];
$sect = $_POST["sect"];
$work_name = $_POST['work_name'];
$islitra = $_POST['islitra'];

$voting = new voting();
if ($votePhoto != "") {
    $data = $voting->voteaddphoto($id, $useragent, $sect, $work_name, $islitra);
} else {
    $data = $voting->voteadd($id, $genre);
}

echo json_encode($data);

?>