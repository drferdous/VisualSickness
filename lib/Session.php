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
  
    public static function setStudyID($study_ID, $pdo) {
        if (self::get('roleid') == 1) {
            $sql = "SELECT study_ID FROM Study
                    WHERE created_by IN (SELECT user.id FROM tbl_users AS user WHERE affiliationid = " . self::get("affiliationid") . ")
                    AND study_ID = $study_ID
                    LIMIT 1;";
        } else {
            $sql = "SELECT user.id FROM tbl_users as user
                JOIN Researcher_Study as study
                ON (user.id = study.researcher_ID)
                WHERE user.id = " . self::get("id") . "
                AND study.study_ID = $study_ID LIMIT 1;";
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
        $sql = "SELECT researcher_ID FROM Researcher_Study
                WHERE researcher_ID = " . self::get('id') . "
                AND is_active = 1
                AND study_ID = $study_ID;";
        $result = $pdo->query($sql);
        if (!$result->rowCount()) {
            header('Location: view_study');
            exit();
        }
    }
    
    public static function requirePI($study_ID, $pdo) {
        $sql = "SELECT rs.researcher_ID FROM Researcher_Study AS rs
                JOIN Study AS s ON rs.study_ID = s.study_ID
                WHERE rs.researcher_ID = " . self::get('id') . "
                AND rs.is_active = 1
                AND s.is_active = 1
                AND rs.study_ID = $study_ID
                AND rs.study_role = 2;";
        $result = $pdo->query($sql);
        if (!$result->rowCount()) {
            header('Location: study_details');
            exit();
        }
    }
    
    public static function requirePIorRA($study_ID, $pdo) {
        $sql = "SELECT rs.researcher_ID FROM Researcher_Study AS rs
                JOIN Study AS s ON rs.study_ID = s.study_ID
                WHERE rs.researcher_ID = " . self::get('id') . "
                AND rs.is_active = 1
                AND s.is_active = 1
                AND rs.study_ID = $study_ID
                AND rs.study_role = 2 OR study_role = 3;";
        $result = $pdo->query($sql);
        if (!$result->rowCount()) {
            header('Location: study_details');
            exit();
        }
    }
    
    public static function requireCreator($study_ID, $pdo) {
        $sql = "SELECT created_by FROM Study
                WHERE is_active = 1
                AND study_ID = $study_ID;";
        $result = $pdo->query($sql);
        if ($result->fetch(PDO::FETCH_ASSOC)['created_by'] != Session::get('id')) {
            header('Location: study_details');
            exit();
        }
    }
}

?>
