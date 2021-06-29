<!DOCTYPE html>
<head>


<link rel="stylesheet" href="<?php echo $set->url; ?>../css/bootstrap.min.css">

<style>

    .Header {
  background-color: white;
  text-align: center;
        padding: 10px;
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
  width: 180px;
  margin: auto;

    margin: auto;
}
    body 
    {

        padding: 0px;
          align-content: : center;
        background-color: lightblue;
    }

    h1   
    {
        color: lightblue;
        text-align: center;
        font-size: : 40px;
        font-family: 'Work Sans', bold;
    }

        h2
    {
        color: gray;
        text-align: left;
        font-size: : 30px;
        font-family: 'Work Sans', bold;
    }

    p    
    {
        color: red;
        
    } 


/* IMAGE STYLES */
[type=radio] + img {
  cursor: pointer;
}

/* CHECKED STYLES */
[type=radio]:checked + img {
  outline: 2px solid #f00;
}
</style>

</head>


<html>
<body>

<center>
<div class="Header">
  <hr>
  <h1>Cybersickness Online Questionnaire</h1>
  <hr>
</div>

<div class="symptoms">
  <h2>General Discomfort</h2>
  <hr>
<form action="insert_quiz.php" method="post">
    <input type="hidden" id="ssq_ID" name="ssq_ID" value="0">
    <div class = "pictures">
        <label>
            <input type="radio" id="discomfort0" name="general_discomfort" value="0" checked> None
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
</div>

<div class="symptoms">
  <h2>Fatigue</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="fatigue0" name="fatigue" value="0" checked> None
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

<div class="symptoms">
  <h2>Headache</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="headache0" name="headache" value="0" checked> None
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

<div class="symptoms">
  <h2>Eye Strain</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="eyestrain0" name="eye_strain" value="0" checked> None
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

<div class="symptoms">
  <h2>Difficulty Focusing</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="focusing0" name="difficulty_focusing" value="0" checked> None
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

<div class="symptoms">
  <h2>Increased Salivation</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="increased_salivation0" name="increased_salivation" value="0" checked> None
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

<div class="symptoms">
  <h2>Sweating</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="sweat0" name="sweating" value="0" checked> None
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

<div class="symptoms">
  <h2>Nausea</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="nausea0" name="nausea" value="0" checked> None
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

<div class="symptoms">
  <h2>Difficulty Concentrating</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="focus0" name="difficulty_concentrating" value="0" checked> None
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

<div class="symptoms">
  <h2>Fullness of the Head</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="fullness0" name="fullness_of_head" value="0" checked> None
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

<div class="symptoms">
  <h2>Blurred Vision</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="blurred0" name="blurred_vision" value="0" checked> None
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

<div class="symptoms">
  <h2>Dizziness with Eyes Open</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="dizzinessEyes0" name="dizziness_with_eyes_open" value="0" checked> None
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


<div class="symptoms">
  <h2>Dizziness with Eyes Closed</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="dizzyclose0" name="dizziness_with_eyes_closed" value="0" checked> None
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



<div class="symptoms">
  <h2>Vertigo</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="vertigo0" name="vertigo" value="0" checked> None
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



<div class="symptoms">
  <h2>Stomach Awareness</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="stomach0" name="stomach_awareness" value="0" checked> None
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



<div class="symptoms">
  <h2>Burping</h2>
  <hr>

    <div class = "pictures">
        <label>
            <input type="radio" id="burp0" name="burping" value="0" checked> None
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

    <input type="hidden" id="pre_post" name="pre_post" value="0">
    <input type="hidden" id="session_ID" name="session_ID" value="0">
    <input type="submit" name="Submit" value="Submit">
</div>
</form>
</center>
</body>
</html>
