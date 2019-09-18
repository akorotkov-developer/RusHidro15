<?

include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/voting.php";

$sql = new Sql();
$sql->connect();

$id = $_POST['id'];

$voting = new voting();
$data = $voting->voteinfo($id);

echo json_encode($data);

?>