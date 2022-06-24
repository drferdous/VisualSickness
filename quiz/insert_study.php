<?php
include_once 'database.php';
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


    // Calculate scores
    $nausea_sum = $general_discomfort + $increased_salivation + $sweating + $nausea + $difficulty_concentrating + $stomach_awareness + $burping;
    $nausea_score = $nausea_sum * 9.54;

    $oculomotor_sum = $general_discomfort + $fatigue + $headache + $eye_strain + $difficulty_focusing + $difficulty_concentrating + $blurred_vision;
    $ocuomotor_score = $oculomotor_sum * 7.58;

    $disorient_sum = $difficulty_focusing + $nausea + $fullness_of_head + $blurred_vision + $dizziness_with_eyes_open + $dizziness_with_eyes_closed + $vertigo;
    $disorient_score = $disorient_sum * 13.92;

    $SSQ_Sum = $nausea_sum + $oculomotor_sum + $disorient_sum;
    $SSQ_Score = $SSQ_Sum * 3.74; 


     $sql = "INSERT INTO ssq (ssq_id, general_discomfort, fatigue, headache, difficulty_focusing, eye_strain, increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping, pre_post, session_id)
          VALUES ('$ssq_ID', '$general_discomfort', '$fatigue', '$headache', '$difficulty_focusing', '$eye_strain', '$increased_salivation', '$sweating', '$nausea', '$difficulty_concentrating', '$fullness_of_head', '$blurred_vision', '$dizziness_with_eyes_open', '$dizziness_with_eyes_closed', '$vertigo', '$stomach_awareness', '$burping', '$pre_post', '$session_ID')";
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
     } else {
        echo "Error: " . $sql . "
" . mysqli_error($conn);
     }
     mysqli_close($conn);
}
?>