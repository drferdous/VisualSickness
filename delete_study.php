<?php
include_once 'lib/Database.php';

$id = $_GET['id']; // $id is now defined
$sql = "DELETE FROM study
        WHERE study_id = " . $id;

Database::getInstance()->pdo->query($sql);
header("Location: study_list");
?>
