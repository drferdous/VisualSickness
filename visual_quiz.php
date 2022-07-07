<?php
include 'inc/header.php';
include_once 'lib/Database.php';
$db = Database::getInstance();
$pdo = $db->pdo;

Session::requireStudyID();
Session::requireResearcherOrUser(Session::get('study_ID'), $pdo);
if (isset($_POST['submitQuiz']) && Session::CheckPostID($_POST)) {
    $submitted = $studies->insertQuiz($_POST);
    if (isset($submitted)) {
        echo $submitted; ?>
            <script>
                $(document).ready(() => {
                    $('.modal').on('hidden.bs.modal', () => {
                        location.href = 'session_details';
                    });
                });
            </script>
        <?php
    }
}
if (isset($_POST['viewResults'])) {
    echo Util::getModalForSSQ($pdo, FALSE);
}

if (isset($_POST['ssq_ID']) && isset($_POST['iv'])) {
    $iv = hex2bin($_POST['iv']);
    $ssq_ID = Crypto::decrypt($_POST['ssq_ID'], $iv);
    Session::set('ssq_ID', $ssq_ID);
}

$ssq_ID = Session::get('ssq_ID');

$role_sql = "SELECT study_role FROM researchers WHERE study_id = " . Session::get('study_ID') . "
             AND researcher_id = " . Session::get("id") . " 
             AND is_active = 1;";
                    
$role_result = $pdo->query($role_sql);
$role = $role_result->fetch(PDO::FETCH_ASSOC);

$id_sql = "SELECT created_by, end_time FROM session 
           WHERE session_id = " . Session::get('session_ID') . ";";
$id_result = $pdo->query($id_sql);
$id_row = $id_result->fetch(PDO::FETCH_ASSOC);

$study_sql = "SELECT is_active FROM study WHERE study_id = " . Session::get('study_ID') . " LIMIT 1;";
$study_result = $pdo->query($study_sql);
$study_is_active = $study_result->fetch(PDO::FETCH_ASSOC)['is_active'] == 1;

$rand = bin2hex(openssl_random_pseudo_bytes(16));
Session::set("post_ID", $rand);
?>
<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg" style="display: none;">
    <a href="#" 
       class="close" 
       data-dismiss="alert" 
       aria-label="close">
        &times;
    </a>
    <strong>Error!</strong> Please select an answer to all questions.
</div>
<div class="card">
    <div class="card-header">
            <div class="float-right">
                <?php if ($ssq_ID !== -1) { ?>
                    <?php if(($role['study_role'] == 2 || $id_row['created_by'] == Session::get('id')) && $study_is_active && $id_row['end_time'] == NULL) { ?>
                    <form class="d-inline" onsubmit="return confirm('Are you sure you want to delete this SSQ? This action cannot be undone.');" action="session_details" method="post">
                        <button type="submit" name="deleteQuiz" class="btn btn-danger">Delete</button>
                    </form>
                    <?php } ?>
                    <form class="d-inline" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
                        <button type="submit" name="viewResults" class="btn btn-success mx-1">Results</button>
                    </form>
                <?php } ?>
                    <a href="session_details" class="backBtn btn btn-primary">Back</a>
                    </div>
    </div>
<div class="card-body pr-2 pl-2">

<!-- <div class="Header"> -->

<form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post" id="quizForm" class="text-center">    
    
  <hr>
  
  <h1>Cybersickness Online Questionnaire</h1>
  <p>Please pick your current discomfort level on the categories mentioned below.</p>
    <fieldset> 
      <legend class="h2">General Discomfort</legend>
        <hr>

        <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="discomfort0" name="general_discomfort" value="0">
              <img src="images/base0.png" alt="No general discomfort">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="discomfort1" name="general_discomfort" value="1">
              <img src="images/discomfort1.png" alt="Slight general discomfort">
              <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="discomfort2" name="general_discomfort" value="2">
              <img src="images/discomfort2.png" alt="Moderate general discomfort">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="discomfort3" name="general_discomfort" value="3">
              <img src="images/discomfort3.png" alt="Severe general discomfort">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
  </fieldset>

