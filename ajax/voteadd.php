<?

include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/voting.php";

$sql = new Sql();
$sql->connect();

$id = $_POST['id'];
$genre = $_POST['genre'];
$votePhoto = $_POST['votephoto'];

$voting = new voting();
if ($votePhoto != "") {
    $data = $voting->voteaddphoto($id);
} else {
    $data = $voting->voteadd($id, $genre);
}

echo json_encode($data);

?>