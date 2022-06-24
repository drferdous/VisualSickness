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
        } else {
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
            if (self::get('roleid') == '1') {
                $homepage = "user_list";
            } else {
                $homepage = "study_list";
            }
            header("Location: " . $homepage);
            exit();
        }
    }
    
    public static function CheckPostID($data){
        $key = "randCheck";
        return isset($data[$key]) && self::get("post_ID") === $data[$key];
    }
  
    public static function RedirectIfUser(){
        $role_ID = intval(self::get("roleid"));
        if ($role_ID > 3){
            header("Location: 404");
            exit();
        }
    }
  
    public static function setStudyID($study_ID, $pdo) {
        if (self::get('roleid') == 1) {
            $sql = "SELECT study_id FROM study
                    WHERE created_by IN (SELECT user_id FROM users WHERE affiliation_id = " . self::get("affiliationid") . ")
                    AND study_id = $study_ID
                    LIMIT 1;";
        } else {
            $sql = "SELECT users.id FROM users
                JOIN researchers as researcher
                ON (users.id =  researcher.researcher_id)
                WHERE users.id = " . self::get("id") . "
                AND researcher.study_id = $study_ID LIMIT 1;";
        }
        $result = $pdo->query($sql);
        if ($result->rowCount() === 1) {
            self::set('study_ID', $study_ID);
        } else {
            self::set('study_ID', 0);
        }
    }
    
    public static function requireAdmin() {
        if (self::get("roleid") != 1) {
            header('Location: 404');
            exit();
        }
    }
    
    public static function requireResearcherOrUser($study_ID, $pdo) {
        $sql = "SELECT researcher_id FROM researchers
                WHERE researcher_id = " . self::get('id') . "
                AND is_active = 1
                AND study_id = $study_ID;";
        $result = $pdo->query($sql);
        if (!$result->rowCount()) {
            header('Location: study_list');
            exit();
        }
    }
    
    public static function requirePI($study_ID, $pdo) {
        $sql = "SELECT researcher.researcher_id FROM researchers AS researcher
                JOIN study AS s ON researcher.study_id = s.study_id
                WHERE researcher.researcher_id = " . self::get('id') . "
                AND researcher.is_active = 1
                AND s.is_active = 1
                AND researcher.study_id = $study_ID
                AND researcher.study_role = 2;";
        $result = $pdo->query($sql);
        if (!$result->rowCount()) {
            header('Location: study_details');
            exit();
        }
    }
    
    public static function requirePIorRA($study_ID, $pdo) {
        $sql = "SELECT researcher.researcher_id FROM researchers AS researcher
                JOIN study AS s ON researcher.study_id = s.study_id
                WHERE researcher.researcher_id = " . self::get('id') . "
                AND researcher.is_active = 1
                AND s.is_active = 1
                AND researcher.study_id = $study_ID
                AND researcher.study_role = 2 OR study_role = 3;";
        $result = $pdo->query($sql);
        if (!$result->rowCount()) {
            header('Location: study_details');
            exit();
        }
    }
    
    public static function requireCreator($study_ID, $pdo) {
        $sql = "SELECT created_by FROM study
                WHERE is_active = 1
                AND study_id = $study_ID;";
        $result = $pdo->query($sql);
        if ($result->fetch(PDO::FETCH_ASSOC)['created_by'] != Session::get('id')) {
            header('Location: study_details');
            exit();
        }
    }
}

?>
