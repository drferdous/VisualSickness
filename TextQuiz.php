<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

if(isset($_GET['code']) == "" && Session::get('login') === FALSE) {
  header('Location: index');
  exit();
}

$isFirstTime = ($_POST['is_first_time'] === "true");
$ssq_ID;
        
if ($isFirstTime){
    $ssq_ID = -1;
}
else{
    $ssq_ID = intval($_POST['ssq_ID']);
}

?>
      
<div class="card ">
    <div class="card-header">
        <h3><span class="float-right">
            <form action="delete_quiz" method="post">
                <input type="hidden" id="ssq_ID" name="ssq_ID" value="<?php echo $ssq_ID; ?>">    
                <button type="submit" name="deleteQuiz" class="btn btn-danger">Delete SSQ</button>      
            </form>
            </span></strong>
        </span></h3>
    </div>
    <div class="card-body pr-2 pl-2">
    <center>
        <div class="Header">
            <hr>
        </div>
    </center>

<form action="insert_quiz" method="post">
    <center>
    <div class="symptoms">
        <?php if (Session::get('login') === FALSE) { ?>
            <h1>Please Enter Your Demographic Data</h1>
                <label>
                    <h2>Age</h2>
                    <input type="text" id="age" name="age" required />
                </label><br>
                
                <br/>
    
                <h2>Gender</h2>
                <input type="radio" id="male" name="gender" value="Male" required>
                <label for="male">Male</label><br>
                <input type="radio" id="female" name="gender" value="Female">
                <label for="female">Female</label><br>
                <input type="radio" id="other" name="gender" value="Other">
                <label for="other">Other</label><br>
                <input type="radio" id="no" name="gender" value="Prefer Not To Answer">
                <label for="other">Prefer Not To Answer</label>
    
                <br/>
                
                <h2>Education</h2>
                <input type="radio" id="elementary" name="education" value="Elementary School" required> 
                <label for="elementary">Elementary School</label><br>
                <input type="radio" id="middle" name="education" value="Middle School">
                <label for="middle">Middle School</label><br>
                <input type="radio" id="high" name="education" value="High School">
                <label for="high">High School</label><br>
                <input type="radio" id="twoYear" name="education" value="2 Year College">
                <label for="twoYear">2 Year College</label><br>
                <input type="radio" id="fourYear" name="education" value="4 Year College">
                <label for="fourYear">4 Year College</label><br>
                <input type="radio" id="no" name="education" value="Prefer Not To Answer">
                <label for="other">Prefer Not To Answer</label>
    
                <br/>
    
                <h2>Race/Ethnicity</h2>
                <input type="radio" id="aian" name="race" value="American Indian or Alaska Native" required>
                <label for="aian">American Indian or Alaska Native</label><br>
                <input type="radio" id="asian" name="race" value="Asian">
                <label for="asian">Asian</label><br>
                <input type="radio" id="black" name="race" value="Black or African American">
                <label for="black">Black or African American</label><br>
                <input type="radio" id="nhopi" name="race" value="Native Hawaiian or Other Pacific Islander">
                <label for="nhopi">Native Hawaiian or Other Pacific Islander</label><br>
                <input type="radio" id="white" name="race" value="White">
                <label for="white">White</label><br>
                <input type="radio" id="other" name="race" value="Other">
                <label for="other">Other</label><br>
                <input type="radio" id="no" name="race" value="Prefer Not To Answer">
                <label for="other">Prefer Not To Answer</label>
                <br/>
    </div>
    </center>
    <?php  } ?>          

  <hr>
  <h1>Cybersickness Online Questionnaire</h1>
  <p>Please pick your current discomfort level on the categories mentioned below. If you do not understand the meaning of the symptom, pick "Do not Understand".</p>

<center>
<div class="symptoms">
  <h2>General Discomfort</h2>
  <hr>
    <div class = "pictures">
        <label>
            <input type="radio" id="discomfort4" name="general_discomfort" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="fatigue4" name="fatigue" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="headache4" name="headache" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="eyestrain4" name="eye_strain" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="focusing4" name="difficulty_focusing" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="increased_salivation4" name="increased_salivation" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="sweat4" name="sweating" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="nausea4" name="nausea" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="focus4" name="difficulty_concentrating" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="fullness4" name="fullness_of_head" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="blurred4" name="blurred_vision" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="dizzinessEyes4" name="dizziness_with_eyes_open" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="dizzyclose4" name="dizziness_with_eyes_closed" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="vertigo4" name="vertigo" value="-1"> Do Not Understand
        </label>
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

    <div class = "pictures">
        <label>
            <input type="radio" id="stomach4" name="stomach_awareness" value="-1"> Do Not Understand
        </label>
        <label>
            <input type="radio" id="stomach0" name="stomach_awareness" value="0"> None
        </label>
        <label>
            <input type="radio" id="stomach1" name="stomach_awareness" value="1"> Slight
        </label>
        <label>
            <input type="radio" id="stomach2" name="stomach_awareness" value="2"> Moderate
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

    <div class = "pictures">
        <label>
            <input type="radio" id="burp4" name="burping" value="-1"> Do Not Understand
        </label>
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
</div>
    
    <input type="hidden" id="ssq_ID" name="ssq_ID" value="<?php echo $ssq_ID; ?>">
    <input type="hidden" id="ssq_time" name="ssq_time" value="<?php echo $_POST['ssq_time']; ?>">
    <input type="hidden" id="ssq_type" name="ssq_type" value="0">
    <input type="hidden" id="session_ID" name="session_ID" value="<?php echo Session::get('session_ID'); ?>">
    <?php
    // Ask professor how to deal with the face that the code is stored in the URL.
    if (isset($_GET['code'])){?>
        <input type="hidden" id="code" name="code" value=" <?php echo $_GET['code'] ?>">
    <?php }
    else{ ?>
        <input type="hidden" id="code" name="code" value="">
    <?php } ?>
    
    <?php if ($isFirstTime){ ?>
        <input type="submit" class="btn btn-success float-left" name="Submit" value="Submit">
    <?php }
          else{ ?>
        <input type="submit" class="btn btn-success float-left" name="Submit" value="Update">
    <?php } ?>
</form>

<br>
<br>
<form action="session_details" method="POST">
    <input type="hidden" name="session_ID" value="<?php echo Session::get('session_ID'); ?>">
    <input type="submit" class="btn btn-danger float-left" name="Cancel" value="Cancel">
</form>

<?php
    $sql = "SELECT general_discomfort, fatigue, headache, eye_strain, difficulty_focusing, increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping
        FROM SSQ 
        WHERE ssq_ID = " . $ssq_ID . "
        LIMIT 1;";
            
    $result = $pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_NUM);
    
    if ($result->rowCount() === 0){
        $row = array();
        
        for ($i = 0; $i < $result->columnCount(); ++$i){
            array_push($row, -1);
        }
    } ?>

<script type="text/javascript">
    $(document).ready(function() {
        let answerChoices = document.body.getElementsByClassName("pictures");
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

<?php
  include 'inc/footer.php';
?>