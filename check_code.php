<?php
    include_once 'lib/Database.php';
    $db = Database::getInstance();
    $pdo = $db->pdo;

	$sql = "SELECT code FROM code_ssq WHERE BINARY code = '" .$_POST['userCode'] . "';";
	$result = $pdo->query($sql);
	
	if (!$result){
        echo $pdo->errorInfo();
    }
	
	if ($result->rowCount() > 0) {
	    echo "exist";
	} else echo 'notexist';
?>