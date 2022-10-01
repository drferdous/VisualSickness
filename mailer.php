<?php

function sendEmail($to, $subject, $body) {
    $from = "visualsicknessstudy@gmail.com";
    
    $headers = "From: Visual Sickness Study <$from>\nReply-To: $from\nX-Mailer: PHP/" . phpversion() . "\nContent-type: text/html\n";
    
    mail($to, $subject, $body, $headers);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    if (!isset($_POST["email"]) || !isset($_POST["subject"]) || !isset($_POST["message"])){
        exit();
    }
    $to = $_POST["email"];
    $subject = $_POST["subject"];
    $message =  $_POST['message'];
    
    sendEmail($to, $subject, $message);
}
?>