<fieldset> 
  <legend class="h2">Fatigue</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="fatigue0" name="fatigue" value="0">
              <img src="images/base0.png" alt="No fatigue">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="fatigue1" name="fatigue" value="1">
              <img src="images/fatigue1.gif" alt="Sligth fatigue">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="fatigue2" name="fatigue" value="2">
              <img src="images/fatigue2.gif" alt="Moderate fatigue">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="fatigue3" name="fatigue" value="3">
              <img src="images/fatigue3.gif" alt="Severe fatigue">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset> 

<fieldset> 
  <legend class="h2">Headache</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="headache0" name="headache" value="0">
              <img src="images/base0.png" alt="No headache">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="headache1" name="headache" value="1">
              <img src="images/headache1.gif" alt="Slight headache">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="headache2" name="headache" value="2">
              <img src="images/headache2.gif" alt="Moderate headache">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="headache3" name="headache" value="3">
              <img src="images/headache3.gif" alt="Severe headache">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Eye Strain</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="eyestrain0" name="eye_strain" value="0">
              <img src="images/base0.png" alt="No eye strain">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="eyestrain1" name="eye_strain" value="1">
              <img src="images/eyeStrain1.gif" alt="Slight eye strain">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="eyestrain2" name="eye_strain" value="2">
              <img src="images/eyeStrain2.gif" alt="Moderate eye strain">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="eyestrain3" name="eye_strain" value="3">
              <img src="images/eyeStrain3.gif" alt="Severe eye strain">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Difficulty Focusing</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="difficulty_focusing0" name="difficulty_focusing" value="0">
              <img src="images/base0.png" alt="No difficulty focusing">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="difficulty_focusing1" name="difficulty_focusing" value="1">
              <img src="images/difficultyfocusing1.gif" alt="Slight difficulty focusing">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="difficulty_focusing2" name="difficulty_focusing" value="2">
              <img src="images/difficultyfocusing2.gif" alt="Moderate difficulty focusing">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="difficulty_focusing3" name="difficulty_focusing" value="3">
              <img src="images/difficultyfocusing3.gif" alt="Severe difficulty focusing">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Increased Salivation</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="saliva0" name="increased_salivation" value="0">
              <img src="images/base0.png" alt="No increased salivation">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="saliva1" name="increased_salivation" value="1">
              <img src="images/saliva1.gif" alt="Slight increased salivation">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="saliva2" name="increased_salivation" value="2">
              <img src="images/saliva2.gif" alt="Moderate increased salivation">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="saliva3" name="increased_salivation" value="3">
              <img src="images/saliva3.gif" alt="Severe increased salivation">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Sweating</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="sweat0" name="sweating" value="0">
              <img src="images/base0.png" alt="No sweating">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="sweat1" name="sweating" value="1">
              <img src="images/sweat1.gif" alt="Slight sweating">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="sweat2" name="sweating" value="2">
              <img src="images/sweat2.gif" alt="Moderate sweating">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="sweat3" name="sweating" value="3">
              <img src="images/sweat3.gif" alt="Severe sweating">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Nausea</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="nausea0" name="nausea" value="0">
              <img src="images/base0.png" alt="No nausea">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="nausea1" name="nausea" value="1">        
              <img src="images/nausea1.png" alt="Slight nausea">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="nausea2" name="nausea" value="2">
              <img src="images/nausea2.png" alt="Moderate nausea">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="nausea3" name="nausea" value="3">
              <img src="images/nausea3.png" alt="Severe nausea">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Difficulty Concentrating</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="focus0" name="difficulty_concentrating" value="0">
              <img src="images/base0.png" alt="No difficulty concentrating">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="focus1" name="difficulty_concentrating" value="1">
              <img src="images/focus1.gif" alt="Slight difficulty concentrating">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="focus2" name="difficulty_concentrating" value="2">
              <img src="images/focus2.gif" alt="Moderate difficulty concentrating">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="focus3" name="difficulty_concentrating" value="3">
              <img src="images/focus3.gif" alt="Severe difficulty concentrating">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Fullness of the Head</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="fullness0" name="fullness_of_head" value="0">
              <img src="images/base0.png" alt="No fullness of the head">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="fullness1" name="fullness_of_head" value="1">
              <img src="images/fulness1.gif" alt="Slight fullness of the head">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="fullness2" name="fullness_of_head" value="2">
              <img src="images/fulness2.gif" alt="Moderate fullness of the head">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="fullness3" name="fullness_of_head" value="3">
              <img src="images/fulness3.gif" alt="Severe fullness of the head">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Blurred Vision</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="blurred0" name="blurred_vision" value="0">
              <img src="images/base0.png" alt="No blurred vision">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="blurred1" name="blurred_vision" value="1">
              <img src="images/blur1.png" alt="Slight blurred vision">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="blurred2" name="blurred_vision" value="2">
              <img src="images/blur2.png" alt="Moderate blurred vision">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="blurred3" name="blurred_vision" value="3">
              <img src="images/blur3.png" alt="Severe blurred vision">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Dizziness with Eyes Open</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="dizzinessEyes0" name="dizziness_with_eyes_open" value="0">
              <img src="images/base0.png" alt="No dizziness with eyes open">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="dizzinessEyes1" name="dizziness_with_eyes_open" value="1">
              <img src="images/dizzy1.gif" alt="Slight dizziness with eyes open">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="dizzinessEyes2" name="dizziness_with_eyes_open" value="2">
              <img src="images/dizzy2.gif" alt="Moderate dizziness with eyes open">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="dizzinessEyes3" name="dizziness_with_eyes_open" value="3">
              <img src="images/dizzy3.gif" alt="Severe dizziness with eyes open">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Dizziness with Eyes Closed</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="dizzyclose0" name="dizziness_with_eyes_closed" value="0">
              <img src="images/base0.png" alt="No dizziness with eyes closed">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="dizzyclose1" name="dizziness_with_eyes_closed" value="1">
              <img src="images/dizzyClose1.gif" alt="Slight dizziness with eyes closed">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="dizzyclose2" name="dizziness_with_eyes_closed" value="2">
              <img src="images/dizzyClose2.gif" alt="Moderate dizziness with eyes closed">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="dizzyclose3" name="dizziness_with_eyes_closed" value="3">
              <img src="images/dizzyClose3.gif" alt="Severe dizziness with eyes closed">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Vertigo</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="vertigo0" name="vertigo" value="0">
              <img src="images/base0.png" alt="No vertigo">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="vertigo1" name="vertigo" value="1">
              <img src="images/vertigo1.gif" alt="Slight vertigo">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="vertigo2" name="vertigo" value="2">
              <img src="images/vertigo2.gif" alt="Moderate vertigo">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="vertigo3" name="vertigo" value="3">
              <img src="images/vertigo3.gif" alt="Severe vertigo">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Stomach Awareness</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="stomach0" name="stomach_awareness" value="0">
              <img src="images/base0.png" alt="No stomach awareness">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="stomach1" name="stomach_awareness" value="1">
              <img src="images/stomach1.png" alt="Slight stomach awareness">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="stomach2" name="stomach_awareness" value="2">
              <img src="images/stomach2.png" alt="Moderate stomach awareness">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="stomach3" name="stomach_awareness" value="3">
              <img src="images/stomach3.png" alt="Severe stomach awareness">
                <span class="d-block">Severe</span>
          </label>
        </div>
    </div>
