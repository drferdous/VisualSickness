<?php

class Util {
    
  // Date formate Method
   public static function formatDate($date){
     // date_default_timezone_set('Asia/Dhaka');
      $strtime = strtotime($date);
    return date('Y-m-d H:i:s', $strtime);
   }
   
   public static function getModalForSSQ($pdo, $new=TRUE) {
        $ssq_sql = "SELECT * FROM SSQ WHERE ssq_ID = " . Session::get('ssq_ID');
        $ssq_result = $pdo->query($ssq_sql);
        $row = $ssq_result->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $general_discomfort = $row['general_discomfort'];
            $fatigue = $row['fatigue'];
            $headache = $row['headache'];
            $eye_strain = $row['eye_strain'];
            $difficulty_focusing = $row['difficulty_focusing'];
            $increased_salivation = $row['increased_salivation'];
            $sweating = $row['sweating'];
            $nausea = $row['nausea'];
            $difficulty_concentrating = $row['difficulty_concentrating'];
            $fullness_of_head = $row['fullness_of_head'];
            $blurred_vision = $row['blurred_vision'];
            $dizziness_with_eyes_open = $row['dizziness_with_eyes_open'];
            $dizziness_with_eyes_closed = $row['dizziness_with_eyes_closed'];
            $vertigo = $row['vertigo'];
            $stomach_awareness = $row['stomach_awareness'];
            $burping = $row['burping'];
            
            $nausea_sum = $general_discomfort + $increased_salivation + $sweating + $nausea + $difficulty_concentrating + $stomach_awareness + $burping;
            $nausea_score = $nausea_sum * 9.54;
        
            $oculomotor_sum = $general_discomfort + $fatigue + $headache + $eye_strain + $difficulty_focusing + $difficulty_concentrating + $blurred_vision;
            $oculomotor_score = $oculomotor_sum * 7.58;
        
            $disorient_sum = $difficulty_focusing + $nausea + $fullness_of_head + $blurred_vision + $dizziness_with_eyes_open + $dizziness_with_eyes_closed + $vertigo;
            $disorient_score = $disorient_sum * 13.92;
        
            $SSQ_Sum = $nausea_sum + $oculomotor_sum + $disorient_sum;
            $ssq_score = $SSQ_Sum * 3.74;
            return "
            <div class='modal fade' role='dialog' id='ssqModal' aria-labelledby='modalLabel'>
                <div class='modal-dialog' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='modalLabel'>" . ($new ? "New Record Created" : "SSQ Results") . "</h5>
                            <button type='button' class='close modalClose' data-dismiss='modal' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>
                        <div class='modal-body'>
                            <p>Nausea Score: $nausea_score</p>
                            <p>Oculomotor Score: $oculomotor_score</p>
                            <p>Disorient Score: $disorient_score</p>
                            <p>SSQ Score: $ssq_score</p>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary modalClose' data-dismiss='modal'>Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(() => {
                    $('.modal').modal('toggle');
                    $('.modalClose').on('click', () => {
                        $('.modal').modal('toggle');
                        location.href = 'session_details';
                    });
                });
            </script>";
        } else {
           return "
            <div class='modal fade' tabindex='-1' role='dialog' id='ssqModal' aria-labelledby='modalLabel'>
                <div class='modal-dialog' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='modalLabel'>An Error Occurred</h5>
                            <button type='button' class='modalClose close' data-dismiss='modal' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>
                        <div class='modal-body'>
                            <p>An error occurred displaying SSQ results. Please try again.</p>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary modalClose' data-dismiss='modal'>Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(() => {
                    $('.modal').modal('toggle');
                    $('.modalClose').on('click', () => {
                        $('.modal').modal('toggle');
                        location.href = 'session_details';
                    });
                });
            </script>";
       }
   }
   
   public static function generateErrorMessage($errorMessage){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error! </strong>' . $errorMessage . '</div>';
        return $msg;
   }
   
   public static function generateSuccessMessage($successMessage){
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success! </strong>' . $successMessage . '</div>';
        return $msg;
   }

  // Check Exist Email Address Method
  public static function checkExistEmail($email, $db){
    $sql = "SELECT email FROM tbl_users 
            WHERE email = :email AND status < 2
            LIMIT 1";
    $stmt = $db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    }else{
      return false;
    }
  }
  
  public static function getUserEmailById($pdo, $id) {
      $sql = "SELECT email FROM tbl_users WHERE id = $id";
      $result = $pdo->query($sql);
      if (!$result) {
          echo $pdo->errorInfo();
          return null;
      }
      return $result->fetch(PDO::FETCH_ASSOC)['email'];
  }
  
  public static function generateRandomPassword(){
    $MIN_PASSWORD_LENGTH = 8;
    $MAX_PASSWORD_LENGTH = 16;
      
    $availableChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $maxCharsIndex = strlen($availableChars) - 1;
    $passwordLength = rand($MIN_PASSWORD_LENGTH, $MAX_PASSWORD_LENGTH);
    $randomPassword = "";
      
    for ($i = 0; $i < $passwordLength; $i++){
        $randomPassword = $randomPassword . $availableChars[rand(0, $maxCharsIndex)];       
    }
    // $randomPassword = SHA1($randomPassword);
    return $randomPassword;
  }
  
  public static function getAdminsFromAffiliation($pdo, $affiliationid) {
      $sql = "SELECT email FROM tbl_users WHERE roleid = 1 AND affiliationid = $affiliationid";
      $result = $pdo->query($sql);
      if (!$result) {
          echo $pdo->errorInfo();
          return null;
      }
      $emails = array();
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
          array_push($emails, $row['email']);
      }
      return implode(', ', $emails);
  }
  
  public static function getAffiliationNameById($pdo, $affiliationId) {
      $sql = "SELECT Name FROM Affiliation WHERE id = $affiliationId";
      $result = $pdo->query($sql);
      if (!$result) {
          echo $pdo->errorInfo();
          return null;
      }
      return $result->fetch(PDO::FETCH_ASSOC)['Name'];
  }
  
  public static function getValueFromPost($str, $post) {
      $return = isset($post[$str]) ? htmlspecialchars($post[$str]) : '';
       return $return;
  }
}