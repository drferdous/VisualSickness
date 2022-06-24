<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

if (Session::get('study_ID') == 0) {
    header('Location: study_list');
    exit();
}
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

$role_sql = "SELECT study_role FROM Researcher_Study WHERE study_ID = " . Session::get('study_ID') . "
             AND  researcher_ID = " . Session::get("id") . " 
             AND is_active = 1;";
                    
$role_result = $pdo->query($role_sql);
$role = $role_result->fetch(PDO::FETCH_ASSOC);

$id_sql = "SELECT created_by FROM Session 
           WHERE session_ID = " . Session::get('session_ID') . "
           AND is_active = 1;";
$id_result = $pdo->query($id_sql);
$id_row = $id_result->fetch(PDO::FETCH_ASSOC);

$study_sql = "SELECT is_active FROM Study WHERE study_ID = " . Session::get('study_ID') . " LIMIT 1;";
$study_result = $pdo->query($study_sql);
$study_is_active = $study_result->fetch(PDO::FETCH_ASSOC)['is_active'] == 1;

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
            <h3>
                <?php if(($role['study_role'] == 2 || $id_row['created_by'] == Session::get('id')) && $study_is_active) { ?>
                    <form class="float-right ml-2" onsubmit="return confirm('Are you sure you want to delete this SSQ? This action cannot be undone.');" action="delete_quiz" method="post">
                        <button type="submit" name="deleteQuiz" class="btn btn-danger">Delete</button>
                    </form>
                <?php }
                if ($ssq_ID !== -1) { ?>
                    <form class="float-right" action="" method="post">
                        <button type="submit" name="viewResults" class="btn btn-success">Results</button>
                    </form>
                <?php } ?>
            </h3>
    </div>
    <div class="card-body pr-2 pl-2">
<form action="" method="post" id="quizForm">
    <?php 
        $rand = bin2hex(openssl_random_pseudo_bytes(16));
        Session::set("post_ID", $rand);
    ?>
    <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">

    <hr>
    <h1 class="text-center">Cybersickness Online Questionnaire</h1>
    <p class="text-center">Please pick your current discomfort level on the categories mentioned below. If you do not understand the meaning of the symptom, pick "Do not Understand".</p>

<center>
<div class="symptoms">
  <h2>General Discomfort</h2>
  <hr>
    <div class="answer_choices">
        <label>
            <input type="radio" id="discomfort0" name="general_discomfort" value="0"> None
        </label>
        <label>
            <input type="radio" id="discomfort1" name="general_discomfort" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="discomfort2" name="general_discomfort" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="discomfort3" name="general_discomfort" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Fatigue</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="fatigue0" name="fatigue" value="0"> None
        </label>
        <label>
            <input type="radio" id="fatigue1" name="fatigue" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="fatigue2" name="fatigue" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="fatigue3" name="fatigue" value="3"> Severe
        </label>
    </div>
</div>

</center>

<center>
<div class="symptoms">
  <h2>Headache</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="headache0" name="headache" value="0"> None
        </label>
        <label>
            <input type="radio" id="headache1" name="headache" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="headache2" name="headache" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="headache3" name="headache" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Eye Strain</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="eyestrain0" name="eye_strain" value="0"> None
        </label>
        <label>
            <input type="radio" id="eyestrain1" name="eye_strain" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="eyestrain2" name="eye_strain" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="eyestrain3" name="eye_strain" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Difficulty Focusing</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="focusing0" name="difficulty_focusing" value="0"> None
        </label>
        <label>
            <input type="radio" id="focusing1" name="difficulty_focusing" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="focusing2" name="difficulty_focusing" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="focusing3" name="difficulty_focusing" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Increased Salivation</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="increased_salivation0" name="increased_salivation" value="0"> None
        </label>
        <label>
            <input type="radio" id="increased_salivation1" name="increased_salivation" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="increased_salivation2" name="increased_salivation" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="increased_salivation3" name="increased_salivation" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Sweating</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="sweat0" name="sweating" value="0"> None
        </label>
        <label>
            <input type="radio" id="sweat1" name="sweating" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="sweat2" name="sweating" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="sweat3" name="sweating" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Nausea</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="nausea0" name="nausea" value="0"> None
        </label>
        <label>
            <input type="radio" id="nausea1" name="nausea" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="nausea2" name="nausea" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="nausea3" name="nausea" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Difficulty Concentrating</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="focus0" name="difficulty_concentrating" value="0"> None
        </label>
        <label>
            <input type="radio" id="focus1" name="difficulty_concentrating" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="focus2" name="difficulty_concentrating" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="focus3" name="difficulty_concentrating" value="3"> Severe
        </label>

            
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Fullness of the Head</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="fullness0" name="fullness_of_head" value="0"> None
        </label>
        <label>
            <input type="radio" id="fullness1" name="fullness_of_head" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="fullness2" name="fullness_of_head" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="fullness3" name="fullness_of_head" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Blurred Vision</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="blurred0" name="blurred_vision" value="0"> None
        </label>
        <label>
            <input type="radio" id="blurred1" name="blurred_vision" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="blurred2" name="blurred_vision" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="blurred3" name="blurred_vision" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Dizziness with Eyes Open</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="dizzinessEyes0" name="dizziness_with_eyes_open" value="0"> None
        </label>
        <label>
            <input type="radio" id="dizzinessEyes1" name="dizziness_with_eyes_open" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="dizzinessEyes2" name="dizziness_with_eyes_open" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="dizzinessEyes3" name="dizziness_with_eyes_open" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Dizziness with Eyes Closed</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="dizzyclose0" name="dizziness_with_eyes_closed" value="0"> None
        </label>
        <label>
            <input type="radio" id="dizzyclose1" name="dizziness_with_eyes_closed" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="dizzyclose2" name="dizziness_with_eyes_closed" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="dizzyclose3" name="dizziness_with_eyes_closed" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Vertigo</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="vertigo0" name="vertigo" value="0"> None
        </label>
        <label>
            <input type="radio" id="vertigo1" name="vertigo" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="vertigo2" name="vertigo" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="vertigo3" name="vertigo" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Stomach Awareness</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="stomach0" name="stomach_awareness" value="0"> None
        </label>
        <label>
            <input type="radio" id="stomach1" name="stomach_awareness" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="stomach2" name="stomach_awareness" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="stomach3" name="stomach_awareness" value="3"> Severe
        </label>
    </div>
