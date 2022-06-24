<?php
    include_once 'lib/Database.php';
    include_once 'classes/Util.php';
    include "lib/Session.php";
    
    Session::init();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['remove'])) {
        header("Location: 404");
        exit();
    }
    
    $pdo = Database::getInstance()->pdo;
    
    $study_ID = Session::get('study_ID');
    $remove = $_POST["remove"];
    
    $remove_sql = "UPDATE session_times SET is_active = 0 WHERE study_id = $study_ID AND name IN ($remove)";
    $result = $pdo->query($remove_sql);
        
    if (!$result) {
        echo Util::generateErrorMessage("Something went wrong. Try editing again!");
        exit();
    }
    
    echo Util::generateSuccessMessage("You have edited this study!");
 ?>