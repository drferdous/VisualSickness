<?php
	include_once 'lib/Database.php';
	$db = Database::getInstance();
	$pdo = $db->pdo;

	$sql = "SELECT code FROM SSQ WHERE code = ''";
	$select = $pdo->query($sql);
	$row = $select->fetch(PDO::FETCH_NUM);
	echo print_r($row);

	if ($select->rowCount() > 10) {
	    echo "exist";
	}else echo 'notexist';
?>