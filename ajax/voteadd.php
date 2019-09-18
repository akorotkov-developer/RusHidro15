<?

include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/voting.php";

$sql = new Sql();
$sql->connect();

$id = $_POST['id'];
$genre = $_POST['genre'];

$voting = new voting();
$data = $voting->voteadd($id,$genre);

echo json_encode($data);

?>