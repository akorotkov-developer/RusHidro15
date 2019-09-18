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

$catalog_wrappers = array (
'catalog' => array ('module'=> 'catalog', 'html'=>'inner.html'),
'group' => array ('module'=> 'catalog', 'html'=>'inner.html'),
);
$wrappers = array_merge($wrappers, $catalog_wrappers);
?>