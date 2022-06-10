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
        echo '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Error!</strong> Something went wrong, try editing again!</div>';
        exit();
        
    }
    
    /*$inactive_sql = "UPDATE SSQ SET is_active = 0 WHERE ssq_time IN (SELECT id FROM SSQ_times WHERE study_id = $study_ID AND name IN ($remove))";
    $result = $pdo->query($inactive_sql);
    
    if (!$result) {
        echo '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Error!</strong> Something went wrong, try editing again!</div>';
        exit();
    }*/
    
    echo '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> You have edited this study!</div>';
 
 ?>