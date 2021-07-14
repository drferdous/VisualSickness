<?php
include 'inc/header.php';
include 'database.php';

Session::CheckSession();
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

          <?php
if(isset($_POST['Submit']))
{    
    $ssq_ID = $_POST['ssq_ID'];
    $general_discomfort = $_POST['general_discomfort'];
    $fatigue = $_POST['fatigue'];
    $headache = $_POST['headache'];
    $eye_strain = $_POST['eye_strain'];
    $difficulty_focusing = $_POST['difficulty_focusing'];
    $increased_salivation = $_POST['increased_salivation'];
    $sweating = $_POST['sweating'];
    $nausea = $_POST['nausea'];
    $difficulty_concentrating = $_POST['difficulty_concentrating'];
    $fullness_of_head = $_POST['fullness_of_head'];
    $blurred_vision = $_POST['blurred_vision'];
    $dizziness_with_eyes_open = $_POST['dizziness_with_eyes_open'];
    $dizziness_with_eyes_closed = $_POST['dizziness_with_eyes_closed'];
    $vertigo = $_POST['vertigo'];
    $stomach_awareness = $_POST['stomach_awareness'];
    $burping = $_POST['burping'];
    $pre_post = $_POST['pre_post'];
    $session_ID = $_POST['session_ID'];

    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $code = $_POST['code'];  // Note: Code can have a maximum of 8 characters.
    $race = $_POST['race'];
    $education = $_POST['education'];
    $quiz = '1';

    // Calculate scores
    $nausea_sum = $general_discomfort + $increased_salivation + $sweating + $nausea + $difficulty_concentrating + $stomach_awareness + $burping;
    $nausea_score = $nausea_sum * 9.54;

    $oculomotor_sum = $general_discomfort + $fatigue + $headache + $eye_strain + $difficulty_focusing + $difficulty_concentrating + $blurred_vision;
    $ocuomotor_score = $oculomotor_sum * 7.58;

    $disorient_sum = $difficulty_focusing + $nausea + $fullness_of_head + $blurred_vision + $dizziness_with_eyes_open + $dizziness_with_eyes_closed + $vertigo;
    $disorient_score = $disorient_sum * 13.92;

    $SSQ_Sum = $nausea_sum + $oculomotor_sum + $disorient_sum;
    $SSQ_Score = $SSQ_Sum * 3.74; 


    $sql = "INSERT INTO SSQ (ssq_ID, general_discomfort, fatigue, headache, difficulty_focusing, eye_strain,              increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision,           dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping, pre_post,          session_ID, code)
            VALUES ('$ssq_ID', '$general_discomfort', '$fatigue', '$headache', '$difficulty_focusing', '$eye_strain', '$increased_salivation', '$sweating', '$nausea', '$difficulty_concentrating', '$fullness_of_head', '$blurred_vision', '$dizziness_with_eyes_open', '$dizziness_with_eyes_closed', '$vertigo', '$stomach_awareness', '$burping', '$pre_post', '$session_ID', '$code')";
    if (mysqli_query($conn, $sql)) {
        echo "New record created successfully!";
        echo "<br>";
        echo "Nausea Score: ";
        echo $nausea_score;
        echo "<br>";
        echo "Oculomotor Score: ";
        echo $ocuomotor_score;
        echo "<br>";
        echo "Disorient Score: ";
        echo $disorient_score;
        echo "<br>";
        echo "SSQ Score: ";
        echo $SSQ_Score;
    }
    else{
        echo "Error: " . $sql;
        echo mysqli_error($conn);
    }
     
    $sql2 = "INSERT INTO Demographics (Code, Age, Race_Ethnicity, Gender, Education, Quiz)
            VALUES ('$code', '$age', '$race', '$gender', '$education', '$quiz')";
    if (mysqli_query($conn, $sql2)) {
        echo "Inserted";
    }
    else {
        echo "Error: " . $sql;
        echo mysqli_error($conn);
    }
}

    $sql = "SELECT study_ID
            FROM Session
            WHERE session_ID = " . $_POST['session_ID'] . 
            " LIMIT 1;";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    header("Location: session_list.php?study_ID=" . $row['study_ID']);
?>


        </div>
      </div>



  <?php
  include 'inc/footer.php';

  ?>
