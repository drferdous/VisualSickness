<?php

include_once 'lib/Database.php';
include_once 'lib/Session.php';
include_once 'classes/Util.php';

class Studies {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }

  // Add participant to Session table and Demographics table
  public function addNewParticipant($data){
  // Note: this function does not work because the function does not take into account the study_ID yet.
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
    $study_ID = $data['study_ID'];
    
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
        
        
        $sql2 = "INSERT INTO Participants (demographics_id, anonymous_name, dob, weight, occupation, phone_no, email, comments, study_id) 
        VALUES(:demographics_id, :anonymous_name, :dob, :weight, :occupation, :phone_no, :email, :comments, :study_ID);";
        
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
        
        $result2 = $stmt2->execute();   
        
        if (!$result2){
            throw new Exception($stmt->error);
        }
        $this->db->pdo->commit();
    }
    catch (PDOException $excptn){
        $this->db->pdo->rollBack();
    }
    if ($result2) {
        return Util::generateSuccessMessage("You registered a participant!");
    }
    else {
        return Util::generateErrorMessage("Something went wrong.");
    }
  }

 // Add researcher to study 
  public function addResearcher($data){
    if (empty($data['researcher_ID'])){
        return Util::generateErrorMessage("Please select a researcher!");
    }
    if (empty($data['study_role'])){
        return Util::generateErrorMessage("Please select a role!");
    }
    
    $researcher_ID = $data['researcher_ID'];          
    $study_ID = $data['study_ID']; 
    $study_role = $data['study_role'];    
      
    $sql = "INSERT INTO Researcher_Study (researcher_ID, study_ID, study_role) VALUES (:researcher_ID, :study_ID, :study_role)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':researcher_ID', $researcher_ID);
        $stmt->bindValue(':study_ID', $study_ID);
        $stmt->bindValue(':study_role', $study_role);     
        $result = $stmt->execute(); 
    
        if ($result){ 
            return Util::generateSuccessMessage("You have added a researcher!");
        } 
        else{
            return Util::generateErrorMessage("Something went wrong. Try registering again!");
        }    
    }
    
// Delete researcher to study 
  public function removeResearcher($data){
    if (empty($data['researcher_ID'])){
        return Util::generateErrorMessage("Please select a researcher to remove!");
    }
    if (empty($data['study_ID'])){
        return Util::generateErrorMessage("Please select a study!");
    }
    
    $researcher_ID = $data['researcher_ID'];          
    $study_ID = $data['study_ID'];     
      
    $sql = "UPDATE Researcher_Study SET is_active = 0 WHERE researcher_ID = :researcher_ID AND study_ID = :study_ID";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':researcher_ID', $researcher_ID);
    $stmt->bindValue(':study_ID', $study_ID);
    $result = $stmt->execute(); 
    
    if ($result) {
        return Util::generateSuccessMessage("You have removed a researcher!");
    }
    else{
        return Util::generateErrorMessage("Something went wrong. Try removing again!");
    }
  }
  
// Delete participant to study 
  public function removeParticipant($data){
    if (empty($data['participant_ID'])){
        return Util::generateErrorMessage("Please select a participant to remove!");
    }
    if (empty($data['study_ID'])){
        return Util::generateErrorMessage("Please select a study!");
    }
    
    $participant_ID = $data['participant_ID'];          
    $study_ID = $data['study_ID'];     
      
    $sql = "UPDATE Participants SET is_active = 0 WHERE participant_ID = :participant_ID AND study_ID = :study_ID";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':participant_ID', $participant_ID);
    $stmt->bindValue(':study_ID', $study_ID);
    $result = $stmt->execute(); 
    
    if ($result) {
        return Util::generateSuccessMessage("You have removed a participant!");
    }
    else{
        return Util::generateErrorMessage("Something went wrong. Try removing again!");
    }
  }
  
  // take SSQ quiz from Session
public function takeSSQ($data){
    if (!(isset($data['quiz_type']) && isset($data['ssq_time']))){
        return Util::generateErrorMessage("Please select a quiz type and a quiz time!");
    }
    if (!(isset($data['session_ID']))){
        return Util::generateErrorMessage("User does not have a valid session ID!");
    }
    
    $quiz_type = $data['quiz_type'];
    $ssq_time = $data['ssq_time'];
    $session_ID = $data['session_ID'];
    
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
    $ssq_ID = $data["ssq_ID"];          
      
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
    $full_name = $data['full_name'];
    $short_name = $data['short_name'];
    $IRB = $data['IRB'];
    $description = $data['description']; 
    $ssq_times = explode(",", $data["ssq_times"]);
    array_walk($ssq_times, create_function('&$val', '$val = trim($val);'));
    $ssq_times = array_filter($ssq_times, function ($time) { return $time != ''; });
    $created_by = Session::get('id');
    $last_edited_by = Session::get('id');    

    if ($full_name == "" || $short_name == "" || $IRB == "") {
        return Util::generateErrorMessage("Study registration fields must not be empty!");
    } 
    if (count($ssq_times) !== count(array_unique($ssq_times))){
        return Util::generateErrorMessage("There should be no duplicate SSQ times!");
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
        return Util::generateSuccessMessage("You have created a study!");
    } 
    else {
        return Util::generateErrorMessage("Something went wrong. Try again!");
    }
} 

    // Edit a user's study
    public function updateStudy($data){
        $full_name = $data['full_name'];
        $short_name = $data['short_name'];
        $IRB = $data['IRB'];
        $description = $data['description'];
        $ssq_times_str = $data['ssq_times'];
        $last_edited_by = Session::get('id');
        $study_ID = $data['study_ID'];
        $ssq_times = explode(',', $ssq_times_str);
        array_walk($ssq_times, function (&$time) {
            $time = ucwords(trim($time));
        });
        $ssq_times = array_filter($ssq_times, function ($time) { return $time !== ''; });
        
        if ($full_name == "" || $short_name == ""|| $IRB == "") {
            return Util::generateErrorMessage("You cannot leave required field empty!");
        }
        if ($ssq_times !== array_unique($ssq_times)) {
            return Util::generateErrorMessage("You cannot have multiple SSQ times of the same name!");
        }
        $pdo = $this->db->pdo;
        $sql = "UPDATE Study SET full_name = :full_name, short_name = :short_name, IRB = :IRB, description = :description, last_edited_by = :last_edited_by WHERE study_ID = $study_ID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':full_name', $full_name);
        $stmt->bindValue(':short_name', $short_name);  
        $stmt->bindValue(':IRB', $IRB);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue('last_edited_by', $last_edited_by);
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
        $sql = "UPDATE Study
                SET is_active = 1
                WHERE study_ID = :study_ID;
                LIMIT 1;";
                
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(":study_ID", $study_ID);
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
        $sql = "UPDATE Study
                SET is_active = 0
                WHERE study_ID = :study_ID
                LIMIT 1;";
        
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':study_ID', $study_ID);
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
        $sql = "UPDATE Researcher_Study
                SET is_active = 0
                WHERE researcher_ID = :researcher_ID
                AND study_ID = :study_ID
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
    
    // inserts a session of a  study into DB
    public function insert_session($data){
        $created_by = Session::get('id');
        $last_edited_by = Session::get('id');    
        
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
            
            $stmt->bindValue(':study_ID', $data["study_ID"]);
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