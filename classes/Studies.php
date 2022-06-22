<?php

include_once 'lib/Database.php';
include_once 'lib/Session.php';
include_once 'classes/Util.php';

class Studies {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function insertQuiz($data) {
        if(isset($_POST['submitQuiz'])){    
            $ssq_ID = Session::get('ssq_ID');
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
            $result = $this->db->pdo->query($sql);
            if ($result) {
                if ($ssq_ID == -1) Session::set('ssq_ID', $this->db->pdo->lastInsertId());
                $message = Util::getModalForSSQ($this->db->pdo);
            }
            else{
                $message = "Error: " . $sql;
                $message .= $this->db->pdo->errorInfo();
            }
            
            if (Session::get('login') === FALSE) {     
                $sql2 = "INSERT INTO Demographics (Age, Race_Ethnicity, Gender, Education, Quiz)
                        VALUES ('$age', '$race', '$gender', '$education')";
                $this->db->pdo->query($sql2);
            }
            return $message;
        }
    }

  // Add participant to Session table and Demographics table
  public function addNewParticipant($data){
  // Note: this function does not work because the function does not take into account the study_ID yet.
    array_walk($data, function (&$val) {
        $val = trim($val);
    });
    $anonymous_name = $data['anonymous_name'];
    $dob = $data['dob'];
    $age = $data['age'];    
    $weight = $data['weight'];
    $gender = $data['gender'];
    $race_ethnicity = $data['ethnicity'];
    $occupation = $data['occupation'];
    $education = $data['education'];
    $phone_no = $data['phone_no'];
    $email = $data['email'];
    $comments = $data['comments'];
    $study_ID = Session::get('study_ID');
    
    $checkEmail = Util::checkExistEmail($email, $this->db);

    if (empty($anonymous_name)){
        return Util::generateErrorMessage("Name of participant must not be empty!");
    }
    if (empty($dob)){
        return Util::generateErrorMessage("Date of birth field must not be empty!");
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
        return Util::generateErrorMessage("Invalid email address!");
    }
    if ($checkEmail == TRUE) {
        return Util::generateErrorMessage("Email already exists. Try another email!");
    }
    if (empty($weight)){
        $weight = NULL;
    }
    
    $anonymous_name = Crypto::encrypt($data['anonymous_name'], $iv);
    $iv = bin2hex($iv);
    
        
    $this->db->pdo->beginTransaction();
    $result2 = '';
    try {
        $sql = "INSERT INTO Demographics (age, gender, education, race_ethnicity) 
        VALUES(:age, :gender, :education, :race_ethnicity);";  
        $stmt = $this->db->pdo->prepare($sql);
        
        $stmt->bindValue(':age', $age);
        $stmt->bindValue(':gender', $gender);
        $stmt->bindValue(':education', $education);    
        $stmt->bindValue(':race_ethnicity', $race_ethnicity);
        
        $result = $stmt->execute(); 
        if (!$result){
            throw new Exception($stmt->error);
        }
        
        $result_id = $this->db->pdo->lastInsertId();
        
        
        $sql2 = "INSERT INTO Participants (demographics_id, anonymous_name, dob, weight, occupation, phone_no, email, comments, study_id, iv) 
        VALUES(:demographics_id, :anonymous_name, :dob, :weight, :occupation, :phone_no, :email, :comments, :study_ID, :iv);";
        
        $stmt2 = $this->db->pdo->prepare($sql2);
        $stmt2->bindValue(':demographics_id', $result_id);        
        $stmt2->bindValue(':anonymous_name', $anonymous_name);
        $stmt2->bindValue(':dob', $dob);
        $stmt2->bindValue(':weight', $weight);
        $stmt2->bindValue(':occupation', $occupation);
        $stmt2->bindValue(':phone_no', $phone_no);
        $stmt2->bindValue(':email', $email);
        $stmt2->bindValue(':comments', $comments);        
        $stmt2->bindValue(':study_ID', $study_ID);        
        $stmt2->bindValue(':iv', $iv); 
        
        $result2 = $stmt2->execute();   
        
        if (!$result2){
            throw new Exception($stmt->error);
        }
        $this->db->pdo->commit();
    }
    catch (PDOException $excptn){
        $this->db->pdo->rollBack();
        return $excptn;
    }
    
    $last_edited_by = Session::get('id'); 
    $currentDate = new DateTime();
        
        $sql3 = "UPDATE Study
                SET last_edited_by = :last_edited_by, last_edited_at = :last_edited_at 
                WHERE study_ID = :study_ID
                LIMIT 1;";
        $stmt = $this->db->pdo->prepare($sql3);
        $stmt->bindValue(':last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':study_ID', $study_ID);
        $result_edit = $stmt->execute();
        
    if (!$result_edit){
        throw new Exception($stmt->error);
    }
        
    if ($result2) {
        return Util::generateSuccessMessage("You registered a participant!");
    }
    else {
        return Util::generateErrorMessage("Something went wrong.");
    }
  }

 // Add researcher to study 
  public function addResearcher($researcher_ID, $study_role){
    if (Session::get("study_ID") == 0){
        return Util::generateErrorMessage("You have an invalid study ID!");
    }
    $researcher_ID = trim($researcher_ID);
    $study_role = trim($study_role);
    if (empty($researcher_ID)){
        return Util::generateErrorMessage("Please select a researcher!");
    }
    if (empty($study_role)){
        return Util::generateErrorMessage("Please select a role!");
    }
    
    $study_ID = Session::get("study_ID");
    $last_edited_by = Session::get('id'); 
    $currentDate = new DateTime();
    
    $sql = "SELECT roleid FROM tbl_users
            WHERE id = :researcher_ID
            LIMIT 1;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':researcher_ID', $researcher_ID);
    $result = $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!isset($row['roleid'])){
        return Util::generateErrorMessage("We could not verify this user's role.");
    }
    if ($study_role < $row['roleid'] || $study_role > 4){
        return Util::generateErrorMessage("An invalid role was selected!");
    }
      
