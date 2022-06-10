
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
 
 <script type="text/javascript">
  function quizRoute() {
    var age = document.getElementById("age").value;
    var gender = document.getElementById("gender").value;
    var education = document.getElementById("education").value;
    var race = document.getElementById("race").value;
    alert("2");

    var code = "<?php echo $code; ?>";
    var quiz = "<?php echo $quiz; ?>";
    

    alert("JS");
    if (quiz == "0") {
        alert("0");
        window.location.replace("https://visualsickness.000webhostapp.com/VisualQuiz.php?code=" + code + "&?quiz=" + quiz + "&?age=" + age + "&gender=" + gender + "&?education=" + education + "&?race=" + race);
    } else if (quiz == "1") {
        alert("1");
        window.location.replace("https://visualsickness.000webhostapp.com/TextQuiz.php?code=" + code + "&?quiz=" + quiz + "&?age=" + age + "&gender=" + gender + "&?education=" + education + "&?race=" + race);
    }
  }

</script>

      <div class="card">
        <div class="card-header">
          <h3><span class="float-right">Welcome! <strong>
            <span class="badge badge-lg badge-secondary text-white">
<?php
$name = Session::get("name");
if (isset($name)) {
  echo $name;
}
 ?></span>

          </strong></span></h3>
        </div>


        <div class="card-body pr-2 pl-2">
          <form>
            <h2>Please Enter Your Demographic Data</h2>
            <label for="age" class="required">Age</label><br>
            <input type="text" id="age"/>
            <br/>
            
            <label for="gender" class="required">Gender</label><br>
            <input type="text" id="gender"/>
            <br/>

            <label for="education" class="required">Current Level of Education</label><br>
            <input type="text" id="education"/>
            <br/>

            <label for="race" class="required">Race/Ethnicity</label><br>
            <input type="text" id="race"/>
            <br/>
    
            <input type="button" value="Submit" onclick="quizRoute()"/>
          </form>

        </div>
      </div>



  <?php
  include 'inc/footer.php';

  ?>