</div>
</center>

<center>
<div class="symptoms">
  <h2>Burping</h2>
  <hr>

    <div class="answer_choices">
        <label>
            <input type="radio" id="burp0" name="burping" value="0"> None
        </label>
        <label>
            <input type="radio" id="burp1" name="burping" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="burp2" name="burping" value="2"> Moderate
        </label>
        <label>
            <input type="radio" id="burp3" name="burping" value="3"> Severe
        </label>
    </div>
</div>
</center>
    
    <input type="hidden" id="ssq_type" name="ssq_type" value="0">
    <input type="hidden" id="ssq_time" name="ssq_time" value="<?= $_POST['ssq_time'] ?>">
    <?php
    // Ask professor how to deal with the face that the code is stored in the URL.
    if (isset($_GET['code'])){?>
        <input type="hidden" id="code" name="code" value=" <?php echo $_GET['code'] ?>">
    <?php }
    else{ ?>
        <input type="hidden" id="code" name="code" value="">
    <?php } ?>
    <span class="float-right"> <a href='session_details' class="btn btn-danger redirectUser ml-2 mr-2">Cancel</a></span>
    <?php if ($ssq_ID == -1){ ?>
        <input type="submit" class="btn btn-success float-right" value="Submit" id="submitButton">
        <input type="hidden" name="submitQuiz" value="submitQuiz">
    <?php }
          else{ ?>
        <?php
            if(($role['study_role'] == 2 || $id_row['created_by'] == Session::get('id')) && $study_is_active) {
        ?>
        <input type="submit" class="btn btn-success float-right" value="Update">
        <input type="hidden" name="submitQuiz" value="submitQuiz">
    <?php }
        } ?>
</form>
</div>

<?php
    $sql = "SELECT general_discomfort, fatigue, headache, eye_strain, difficulty_focusing, increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping
        FROM SSQ 
        WHERE ssq_ID = " . $ssq_ID . "
        LIMIT 1;";
            
    $result = $pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_NUM);
    
      if ($result->rowCount() > 0){ ?>
        <script type="text/javascript">
            $(document).ready(function() {
            let answerChoices = document.body.getElementsByClassName("answer_choices");
            let radioButtons;
            <?php for ($colNum = 0; $colNum < count($row); ++$colNum){ ?>
                radioButtons = answerChoices[<?php echo $colNum; ?>].querySelectorAll("label > input");
                for (let i = 0; i < radioButtons.length; ++i){
                    if (parseInt(radioButtons[i].getAttribute("value"), 10) === <?php echo $row[$colNum]; ?>){
                        radioButtons[i].setAttribute("checked", "checked");
                    }
                }
            <?php } ?>
            });
        </script>  
<?php } ?>

<script type="text/javascript">
    $(document).ready(function() {
         let form = document.getElementById("quizForm");
         $(form).submit(function (event){
             event.preventDefault();
             let questions = document.body.getElementsByClassName("answer_choices");
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