    $sql = "INSERT INTO Researcher_Study (researcher_ID, study_ID, study_role) VALUES (:researcher_ID, :study_ID, :study_role)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':researcher_ID', $researcher_ID);
        $stmt->bindValue(':study_ID', $study_ID);
        $stmt->bindValue(':study_role', $study_role); 
        $result = $stmt->execute(); 
        
    $sql = "UPDATE Study
                SET last_edited_by = :last_edited_by, last_edited_at = :last_edited_at 
                WHERE study_ID = :study_ID
                LIMIT 1;";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue('last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':study_ID', $study_ID);
        $result2 = $stmt->execute();
    
        if ($result && $result2){ 
            return Util::generateSuccessMessage("You have added a researcher!");
        } 
        else{
            return Util::generateErrorMessage("Something went wrong. Try registering again!");
        }    
    }
    
// Delete researcher to study 
  public function removeResearcher($researcher_ID){
    $researcher_ID = trim($researcher_ID);   
    if (empty($researcher_ID)){
        return Util::generateErrorMessage("Please select a researcher to remove!");
    }
           
    $study_ID = Session::get('study_ID');  
    $last_edited_by = Session::get('id'); 
    $currentDate = new DateTime();
      
    $sql = "UPDATE Researcher_Study SET is_active = 0 WHERE researcher_ID = :researcher_ID AND study_ID = :study_ID";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':researcher_ID', $researcher_ID);
    $stmt->bindValue(':study_ID', $study_ID);
    $result = $stmt->execute(); 
    
    $sql = "UPDATE Study
                SET last_edited_by = :last_edited_by, last_edited_at = :last_edited_at 
                WHERE study_ID = :study_ID
                LIMIT 1;";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':study_ID', $study_ID);
        $result2 = $stmt->execute();
    
    if ($result && $result2){
        return Util::generateSuccessMessage("You have removed a researcher!");
    }
    else{
        return Util::generateErrorMessage("Something went wrong. Try removing again!");
    }
  }
  
 // Edit researcher 
  public function editResearcher($researcher_ID, $study_role){
    $researcher_ID = trim($researcher_ID);
    $study_role = trim($study_role);
    if (Session::get("study_ID") == 0){
        return Util::generateErrorMessage("You have an invalid study ID!");
    }
    if (empty($researcher_ID)){
        return Util::generateErrorMessage("Please select a researcher!");
    }
    if (empty($study_role)){
        return Util::generateErrorMessage("Please select a role!");
    }
    
    $study_ID = Session::get("study_ID");
    $last_edited_by = Session::get('id'); 
    $currentDate = new DateTime();
    
    $sql = "SELECT roleid FROM tbl_users
            WHERE id = :researcher_ID
            LIMIT 1;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue('researcher_ID', $researcher_ID);
    $result = $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!isset($row["roleid"])){
        return Util::generateErrorMessage("We could not verify this user's role.");
    }
    if ($study_role < $row["roleid"] || $study_role > 4){
        return Util::generateErrorMessage("An invalid role was selected.");
    }
      
    $sql = "UPDATE Researcher_Study SET study_role = :study_role WHERE study_ID = :study_ID AND researcher_ID = :researcher_ID";
    
    
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':researcher_ID', $researcher_ID);
        $stmt->bindValue(':study_ID', $study_ID);
        $stmt->bindValue(':study_role', $study_role);   
        $result = $stmt->execute(); 
    
    $sql2 = "UPDATE Study
            SET last_edited_by = :last_edited_by, last_edited_at = :last_edited_at 
            WHERE study_ID = :study_ID
            LIMIT 1;";
        $stmt = $this->db->pdo->prepare($sql2);
        $stmt->bindValue('last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':study_ID', $study_ID);
        $result2 = $stmt->execute();
    
        if ($result && $result2){ 
            return Util::generateSuccessMessage("You have edited a researcher!");
        } 
        else{
            return Util::generateErrorMessage("Something went wrong. Try registering again!");
        }    
    }
  
