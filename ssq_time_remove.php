<?php
    include_once 'lib/Database.php';
    include "lib/Session.php";
    
    Session::init();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['studyID'])) {
        exit();
    }
    
    $pdo = Database::getInstance()->pdo;
    
    $study_ID = $_POST["studyID"];
    $remove = $_POST["remove"];
    
    $remove_sql = "UPDATE SSQ_times SET is_active = 0 WHERE study_id = $study_ID AND name IN ($remove)";
    $result = $pdo->query($remove_sql);
        
    if (!$result) {
        echo Util::generateErrorMessage("Something went wrong. Try editing again!");
        exit();
        
    }
    
    /*$inactive_sql = "UPDATE ssq SET is_active = 0 WHERE ssq_time IN (SELECT id FROM SSQ_times WHERE study_id = $study_ID AND name IN ($remove))";
    $result = $pdo->query($inactive_sql);
    
    if (!$result) {
        echo Util::generateErrorMessage("Something went wrong. Try editing again!");
        exit();
    }*/
    
    echo Util::generateSuccessMessage("You have edited this study!");
 ?>