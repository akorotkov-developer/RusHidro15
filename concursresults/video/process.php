<?

$response = $_POST["g-recaptcha-response"];
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = [
    'secret' => '6LekUb4UAAAAAN-y5oEyatxPigHz5LLnWIHwnWTH',
    'response' => $_POST["g-recaptcha-response"]
];
$options = [
    'http' => [
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];
$context = stream_context_create($options);
$verify = file_get_contents($url, false, $context);
$captcha_success = json_decode($verify);
if ($captcha_success->success == false) {
    echo "Ты робот! Не люблю тебя!";
} else if ($captcha_success->success == true) {
    // сохраняем данные, отправляем письма, делаем другую работу. Пользователь не робот
}
