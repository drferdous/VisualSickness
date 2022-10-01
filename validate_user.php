<?php
    include "lib/Session.php";
    include_once "lib/Database.php";
    include "mailer.php";
    include "classes/Crypto.php";
    include_once "classes/Util.php";
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    Session::init();
    
    if (Session::get("roleid") != "1" || !isset($_POST) && Session::CheckPostID($_POST)){
        header("Location: 404");
        exit();
    }
    if (!isset($_POST["iv"]) || !isset($_POST["user_ID"])){
        header("Location: 404");
        exit();
    }
    
    $iv = hex2bin($_POST["iv"]);
    $userid = Crypto::decrypt($_POST["user_ID"], $iv);
    echo print_r($_POST);
    
    $sql = "UPDATE users
            SET registration_status = 2
            WHERE user_id = " . $userid . ";";
    $result = $pdo->query($sql);
    
    if (!$result){
        echo $pdo->errorInfo();
        echo print_r($_POST);
        var_dump($sql);
        exit();
    }
    else{
        $body = "You have been verified for Visual Sickness Study under the affiliation " . Util::getAffiliationNameById($pdo, Session::get('affiliationid')) . ".<br><br>Log in now to access studies!";
        sendEmail(Util::getUserEmailById($pdo, $userid), "Visual Sickness | Verification Status Update", $body);
        header("Location: user_list");
    }
?>