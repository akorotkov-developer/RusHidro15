<?

include_once "../inc/var.php";
include_once "../includes.php";
include_once "../inc/modules/user.php";
$sql = new Sql();
$sql->connect();

$email = $_POST['email'];
$password = $_POST['password'];

$auth = new user();
$user = $auth->auth($email, $password);
var_dump($user);
if ($user->item) {
    session_start();
    $_SESSION['u_login'] = $user->item[0]->email;
    $_SESSION['u_password'] = $user->item[0]->password;
    $_SESSION['u_id'] = $user->item[0]->id;
    setcookie("u_login", $_SESSION['u_login'], time() + 86400 * 7, '/');
    setcookie("u_password", $_SESSION['u_password'], time() + 86400 * 7, '/');
    setcookie("u_id", $_SESSION['u_id'], time() + 86400 * 7, '/');

    return true;
} else {
    return false;
}
?>