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
    $additional_info = $data['additional_info'];
    $comments = $data['comments'];
    $affiliationid = Session::get('affiliationid');
    
    $checkEmail = Util::checkExistEmail($email, $this->db);

    if (empty($anonymous_name)){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error!</strong> Name of participant must not be empty!</div>';
        return $msg;
    }
    if (empty($dob)){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error!</strong> Date of birth field must not be empty!</div>';
        return $msg;
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error!</strong> Invalid email address !</div>';
        return $msg;
    }
    if ($checkEmail == TRUE) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Email already Exists, please try another Email... !</div>';
        return $msg;
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
        
        $last_id = "SELECT LAST_INSERT_ID();"; //help
        $last_id_statement = $this->db->pdo->prepare($last_id);
        $result_id = $last_id_statement->execute();
        if (!$result_id){
            throw new Exception($last_id_statement->error);
        }
        
        $result_id = $last_id_statement->fetch(PDO::FETCH_ASSOC);
        $result_id = intval($result_id['LAST_INSERT_ID()']);
        
        
        $sql2 = "INSERT INTO Participants (demographics_id, anonymous_name, dob, weight, occupation, phone_no, email, additional_info, comments, affiliation_id) 
        VALUES(:demographics_id, :anonymous_name, :dob, :weight, :occupation, :phone_no, :email, :additional_info, :comments, :affiliationid);";
        
        $stmt2 = $this->db->pdo->prepare($sql2);
        $stmt2->bindValue(':demographics_id', $result_id);        
        $stmt2->bindValue(':anonymous_name', $anonymous_name);
        $stmt2->bindValue(':dob', $dob);
        $stmt2->bindValue(':weight', $weight);
        $stmt2->bindValue(':occupation', $occupation);
        $stmt2->bindValue(':phone_no', $phone_no);
        $stmt2->bindValue(':email', $email);
        $stmt2->bindValue(':additional_info', $additional_info);
        $stmt2->bindValue(':comments', $comments);        
        $stmt2->bindValue(':affiliationid', $affiliationid);        
        
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
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id    ="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> You registered a participant!</div>';
        return $msg;
    } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Something went Wrong !</div>';
        return $msg;
    }
  }

 // Add researcher to study 
  public function addResearcher($data){
    if (empty($data['researcher_ID'])){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error! Please select a researcher!</strong> </div>';
        return $msg;
    }
    
    if (empty($data['study_role'])){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error! Please select a role!</strong> </div>';
        return $msg;
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
    
        if ($result) { 
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> You have added a researcher!</div>';
            return $msg; 
        } else {
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Error! Something went wrong, try registering again!</strong> </div>';
            return $msg;
         }    
      }
    
// Delete researcher to study 
  public function removeResearcher($data){
    if (empty($data['researcher_ID'])){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error!</strong> Please select a researcher to remove!</div>';
        return $msg;
    }
    if (empty($data['study_ID'])){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error!</strong> Please select a study!</div>';
        return $msg;
    }
    
    $researcher_ID = $data['researcher_ID'];          
    $study_ID = $data['study_ID'];     
      
    $sql = "DELETE FROM Researcher_Study WHERE researcher_ID = :researcher_ID AND study_ID = :study_ID";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':researcher_ID', $researcher_ID);
    $stmt->bindValue(':study_ID', $study_ID);
    $result = $stmt->execute(); 
    
    if ($result) { 
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> You have removed a researcher!</div>';
        return $msg;
    } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error! Something went wrong, try removing again!</strong> </div>';
        return $msg;
    }
  }
  
  // take SSQ quiz from Session
public function takeSSQ($data){
    if (!(isset($data['quiz_type']) && isset($data['ssq_time']))){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error! Please select a quiz type and quiz time!</strong> </div>';
        return $msg;
    }
    
    if (!(isset($data['session_ID']))){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error! User does not have a valid session ID!</strong> </div>';
        return $msg;
    }
    
    $quiz_type = $data['quiz_type'];
    $ssq_time = $data['ssq_time'];
    $session_ID = $data['session_ID'];
    
    $sql = "SELECT *
            FROM SSQ
            WHERE session_ID = :session_ID
            AND ssq_time = :ssq_time
            AND ssq_type = :quiz_type
            LIMIT 1;";
    
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(":session_ID", $session_ID);
    $stmt->bindValue(":ssq_time", $ssq_time);
    $stmt->bindValue(":quiz_type", $quiz_type);
    $result = $stmt->execute();
    
    if (!$result){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error! Something went wrong, try again!</strong> </div>';
        return $msg;
    }
    
    if ($stmt->rowCount() === 0){
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> You will take the quiz momentarily!</div>';
        return $msg;
    }
    else{
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error! You have already taken a quiz with the same type and time!</strong> </div>';
        return $msg;
    }
}
  
  // remove SSQ quiz from Session
  public function deleteQuiz($data){
    $ssq_ID = intval($_POST['ssq_ID']);          
      
    $sql = "DELETE FROM SSQ WHERE ssq_ID = :ssq_ID";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':ssq_ID', $ssq_ID);
    $result = $stmt->execute(); 
    
    if ($result) { 
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> You have deleted this quiz!</div>';
        return $msg;
    } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error! Something went wrong, try deleting again!</strong> </div>';
        return $msg;
    }
    
    echo $msg;
  }  // Get Study Information By Study Id
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
  $created_by = Session::get('id');
  $last_edited_by = Session::get('id');    

   if ($full_name == "" || $short_name == "" || $IRB == "") {
    $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Study registration fields must not be empty!</div>'; 
      return $msg; // if any field is empty
   } else {
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
   }
   
  if ($result) {
      echo '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Success!</strong> You have created a study!</div>';
      echo "<script>setTimeout(\"location.href = 'view_study';\",1500);</script>";
  } else {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error!</strong> Something went wrong, try again!</div>';
      return $msg;
  }
}

