<?php
include 'inc/header.php';
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteQuiz'])) {
  $deleteQuiz = $studies->deleteQuiz($_POST);
}

if (isset($deleteQuiz)) {
  echo $deleteQuiz;
}

?>

<form action="session_details" method="post">
        <input type="hidden" name="session_ID" value="<?php echo Session::get('session_ID'); ?>">
        <button type="Submit" name="ok-btn" class="btn btn-success form-group">OK</button>
</form>