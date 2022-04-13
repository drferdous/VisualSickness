<?php

class Util {
    
  // Date formate Method
   public static function formatDate($date){
     // date_default_timezone_set('Asia/Dhaka');
      $strtime = strtotime($date);
    return date('Y-m-d H:i:s', $strtime);
   }

  // Check Exist Email Address Method
  public function checkExistEmail($email, $db){
    $sql = "SELECT email FROM tbl_users 
            WHERE email = :email
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
  
  public function getUserEmailById($conn, $id) {
      $sql = "SELECT email FROM tbl_users WHERE id = $id";
      $result = mysqli_query($conn, $sql);
      if (!$result) {
          echo mysqli_error($result);
          return null;
      }
      return mysqli_fetch_assoc($result)['email'];
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
  
  public static function getAdminsFromAffiliation($conn, $affiliationid) {
      $sql = "SELECT email FROM tbl_users WHERE roleid = 1 AND affiliationid = $affiliationid";
      $result = mysqli_query($conn, $sql);
      if (!$result) {
          echo mysqli_error($result);
          return null;
      }
      $emails = array();
      while ($row = mysqli_fetch_assoc($result)) {
          array_push($emails, $row['email']);
      }
      return implode(', ', $emails);
  }
  
  public static function getAffiliationNameById($conn, $affiliationId) {
      $sql = "SELECT Name FROM Affiliation WHERE id = $affiliationId";
      $result = mysqli_query($conn, $sql);
      if (!$result) {
          echo mysqli_error($result);
          return null;
      }
      return mysqli_fetch_assoc($result)['Name'];
  }
}