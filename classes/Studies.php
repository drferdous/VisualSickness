<?php

include_once 'lib/Database.php';
include_once 'lib/Session.php';
include_once 'classes/Util.php';

class Studies {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function insertQuiz($ssq) {
        if(isset($ssq['submitQuiz'])){    
            $ssq_ID = Session::get('ssq_ID');
            $general_discomfort = $ssq['general_discomfort'];
            $fatigue = $ssq['fatigue'];
            $headache = $ssq['headache'];
            $eye_strain = $ssq['eye_strain'];
            $difficulty_focusing = $ssq['difficulty_focusing'];
            $increased_salivation = $ssq['increased_salivation'];
            $sweating = $ssq['sweating'];
            $nausea = $ssq['nausea'];
            $difficulty_concentrating = $ssq['difficulty_concentrating'];
            $fullness_of_head = $ssq['fullness_of_head'];
            $blurred_vision = $ssq['blurred_vision'];
            $dizziness_with_eyes_open = $ssq['dizziness_with_eyes_open'];
            $dizziness_with_eyes_closed = $ssq['dizziness_with_eyes_closed'];
            $vertigo = $ssq['vertigo'];
            $stomach_awareness = $ssq['stomach_awareness'];
            $burping = $ssq['burping'];
            $ssq_time = $ssq['ssq_time'];
            $ssq_type = $ssq['ssq_type'];
            $session_ID = Session::get('session_ID');
            $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
            $role = $this->db->pdo->query($role_check_sql);
            if (!$role) {
                return Util::generateErrorMessage("You do not have proper access in this study!");
            }
            
            if ($ssq_ID > 0){
                $sql = "UPDATE ssq
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
                        WHERE ssq_id = " . $ssq_ID . "
                        LIMIT 1;";
            }
            else{
                $sql = "INSERT INTO ssq (general_discomfort, fatigue, headache, difficulty_focusing, eye_strain, increased_salivation, sweating, nausea, difficulty_concentrating, fullness_of_head, blurred_vision, dizziness_with_eyes_open, dizziness_with_eyes_closed, vertigo, stomach_awareness, burping, ssq_time, ssq_type, session_id)
                    VALUES ('$general_discomfort', '$fatigue', '$headache', '$difficulty_focusing', '$eye_strain', '$increased_salivation', '$sweating', '$nausea', '$difficulty_concentrating', '$fullness_of_head', '$blurred_vision', '$dizziness_with_eyes_open', '$dizziness_with_eyes_closed', '$vertigo', '$stomach_awareness', '$burping', '$ssq_time', '$ssq_type', '$session_ID')";
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
            return $message;
        }
    }
    
    public function editParticipant($participant_ID, $participantInfo) {
        array_walk($participantInfo, function (&$val) {
            $val = trim($val);
        });
        $anonymous_name = $participantInfo['name'];
        $dob = $participantInfo['dob'];  
        $weight = $participantInfo['weight'];
        $gender = $participantInfo['gender'];
        $race_ethnicity = $participantInfo['race_ethnicity'];
        $occupation = $participantInfo['occupation'];
        $education = $participantInfo['education'];
        $phone_no = $participantInfo['phone_no'];
        $email = $participantInfo['email'];
        $comments = $participantInfo['comments'];
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
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] == 4) {
            return Util::generateErrorMessage("You do not have proper access in this study!");
        }
        
        $anonymous_name = Crypto::encrypt($anonymous_name, $iv);
        $iv = bin2hex($iv);
        
