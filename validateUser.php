<?php
    include "lib/Session.php";
    include_once "lib/Database.php";
    include "mailer.php";
    include_once "classes/Util.php";
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    Session::init();
    
    if (Session::get("roleid") != "1" || !isset($_POST)){
        // header("Location: 404");
        var_dump($_POST);
        exit();
    }
    
    $sql = "UPDATE tbl_users
            SET reg_stat = 2
            WHERE id = " . $_POST["user_ID"] . ";";
    $result = $pdo->query($sql);
    
    if (!$result){
        echo $pdo->errorInfo();
        echo print_r($_POST);
        var_dump($sql);
        exit();
    }
    else{
        $body = "You have been verified for Visual Sickness Study under the affiliation " . Util::getAffiliationNameById($pdo, Session::get('affiliationid')) . ".<br><br>Log in now to access studies!";
        sendEmail(Util::getUserEmailById($pdo, $_POST["user_ID"]), "Visual Sickness | Verification Status Update", $body);
        header("Location: userlist");
    }
?>