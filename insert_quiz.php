<?php
include 'inc/header.php';
include_once 'lib/Database.php';
$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();
?>

<div class="card ">
    <div class="card-header">
        <h3><span class="float-right">Welcome! 
            <strong><span class="badge badge-lg badge-secondary text-white">
            <?php
                $name = Session::get("name");
                if (isset($name)) {
                    echo $name;
                }
            ?>
            </span></strong>
        </span></h3>
    </div>
        
<div class="card-body pr-2 pl-2">

<?php
if(isset($_POST['Submit'])){    
    $ssq_ID = intval($_POST['ssq_ID']);
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
    $ssq_time = $_POST['ssq_time'];
    $ssq_type = $_POST['ssq_type'];
    $session_ID = Session::get('session_ID');
    $code = $_POST['code'];

    if (Session::get('login') === FALSE) { 
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $race = $_POST['race'];
        $education = $_POST['education'];
    }

    // Calculate scores
    $nausea_sum = $general_discomfort + $increased_salivation + $sweating + $nausea + $difficulty_concentrating + $stomach_awareness + $burping;
    $nausea_score = $nausea_sum * 9.54;

    $oculomotor_sum = $general_discomfort + $fatigue + $headache + $eye_strain + $difficulty_focusing + $difficulty_concentrating + $blurred_vision;
    $ocuomotor_score = $oculomotor_sum * 7.58;

    $disorient_sum = $difficulty_focusing + $nausea + $fullness_of_head + $blurred_vision + $dizziness_with_eyes_open + $dizziness_with_eyes_closed + $vertigo;
    $disorient_score = $disorient_sum * 13.92;

    $SSQ_Sum = $nausea_sum + $oculomotor_sum + $disorient_sum;
    $SSQ_Score = $SSQ_Sum * 3.74;
    
    if ($ssq_ID > 0){
        $sql = "UPDATE SSQ
                SET general_discomfort = " . $general_discomfort . ",
                    fatigue = " . $fatigue . ",
                    headache = " . $headache . ",
                    difficulty_focusing = " . $difficulty_focusing . ",
                    eye_strain = " . $eye_strain . ",
                    increased_salivation = " . $increased_salivation . ",
                    sweating = " . $sweating . ",
                    nausea = " . $nausea . ",
                    difficulty_concentrating = " . $difficulty_concentrating . ",
                    fullness_of_head = " . $fullness_of_head . ",
                    blurred_vision = " . $blurred_vision . ",
                    dizziness_with_eyes_open = " . $dizziness_with_eyes_open . ",
                    dizziness_with_eyes_closed = " . $dizziness_with_eyes_closed . ",
                    vertigo = " . $vertigo . ",
                    stomach_awareness = " . $stomach_awareness . ",
                    burping = " . $burping . "
                WHERE ssq_ID = " . $ssq_ID . "
                LIMIT 1;";
    }
    else{
        $sql = "INSERT INTO SSQ (general_discomfort, fatigue, headache, difficulty_focusing, eye_strain,              increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping, ssq_time, ssq_type, session_ID, code)
            VALUES ('$general_discomfort', '$fatigue', '$headache', '$difficulty_focusing', '$eye_strain', '$increased_salivation', '$sweating', '$nausea', '$difficulty_concentrating', '$fullness_of_head', '$blurred_vision', '$dizziness_with_eyes_open', '$dizziness_with_eyes_closed', '$vertigo', '$stomach_awareness', '$burping', '$ssq_time', '$ssq_type', '$session_ID', '$code')";
    }
    
    $result = $pdo->query($sql);
    if ($result) {
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
        echo $pdo->errorInfo($conn);
    }
     
    if (Session::get('login') === FALSE) {     
        $sql2 = "INSERT INTO Demographics (Age, Race_Ethnicity, Gender, Education, Quiz)
                VALUES ('$age', '$race', '$gender', '$education')";
        mysqli_query($conn, $sql2);
    }
}

?>

<form action="session_details" method="post">
        <input type="hidden" name="session_ID" value="<?php echo Session::get('session_ID'); ?>">
        <button type="Submit" name="ok-btn" class="btn btn-success form-group">OK</button>
</form>

</div>
</div>

<?php
    include 'inc/footer.php';
?>