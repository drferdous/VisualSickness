<?php

// Class Name: Session

class Session{
  
  // Session Start Method
  public static function init(){

    if (version_compare(phpversion(), '5.4.0', '<')) {
      if (session_id() == '') {
        session_start();
      }
    } else {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    } 
  }


  // Session Set Method
  public static function set($key, $val){
    $_SESSION[$key] = $val;
  }

  // Session Get Method
  public static function get($key){
    if (isset($_SESSION[$key])) {
      return $_SESSION[$key];
    }else{
      return false;
    }
  }
  
  
  // User logout Method
  public static function destroy(){
    session_destroy();
    session_unset();
    header('Location:login');
  }


  // Check Session Method
  public static function CheckSession(){
    if (self::get('login') == FALSE) {
      header('Location:login');
    }
  }


  // Check Login Method
  public static function CheckLogin(){
    if (self::get("login") == TRUE) {
        header('Location: index');
    }
  }
  
  public static function RedirectIfUser(){
      $role_ID = intval(self::get("roleid"));
      if ($role_ID > 3){
          header("Location: 404");
          exit();
      }
  }
}

?>