</fieldset>

<fieldset>
  <legend class="h2">Burping</legend>
  <hr>

    <div class = "pictures">
        <div>
          <label>
              <input type="radio" id="burp0" name="burping" value="0">
              <img src="images/base0.png" alt="No burping">
                <span class="d-block">None</span>
          </label>
          <label>
              <input type="radio" id="burp1" name="burping" value="1">
              <img src="images/burp1.gif" alt="Slight burping">
                <span class="d-block">Slight</span>
          </label>
        </div>
        <div>
          <label>
              <input type="radio" id="burp2" name="burping" value="2">
              <img src="images/burp2.gif" alt="Moderate burping">
                <span class="d-block">Moderate</span>
          </label>
          <label>
              <input type="radio" id="burp3" name="burping" value="3">
              <img src="images/burp3.gif" alt="Severe burping">
                <span class=d-block>Severe</span>
          </label>
        </div>
    </div>
</fieldset>
    
    <?php
        $sql = "SELECT ssq_time, ssq_type FROM ssq
                WHERE ssq_id = " . Session::get("ssq_ID") . "
                LIMIT 1;";
        $result = $pdo->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
    ?>
    <?php if(Session::get("ssq_ID") != -1) { ?>
        <input type="hidden" id="ssq_time" name="ssq_time" value="<?php echo $row["ssq_time"]; ?>">
    <?php } else { ?>
        <input type="hidden" id="ssq_time" name="ssq_time" value="<?php echo $_POST['ssq_time']; ?>">
    <?php } ?>
    <input type="hidden" id="ssq_type" name="ssq_type" value="1">
    <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">

    <?php if (Session::get('ssq_ID') == -1){?>
        <input type="submit" class="btn btn-success" value="Submit">
        <input type="hidden" name="submitQuiz" value="submitQuiz">
    <?php }
          else{ ?>
        <?php
        
        if(($role['study_role'] == 2 || $id_row['created_by'] == Session::get('id')) && $study_is_active && $id_row['end_time'] == NULL) {
        ?>
            <input type="submit" class="btn btn-success" value="Update">
            <input type="hidden" name="submitQuiz" value="submitQuiz">
    <?php }
        } ?>
