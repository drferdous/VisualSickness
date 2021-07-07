<?php

include 'lib/Database.php';
include_once 'lib/Session.php';  

class Users{

  // Db Property
  private $db;

  // Db __construct Method
  public function __construct(){
    $this->db = new Database();
  }

  // Date formate Method
   public function formatDate($date){
     // date_default_timezone_set('Asia/Dhaka');
      $strtime = strtotime($date);
    return date('Y-m-d H:i:s', $strtime);
   }

  // Check Exist Email Address Method
  public function checkExistEmail($email){
    $sql = "SELECT email from  tbl_users WHERE email = :email";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
     $stmt->execute();
    if ($stmt->rowCount()> 0) {
      return true;
    }else{
      return false;
    }
  }

  // User Registration Method
  public function userRegistration($data){
    $name = $data['name'];
    $username = $data['username'];
    $email = $data['email'];
    $mobile = $data['mobile'];
    $password = $data['password'];
    $confirm_password = $data['confirm_password'];
    $roleid = $data['roleid'];

    $checkEmail = $this->checkExistEmail($email);

    if ($name == "" || $username == "" || $email == "" || $password == "" || $roleid == "") {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> User registration fields must not be empty!</div>'; 
        return $msg; // if any field is empty
    } elseif (strlen($username) < 3) {  
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Your username is too short, make it at least 3 characters!</div>';
        return $msg; // if username too short
    } elseif (filter_var($mobile,FILTER_SANITIZE_NUMBER_INT) == FALSE) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Enter only number characters for the phone number field!</div>';
        return $msg; // if phone number contains chars         
    } elseif(strlen($password) < 5) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Password must be at least 6 characters!</div>';
        return $msg; // if password too short
    } elseif ($password != $confirm_password) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Your passwords must match!</div>'; 
      return $msg; // if password doesn't verify
    } elseif(!preg_match("#[0-9]+#",$password)) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Your password must contain at least one number!</div>';
        return $msg; // if no number in password
    } elseif(!preg_match("#[a-z]+#",$password)) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Your password must contain at least one number!</div>';
        return $msg;  // if no number in password
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Your email address must be valid!</div>';
        return $msg; // if email address invalid
    } elseif ($checkEmail == TRUE) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Email already exists, please try another email!</div>';
        return $msg; // if email already used
    } else {
      // if everything is sucessful, insert into DB
      $sql = "INSERT INTO tbl_users(name, username, email, mobile, password, roleid) VALUES(:name, :username, :email, :mobile, :password, :roleid)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':mobile', $mobile);
      $stmt->bindValue(':password', SHA1($password)); //encrypt password
      $stmt->bindValue(':roleid', $roleid);
      $result = $stmt->execute();
      if ($result) {
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success!</strong> You have registered successfully!</div>';
          return $msg;
      }else{
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error!</strong> Something went wrong, try registering again!</div>';
          return $msg;
      }
    }
  }

 // Insert user's study in Study table
 public function insert_study($data) {
    $full_name = $data['full_name'];
    $short_name = $data['short_name'];
    $IRB = $data['IRB'];
    $created_by = Session::get('id');
    $last_edited_by = Session::get('id');    

     if ($full_name == "" || $short_name == "" || $IRB == "") {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<strong>Error!</strong> Study registration fields must not be empty!</div>'; 
        return $msg; // if any field is empty
     } else {
        $sql = "INSERT INTO Study (full_name, short_name, IRB, created_by, last_edited_by)
              VALUES ('$full_name', '$short_name', '$IRB', '$created_by', '$last_edited_by')";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue('full_name', $full_name);
        $stmt->bindValue('short_name', $short_name);
        $stmt->bindValue('IRB', $IRB);
        $stmt->bindValue('created_by', $created_by);
        $stmt->bindValue('last_edited_by', $created_by);        
        $result = $stmt->execute();        
     }
     
    if ($result) {
        /* $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Success!</strong> You have created a study!</div>';
        return $msg; */
        echo '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Success!</strong> You have created a study!</div>';
        echo "<script>setTimeout(\"location.href = 'view_study.php';\",1500);</script>";
    } else {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Error!</strong> Something went wrong, try again!</div>';
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
    
    echo $msg;
  }

  // Add participant to study
  public function addNewParticipant($data){
    $anonymous_name = $data['anonymous_name'];
    $dob = $data['dob'];
    $weight = $data['weight'];
    $gender = $data['gender'];
    $ethnicity = $data['ethnicity'];
    $occupation = $data['occupation'];
    $education = $data['education'];
    $phone_no = $data['phone_no'];
    $email = $data['email'];
    $additional_info = $data['additional_info'];
    $comments = $data['comments'];

    $checkEmail = $this->checkExistEmail($email);
    
    if (empty($anonymous_name)){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Name of participant must not be empty!</div>';
        return $msg;
    }
    if (empty($dob)){
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Date of birth field must not be empty!</div>';
        return $msg;
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Invalid email address !</div>';
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
    
    $sql = "INSERT INTO Participant (anonymous_name, dob, weight, gender, ethnicity, occupation, education, phone_no, email, additional_info, comments) VALUES(:anonymous_name, :dob, :weight, :gender, :ethnicity, :occupation, :education, :phone_no, :email, :additional_info, :comments)";
    $stmt = $this->db->pdo->prepare($sql);
    
    $stmt->bindValue(':anonymous_name', $anonymous_name);
    $stmt->bindValue(':dob', $dob);
    $stmt->bindValue(':weight', $weight);
    $stmt->bindValue(':gender', $gender);
    $stmt->bindValue(':ethnicity', $ethnicity);
    $stmt->bindValue(':occupation', $occupation);
    $stmt->bindValue(':education', $education);
    $stmt->bindValue(':phone_no', $phone_no);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':additional_info', $additional_info);
    $stmt->bindValue(':comments', $comments);
        
    $result = $stmt->execute();
    if ($result) {
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id    ="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> You registered a participant!</div>';
        return $msg;
    } 
    else{
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Something went Wrong !</div>';
        return $msg;
    }
  }  

  // Select All User Method
  public function selectAllUserData(){
    $sql = "SELECT * FROM tbl_users ORDER BY id DESC";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }


  // User login Autho Method
  public function userLoginAutho($email, $password){
    $password = SHA1($password);
    $sql = "SELECT * FROM tbl_users WHERE email = :email and password = :password LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $password);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
  
  // Check User Account Status
  public function CheckActiveUser($email){
    $sql = "SELECT * FROM tbl_users WHERE email = :email and isActive = :isActive LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':isActive', 1);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

    // User Login Authentication Method
    public function userLoginAuthentication($data){
      $email = $data['email'];
      $password = $data['password'];


      $checkEmail = $this->checkExistEmail($email);

      if ($email == "" || $password == "" ) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Email or Password not be Empty !</div>';
          return $msg;

      }elseif (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Invalid email address !</div>';
          return $msg;
      }elseif ($checkEmail == FALSE) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Email did not Found, use Register email or password please !</div>';
          return $msg;
      }else{


        $logResult = $this->userLoginAutho($email, $password);
        $chkActive = $this->CheckActiveUser($email);

        if ($chkActive == TRUE) {
          $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Sorry, Your account is Deactivated, Contact with Admin !</div>';
            return $msg;
        }elseif ($logResult) {

          Session::init();
          Session::set('login', TRUE);
          Session::set('id', $logResult->id);
          Session::set('roleid', $logResult->roleid);
          Session::set('name', $logResult->name);
          Session::set('email', $logResult->email);
          Session::set('username', $logResult->username);
          Session::set('logMsg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Success !</strong> You are Logged In Successfully !</div>');
          echo "<script>location.href='index.php';</script>";

        }else{
          $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Email or Password did not Matched !</div>';
            return $msg;
        }

      }


    }



    // Get Single User Information By Id Method
    public function getUserInfoById($userid){
      $sql = "SELECT * FROM tbl_users WHERE id = :id LIMIT 1";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $userid);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      }else{
        return false;
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
    }
    

  //   Update user profile info
    public function updateUserByIdInfo($userid, $data){
      $name = $data['name'];
      $username = $data['username'];
      $email = $data['email'];
      $mobile = $data['mobile'];
      $roleid = $data['roleid'];

      if ($name == "" || $username == ""|| $email == "" || $mobile == ""  ) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Input Fields must not be Empty !</div>';
          return $msg;
        }elseif (strlen($username) < 3) {
          $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Username is too short, at least 3 Characters !</div>';
            return $msg;
        }elseif (filter_var($mobile,FILTER_SANITIZE_NUMBER_INT) == FALSE) {
          $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Enter only Number Characters for Mobile number field !</div>';
            return $msg;
      }elseif (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Invalid email address !</div>';
          return $msg;
      }else{

        $sql = "UPDATE tbl_users SET
          name = :name,
          username = :username,
          email = :email,
          mobile = :mobile,
          roleid = :roleid
          WHERE id = :id";
          $stmt= $this->db->pdo->prepare($sql);
          $stmt->bindValue(':name', $name);
          $stmt->bindValue(':username', $username);
          $stmt->bindValue(':email', $email);
          $stmt->bindValue(':mobile', $mobile);
          $stmt->bindValue(':roleid', $roleid);
          $stmt->bindValue(':id', $userid);
        $result =   $stmt->execute();

        if ($result) { $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success!</strong> You have updated your information!</div>';
           return $msg;
        } else {
         $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error!</strong> Your profile could not be updated!</div>';
           return $msg;
        }
      }
    }
    
    // Edit a user's study
    public function updateStudy($study_ID, $data){
        $full_name = $data['full_name'];
        $short_name = $data['short_name'];
        $IRB = $data['IRB'];
        $last_edited_by = Session::get('id');
        
          if ($full_name == "" || $short_name == ""|| $IRB == "") {
          $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error!</strong> You cannot leave Study fields empty!</div>'; 
            return $msg; // if any field is empty
          } else {
            $sql = "UPDATE Study SET full_name = :full_name, short_name = :short_name, IRB = :IRB, last_edited_by = :last_edited_by WHERE study_ID = $study_ID";
                $stmt = $this->db->pdo->prepare($sql);
                $stmt->bindValue(':full_name', $full_name);
                $stmt->bindValue(':short_name', $short_name);
                $stmt->bindValue(':IRB', $IRB);
                $stmt->bindValue('last_edited_by', $last_edited_by);
                $result = $stmt->execute();     
                
            if ($result) {
                $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Success!</strong> You have edited this study!</div>';
                return $msg;
            } else {
                $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error!</strong> Something went wrong, try editing again!</div>';
                return $msg;
            }
          }
    }
    
    // Deactivates study based on the given study_ID.
    public function deactivateStudy($study_ID){
        $sql = "UPDATE Study
                SET is_active = 1
                WHERE study_ID = :study_ID;";
        
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
    
    public function insert_session($study_ID, $data){
        if (empty($data["participant_ID"])){
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error !</strong> Please select a participant!</div>';
            return $msg;
        }
        
        if (empty($data["comment"])){
            $data["comment"] = NULL;
        }
        
        $sql = "INSERT INTO Session (study_ID, participant_ID, comment)
                VALUES (:study_ID, :participant_ID, :comment);";
        $stmt = $this->db->pdo->prepare($sql);
        
        $stmt->bindValue(':study_ID', $study_ID);
        $stmt->bindValue(':participant_ID', $data["participant_ID"]);
        $stmt->bindValue(':comment', $data["comment"]);
        
        $result = $stmt->execute();
        if ($result){
            $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success !</strong> You created a session!</div>';
            return $msg;
        }
        else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error !</strong> Something went wrong, try again!</div>';
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

    // Delete User by Id Method
    public function deleteUserById($remove){
      $sql = "DELETE FROM tbl_users WHERE id = :id ";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $remove);
        $result =$stmt->execute();
        if ($result) {
          $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Success !</strong> User account Deleted Successfully !</div>';
            return $msg;
        }else{
          $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Data not Deleted !</div>';
            return $msg;
        }
    }

    // User Deactivated By Admin
    public function userDeactiveByAdmin($deactive){
      $sql = "UPDATE tbl_users SET

       isActive=:isActive
       WHERE id = :id";

       $stmt = $this->db->pdo->prepare($sql);
       $stmt->bindValue(':isActive', 1);
       $stmt->bindValue(':id', $deactive);
       $result =   $stmt->execute();
        if ($result) {
          echo "<script>location.href='index.php';</script>";
          Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success !</strong> User account Diactivated Successfully !</div>');

        }else{
          echo "<script>location.href='index.php';</script>";
          Session::set('msg', '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Data not Diactivated !</div>');

            return $msg;
        }
    }


    // User Deactivated By Admin
    public function userActiveByAdmin($active){
      $sql = "UPDATE tbl_users SET
       isActive=:isActive
       WHERE id = :id";

       $stmt = $this->db->pdo->prepare($sql);
       $stmt->bindValue(':isActive', 0);
       $stmt->bindValue(':id', $active);
       $result =   $stmt->execute();
        if ($result) {
          echo "<script>location.href='index.php';</script>";
          Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success !</strong> User account activated Successfully !</div>');
        }else{
          echo "<script>location.href='index.php';</script>";
          Session::set('msg', '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error !</strong> Data not activated !</div>');
        }
    }

    // Check Old password method
    public function CheckOldPassword($userid, $old_pass){
      $old_pass = SHA1($old_pass);
      $sql = "SELECT password FROM tbl_users WHERE password = :password AND id =:id";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':password', $old_pass);
      $stmt->bindValue(':id', $userid);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        return true;
      }else{
        return false;
      }
    }



    // Change User pass By Id
    public  function changePasswordBysingelUserId($userid, $data){

      $old_pass = $data['old_password'];
      $new_pass = $data['new_password'];


      if ($old_pass == "" || $new_pass == "" ) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> Password field must not be Empty !</div>';
          return $msg;
      }elseif (strlen($new_pass) < 6) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error !</strong> New password must be at least 6 character !</div>';
          return $msg;
       }

         $oldPass = $this->CheckOldPassword($userid, $old_pass);
         if ($oldPass == FALSE) {
           $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
     <strong>Error !</strong> Old password did not Matched !</div>';
             return $msg;
         }else{
           $new_pass = SHA1($new_pass);
           $sql = "UPDATE tbl_users SET

            password=:password
            WHERE id = :id";

            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':password', $new_pass);
            $stmt->bindValue(':id', $userid);
            $result =   $stmt->execute();

          if ($result) {
            echo "<script>location.href='index.php';</script>";
            Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success !</strong> Great news, Password Changed successfully !</div>');

          }else{
            $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Error !</strong> Password did not changed !</div>';
              return $msg;
          }

         }
    }
}