// Edit a user's study
public function updateStudy($data){
    $full_name = $data['full_name'];
    $short_name = $data['short_name'];
    $IRB = $data['IRB'];
    $ssq_times_str = $data['ssq_times'];
    $last_edited_by = Session::get('id');
    $study_ID = $data['study_ID'];
    $ssq_times = explode(',', $ssq_times_str);
    array_walk($ssq_times, function (&$time) {
        $time = ucwords(trim($time));
    });
    
    if ($full_name == "" || $short_name == ""|| $IRB == "") {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error!</strong> You cannot leave Study fields empty!</div>'; 
        return $msg; // if any field is empty
    } else if ($ssq_times !== array_unique($ssq_times)) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error!</strong>You cannot have multiple SSQ times of the same name.</div>'; 
        return $msg;
    }
    $pdo = $this->db->pdo;
    $sql = "UPDATE Study SET full_name = :full_name, short_name = :short_name, IRB = :IRB, last_edited_by = :last_edited_by WHERE study_ID = $study_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':full_name', $full_name);
    $stmt->bindValue(':short_name', $short_name);  
    $stmt->bindValue(':IRB', $IRB);
    $stmt->bindValue('last_edited_by', $last_edited_by);
    $result = $stmt->execute();
    
    if (!$result) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Error!</strong> Something went wrong, try editing again!</div>';
        return $msg;
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
                $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error!</strong> Something went wrong, try editing again!</div>';
                return $msg;
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
                $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error!</strong> Something went wrong, try editing again!</div>';
                return $msg;
            }
        }
    }
    
    if (count($removed_ssq_times) > 0) {
        // removed 1 or more time names
        $remove = implode(', ', array_map(function ($time) { return "'$time'"; }, $removed_ssq_times));
        $remove_sql = "UPDATE SSQ_times SET is_active = 0 WHERE study_id = $study_ID AND name IN ($remove)";
        $result = $pdo->query($remove_sql);
        
        if (!$result) {
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error!</strong> Something went wrong, try editing again!</div>';
            return $msg;
        }
    }
    
    $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> You have edited this study!</div>';
    return $msg;
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
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You activated this study!</div>';
        }
        else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error!</strong> Something went wrong, try activating again!</div>';
        }
        
        return $msg;
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
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You deactivated this study!</div>';
        }
        else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error!</strong> Something went wrong, try deactivating again!</div>';
        }
        
        return $msg;
    }
    
    // leaves the current study
    public function leaveStudy($study_ID){
        $sql = "DELETE FROM Researcher_Study
                WHERE researcher_ID = :researcher_ID
                AND study_ID = :study_ID
                LIMIT 1;";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':researcher_ID', Session::get('id'));
        $stmt->bindValue(':study_ID', $study_ID);
        
        $result = $stmt->execute();
        if ($result){
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You left this study!</div>';
        }
        else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error!</strong> Something went wrong, try leaving again!</div>';
        }
        return $msg;
    }
    
    // inserts a session of a  study into DB
    public function insert_session($data){
        $created_by = Session::get('id');
        $last_edited_by = Session::get('id');    
        
        if (empty($data["participant_ID"])){
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error !</strong> Please select a participant!</div>';
            return $msg;
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
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> You created a new session! You will now be redirected to the Session Details page for this study.</div>';
        }
        catch (PDOException $excptn){
            $this->db->pdo->rollBack();
            
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error !</strong> Something went wrong, try creating a session again!</div>';
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
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success !</strong> Session restarted!</div>';
            return $msg;
        }
        else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error !</strong> Something went wrong, try restarting again!</div>';
            return $msg;
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
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success !</strong> Session ended!</div>';
            return $msg;
        }
        else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error !</strong> Something went wrong, try ending again!</div>';
            return $msg;
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
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success !</strong> Session ended!</div>';
            return $msg;
        }
        else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error !</strong> Something went wrong, try ending again!</div>';
            return $msg;
        }
    }    
}