</form>

<form action="session_details" method="POST">
    <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
</form>

<?php

    $sql = "SELECT general_discomfort, fatigue, headache, eye_strain, difficulty_focusing, increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping
        FROM ssq 
        WHERE ssq_id = " . $ssq_ID . "
        LIMIT 1;";
            
    $result = $pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_NUM);
    
      if ($result->rowCount() > 0){ ?>
        <script>
            $(document).ready(function(){
                let answerChoices = document.body.getElementsByClassName("pictures");
                let pictures;
                <?php for ($i = 0; $i < count($row); ++$i){ ?>
                    pictures = answerChoices[<?php echo $i; ?>].querySelectorAll("label > input");
                    for (let j = 0; j < pictures.length; ++j){
                        if (parseInt(pictures[j].getAttribute("value"), 10) === <?php echo $row[$i]; ?>){
                            pictures[j].setAttribute("checked", "checked");
                        }
                        else{
                        <?php if ((!$study_is_active) || (Session::get("id") != $id_row["created_by"] && $role["study_role"] != 2) || isset($id_row['end_time']) || !$study_is_active){ ?>
                            pictures[j].setAttribute("disabled", "disabled");
                        <?php } ?>
                        }
                    }
                <?php } ?>
            });
        </script>
<?php } ?>



        </div>
      </div>
<script>
    $(document).ready(function(){
        let form = document.getElementById("quizForm");
        $(form).submit(function(event){
            event.preventDefault();
            let questions = document.getElementsByClassName("pictures");
            let errorMessage;
            let checkedAnswers;
            for (let i = 0; i < questions.length; ++i){
                checkedAnswers = questions[i].querySelector("input:checked");
                if (checkedAnswers === null){
                    errorMessage = document.getElementById("flash-msg");
                    errorMessage.removeAttribute("style");
                    window.scrollTo(0, 0);
                    return;
                }
            }
            form.submit();
        });
    });
</script>


  <?php
  include 'inc/footer.php';

  ?>