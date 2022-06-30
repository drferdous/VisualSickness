<?php
    include "../mailer.php";
    if (isset($_POST["sendSupportEmail"])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $msg = trim($_POST['msg']);
        $phone = trim($_POST['phone_no']);
        if (!$name || !$email || !$msg) {
            header('Location: /contact?success=false');
        }
        $host_url = $_SERVER['HTTP_REFERER'];
        $body = "<p>A support message has been sent from " . parse_url($host_url, PHP_URL_HOST) . " on behalf of <string>$name</strong> (<a href='mailto:$email'>$email</a>):</p><br><p>$msg</p><br><p>Respond via email at $email" . ($phone ? " or via phone at $phone.</p>" : ".</p>");
        sendEmail("visualsicknessstudy@gmail.com", "[Support Email received from $email]", $body);
            header('Location: /contact?success=true');
    } else {
        header('Location: /contact?success=false');
    }