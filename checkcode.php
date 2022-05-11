<?php
	include_once 'database.php';

	$sql = "SELECT code FROM SSQ WHERE code = " .$_POST['txtName'];
	$select = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($select);

	if (mysqli_num_rows > 10) {
	    echo "exist";
	}else echo 'notexist';
?>