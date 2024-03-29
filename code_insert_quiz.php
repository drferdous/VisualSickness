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
            if(isset($_POST['Submit']) && Session::CheckPostID($_POST)) {
                $rand = bin2hex(openssl_random_pseudo_bytes(16));
                Session::set("post_ID", $rand); 
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

		if ($code == 'AB4e3090' || $code == 'aB4e3090' || $code == 'AB4e8090' || $code == 'aB4e8090') {
			exit();
		}

                $sheetAPI = new Google_Service_Sheets($client);
                $spreadsheetID = "19X2usqE12rsZGdbNWJ84Xwh7oCrsRhH2xTsOPLn7Dx0";
                $range = "Form Responses 1!T3:Y";
                $response = $sheetAPI->spreadsheets_values->get($spreadsheetID, $range);
                $values = $response->getValues();
                if (empty($values)){
                    echo "No data!";
                }
                else{
                    foreach($values as $key=>$value) {
                        if (in_array($code, $value)) {
                            $location = $key + 3;
                        } 
                    }
                    $range = "Form Responses 1!T" . $location;
                    $response = $sheetAPI->spreadsheets_values->get($spreadsheetID, $range);
                    $value = $response->getValues();
                    $parentCode = $value[0][0];
                }
            
            
                $sql = "INSERT INTO code_ssq (general_discomfort, fatigue, headache, difficulty_focusing, eye_strain, increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping, ssq_type, code, parent_code)
                      VALUES ('$general_discomfort', '$fatigue', '$headache', '$difficulty_focusing', '$eye_strain', '$increased_salivation', '$sweating', '$nausea', '$difficulty_concentrating', '$fullness_of_head', '$blurred_vision', '$dizziness_with_eyes_open', '$dizziness_with_eyes_closed', '$vertigo', '$stomach_awareness', '$burping', '$ssq_type', '$code','$parentCode')";
                      
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
                
                $sheetAPI = new Google_Service_Sheets($client);
                $spreadsheetID = "19X2usqE12rsZGdbNWJ84Xwh7oCrsRhH2xTsOPLn7Dx0";
                $range = "Form Responses 1!T3:Y";
                $response = $sheetAPI->spreadsheets_values->get($spreadsheetID, $range);
                $values = $response->getValues();
                if (!empty($values)) {
                    foreach($values as $key=>$value) {
                        if (in_array($_POST['code'], $value)) {
                            $location = $key + 3;
                        }
                    }
                    if (!isset($location)) exit();

                    $range = "Form Responses 1!E" . $location;
                    $response = $sheetAPI->spreadsheets_values->get($spreadsheetID, $range);
                    $value = $response->getValues();
                    $email = $value[0][0];
                    $to          = $email;
                    $from        = "Visual Sickness Study <visualsicknessstudy@gmail.com>";
                    $subject     = "Thank You for Your Participation [$code]"; // email subject
                    if(ctype_upper($code[0])) {
                        $body        = "Hello,<br><br>
                                    You have successfully completed the study with code $code. Once all willing participants in your family have finished participating in the study please reply to this email with the email you would like your gift card sent to.";
                    } else {
                        $body        = "Hello,<br><br>
                                    You have successfully completed the study with code $code.";
                    }

                    $eol = PHP_EOL;
                    $semi_rand     = md5(time());
                    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
                    $headers       = "From: $from$eol" .
                      "MIME-Version: 1.0$eol" .
                      "Reply-To: $from$eol" .
                      "CC: visualsicknessstudy@gmail.com$eol" .
                      "Content-Type: multipart/mixed;$eol" .
                      " boundary=\"$mime_boundary\"";
                    
                    $message = "--$mime_boundary$eol" .
                    "Content-Type: text/html; charset=\"iso-8859-1\"$eol" .
                    "Content-Transfer-Encoding: 7bit$eol$eol" .
                    $body . $eol . "--$mime_boundary--";
                    
                    // Send the email
                    mail($to, $subject, $message, $headers);
                }
            }
        ?>


    </div>
</div>



<?php
  include 'inc/footer.php';
?>