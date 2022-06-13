<?php

include_once 'lib/Database.php';
include_once 'lib/Session.php';  
include 'mailer.php';
include_once 'classes/Util.php';

class Users{

  // Db Property
  private $db;

  // Db __construct Method
  public function __construct(){
    $this->db = Database::getInstance();
  }

  // User Registration Method
  public function userRegistration($data){
    $name = $data['name'];
    $password = Util::generateRandomPassword();
    $email = $data['email'];
    $affiliationid = $data['affiliationid'];    
    $mobile = $data['mobile'];
    $roleid = $data['roleid'];
      
    if (empty($name) || empty($email) || empty($roleid) || empty($affiliationid)){
        return Util::generateErrorMessage("User registration fields must not be empty!");
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
        return Util::generateErrorMessage("Your email address must be valid!");
    }
    if (Util::checkExistEmail($email, $this->db) !== FALSE) {
        return Util::generateErrorMessage("This email already exists. Try another email!");
    }
    else{
        // if everything is sucessful, insert into DB
        $sql = "INSERT INTO tbl_users(name, email, affiliationid, password, mobile, roleid) 
                VALUES(:name, :email, :affiliationid, :password, :mobile, :roleid)";

        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':affiliationid', $affiliationid);
        $stmt->bindValue(':password', SHA1($password));
        $stmt->bindValue(':mobile', $mobile);
        $stmt->bindValue(':roleid', $roleid);
      
        $result = $stmt->execute();
        if ($result){
            $pdo = $this->db->pdo;
            $id = $pdo->lastInsertId();
            $update_sql = "UPDATE tbl_users SET updated_by=$id WHERE id=$id;";
            $pdo->query($update_sql);
            $body = "<p>This email was recently used to sign up with the account $name. Below is a temporary password to use for your first login. If this is not your account, please ignore this email.<br><br>Temporary password: $password</p>";
            sendEmail($email, "Temporary Password | Visual Sickness", $body);
            
            return Util::generateSuccessMessage("You have registered successfully! Please check your email for a temporary password to login with.");
        } 
        else{
            return Util::generateErrorMessage("Something went wrong! Try registering again!");
        }
    }
  } 
  
  // Select All User Method
  public function selectAllUserData($showPendingUserFlag, $showDeactivatedUserFlag, $affiliationid){
    $args = array();
    if ($showPendingUserFlag) {
        $args['reg_stat'] = 1;
    }
    if ($showDeactivatedUserFlag) {
        $args['isActive'] = 0;
    }
    $argsString = "";
    foreach ($args as $key => $value) {
        $argsString .= "AND $key = $value ";
    }
    $sql = "SELECT * FROM tbl_users WHERE affiliationid = $affiliationid AND isActive != 2 $argsString
            ORDER BY id ASC;";
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


      $checkEmail = Util::checkExistEmail($email, $this->db);

      if ($email == "" || $password == "" ) {
          return Util::generateErrorMessage("Email or password must not be empty!");
      }
      if (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
          return Util::generateErrorMessage("Email address is invalid!");
      }
      if ($checkEmail == FALSE) {
          return Util::generateErrorMessage("Email address is not found. Please register for an account!");
      }else{

        $logResult = $this->userLoginAutho($email, $password);
        $isUserActive = $this->CheckActiveUser($email);

        if (! ($isUserActive == TRUE)) {
            $affiliationid_sql = "SELECT affiliationid FROM tbl_users WHERE email = :email";
            $stmt = $this->db->pdo->prepare($affiliationid_sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $affiliationid = $stmt->fetch(PDO::FETCH_ASSOC)['affiliationid'];
            
            $errorMessage = "Sorry, your account is deactivated. Please contact an admin. (" . Util::getAdminsFromAffiliation($this->db->pdo, $affiliationid) . ")";
            return Util::generateErrorMessage($errorMessage);
        }elseif ($logResult) {
          Session::init();
          Session::set('login', TRUE);
          Session::set('id', $logResult->id);
          Session::set('roleid', $logResult->roleid);
          Session::set('name', $logResult->name);
          Session::set('email', $logResult->email);
          Session::set('affiliationid', $logResult->affiliationid);
          Session::set('reg_stat', $logResult->reg_stat);
          Session::set('session_ID', -1);
          Session::set('logMsg', Util::generateSuccessMessage("You logged in!"));
        //   echo "<script>location.href='index';</script>";

        }
        else{
            return Util::generateErrorMessage("Email or password did not match!");
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
      $mobile = $data['mobile'];
      $roleid = $data['roleid'];

    if ($name == "" || $mobile == ""){
        return Util::generateErrorMessage("Input fields must not be empty!");
    }
    if (filter_var($mobile,FILTER_SANITIZE_NUMBER_INT) == FALSE) {
        return Util::generateErrorMessage("Please enter only numeric characters for phone number!");
    }
    else{
        $sql = "UPDATE tbl_users SET
          name = :name,
          mobile = :mobile,
          roleid = :roleid,
          updated_by = :updated_by,
          updated_at = CURRENT_TIMESTAMP
          WHERE id = :id";
          $stmt= $this->db->pdo->prepare($sql);
          $stmt->bindValue(':name', $name);
          $stmt->bindValue(':mobile', $mobile);
          $stmt->bindValue(':roleid', $roleid);
          $stmt->bindValue(':id', $userid);
          $stmt->bindValue(':updated_by', Session::get('id'));
        $result =   $stmt->execute();

        if ($result){ 
            return Util::generateSuccessMessage("you have updated your information!");
        } 
        else {
            return Util::generateErrorMessage("Your profile could not be updated!");
        }
      }
    }
    
 // Insert user's study in Study table
 public function insert_study($data) {
  $full_name = $data['full_name'];
  $short_name = $data['short_name'];
  $IRB = $data['IRB'];
  $description = $data['description'];  
  $created_by = Session::get('id');
  $last_edited_by = Session::get('id');    

   if ($full_name == "" || $short_name == "" || $IRB == "") {
       return Util::generateErrorMessage("Study registration fields must not be empty!");
   } 
   else {
      $sql = "INSERT INTO Study (full_name, short_name, IRB, description, created_by, last_edited_by)
            VALUES ('$full_name', '$short_name', '$IRB', '$description', '$created_by', '$last_edited_by')";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue('full_name', $full_name);
      $stmt->bindValue('short_name', $short_name);
      $stmt->bindValue('IRB', $IRB);
      $stmt->bindValue('description', $description);      
      $stmt->bindValue('created_by', $created_by);
      $stmt->bindValue('last_edited_by', $created_by);        
      $result = $stmt->execute();        
   }
   
  if ($result) {
      return Util::generateSuccessMessage("You have created a study!");
  } 
  else {
      return Util::genereateErrorMessage("Something went wrong. Try creating a study again!");
  }
} 

    // Edit a user's study
    public function updateStudy($data){
        $full_name = $data['full_name'];
        $short_name = $data['short_name'];
        $IRB = $data['IRB'];
        $description = $data['$description'];  
        $last_edited_by = Session::get('id');
        $study_ID = $data['study_ID'];
        
          if ($full_name == "" || $short_name == ""|| $IRB == "") {
              return Util::generateErrorMessage("You cannot leave study fields empty!");
          } 
          else {
            $sql = "UPDATE Study SET full_name = :full_name, short_name = :short_name, IRB = :IRB, last_edited_by = :last_edited_by WHERE study_ID = $study_ID";
                $stmt = $this->db->pdo->prepare($sql);
                $stmt->bindValue(':full_name', $full_name);
                $stmt->bindValue(':short_name', $short_name);
                $stmt->bindValue(':description', $description);      
                $stmt->bindValue(':IRB', $IRB);
                $stmt->bindValue('last_edited_by', $last_edited_by);
                $result = $stmt->execute();     
                
            if ($result) {
                return Util::generateSuccessMessage("You have edited this study!");
            } 
            else {
                return Util::generateErrorMessage("Something went wrong. Try editing again!");
            }
          }
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
            return Util::generateErroMessage("SOmething went wrong. Try activating again!");
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
        $sql = "DELETE FROM Researcher_Study
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
            $msg = Util::generateSuccessMessage("You created a new session! You will not be redirected to the Session Details page for this study.");
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
            Session::set('session_ID', -1);
            return Util::generateSuccessMessage("Session ended!");
        }
        else{
            return Util::generateErrorMessage("Something went wrong. Try ending again!");
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
            return Util::generateSuccessMessage('Session ended!');
        }
        else{
            return Util::generateErrorMessage("Something went wrong. Try ending again!");
        }
    }    

    // Delete User by Id Method
    public function deleteUserById($remove){
        $localId = Session::get('id');
        $sql = "UPDATE tbl_users SET isActive = 2, updated_by = :localId, updated_at = CURRENT_TIMESTAMP WHERE id = :id ";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':localId', $localId);
        $stmt->bindValue(':id', $remove);
        $result =$stmt->execute();
        if ($result) {
            return Util::generateSuccessMessage("User account deleted!");
        }
        else{
            return Util::generateErrorMessage("Data was not deleted for some reason.");
        }
    }

    // User Deactivated By Admin
    public function userDeactiveByAdmin($deactive){
        $localId = Session::get('id');
        $sql = "UPDATE tbl_users SET
            isActive=:isActive,
            updated_by = :localId,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':isActive', 1);
        $stmt->bindValue(':id', $deactive);
        $result =   $stmt->execute();
        if ($result) {
            echo "<script>location.href='index';</script>";
            Session::set('msg', Util::generateSuccessMessage("User account is deactivated"));
        } 
        else {
            echo "<script>location.href='index';</script>";
            Session::set('msg', Util::generateErrorMessage("Data was not deactivated!"));
        }
    }


    // User Deactivated By Admin
    public function userActiveByAdmin($active){
        $localId = Session::get('id');
        $sql = "UPDATE tbl_users SET
            isActive=:isActive,
            updated_by = :localId,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':isActive', 0);
        $stmt->bindValue(':localId', $localId);
        $stmt->bindValue(':id', $active);
        $result =   $stmt->execute();
        if ($result) {
            echo "<script>location.href='index';</script>";
            Session::set('msg', Util::generateSuccessMessage("User account activated!"));
        }
        else{
            echo "<script>location.href='index';</script>";
            Session::set('msg', Util::generateErrorMessage("User account was not activated!"));
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
    
    // update user password without old password
    public function resetPass($email, $new_pass) {
        if ($new_pass == "") {
            return Util::generateErrorMessage("Password field must not be empty!");
        }
        if (strlen($new_pass) < 6) {
            return Util::generateErrorMessage("New password must be at least 6 characters!");
        }
        $new_pass = SHA1($new_pass);
        $id_sql = "SELECT id FROM tbl_users WHERE email = :email LIMIT 1";
        $stmt = $this->db->pdo->prepare($id_sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        $sql = "UPDATE tbl_users SET
            password=:password,
            updated_by = $id,
            updated_at = CURRENT_TIMESTAMP
            WHERE email = :email";
        
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':password', $new_pass);
        $stmt->bindValue(':email', $email);
        $result = $stmt->execute();
        
        if (!$result) {
            return Util::generateErrorMessage("Password did not change!");
        }
        
    }

    // Change User pass By Id
    public  function changePasswordBysingelUserId($userid, $data){

      $old_pass = $data['old_password'];
      $new_pass = $data['new_password'];


      if ($old_pass == "" || $new_pass == "" ) {
          return Util::generateErrorMessage("Password field must not be empty!");
      }
      elseif (strlen($new_pass) < 6) {
          return Util::generateErrorMessage("New password must be at least 6 characters!");
      }
         $oldPass = $this->CheckOldPassword($userid, $old_pass);
         if ($oldPass == FALSE) {
            return Util::generateErrorMessage("Old password is not correct!");
         }
         else{
            $new_pass = SHA1($new_pass);
            $sql = "UPDATE tbl_users SET
                password=:password,
                updated_by = :localId,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':password', $new_pass);
            $stmt->bindValue(':localId', Session::get('id'));
            $stmt->bindValue(':id', $userid);
            $result =   $stmt->execute();

          if ($result) {
            return Util::generateSuccessMessage("Password changed!");
            Session::set('msg', Util::generateSuccessMessage("Your password is now changed!"));
          } 
          else {
            return Util::generateErrorMessage("Password did not change!");
          }

         }
    }
}