        $currentDate = new DateTime();
        $object_dob = new DateTime($dob);
        $age = $currentDate->diff($object_dob)->y;
        
            
        $demo_id_sql = "SELECT demographics_id FROM participants
                        WHERE participant_id = $participant_ID";
        $demo_result = $this->db->pdo->query($demo_id_sql);
        $this->db->pdo->beginTransaction();
        $result2 = '';
        try {
            $sql = "UPDATE demographics SET age = :age, gender = :gender, education = :education, race_ethnicity = :race_ethnicity WHERE demographics_id = " . $demo_result->fetch(PDO::FETCH_ASSOC)['demographics_id'];  
            $stmt = $this->db->pdo->prepare($sql);
            
            $stmt->bindValue(':age', $age);
            $stmt->bindValue(':gender', $gender);
            $stmt->bindValue(':education', $education);    
            $stmt->bindValue(':race_ethnicity', $race_ethnicity);
            
            $result = $stmt->execute(); 
            if (!$result){
                throw new Exception($stmt->error);
            }
            
            
            $sql2 = "UPDATE participants SET
                     anonymous_name = :anonymous_name,
                     dob = :dob,
                     weight = :weight,
                     occupation = :occupation,
                     phone_no = :phone_no,
                     email = :email,
                     comments = :comments,
                     iv = :iv
                     WHERE participant_id = $participant_ID;";
            
            $stmt2 = $this->db->pdo->prepare($sql2);
            $stmt2->bindValue(':anonymous_name', $anonymous_name);
            $stmt2->bindValue(':dob', $dob);
            $stmt2->bindValue(':weight', $weight);
            $stmt2->bindValue(':occupation', $occupation);
            $stmt2->bindValue(':phone_no', $phone_no);
            $stmt2->bindValue(':email', $email);
            $stmt2->bindValue(':comments', $comments);   
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
        $result_edit = Util::updateStudy($study_ID, $last_edited_by, $this->db->pdo);
            
        if (!$result_edit){
            throw new Exception($stmt->error);
        }
            
        if ($result2) {
            return Util::generateSuccessMessage("You updated a participant!");
        }
        else {
            return Util::generateErrorMessage("Something went wrong.");
        }
    }

  // Add participant to Session table and Demographics table
  public function addNewParticipant($participantInfo){
    array_walk($participantInfo, function (&$val) {
        $val = trim($val);
    });
    $anonymous_name = $participantInfo['anonymous_name'];
    $dob = $participantInfo['dob'];  
    $weight = $participantInfo['weight'];
    $gender = $participantInfo['gender'];
    $race_ethnicity = $participantInfo['ethnicity'];
    $occupation = $participantInfo['occupation'];
    $education = $participantInfo['education'];
    $phone_no = $participantInfo['phone_no'];
    $email = $participantInfo['email'];
    $comments = $participantInfo['comments'];
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
    $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
    $role = $this->db->pdo->query($role_check_sql);
    if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] == 4) {
        return Util::generateErrorMessage("You do not have proper access in this study!");
    }
    
    $anonymous_name = Crypto::encrypt($participantInfo['anonymous_name'], $iv);
    $iv = bin2hex($iv);
    
    $currentDate = new DateTime();
    $object_dob = new DateTime($dob);
    $age = $currentDate->diff($object_dob)->y;
    
        
    $this->db->pdo->beginTransaction();
    $result2 = '';
    try {
        $sql = "INSERT INTO demographics (age, gender, education, race_ethnicity) 
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
        
        
        $sql2 = "INSERT INTO participants (demographics_id, anonymous_name, dob, weight, occupation, phone_no, email, comments, study_id, iv) 
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
    $result_edit = Util::updateStudy($study_ID, $last_edited_by, $this->db->pdo);
        
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
    $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
    $role = $this->db->pdo->query($role_check_sql);
    if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2) {
        return Util::generateErrorMessage("You do not have proper access in this study!");
    }
    
    $study_ID = Session::get("study_ID");
    $last_edited_by = Session::get('id'); 
    
    $sql = "SELECT role_id FROM users
            WHERE user_id = :researcher_ID
            LIMIT 1;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':researcher_ID', $researcher_ID);
    $result = $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!isset($row['role_id'])){
        return Util::generateErrorMessage("We could not verify this user's role.");
    }
    if ($study_role < $row['role_id'] || $study_role > 4){
        return Util::generateErrorMessage("An invalid role was selected!");
    }
      
    $sql = "INSERT INTO researchers (researcher_id, study_id, study_role) VALUES (:researcher_id, :study_id, :study_role)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':researcher_id', $researcher_ID);
        $stmt->bindValue(':study_id', $study_ID);
        $stmt->bindValue(':study_role', $study_role); 
        $result = $stmt->execute(); 
        
    $result2 = Util::updateStudy($study_ID, $last_edited_by, $this->db->pdo);
    
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
    $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
    $role = $this->db->pdo->query($role_check_sql);
    if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] == 4) {
        return Util::generateErrorMessage("You do not have proper access in this study!");
    }
           
    $study_ID = Session::get('study_ID');  
    $last_edited_by = Session::get('id'); 
      
    $sql = "UPDATE researchers SET is_active = 0 WHERE researcher_id = :researcher_id AND study_id = :study_id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':researcher_id', $researcher_ID);
    $stmt->bindValue(':study_id', $study_ID);
    $result = $stmt->execute(); 
    
    $result2 = Util::updateStudy($study_ID, $last_edited_by, $this->db->pdo);
    
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
    $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
    $role = $this->db->pdo->query($role_check_sql);
    if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2) {
        return Util::generateErrorMessage("You do not have proper access in this study!");
    }
    
    $study_ID = Session::get("study_ID");
    $last_edited_by = Session::get('id');
    
    $sql = "SELECT role_id FROM users
            WHERE user_id = :researcher_ID
            LIMIT 1;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue('researcher_ID', $researcher_ID);
    $result = $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!isset($row["role_id"])){
        return Util::generateErrorMessage("We could not verify this user's role.");
    }
    if ($study_role < $row["role_id"] || $study_role > 4){
        return Util::generateErrorMessage("An invalid role was selected.");
    }
      
    $sql = "UPDATE researchers SET study_role = :study_role WHERE study_id = :study_id AND researcher_id = :researcher_id";
    
    
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':researcher_id', $researcher_ID);
        $stmt->bindValue(':study_id', $study_ID);
        $stmt->bindValue(':study_role', $study_role);   
        $result = $stmt->execute(); 

        $result2 = Util::updateStudy($study_ID, $last_edited_by, $this->db->pdo);
    
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
    
    $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
    $role = $this->db->pdo->query($role_check_sql);
    if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2) {
        return Util::generateErrorMessage("You do not have proper access in this study!");
    }
      
    $sql = "UPDATE participants SET is_active = 0 WHERE participant_id = :participant_id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':participant_id', $participant_ID);
    $result = $stmt->execute(); 
    
    $last_edited_by = Session::get('id'); 
    $result2 = Util::updateStudy(Session::get('study_ID'), $last_edited_by, $this->db->pdo);
    
    if ($result && $result2) {
        return Util::generateSuccessMessage("You have removed a participant!");
    }
    else{
        return Util::generateErrorMessage("Something went wrong. Try removing again!");
    }
  }
  
  // take SSQ quiz from Session
