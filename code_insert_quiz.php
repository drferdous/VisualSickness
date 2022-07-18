<?php
    include 'inc/header.php';
    include_once 'lib/Database.php';
    $db = Database::getInstance();
    $pdo = $db->pdo;
?>

<div class="card ">
    <div class="card-header"></div>
    <div class="card-body pr-2 pl-2">

        <?php
        
            if(isset($_POST['Submit'])) { 
                $code = $_POST['code'];
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
                $ssq_type = $_POST['ssq_type'];
            
                $age = $_POST['age'];
                $gender = $_POST['gender'];
                $race = $_POST['race'];
                $education = $_POST['education'];
            
                // Calculate scores
                $nausea_sum = $general_discomfort + $increased_salivation + $sweating + $nausea + $difficulty_concentrating + $stomach_awareness + $burping;
                $nausea_score = $nausea_sum * 9.54;
            
                $oculomotor_sum = $general_discomfort + $fatigue + $headache + $eye_strain + $difficulty_focusing + $difficulty_concentrating + $blurred_vision;
                $ocuomotor_score = $oculomotor_sum * 7.58;
            
                $disorient_sum = $difficulty_focusing + $nausea + $fullness_of_head + $blurred_vision + $dizziness_with_eyes_open + $dizziness_with_eyes_closed + $vertigo;
                $disorient_score = $disorient_sum * 13.92;
            
                $SSQ_Sum = $nausea_sum + $oculomotor_sum + $disorient_sum;
                $SSQ_Score = $SSQ_Sum * 3.74; 
            
            
                $sql = "INSERT INTO code_ssq (general_discomfort, fatigue, headache, difficulty_focusing, eye_strain, increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping, ssq_type, code)
                      VALUES ('$general_discomfort', '$fatigue', '$headache', '$difficulty_focusing', '$eye_strain', '$increased_salivation', '$sweating', '$nausea', '$difficulty_concentrating', '$fullness_of_head', '$blurred_vision', '$dizziness_with_eyes_open', '$dizziness_with_eyes_closed', '$vertigo', '$stomach_awareness', '$burping', '$ssq_type', '$code')";
                      
                $result = $pdo->query($sql);
                
                if ($result) {
                    echo "Thank you for successfully completing the survey. Please be on the lookout for an email from visualsicknessstudy@gmail.com regarding compensation for completing the study.";
                } else {
                    echo $pdo->errorInfo();
                }
                
                $ssq_id = $pdo->lastInsertId();
                 
                $sql3 = "INSERT INTO code_demographics (Age, Race_Ethnicity, Gender, Education, ssq_ID)
                      VALUES ('$age', '$race', '$gender', '$education', '$ssq_id')";
                      
                $result3 = $pdo->query($sql3);
                if (!$result3) {
                    echo $pdo->errorInfo();
                }
            }
        ?>


    </div>
</div>



<?php
  include 'inc/footer.php';
?>