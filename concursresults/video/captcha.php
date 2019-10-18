
        <?php
        require_once('recaptchalibv2.php');
        if ($_POST["g-recaptcha-response"]) {
            // Get a key from https://www.google.com/recaptcha/admin
            $publickey = "6LekUb4UAAAAAIPu4bAeHHKyVDwZgGGtCN59T1nO";
            $privatekey = "6LekUb4UAAAAAN-y5oEyatxPigHz5LLnWIHwnWTH";
            # the response from reCAPTCHA
            $resp = null;
            # the error code from reCAPTCHA, if any
            $error = null;
            # was there a reCAPTCHA response?
            if ($_POST["g-recaptcha-response"]) {
                $resp = recaptcha_check_answer($privatekey,
                    $_SERVER["REMOTE_ADDR"],
                    $_POST["g-recaptcha-response"]);
                if ($resp->success) {
                    die("Y");
                } else {
                    # set the error code so that we can display it
                    $error = $resp['error-codes'];
                }
            }
        }
        ?>
