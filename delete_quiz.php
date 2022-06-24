<?php
include 'inc/header.php';
include_once 'lib/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteQuiz'])) {
    $deleteQuiz = $studies->deleteQuiz();
}

if (isset($deleteQuiz)) {
    echo $deleteQuiz;
}

?>

<form action="session_details" method="post">
    <button type="Submit" name="ok-btn" class="btn btn-success form-group">OK</button>
</form>