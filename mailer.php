<?php

function sendEmail($to, $subject, $body) {
    $from = "visualsicknessstudy@gmail.com";
    
    $headers = "From: Visual Sickness Study <$from>\nReply-To: $from\nX-Mailer: PHP/" . phpversion() . "\nContent-type: text/html\n";
    
    // echo $subject;
    mail($to, $subject, $body, $headers);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    $to = $_POST["email"];
    $subject = $_POST["subject"];
    $message =  $_POST['message'];
    
    sendEmail($to, $subject, $message);
    
    // echo "<pre>";
    //     var_dump($_POST);
    // echo "</pre>";
}

?> 