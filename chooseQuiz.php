<script type="text/javascript">
    function quiz() {
        var quizType = (document.querySelector('input[name="quizValue"]:checked').value).toString();
        if (quizType == "visual") {
          window.location.replace("https://visualsickness.000webhostapp.com/VisualQuiz.php");
        } else if (quizType == "text") {
          window.location.replace("https://visualsickness.000webhostapp.com/TextQuiz.php");
        }
    }
</script>

<?php
include 'inc/header.php';

Session::CheckSession();

$logMsg = Session::get('logMsg');
if (isset($logMsg)) {
  echo $logMsg;
}
$msg = Session::get('msg');
if (isset($msg)) {
  echo $msg;
}
Session::set("msg", NULL);
Session::set("logMsg", NULL);
?>
<?php

if (isset($_GET['remove'])) {
  $remove = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['remove']);
  $removeUser = $users->deleteUserById($remove);
}

if (isset($removeUser)) {
  echo $removeUser;
}
if (isset($_GET['deactive'])) {
  $deactive = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['deactive']);
  $deactiveId = $users->userDeactiveByAdmin($deactive);
}

if (isset($deactiveId)) {
  echo $deactiveId;
}
if (isset($_GET['active'])) {
  $active = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['active']);
  $activeId = $users->userActiveByAdmin($active);
}

if (isset($activeId)) {
  echo $activeId;
}


 ?>
      <div class="card ">
        <div class="card-header">
          <h3><span class="float-right">Welcome! <strong>
            <span class="badge badge-lg badge-secondary text-white">
<?php
$username = Session::get('username');
if (isset($username)) {
  echo $username;
}
 ?></span>

          </strong></span></h3>
        </div>
        <div class="card-body pr-2 pl-2">
          <form>
            <h2>Select Your Quiz</h2>
            <input type="radio" id="visual" name="quizValue" value="visual">
            <label for=visual">Visual Quiz</label><br>
            <input type="radio" id="text" name="quizValue" value="text">
            <label for="text">Text Quiz</label><br>
            <input type="button" value="Submit" onclick="quiz()"/>
          </form>

        </div>
      </div>



  <?php
  include 'inc/footer.php';

  ?>
