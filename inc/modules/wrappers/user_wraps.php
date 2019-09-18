<?

/* здесь уже доступен $control->template, $control->oper и остальные параметры URL, 
поэтому при необходимости можно динамически менять враппер для модуля
(например для новостей указать разные базовые шаблон у списка новостей и у конкретной новости) :

    global $control;

    if ($control->template == "news" && $control->oper !== "view")
        враппер 1
    else
        враппер 2

*/

$user_wrappers = array (
'registration' => array ('module'=> 'user', 'html'=>'inner.html'),
'remember' => array ('module'=> 'user', 'html'=>'inner.html'),
);
$wrappers = array_merge($wrappers, $user_wrappers);
?>