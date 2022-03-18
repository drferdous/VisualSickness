<?php
include 'database.php';

$id = $_GET['id']; // $id is now defined
$sql = "DELETE FROM Study
        WHERE study_id = " . $id;

mysqli_query($conn, $sql);
mysqli_close($conn);
header("Location: index");
?>
