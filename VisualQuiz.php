<?php
include 'inc/header.php';

if(isset($_GET['code']) == "" && Session::get('login') === FALSE) {
  header('Location: index');
  exit();
}

$isFirstTime = ($_POST['is_first_time'] === "true");

?>

<div class="card ">
    <div class="card-header">
        <h3><span class="float-right">Welcome! 
            <strong><span class="badge badge-lg badge-secondary text-white">
                <?php
                    $username = Session::get('username');
                    if (isset($username)){
                        echo $username;
                    }
                ?>
            </span></strong>
        </span></h3>
    </div>
    
    <div class="card-body pr-2 pl-2">

<style type="text/css">
  /* HIDE RADIO */
.pictures [type=radio] { 
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.symptoms {
  margin: auto;
  margin-top: 10px;
  background: white;
  width: 53%;
  padding: 10px;
}

 .pictures {
  margin: auto;
  margin-top: 10px;
  background: white;
  align-items:  : center;
  padding: 10px;
}

.pictures img {
  border: 3px solid lightblue;
  border-radius: 4px;
  width: 100px;
  margin: auto;

    margin: auto;
}

/* IMAGE STYLES */
.pictures [type=radio] + img {
  cursor: pointer;
}

/* CHECKED STYLES */
.pictures [type=radio]:checked + img {
  outline: 2px solid #f00;
}

.pictures [type=radio]:checked {
  border-color: #f00;
}
</style>
<body>


<!-- <div class="Header"> -->

<form action="insert_quiz" method="post">    
    <?php if (Session::get('login') === FALSE) { ?>
        <div class="symptoms">
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
            <br />
        </div>
    <?php  } ?>     
    
  <hr>
  
  <h1>Cybersickness Online Questionnaire</h1>
  <p>Please pick your current discomfort level on the categories mentioned below. If you do not understand the meaning of the symptom, pick "Do not Understand".</p>
    <input type="hidden" id="ssq_ID" name="ssq_ID" value="0">
    <div class = "pictures">
        <h2>General Discomfort</h2>
        <hr>
        <label>
            <input type="radio" id="discomfort4" name="general_discomfort" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="discomfort0" name="general_discomfort" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="discomfort1" name="general_discomfort" value="1">
            <img src="images/discomfort1.png" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="discomfort2" name="general_discomfort" value="2">
            <img src="images/discomfort2.png" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="discomfort3" name="general_discomfort" value="3">
            <img src="images/discomfort3.png" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Fatigue</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="fatigue4" name="fatigue" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fatigue0" name="fatigue" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fatigue1" name="fatigue" value="1">
            <img src="images/fatigue1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fatigue2" name="fatigue" value="2">
            <img src="images/fatigue2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fatigue3" name="fatigue" value="3">
            <img src="images/fatigue3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>


  <h2>Headache</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="headache4" name="headache" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="headache0" name="headache" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="headache1" name="headache" value="1">
            <img src="images/headache1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="headache2" name="headache" value="2">
            <img src="images/headache2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="headache3" name="headache" value="3">
            <img src="images/headache3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Eye Strain</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="eyestrain4" name="eye_strain" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="eyestrain0" name="eye_strain" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="eyestrain1" name="eye_strain" value="1">
            <img src="images/eyeStrain1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="eyestrain2" name="eye_strain" value="2">
            <img src="images/eyeStrain2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="eyestrain3" name="eye_strain" value="3">
            <img src="images/eyeStrain3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Difficulty Focusing</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="difficulty_focusing4" name="difficulty_focusing" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="difficulty_focusing0" name="difficulty_focusing" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="difficulty_focusing1" name="difficulty_focusing" value="1">
            <img src="images/difficultyfocusing1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="difficulty_focusing2" name="difficulty_focusing" value="2">
            <img src="images/difficultyfocusing2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="difficulty_focusing3" name="difficulty_focusing" value="3">
            <img src="images/difficultyfocusing3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Increased Salivation</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="saliva4" name="increased_salivation" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="saliva0" name="increased_salivation" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="saliva1" name="increased_salivation" value="1">
            <img src="images/saliva1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="saliva2" name="increased_salivation" value="2">
            <img src="images/saliva2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="saliva3" name="increased_salivation" value="3">
            <img src="images/saliva3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Sweating</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="sweating4" name="sweating" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="sweat0" name="sweating" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="sweat1" name="sweating" value="1">
            <img src="images/sweat1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="sweat2" name="sweating" value="2">
            <img src="images/sweat2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="sweat3" name="sweating" value="3">
            <img src="images/sweat3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Nausea</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="nausea4" name="nausea" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="nausea0" name="nausea" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="nausea1" name="nausea" value="1">        
            <img src="images/nausea1.png" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="nausea2" name="nausea" value="2">
            <img src="images/nausea2.png" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="nausea3" name="nausea" value="3">
            <img src="images/nausea3.png" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Difficulty Concentrating</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="difficulty_concentrating4" name="difficulty_concentrating" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="focus0" name="difficulty_concentrating" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="focus1" name="difficulty_concentrating" value="1">
            <img src="images/focus1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="focus2" name="difficulty_concentrating" value="2">
            <img src="images/focus2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="focus3" name="difficulty_concentrating" value="3">
            <img src="images/focus3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Fullness of the Head</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="fullness_of_head4" name="fullness_of_head" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fullness0" name="fullness_of_head" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fullness1" name="fullness_of_head" value="1">
            <img src="images/fulness1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fullness2" name="fullness_of_head" value="2">
            <img src="images/fulness2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="fullness3" name="fullness_of_head" value="3">
            <img src="images/fulness3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Blurred Vision</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="blurred_vision4" name="blurred_vision" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="blurred0" name="blurred_vision" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="blurred1" name="blurred_vision" value="1">
            <img src="images/blur1.png" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="blurred2" name="blurred_vision" value="2">
            <img src="images/blur2.png" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="blurred3" name="blurred_vision" value="3">
            <img src="images/blur3.png" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

  <h2>Dizziness with Eyes Open</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="dizzinessEyes4" name="dizziness_with_eyes_open" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzinessEyes0" name="dizziness_with_eyes_open" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzinessEyes1" name="dizziness_with_eyes_open" value="1">
            <img src="images/dizzy1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzinessEyes2" name="dizziness_with_eyes_open" value="2">
            <img src="images/dizzy2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzinessEyes3" name="dizziness_with_eyes_open" value="3">
            <img src="images/dizzy3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>


  <h2>Dizziness with Eyes Closed</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="dizzyclose4" name="dizziness_with_eyes_closed" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzyclose0" name="dizziness_with_eyes_closed" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzyclose1" name="dizziness_with_eyes_closed" value="1">
            <img src="images/dizzyClose1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzyclose2" name="dizziness_with_eyes_closed" value="2">
            <img src="images/dizzyClose2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="dizzyclose3" name="dizziness_with_eyes_closed" value="3">
            <img src="images/dizzyClose3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>


  <h2>Vertigo</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="vertigo4" name="vertigo" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="vertigo0" name="vertigo" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="vertigo1" name="vertigo" value="1">
            <img src="images/vertigo1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="vertigo2" name="vertigo" value="2">
            <img src="images/vertigo2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="vertigo3" name="vertigo" value="3">
            <img src="images/vertigo3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>


  <h2>Stomach Awareness</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="stomach4" name="stomach_awareness" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="stomach0" name="stomach_awareness" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="stomach1" name="stomach_awareness" value="1">
            <img src="images/stomach1.png" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="stomach2" name="stomach_awareness" value="2">
            <img src="images/stomach2.png" alt="Basic">
              <center>
                <p>Moderate</p>
              </center>
        </label>
        <label>
            <input type="radio" id="stomach3" name="stomach_awareness" value="3">
            <img src="images/stomach3.png" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>


  <h2>Burping</h2>
  <hr>

    <div class = "pictures">
      <label>
            <input type="radio" id="burp4" name="burping" value="-1" checked>
            <img src="images/question.png" alt="Basic">
            <center>
              <p>Don't Understand</p>
            </center>
        </label>
        <label>
            <input type="radio" id="burp0" name="burping" value="0">
            <img src="images/base0.png" alt="Basic">
            <center>
              <p>None</p>
            </center>
        </label>
        <label>
            <input type="radio" id="burp1" name="burping" value="1">
            <img src="images/burp1.gif" alt="Basic">
            <center>
              <p>Slight</p>
            </center>
        </label>
        <label>
            <input type="radio" id="burp2" name="burping" value="2">
            <img src="images/burp2.gif" alt="Basic">
            <center>
              <p>Moderate</p>
            </center>
        </label>
        <label>
            <input type="radio" id="burp3" name="burping" value="3">
            <img src="images/burp3.gif" alt="Basic">
            <center>
              <p>Severe</p>
            </center>
        </label>
    </div>

    <input type="hidden" id="ssq_time" name="ssq_time" value="<?php echo $_POST['ssq_time']; ?>">
    <input type="hidden" id="ssq_type" name="ssq_type" value="1">
    <input type="hidden" id="session_ID" name="session_ID" value="<?php echo Session::get('session_ID'); ?>">
    
    <?php
    // Ask professor how to deal with code in URL.
    if (isset($_GET['code'])){?>
        <input type="hidden" id="code" name="code" value=" <?php echo $_GET['code'] ?>">
    <?php }
    else{ ?>
        <input type="hidden" id="code" name="code" value="">
    <?php } ?>

    <?php if ($isFirstTime){?>
        <input type="submit" class="btn btn-success" name="Submit" value="Submit">
    <?php } 
          else{ ?>
        <input type="submit" class="btn btn-success" name="Update" value="Update">
    <?php } ?>

</div>
</div>
</div>
</form>

<form action="session_details" method="POST">
    <input type="hidden" name="session_ID" value="<?php echo Session::get('session_ID'); ?>">
    <input type="submit" class="btn btn-danger" name="Cancel" value="Cancel">
</form>
</body>
</html>



        </div>
      </div>



  <?php
  include 'inc/footer.php';

  ?>