<?php
    include "lib/Database.php";
    include "lib/Session.php";
    include "classes/Util.php";
    
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["remove_ssq"]) || !isset($_POST["remove_session"])){
        header("Location: 404");
        exit();
    }
    
    Session::init();
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    $study_ID = Session::get("study_ID");
    $remove_ssq = $_POST["remove_ssq"];
    $remove_session = $_POST["remove_session"];
    
    $remove_ssq_sql = "UPDATE ssq_times
                       SET is_active = 0
                       WHERE study_id = " . $study_ID . "
                       AND name IN (" . $remove_ssq . ");";
    $result = $pdo->query($remove_ssq_sql);
    if (!$result){
        echo Util::generateErrorMessage("Something went wrong. Try again!");
        exit();
    }
    
    $remove_session_sql = "UPDATE session_times
                           SET is_active = 0
                           WHERE study_id = " . $study_ID . "
                           AND name IN (" . $remove_session . ");";
    $result = $pdo->query($remove_session_sql);
    if (!$result){
        echo Util::generateErrorMessage("Something went wrong. Try again!");
        exit();
    }
    
    echo Util::generateSuccessMessage("You have edited this study!");
?>