<?php
    include "lib/Session.php";
    include "database.php";
    include "mailer.php";
    include_once "classes/Util.php";
    
    Session::init();
    
    if (Session::get("roleid") != "1" || !isset($_POST)){
        // header("Location: 404");
        var_dump($_POST);
        exit();
    }
    
    $sql = "UPDATE tbl_users
            SET reg_stat = 2
            WHERE id = " . $_POST["user_ID"] . ";";
    $result = mysqli_query($conn, $sql);
    
    if (!$result){
        echo mysqli_error($conn);
        echo print_r($_POST);
        var_dump($sql);
        exit();
    }
    else{
        $body = "You have been verified for Visual Sickness Study under the affiliation " . Util::getAffiliationNameById($conn, Session::get('affiliationid')) . ".<br><br>Log in now to access studies!";
        sendEmail(Util::getUserEmailById($conn, $_POST["user_ID"]), "Visual Sickness | Verification Status Update", $body);
        header("Location: userlist");
    }
?>