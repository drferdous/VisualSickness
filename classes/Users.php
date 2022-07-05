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
    $affil_sql = "SELECT domain, domain_required from affiliation WHERE affiliation_id = $affiliationid;";
    $affil_result = $this->db->pdo->query($affil_sql);
    $affil_domain = $affil_result->fetch(PDO::FETCH_ASSOC);
    if ($affil_domain['domain_required'] == 1) {
        $arr = explode('@', $email);
        $register_domain = array_pop($arr);
        if ($affil_domain != $register_domain) {
            return Util::generateErrorMessage("This affiliation requires the use of a " . $affil_domain['domain'] . " email account during registration.");
        }
    }
    
    // if everything is sucessful, insert into DB
    $sql = "INSERT INTO users(name, email, affiliation_id, password, mobile, role_id) 
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
        $update_sql = "UPDATE users SET updated_by=$id WHERE user_id=$id;";
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
        $args['registration_status'] = 1;
    }
    if ($showDeactivatedUserFlag) {
        $args['status'] = 0;
    }
    $argsString = "";
    foreach ($args as $key => $value) {
        $argsString .= "AND $key = $value ";
    }
    $sql = "SELECT * FROM users WHERE affiliation_id = $affiliationid AND status != 2 $argsString
            ORDER BY user_id ASC;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }


  // User login Autho Method 
  public function userLoginAutho($email, $password){
    $password = SHA1($password);
    $sql = "SELECT * FROM users 
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
    $sql = "SELECT * FROM users 
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
      } else {

        $logResult = $this->userLoginAutho($email, $password);
        $isUserActive = $this->CheckActiveUser($email);

        if (! ($isUserActive == TRUE)) {
            $affiliationid_sql = "SELECT affiliation_id FROM users WHERE email = :email AND status = 0 OR status = 1;";
            $stmt = $this->db->pdo->prepare($affiliationid_sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $affiliationid = $stmt->fetch(PDO::FETCH_ASSOC)['affiliation_id'];
            
            $errorMessage = "Sorry, your account is deactivated. Please contact an admin. (" . Util::getAdminsFromAffiliation($this->db->pdo, $affiliationid) . ")";
            return Util::generateErrorMessage($errorMessage);
        } elseif ($logResult) {
          Session::init();
          Session::set('login', TRUE);
          Session::set('id', $logResult->user_id);
          Session::set('roleid', $logResult->role_id);
          Session::set('name', $logResult->name);
          Session::set('email', $logResult->email);
          Session::set('affiliationid', $logResult->affiliation_id);
          Session::set('reg_stat', $logResult->registration_status);
          Session::set('session_ID', 0);
          Session::set('logMsg', Util::generateSuccessMessage("You logged in!"));
          Session::set('study_ID', 0);
          Session::set('ssq_ID', -1);

        } else{
            return Util::generateErrorMessage("Email or password did not match!");
        }
      }
    }

    // Get Single User Information By Id Method
    public function getUserInfoById($userid){
      $sql = "SELECT * FROM users WHERE user_id = :id LIMIT 1";
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
        $sql = "SELECT * FROM study WHERE study_id = :study_ID LIMIT 1";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':study_ID', trim($study_ID));
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if ($result) {
            return $result;
        } else {
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
        if ($userid != Session::get('id')) {
            $role_check_sql = "SELECT role_id FROM users WHERE user_id = $userid";
            $role = $this->db->pdo->query($role_check_sql);
            if (!$role || $role->fetch(PDO::FETCH_ASSOC)['role_id'] == 1) {
                return Util::generateErrorMessage("You do not have access to edit this user!");
            }
        }
        if ($name == ""){
            return Util::generateErrorMessage("Name field should not be empty!");
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
        $sql = "UPDATE users SET
                name = :name,
                mobile = :mobile, "
                . (isset($roleid) ? "role_id = :roleid," : "") . "
                updated_by = :updated_by,
                updated_at = CURRENT_TIMESTAMP
                WHERE user_id = :id";
        $stmt= $this->db->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':mobile', $mobile);
        if (isset($roleid)){
            $stmt->bindValue(":roleid", $roleid);
        }
        $stmt->bindValue(':id', trim($userid));
        $stmt->bindValue(':updated_by', Session::get('id'));
        $result =   $stmt->execute();

        if ($result && (trim($userid) != Session::get('id'))){ 
            return Util::generateSuccessMessage("You have updated this profile's information!");
        } else if ($result) {
            return Util::generateSuccessMessage("You have updated your information!");
        } else{
            return Util::generateErrorMessage("Your profile could not be updated!");
        }
    }  

    // Check Old password method
    public function checkOldPassword($userid, $old_pass){
      $old_pass = SHA1($old_pass);
      $sql = "SELECT password FROM users WHERE password = :password AND user_id =:id";
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
            $id_sql = "SELECT user_id FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->pdo->prepare($id_sql);
            $stmt->bindValue(':email', trim($email));
            $stmt->execute();
            $id = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];
            $sql = "UPDATE users SET
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

    // Change User password By Id
    public function changePasswordById($userid, $data){

      $old_pass = $data['old_password'];
      $new_pass = $data['new_password'];
      $confirm_pass = $data['confirm_password'];
      
      if ($old_pass == "" || $new_pass == "" || $confirm_pass == "") {
          return Util::generateErrorMessage("Password field must not be empty!");
      }
      elseif (strlen($new_pass) < 6) {
          return Util::generateErrorMessage("New password must be at least 6 characters!");
      }
      
     $oldPass = $this->checkOldPassword($userid, $old_pass);
     if ($oldPass == FALSE) {
        return Util::generateErrorMessage("Old password is not correct!");
     }
     else{
        if(strcmp($new_pass, $confirm_pass) != 0) {
            return Util::generateErrorMessage("Passwords do not match!");
        } else {
            $new_pass = SHA1($new_pass);
            $sql = "UPDATE users SET
                password=:password,
                updated_by = :localId,
                updated_at = CURRENT_TIMESTAMP
                WHERE user_id = :id";
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