// Delete participant from study 
  public function removeParticipant($participant_ID){
    $participant_ID = trim($participant_ID);
    if (empty($participant_ID)){
        return Util::generateErrorMessage("Please select a participant to remove!");
    }
    
      
    $sql = "UPDATE Participants SET is_active = 0 WHERE participant_ID = :participant_ID";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':participant_ID', $participant_ID);
    $result = $stmt->execute(); 
    
    $last_edited_by = Session::get('id'); 
    $currentDate = new DateTime();
    
    $sql = "UPDATE Study
                SET last_edited_by = :last_edited_by, last_edited_at = :last_edited_at 
                WHERE study_ID = :study_ID
                LIMIT 1;";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue('last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':study_ID', Session::get("study_ID"));
        $result2 = $stmt->execute();
    
    if ($result && $result2) {
        return Util::generateSuccessMessage("You have removed a participant!");
    }
    else{
        return Util::generateErrorMessage("Something went wrong. Try removing again!");
    }
  }
  
  // take SSQ quiz from Session
public function takeSSQ($data){
    array_walk($data, function (&$val) {         $val = trim($val);     });
    if (!(isset($data['quiz_type']) && isset($data['ssq_time']))){
        return Util::generateErrorMessage("Please select a quiz type and a quiz time!");
    }
    
    $quiz_type = $data['quiz_type'];
    $ssq_time = $data['ssq_time'];
    $session_ID = Session::get('session_ID');
    
    $sql = "SELECT *
            FROM SSQ
            WHERE session_ID = :session_ID
            AND ssq_time = :ssq_time
            AND ssq_type = :quiz_type
            AND is_active = 1
            LIMIT 1;";
    
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(":session_ID", $session_ID);
    $stmt->bindValue(":ssq_time", $ssq_time);
    $stmt->bindValue(":quiz_type", $quiz_type);
    $result = $stmt->execute();
    
    if (!$result){
        return Util::generateErrorMessage("Something went wrong. Try again!");
    }
    
    if ($stmt->rowCount() === 0){
        return Util::generateSuccessMessage("You will take the quiz momentarily!");
    }
    else{
        return Util::generateErrorMessage("You have already taken a quiz with the same type and time!");
    }
}
  
  // remove SSQ quiz from Session
  public function deleteQuiz($data){
    array_walk($data, function (&$val) {         $val = trim($val);     });
    $ssq_ID = Session::get('ssq_ID');          
      
    $sql = "UPDATE SSQ
            SET is_active = 0
            WHERE ssq_ID = :ssq_ID
            LIMIT 1;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':ssq_ID', $ssq_ID);
    $result = $stmt->execute();
    
    if ($result) { 
        return Util::generateSuccessMessage("You have deleted this quiz!");
    } 
    else {
        return Util::generateErrorMessage("Something went wrong. Try deleting again!");
    }
  }  
  
  // Get Study Information By Study Id
  public function getStudyInfo($study_ID){
      $sql = "SELECT * FROM Study WHERE study_ID = :study_ID LIMIT 1";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':study_ID', $study_ID);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      }else{
        return false;
      }
    }// Insert user's study in Study table
    
 public function insert_study($data) {
    array_walk($data, function (&$val) {         $val = trim($val);     });
    $full_name = $data['full_name'];
    $short_name = $data['short_name'];
    $IRB = $data['IRB'];
    $description = $data['description']; 
    $ssq_times = explode(",", $data["ssq_times"]);
    array_walk($ssq_times, function (&$val) {         $val = ucwords(trim($val));     });
    $ssq_times = array_filter($ssq_times, function ($time) { return $time != ''; });
    $created_by = Session::get('id');
    $last_edited_by = Session::get('id');    

    if ($full_name == "" || $short_name == "" || $IRB == "") {
        return array(Util::generateErrorMessage("Study registration fields must not be empty!"));
    } 
    if (count($ssq_times) !== count(array_unique($ssq_times))){
        return array(Util::generateErrorMessage("There should be no duplicate SSQ times!"));
    }
  
    $sql = "INSERT INTO Study (full_name, short_name, IRB, description, created_by, last_edited_by)
            VALUES (:full_name, :short_name, :IRB, :description, :created_by, :last_edited_by)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue('full_name', $full_name);
    $stmt->bindValue('short_name', $short_name);
    $stmt->bindValue('IRB', $IRB);
    $stmt->bindValue('description', $description);      
    $stmt->bindValue('created_by', $created_by, PDO::PARAM_INT);
    $stmt->bindValue('last_edited_by', $created_by, PDO::PARAM_INT); 
    $result = $stmt->execute();
      
    if (!$result){
        return Util::generateErrorMessage("Something went wrong. Try again!");
    }
      
    $study_ID = $this->db->pdo->lastInsertId();
    $insert = implode(",", array_map(function($time) use($study_ID){
        return "('" . ucwords($time) . "'," . $study_ID . ")"; 
    }, $ssq_times));
    
    $sql = "INSERT INTO SSQ_times (name, study_id) 
            VALUES " . $insert;
    $result = $this->db->pdo->query($sql);
    
    if ($result) {
        return array(Util::generateSuccessMessage("You have created a study!"), $study_ID);
    } 
    else {
        return array(Util::generateErrorMessage("Something went wrong. Try again!"));
    }
} 

    // Edit a user's study
    public function updateStudy($data){
        array_walk($data, function (&$val) {         $val = trim($val);     });
        $full_name = $data['full_name'];
        $short_name = $data['short_name'];
        $IRB = $data['IRB'];
        $description = $data['description'];
        $ssq_times_str = $data['ssq_times'];
        $last_edited_by = Session::get('id');
        $study_ID = Session::get('study_ID');
        $ssq_times = explode(',', $ssq_times_str);
        array_walk($ssq_times, function (&$time) {
            $time = ucwords(trim($time));
        });
        $ssq_times = array_filter($ssq_times, function ($time) { return $time !== ''; });
        
        $currentDate = new DateTime();
        
        if ($full_name == "" || $short_name == ""|| $IRB == "") {
            return Util::generateErrorMessage("You cannot leave required field empty!");
        }
        if ($ssq_times !== array_unique($ssq_times)) {
            return Util::generateErrorMessage("You cannot have multiple SSQ times of the same name!");
        }
        $pdo = $this->db->pdo;
        $sql = "UPDATE Study SET full_name = :full_name, short_name = :short_name, IRB = :IRB, description = :description, last_edited_by = :last_edited_by, last_edited_at = :last_edited_at WHERE study_ID = :study_ID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':full_name', $full_name);
        $stmt->bindValue(':short_name', $short_name);  
        $stmt->bindValue(':IRB', $IRB);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue('last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':study_ID', $study_ID);
        $result = $stmt->execute();
        
        if (!$result) {
            return Util::generateErrorMessage("Something went wrong. Try editing again!");
        }
        
        $old_times_sql = "SELECT name FROM SSQ_times WHERE study_id = $study_ID AND is_active = 1;";
        $old_times_res = $pdo->query($old_times_sql);
        $old_times = array();
        while ($row = $old_times_res->fetch(PDO::FETCH_ASSOC)) {
            array_push($old_times, $row['name']);
        }

        array_walk($old_times, function (&$val) {         $val = trim($val);     });
        $added_ssq_times = array_filter($ssq_times, function ($time) use($old_times) {
            return !in_array($time, $old_times);
        });
        $removed_ssq_times = array_filter($old_times, function ($time) use($ssq_times) {
            return !in_array($time, $ssq_times);
        });
        
        if (count($added_ssq_times) > 0) {
            // added 1 or more time names
            $added = implode(', ', array_map(function ($time) { return "'$time'"; }, $added_ssq_times));
            $added_back_sql = "SELECT name FROM SSQ_times WHERE is_active = 0 AND study_id = $study_ID AND name IN ($added)";
            $added_back_res = $pdo->query($added_back_sql);
            $added_back = array();
            while ($row = $added_back_res->fetch(PDO::FETCH_ASSOC)) {
                array_push($added_back, $row['name']);
            }
            if (count($added_back) > 0) {
                // add back names
                $added_back_str = implode(', ', array_map(function ($time) { return "'$time'"; }, $added_back));
                $add_back_sql = "UPDATE SSQ_times SET is_active = 1 WHERE study_id = $study_ID AND name IN ($added_back_str);";
                $result = $pdo->query($add_back_sql);
                
                if (!$result) {
                    return Util::generateErrorMessage("Something went wrong. Try editing again!");
                }
            }
            // add new names
            if (count(array_filter($added_ssq_times, function ($time) use($added_back) {
                return !in_array($time, $added_back);
            })) > 0) {
                $insert = implode(', ', array_map(function ($time) use($study_ID) {
                    return "('" . $time . "', " . $study_ID . ")";
                }, array_filter($added_ssq_times, function ($time) use($added_back) {
                    return !in_array($time, $added_back);
                })));
                $insert_sql = "INSERT INTO SSQ_times (name, study_id) VALUES $insert;";
                $result = $pdo->query($insert_sql);
                
                if (!$result) {
                    return Util::generateErrorMessage("Something went wrong. Try editing again!");
                }
            }
        }
        
        if (count($removed_ssq_times) > 0) {
            // removed 1 or more time names
            
            $remove = implode(', ', array_map(function ($time) { return "'$time'"; }, $removed_ssq_times));
            
            
            $sql = "SELECT ssq_ID FROM SSQ WHERE ssq_time IN (SELECT id FROM SSQ_times WHERE study_id = $study_ID AND name IN ($remove) AND is_active = 1)";
            $result_ssq = $pdo->query($sql);
            
            if($result_ssq->fetch(PDO::FETCH_ASSOC)) {
                return "<script type='text/javascript'>
                    $(document).ready(() => {
                        if (confirm('Deleting this SSQ Time will result in loss of previously created SSQs')) {
                            $.ajax({
                                url: 'ssq_connect',
                                type: 'POST',
                                cache: false,
                                data:{
                                    studyID: $study_ID,
                                    remove: \"$remove\"
                                },
                            })
                            .done(function(data) {
                                const div = document.createElement('div');
                                $(div).html(data);
                                $('.container').insertBefore(div.firstChild, $('.card'));
                                location.reload();
                            });
                        }
                    });
                </script>";
                // return "";
            } else {
            
                $remove_sql = "UPDATE SSQ_times SET is_active = 0 WHERE study_id = $study_ID AND name IN ($remove)";
                $result = $pdo->query($remove_sql);
                    
                if (!$result) {
                    return Util::generateErrorMessage("Something went wrong. Try editing again!");
                }
            }
        }
        
        return Util::generateSuccessMessage("You have edited this study!");
    }
    
    // Activates study based on the given study_ID.
    public function activateStudy($study_ID){
        $last_edited_by = Session::get('id'); 
        $currentDate = new DateTime();
        
        $sql = "UPDATE Study
                SET is_active = 1, last_edited_by = :last_edited_by, last_edited_at = :last_edited_at
                WHERE study_ID = :study_ID;
                LIMIT 1;";
                
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(":study_ID", $study_ID);
         $stmt->bindValue('last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $result = $stmt->execute();
        
        if ($result){
            return Util::generateSuccessMessage("You activated this study!");
        }
        else{
            return Util::generateErrorMessage("Something went wrong. Try activating again!");
        }
    }
    
    // Deactivates study based on the given study_ID.
    public function deactivateStudy($study_ID){
        $last_edited_by = Session::get('id'); 
        $currentDate = new DateTime();
        $sql = "UPDATE Study
                SET is_active = 0, last_edited_by = :last_edited_by, last_edited_at = :last_edited_at 
                WHERE study_ID = :study_ID
                LIMIT 1;";
        
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':study_ID', $study_ID);
        $stmt->bindValue('last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $result = $stmt->execute();
        
        
        if ($result){
            return Util::generateSuccessMessage("You deactivated this study!");
        }
        else{
            return Util::generateErrorMessage("Something went wrong. Try deactivating again!");
        }
    }
    
    // leaves the current study
    public function leaveStudy($study_ID){
        $pi_sql = "SELECT COUNT(study_role) AS Count FROM Researcher_Study WHERE study_ID = " . Session::get("study_ID") . " AND study_role = 2 AND is_active = 1;";
        $pi_result = $this->db->pdo->query($pi_sql);
        $pi_count = $pi_result->fetch(PDO::FETCH_ASSOC);
        if ($pi_count['Count'] > 1){
            $sql = "UPDATE Researcher_Study
                    SET is_active = 0
                    WHERE researcher_ID = :researcher_ID
                    AND study_ID = :study_ID
                    AND is_active = 1
                    LIMIT 1;";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':researcher_ID', Session::get('id'));
            $stmt->bindValue(':study_ID', $study_ID);
            
            $result = $stmt->execute();
            if ($result){
                return Util::generateSuccessMessage("You left this study!");
            }
            else{
                return Util::generateErrorMessage("Something went wrong. Try leaving again!");
            }
        }
    }
    
    // inserts a session of a  study into DB
    public function insert_session($data){
        array_walk($data, function (&$val) {         $val = trim($val);     });
        $created_by = Session::get('id');
        
        if (empty($data["participant_ID"])){
            return Util::generateErrorMessage("Please select a participant!");
        }
        if (empty($data["comment"])){
            $data["comment"] = NULL;
        }
        
        $this->db->pdo->beginTransaction();
        try{
            $sql = "INSERT INTO Session (study_ID, participant_ID, comment, created_by, last_edited_by)
                    VALUES (:study_ID, :participant_ID, :comment, :created_by, :last_edited_by);";
            $stmt = $this->db->pdo->prepare($sql);
            
            $stmt->bindValue(':study_ID', Session::get('study_ID'));
            $stmt->bindValue(':participant_ID', $data["participant_ID"]);
            $stmt->bindValue(':comment', $data["comment"]);
            $stmt->bindValue(':created_by', $created_by);
            $stmt->bindValue(':last_edited_by', $created_by);  
            
            $result = $stmt->execute();
            if (!$result){
                throw new Exception($stmt->error);
            }
            
            $sql = "SELECT LAST_INSERT_ID();";
            $stmt = $this->db->pdo->prepare($sql);
            $result = $stmt->execute();
            if (!$result){
                throw new Exception($stmt->error);
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            Session::set('session_ID', intval($result['LAST_INSERT_ID()']));
            
            $this->db->pdo->commit();
            $msg = Util::generateSuccessMessage("You created a new session! You will now be redirected to the Session Details page for this study.");
        }
        catch (PDOException $excptn){
            $this->db->pdo->rollBack();
            $msg = Util::generateErrorMessage("Something went wrong. Try creating a session again!");
        }
        finally{
            return $msg;
        }
    }
    
    // restarts the current session within a study by removing the end time of a session.
    public function restart_session($session_ID) {
        $last_edited_by = Session::get('id');        
        
        $sql = "UPDATE Session
                SET end_time = NULL, 
                    last_edited_by = :last_edited_by
                WHERE session_ID = :session_ID
                LIMIT 1;";
                
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':session_ID', $session_ID);
        $stmt->bindValue('last_edited_by', $last_edited_by);
        $result = $stmt->execute();
        
        if ($result){
            Session::set('session_ID', intval($session_ID));
            return Util::generateSuccessMessage("Session restarted!");
        }
        else{
            return Util::generateErrorMessage("Something went wrong. Try restarting again!");
        }
    }
    
    // ends the current session within a study.
    public function endSession($session_ID){
        $currentDate = new DateTime();
        $sql = "UPDATE Session
                SET end_time = :end_time
                WHERE session_ID = :session_ID
                LIMIT 1;";
        
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':end_time', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':session_ID', $session_ID);
        
        $result = $stmt->execute();
        if ($result){
            return Util::generateSuccessMessage("Session ended!");
        }
        else{
            return Util::generateSuccessMessage("Something went wrong. Try ending again!");
        }
    }
    
    // removes current session within a study.
    public function removeSession($session_ID){
        $currentDate = new DateTime();
        $sql = "UPDATE Session
                SET is_active = 0
                WHERE session_ID = :session_ID
                LIMIT 1;";
        
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':session_ID', $session_ID);
        
        $result = $stmt->execute();
        if ($result){
            return Util::generateSuccessMessage("Session has been removed!");
        }
        else{
            return Util::generateSuccessMessage("Something went wrong. Try ending again!");
        }
    }
    
    // ends the current session within a study.
    public function deleteSSQ($session_ID){
        $currentDate = new DateTime();
        $sql = "UPDATE Session
                SET end_time = :end_time
                WHERE session_ID = :session_ID
                LIMIT 1;";
        
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':end_time', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':session_ID', $session_ID);
        
        $result = $stmt->execute();
        if ($result){
            Session::set('session_ID', -1);
            return Util::generateSuccessMessage("Session ended!");
        }
        else{
            return Util::generateErrorMessage("Something went wrong. Try ending again!");
        }
    }    
}