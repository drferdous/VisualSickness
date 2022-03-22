<?php
    include "lib/Session.php";
    include "database.php";
    
    Session::init();
    
    if (Session::get("roleid") !== "1"){
        header("Location: 404");
        exit();
    }
    
    $sql = "UPDATE tbl_users
            SET reg_stat = 1
            WHERE id = " . $_POST["user_ID"] . "
            LIMIT 1;";
    $result = mysqli_query($conn, $sql);
    
    if (!$result){
        echo mysqli_error($conn);
        exit();
    }
    else{
        header("Location: userlist");
    }
?>