<?

/* ����� ��� �������� $control->template, $control->oper � ��������� ��������� URL, 
������� ��� ������������� ����� ����������� ������ ������� ��� ������
(�������� ��� �������� ������� ������ ������� ������ � ������ �������� � � ���������� �������) :

    global $control;

    if ($control->template == "news" && $control->oper !== "view")
        ������� 1
    else
        ������� 2

*/

$error_wrappers = array (
'error' => array ('module'=> 'error', 'html'=>'error.html'),
);
$wrappers = array_merge($wrappers, $error_wrappers);
?>