<?php

require_once "../includes.php";
require_once "../inc/libs/caching.php";

$_SESSION['admin_password'] = '';
$_SESSION['admin_name'] = '';

//sql_connect();
include "templates/includes/new_top.php";


echo sprintt($page, 'templates/login/login.html');

?>

<?php

include "templates/includes/new_bottom.php";

?>