public function takeSSQ($quiz_type, $ssq_time){
    $quiz_type = trim($quiz_type);
    $ssq_time = trim($ssq_time);
    if (!strlen($quiz_type) || !strlen($ssq_time)){
        return Util::generateErrorMessage("Please select a quiz type and a quiz time!");
    }
    $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
    $role = $this->db->pdo->query($role_check_sql);
    if (!$role) {
        return Util::generateErrorMessage("You do not have proper access in this study!");
    }

    $session_ID = Session::get('session_ID');
    
    $sql = "SELECT *
            FROM ssq
            WHERE session_id = :session_id
            AND ssq_time = :ssq_time
            AND ssq_type = :quiz_type
            AND is_active = 1
            LIMIT 1;";
    
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(":session_id", $session_ID);
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
  public function deleteQuiz(){
    $ssq_ID = Session::get('ssq_ID');  
    $creator_check_sql = "SELECT created_by FROM session WHERE session_id = " . Session::get('session_ID') . " AND is_active = 1";
    $creator = $this->db->pdo->query($creator_check_sql);
    $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
    $role = $this->db->pdo->query($role_check_sql);
    if ($creator->fetch(PDO::FETCH_ASSOC)['created_by'] != Session::get('id') && (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2)) {
        return Util::generateErrorMessage("You do not have proper access in this session!");
    }
      
    $sql = "UPDATE ssq
            SET is_active = 0
            WHERE ssq_id = :ssq_ID
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
      $sql = "SELECT * FROM study WHERE study_id = :study_ID LIMIT 1";
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
    
 public function insertStudy($data) {
    array_walk($data, function (&$val) {         $val = trim($val);     });
    $full_name = $data['full_name'];
    $short_name = $data['short_name'];
    $IRB = $data['IRB'];
    $description = $data['description']; 
    $ssq_times = explode(",", $data["ssq_times"]);
    array_walk($ssq_times, function (&$val) {         $val = ucwords(trim($val));     });
    $ssq_times = array_filter($ssq_times, function ($time) { return $time != ''; });
    $session_times = explode(",", $data["session_times"]);
    array_walk($session_times, function (&$val) {         $val = ucwords(trim($val));     });
    $session_times = array_filter($session_times, function ($time) { return $time != ''; });
    $created_by = Session::get('id');

    if ($full_name == "" || $short_name == "") {
        return array(Util::generateErrorMessage("Study registration fields must not be empty!"));
    }
    if (count($ssq_times) !== count(array_unique($ssq_times))){
        return array(Util::generateErrorMessage("There should be no duplicate SSQ times!"));
    }
    if (count($session_times) !== count(array_unique($session_times))){
        return array(Util::generateErrorMessage("There should be no duplicate Session times!"));
    }
    if (Session::get('roleid') > 2) {
        return Util::generateErrorMessage("You do not have proper access in this study!");
    }
    
    $this->db->pdo->beginTransaction();
    $result = "";
    try{
        $sql = "INSERT INTO study (full_name, short_name, IRB, description, created_by, last_edited_by)
                VALUES (:full_name, :short_name, :IRB, :description, :created_by, :last_edited_by);";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':full_name', $full_name);
        $stmt->bindValue(':short_name', $short_name);
        $stmt->bindValue(':IRB', $IRB);
        $stmt->bindValue(':description', $description);      
        $stmt->bindValue(':created_by', $created_by, PDO::PARAM_INT);
        $stmt->bindValue(':last_edited_by', $created_by, PDO::PARAM_INT);
        $result = $stmt->execute();
        if (!$result){
            throw new PDOException($stmt->error);
        }
        
        $study_id = $this->db->pdo->lastInsertId();
        $sql = "INSERT INTO researchers (researcher_id, study_id, study_role, is_active)
                VALUES (:researcher_id, :study_id, :study_role, :is_active);";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(":researcher_id", $created_by);
        $stmt->bindValue(":study_id", $study_id);
        $stmt->bindValue(":study_role", 2);
        $stmt->bindValue(":is_active", 1);
        $result = $stmt->execute();
        if (!$result){
            throw new PDOException($stmt->error);
        }
        
        $insert = implode(",", array_map(function($time) use($study_id){
            return "('" . ucwords($time) . "'," . $study_id . ")"; 
        }, $ssq_times));
    
        $sql = "INSERT INTO ssq_times (name, study_id) 
                VALUES " . $insert;
        $result = $this->db->pdo->query($sql);
        if (!$result){
            throw new PDOException();
        }
        
        $session_insert = implode(",", array_map(function($time) use($study_id){
            return "('" . ucwords($time) . "'," . $study_id . ")"; 
        }, $session_times));
        $sql = "INSERT INTO session_times (name, study_id) 
                VALUES " . $session_insert;
        $result = $this->db->pdo->query($sql);
        if (!$result){
            throw new PDOException();
        }
        
        $this->db->pdo->commit();
    }
    catch (PDOException $pdo_excptn){
        $this->db->pdo->rollBack();
        return array(Util::generateErrorMessage("Something went wrong. Try creating a study again!"));
    }
    catch (Exception $excptn){
        $this->db->pdo->rollBack();
        return array($excptn->getMessage());
    }
    
    Session::set('study_ID', $study_id);
    return array(Util::generateSuccessMessage("You have created a study!"), $study_id);
} 

    // Edit a user's study
    public function updateStudy($data){
        array_walk($data, function (&$val) {         
            $val = trim($val);
        });
        $full_name = $data['full_name'];
        $short_name = $data['short_name'];
        $IRB = $data['IRB'];
        $description = $data['description'];
        $ssq_times_str = $data['ssq_times'];
        $session_times_str = $data['session_times'];
        $last_edited_by = Session::get('id');
        $study_ID = Session::get('study_ID');
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2) {
            return Util::generateErrorMessage("You do not have proper access in this study!");
        }
        
        $ssq_times = explode(',', $ssq_times_str);
        array_walk($ssq_times, function (&$time) {
            $time = ucwords(trim($time));
        });
        $ssq_times = array_filter($ssq_times, function ($time) { 
            return $time !== '';
        });
        
        $session_times = explode(',', $session_times_str);
        array_walk($session_times, function (&$time) {
            $time = ucwords(trim($time));
        });
        $session_times = array_filter($session_times, function ($time) { 
            return $time !== '';
        });
        
        $currentDate = new DateTime();
        
        if ($full_name == "" || $short_name == "") {
            return Util::generateErrorMessage("You cannot leave required field empty!");
        }
        if ($ssq_times !== array_unique($ssq_times)) {
            return Util::generateErrorMessage("You cannot have multiple SSQ times of the same name!");
        }
        if ($session_times !== array_unique($session_times)) {
            return Util::generateErrorMessage("You cannot have multiple Session times of the same name!");
        }
        $pdo = $this->db->pdo;
        $sql = "UPDATE study SET full_name = :full_name, short_name = :short_name, IRB = :IRB, description = :description, last_edited_by = :last_edited_by, last_edited_at = :last_edited_at WHERE study_id = :study_ID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':full_name', $full_name);
        $stmt->bindValue(':short_name', $short_name);  
        $stmt->bindValue(':IRB', $IRB);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':last_edited_by', $last_edited_by);
        $stmt->bindValue(':last_edited_at', $currentDate->format('Y-m-d H:i:s'));
        $stmt->bindValue(':study_ID', $study_ID);
        $result = $stmt->execute();
        
        if (!$result) {
            return Util::generateErrorMessage("Something went wrong. Try editing again!");
        }
        
        $old_times_sql = "SELECT name FROM ssq_times WHERE study_id = $study_ID AND is_active = 1;";
        $old_times_res = $pdo->query($old_times_sql);
        $old_times = array();
        while ($row = $old_times_res->fetch(PDO::FETCH_ASSOC)) {
            array_push($old_times, $row['name']);
        }
        
        $old_session_sql = "SELECT name FROM session_times WHERE study_id = $study_ID AND is_active = 1;";
        $old_session_res = $pdo->query($old_session_sql);
        $old_session = array();
        while ($row = $old_session_res->fetch(PDO::FETCH_ASSOC)) {
            array_push($old_session, $row['name']);
        }

        array_walk($old_times, function (&$val) {         
            $val = trim($val);
        });
        $added_ssq_times = array_filter($ssq_times, function ($time) use($old_times) {
            return !in_array($time, $old_times);
        });
        $removed_ssq_times = array_filter($old_times, function ($time) use($ssq_times) {
            return !in_array($time, $ssq_times);
        });
        
        array_walk($old_session, function (&$val) {         
            $val = trim($val);
        });
        $added_session_times = array_filter($session_times, function ($time) use($old_session) {
            return !in_array($time, $old_session);
        });
        $removed_session_times = array_filter($old_session, function ($time) use($session_times) {
            return !in_array($time, $session_times);
        });
        
        if (count($added_ssq_times) > 0) {
            // added 1 or more time names
            $added = implode(', ', array_map(function ($time) { return "'$time'"; }, $added_ssq_times));
            $added_back_sql = "SELECT name FROM ssq_times WHERE is_active = 0 AND study_id = $study_ID AND name IN ($added)";
            $added_back_res = $pdo->query($added_back_sql);
            $added_back = array();
            while ($row = $added_back_res->fetch(PDO::FETCH_ASSOC)) {
                array_push($added_back, $row['name']);
            }
            if (count($added_back) > 0) {
                // add back names
                $added_back_str = implode(', ', array_map(function ($time) { return "'$time'"; }, $added_back));
                $add_back_sql = "UPDATE ssq_times SET is_active = 1 WHERE study_id = $study_ID AND name IN ($added_back_str);";
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
                $insert_sql = "INSERT INTO ssq_times (name, study_id) VALUES $insert;";
                $result = $pdo->query($insert_sql);
                
                if (!$result) {
                    return Util::generateErrorMessage("Something went wrong. Try editing again!");
                }
            }
        }
        
        if (count($added_session_times) > 0) {
            // added 1 or more time names
            $added = implode(', ', array_map(function ($time) { return "'$time'"; }, $added_session_times));
            $added_back_sql = "SELECT name FROM session_times WHERE is_active = 0 AND study_id = $study_ID AND name IN ($added)";
            $added_back_res = $pdo->query($added_back_sql);
            $added_back = array();
            while ($row = $added_back_res->fetch(PDO::FETCH_ASSOC)) {
                array_push($added_back, $row['name']);
            }
            if (count($added_back) > 0) {
                // add back names
                $added_back_str = implode(', ', array_map(function ($time) { return "'$time'"; }, $added_back));
                $add_back_sql = "UPDATE session_times SET is_active = 1 WHERE study_id = $study_ID AND name IN ($added_back_str);";
                $result = $pdo->query($add_back_sql);
                
                if (!$result) {
                    return Util::generateErrorMessage("Something went wrong. Try editing again!");
                }
            }
            // add new names
            if (count(array_filter($added_session_times, function ($time) use($added_back) {
                return !in_array($time, $added_back);
            })) > 0) {
                $insert = implode(', ', array_map(function ($time) use($study_ID) {
                    return "('" . $time . "', " . $study_ID . ")";
                }, array_filter($added_session_times, function ($time) use($added_back) {
                    return !in_array($time, $added_back);
                })));
                $insert_sql = "INSERT INTO session_times (name, study_Id) VALUES $insert;";
                $result = $pdo->query($insert_sql);
                
                if (!$result) {
                    return Util::generateErrorMessage("Something went wrong. Try editing again!");
                }
            }
        }
        
        if (count($removed_ssq_times) > 0 && count($removed_session_times) > 0){
            $remove_ssq = implode(', ', array_map(function ($time) { return "'$time'"; }, $removed_ssq_times));
            $sql_ssq = "SELECT ssq.ssq_id 
                        FROM ssq JOIN session ON ssq.session_id = session.session_id 
                        WHERE ssq.is_active = 1 
                        AND session.is_active 
                        AND ssq.ssq_time IN (SELECT id 
                                             FROM ssq_times 
                                             WHERE study_id = $study_ID 
                                             AND name IN ($remove_ssq) 
                                             AND is_active = 1)";
            
            $result_ssq = $pdo->query($sql_ssq);
            $row_ssq = $result_ssq->fetch(PDO::FETCH_ASSOC);
            
            $remove_session = implode(', ', array_map(function ($time) { return "'$time'"; }, $removed_session_times));
            $sql_session = "SELECT session_id FROM session WHERE is_active = 1 AND session_time IN (SELECT id FROM session_times WHERE study_id = $study_ID AND name IN ($remove_session) AND is_active = 1)";
            $result_session = $pdo->query($sql_session);
            $row_session = $result_session->fetch(PDO::FETCH_ASSOC);
            
            $warningMessage = "";
            $warningMessageCode = "";
            
            if ($row_ssq !== FALSE && $row_session !== FALSE){
                $warningMessage = "Deleting this SSQ time and Session name will result in loss of previously created SSQs and Sessions.";
            }
            else if ($row_ssq !== FALSE){
                $warningMessage = "Deleting this SSQ time will result in loss of previously created SSQs.";
            }
            else if ($row_session !== FALSE){
                $warningMessage = "Deleting this Session Name will result in loss of previously created Sessions.";
            }
            if (!empty($warningMessage)){
                $warningMessageCode = "if (!confirm(\"" . $warningMessage . "\")){
                                            return;
                                       }";
            }
            return "<script type='text/javascript'>
                    $(document).ready(() => {
                        console.log('This script never works.');
                        " . $warningMessageCode . "
                        $.ajax({
                            url: 'ssq_and_session_time_remove',
                            type: 'POST',
                            cache: false,
                            data:{
                                remove_ssq: \"$remove_ssq\",
                                remove_session: \"$remove_session\",
                            },
                        })
                        .done(function(data) {
                            $(document).ready(() => {
                                const div = document.createElement('div');
                                div.innerHTML = data;
                                document.querySelector('.container').insertBefore(div.firstChild, document.querySelector('.card'));
                                const divMsg = document.getElementById('flash-msg');
                                if (divMsg?.classList.contains('alert-success')){
                                    setTimeout(function(){
                                        location.href = 'study_details';
                                    }, 1000);
                                }
                            });
                        });
                });
                </script>";
        }
        
        
        if (count($removed_ssq_times) > 0) {
            // removed 1 or more time names
            
            $remove = implode(', ', array_map(function ($time) { return "'$time'"; }, $removed_ssq_times));
            
            
            $sql = "SELECT ssq.ssq_id 
                        FROM ssq JOIN session ON ssq.session_id = session.session_id 
                        WHERE ssq.is_active = 1 
                        AND session.is_active 
                        AND ssq.ssq_time IN (SELECT id 
                                             FROM ssq_times 
                                             WHERE study_id = $study_ID 
                                             AND name IN ($remove) 
                                             AND is_active = 1)";
            $result_ssq = $pdo->query($sql);
            
            if($result_ssq->fetch(PDO::FETCH_ASSOC)) {
                return "<script type='text/javascript'>
                    $(document).ready(() => {
                        if (confirm('Deleting this SSQ Time will result in loss of previously created SSQs')) {
                            $.ajax({
                                url: 'ssq_time_remove',
                                type: 'POST',
                                cache: false,
                                data:{
                                    remove: \"$remove\"
                                },
                            })
                            .done(function(data) {
                                const div = document.createElement('div');
                                div.innerHTML = data;
                                document.querySelector('.container').insertBefore(div.firstChild, document.querySelector('.card'));
                                const divMsg = document.getElementById('flash-msg');
                                if (divMsg?.classList.contains('alert-success')){
                                    setTimeout(function(){
                                        location.href = 'study_details';
                                    }, 1000);
                                }
                            });
                        }
                    });
                </script>";
            } 
            else {
                $remove_sql = "UPDATE ssq_times SET is_active = 0 WHERE study_id = $study_ID AND name IN ($remove)";
                $result = $pdo->query($remove_sql);
                    
                if (!$result) {
                    return Util::generateErrorMessage("Something went wrong. Try editing again!");
                }
            }
        }
        
        if (count($removed_session_times) > 0) {
            // removed 1 or more time names
            
            $remove = implode(', ', array_map(function ($time) { return "'$time'"; }, $removed_session_times));
            
            
            $sql = "SELECT session_id FROM session WHERE is_active = 1 AND session_time IN (SELECT id FROM session_times WHERE study_id = $study_ID AND name IN ($remove) AND is_active = 1)";
            $result_ssq = $pdo->query($sql);
            
            if($result_ssq->fetch(PDO::FETCH_ASSOC)) {
                return "<script type='text/javascript'>
                    $(document).ready(() => {
                        if (confirm('Deleting this Session Time will result in loss of previously created Sessions')) {
                            $.ajax({
                                url: 'session_time_remove',
                                type: 'POST',
                                cache: false,
                                data:{
                                    remove: \"$remove\"
                                },
                            })
                            .done(function(data) {
                                $(document).ready(() => {
                                    const div = document.createElement('div');
                                    div.innerHTML = data;
                                    document.querySelector('.container').insertBefore(div.firstChild, document.querySelector('.card'));
                                    const divMsg = document.getElementById('flash-msg');
                                    if (divMsg?.classList.contains('alert-success')){
                                        setTimeout(function(){
                                            location.href = 'study_details';
                                        }, 1000);
                                    }
                                });
                            });
                        }
                    });
                </script>";
            } else {
            
                $remove_sql = "UPDATE session_times SET is_active = 0 WHERE study_id = $study_ID AND name IN ($remove)";
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
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2) {
            return Util::generateErrorMessage("You do not have proper access in this study!");
        }
        
        $sql = "UPDATE study
                SET is_active = 1, last_edited_by = :last_edited_by, last_edited_at = :last_edited_at
                WHERE study_id = :study_ID;
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
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2) {
            return Util::generateErrorMessage("You do not have proper access in this study!");
        }
        $sql = "UPDATE study
                SET is_active = 0, last_edited_by = :last_edited_by, last_edited_at = :last_edited_at 
                WHERE study_id = :study_ID
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
        $pi_sql = "SELECT COUNT(study_role) AS Count FROM researchers WHERE study_id = " . Session::get("study_ID") . " AND study_role = 2 AND is_active = 1;";
        $pi_result = $this->db->pdo->query($pi_sql);
        $pi_count = $pi_result->fetch(PDO::FETCH_ASSOC);
        if ($pi_count['Count'] > 1 || Session::get("roleid") != 2){
            $sql = "UPDATE researchers
                    SET is_active = 0
                    WHERE researcher_id = :researcher_id
                    AND study_id = :study_id
                    AND is_active = 1
                    LIMIT 1;";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':researcher_id', Session::get('id'));
            $stmt->bindValue(':study_id', $study_ID);
            
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
    public function insertSession($session_data){
        array_walk($session_data, function (&$val) {         $val = trim($val);     });
        $created_by = Session::get('id');
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if (!$role) {
            return Util::generateErrorMessage("You do not have proper access in this study!");
        }
        
        if (empty($session_data["participant_ID"])){
            return Util::generateErrorMessage("Please select a participant!");
        }
        if (empty($session_data["session_time"])){
            return Util::generateErrorMessage("Please select a Session time!");
        }
        if (empty($session_data["comment"])){
            $session_data["comment"] = NULL;
        }
        
        $this->db->pdo->beginTransaction();
        try{
            $sql = "INSERT INTO session (study_id, participant_id, comment, created_by, last_edited_by, session_time)
                    VALUES (:study_ID, :participant_ID, :comment, :created_by, :last_edited_by, :session_time);";
            $stmt = $this->db->pdo->prepare($sql);
            
            $stmt->bindValue(':study_ID', Session::get('study_ID'));
            $stmt->bindValue(':participant_ID', $session_data["participant_ID"]);
            $stmt->bindValue(':comment', $session_data["comment"]);
            $stmt->bindValue(':created_by', $created_by);
            $stmt->bindValue(':last_edited_by', $created_by);
            $stmt->bindValue(':session_time', $session_data['session_time']);
            
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
    public function restartSession($session_ID) {
        $last_edited_by = Session::get('id');        
        
        $sql = "UPDATE session
                SET end_time = NULL, 
                    last_edited_by = :last_edited_by
                WHERE session_id = :session_ID
                LIMIT 1;";
        $creator_check_sql = "SELECT created_by FROM session WHERE session_id = " . Session::get('session_ID') . " AND is_active = 1";
        $creator = $this->db->pdo->query($creator_check_sql);
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if ($creator->fetch(PDO::FETCH_ASSOC)['created_by'] != Session::get('id') && (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2)) {
            return Util::generateErrorMessage("You do not have proper access in this session!");
        }
                
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
        $sql = "UPDATE session
                SET end_time = :end_time
                WHERE session_id = :session_ID
                LIMIT 1;";
        $creator_check_sql = "SELECT created_by FROM session WHERE session_id = " . Session::get('session_ID') . " AND is_active = 1";
        $creator = $this->db->pdo->query($creator_check_sql);
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if ($creator->fetch(PDO::FETCH_ASSOC)['created_by'] != Session::get('id') && (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2)) {
            return Util::generateErrorMessage("You do not have proper access in this session!");
        }
        
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
        $last_edited_by = Session::get('id');
        $sql = "UPDATE session
                SET is_active = 0, last_edited_by = :last_edited_by  
                WHERE session_id = :session_ID
                LIMIT 1;";
        $creator_check_sql = "SELECT created_by FROM session WHERE session_id = " . Session::get('session_ID') . " AND is_active = 1";
        $creator = $this->db->pdo->query($creator_check_sql);
        $role_check_sql = "SELECT study_role FROM researchers WHERE researcher_id = " . Session::get('id') . " AND study_id = " . Session::get('study_ID') . " AND is_active = 1";
        $role = $this->db->pdo->query($role_check_sql);
        if ($creator->fetch(PDO::FETCH_ASSOC)['created_by'] != Session::get('id') && (!$role || $role->fetch(PDO::FETCH_ASSOC)['study_role'] != 2)) {
            return Util::generateErrorMessage("You do not have proper access in this session!");
        }
        
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':session_ID', $session_ID);
        $stmt->bindValue(':last_edited_by', $last_edited_by);
        
        $result = $stmt->execute();
        if ($result){
            return Util::generateSuccessMessage("Session has been removed!");
        }
        else{
            return Util::generateSuccessMessage("Something went wrong. Try ending again!");
        }
    }   
}