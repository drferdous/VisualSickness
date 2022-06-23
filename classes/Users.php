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
    array_walk($data, function (&$val) {         $val = trim($val);     });
    $name = $data['name'];
    $password = Util::generateRandomPassword();
    $email = $data['email'];
    $affiliationid = $data['affiliationid'];    
    $mobile = $data['mobile'];
    $roleid = intval($data['roleid']);
      
    if (empty($name) || empty($email) || empty($roleid) || empty($affiliationid)){
        return Util::generateErrorMessage("User registration fields must not be empty!");
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
        return Util::generateErrorMessage("Your email address must be valid!");
    }
    if (Util::checkExistEmail($email, $this->db) !== FALSE) {
        return Util::generateErrorMessage("This email already exists. Try another email!");
    }
    if ($roleid < 2 || $roleid > 4){
        return Util::generateErrorMessage("Invalid role was selected!");
    }
    $affil_sql = "SELECT domain from Affiliation WHERE id = $affiliationid;";
    $affil_result = $this->db->pdo->query($affil_sql);
    $affil_domain = $affil_result->fetch(PDO::FETCH_ASSOC)['domain'];
    // print_r($affil_domain);
    if (!is_null($affil_domain)) {
        $arr = explode('@', $email);
        $register_domain = array_pop($arr);
        if ($affil_domain != $register_domain) {
            return Util::generateErrorMessage("This affiliation requires the use of a $affil_domain email account during registration.");
        }
    }
    
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
  
  // Select All User Method
  public function selectAllUserData($showPendingUserFlag, $showDeactivatedUserFlag, $affiliationid){
    $args = array();
    if ($showPendingUserFlag) {
        $args['reg_stat'] = 1;
    }
    if ($showDeactivatedUserFlag) {
        $args['status'] = 0;
    }
    $argsString = "";
    foreach ($args as $key => $value) {
        $argsString .= "AND $key = $value ";
    }
    $sql = "SELECT * FROM tbl_users WHERE affiliationid = $affiliationid AND status != 2 $argsString
            ORDER BY id ASC;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }


  // User login Autho Method
  public function userLoginAutho($email, $password){
    $password = SHA1($password);
    $sql = "SELECT * FROM tbl_users 
            WHERE email = :email 
            AND password = :password 
            AND status < 2 
            LIMIT 1;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $password);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
  
  // Check User Account Status
  public function CheckActiveUser($email){
    $sql = "SELECT * FROM tbl_users 
            WHERE email = :email 
            AND status = :status 
            LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':status', 1);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

    // User Login Authentication Method
    public function userLoginAuthentication($email, $password){
      $email = trim($email);


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
            $affiliationid_sql = "SELECT affiliationid FROM tbl_users WHERE email = :email AND status = 0 OR status = 1;";
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
          Session::set('session_ID', 0);
          Session::set('logMsg', Util::generateSuccessMessage("You logged in!"));
          Session::set('study_ID', 0);
          Session::set('ssq_ID', -1);

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
      $stmt->bindValue(':id', trim($userid));
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
      $stmt->bindValue(':study_ID', trim($study_ID));
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      if ($result) {
        return $result;
      }else{
        return false;
      }
    }
    

  //   Update user profile info
    public function updateUserByIdInfo($data){
    if (!isset($data["user_ID"]) || !isset($data["iv"])){
        return Util::generateErrorMessage("Invalid user ID!");
    }
    
    array_walk($data, function (&$val) {         $val = trim($val);     });
    $iv = hex2bin($data["iv"]);
    $encryptedUserID = $data["user_ID"];
    $name = $data['name'];
    $mobile = $data['mobile'];
    $userid = Crypto::decrypt($encryptedUserID, $iv);
    
    if ($name == ""){
        return Util::generateErrorMessage("Name field should not be empty!");
    }
    if (!empty($mobile) && filter_var($mobile,FILTER_SANITIZE_NUMBER_INT) !== $mobile) {
        return Util::generateErrorMessage("Please enter only numeric characters for phone number!");
    }
    if (Session::get("roleid") != 1 && isset($data["roleid"])){
        return Util::generateErrorMessage("You do not have permission to change your role!");
    }
    if (isset($data["roleid"])){
        $roleid = intval($data["roleid"]);
        if ($roleid < 2 || $roleid > 4){
            return Util::generateErrorMessage("Please select a valid role!");
        }
    }
    $sql = "UPDATE tbl_users SET
            name = :name,
            mobile = :mobile, "
            . (isset($roleid) ? "roleid = :roleid," : "") . "
            updated_by = :updated_by,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";
    $stmt= $this->db->pdo->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':mobile', $mobile);
    if (isset($roleid)){
        $stmt->bindValue(":roleid", $roleid);
    }
    $stmt->bindValue(':id', trim($userid));
    $stmt->bindValue(':updated_by', Session::get('id'));
    $result =   $stmt->execute();

    if ($result){ 
        return Util::generateSuccessMessage("you have updated your information!");
    } 
    else{
        return Util::generateErrorMessage("Your profile could not be updated!");
    }
}    

    // Delete User by Id Method
    public function deleteUserById($remove){
        $localId = Session::get('id');
        $sql = "UPDATE tbl_users SET status = 2, updated_by = :localId, updated_at = CURRENT_TIMESTAMP WHERE id = :id ";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':localId', $localId);
        $stmt->bindValue(':id', trim($remove));
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
            status=:status,
            updated_by = :localId,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':status', 1);
        $stmt->bindValue(':id', trim($deactive));
        $stmt->bindValue(":localId", $localId);
        $result =   $stmt->execute();
        if ($result) {
            Session::set('msg', Util::generateSuccessMessage("User account is deactivated"));
        } 
        else {
            Session::set('msg', Util::generateErrorMessage("Data was not deactivated!"));
        }
    }


    // User Activated By Admin
    public function userActiveByAdmin($active){
        $localId = Session::get('id');
        $sql = "UPDATE tbl_users SET
            status=:status,
            updated_by = :localId,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':status', 0);
        $stmt->bindValue(':localId', $localId);
        $stmt->bindValue(':id', trim($active));
        $result =   $stmt->execute();
        if ($result) {
            Session::set('msg', Util::generateSuccessMessage("User account activated!"));
        }
        else{
            Session::set('msg', Util::generateErrorMessage("User account was not activated!"));
        }
    }

    // Check Old password method
    public function CheckOldPassword($userid, $old_pass){
      $old_pass = SHA1($old_pass);
      $sql = "SELECT password FROM tbl_users WHERE password = :password AND id =:id";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':password', $old_pass);
      $stmt->bindValue(':id', trim($userid));
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        return true;
      }else{
        return false;
      }
    }
    
    // update user password without old password
    public function resetPass($email, $new_pass, $confirm_pass) {
        $email = trim($email);
        if ($new_pass == "" || $confirm_pass == "") {
            return Util::generateErrorMessage("Password field must not be empty!");
        }
        if (strlen($new_pass) < 6) {
            return Util::generateErrorMessage("New password must be at least 6 characters!");
        }
        if(strcmp($new_pass,$confirm_pass)!= 0) {
            return Util::generateErrorMessage("Passwords do not match!");
        } else {
            $new_pass = SHA1($new_pass);
            $id_sql = "SELECT id FROM tbl_users WHERE email = :email LIMIT 1";
            $stmt = $this->db->pdo->prepare($id_sql);
            $stmt->bindValue(':email', trim($email));
            $stmt->execute();
            $id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            $sql = "UPDATE tbl_users SET
                password=:password,
                updated_by = $id,
                updated_at = CURRENT_TIMESTAMP
                WHERE email = :email";
            
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':password', $new_pass);
            $stmt->bindValue(':email', trim($email));
            $result = $stmt->execute();
            
            if (!$result) {
                return Util::generateErrorMessage("Password did not change!");
            }
        }
    }

    // Change User pass By Id
    public function changePasswordBysingleUserId($userid, $data){

      $old_pass = $data['old_password'];
      $new_pass = $data['new_password'];
      $confirm_pass = $data['confirm_password'];
      


      if ($old_pass == "" || $new_pass == "" || $confirm_pass == "") {
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
        if(strcmp($new_pass, $confirm_pass) != 0) {
            return Util::generateErrorMessage("Passwords do not match!");
        } else {
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
}