<?php
include_once 'lib/Database.php';
include_once 'classes/Crypto.php';
include_once 'lib/Session.php';
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["researcher_ID"])){
	header("Location: 404");
	exit();
}

Session::init();

$db = Database::getInstance();
$pdo = $db->pdo;


if (isset($_POST['researcher_ID']) && !empty($_POST['researcher_ID']) && isset($_POST['iv'])) {
    $id = Crypto::decrypt($_POST['researcher_ID'], hex2bin($_POST['iv']));
	// Fetch user role choices after researcher id is selected
	$sql = "SELECT id, role FROM user_roles WHERE id >= (SELECT role_id from users where user_id = $id)";
	$result = $pdo->query($sql);
	if ($result->rowCount() > 0) {
	    $sql_study_role = "SELECT study_role FROM researchers 
	            WHERE researcher_id = " . $id . "
	            AND study_id = " . Session::get("study_ID") . "
	            AND is_active = 1
	            LIMIT 1;";
	    $result_study_role = $pdo->query($sql_study_role);
	    $row_study_role = $result_study_role->fetch(PDO::FETCH_ASSOC);
	    if ($result_study_role->rowCount() === 0){
	        echo '<option value="" selected hidden disabled>Study Role</option>';    
	    }
    	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    	    if($row['id']>1){
    	        if ($result_study_role->rowCount() > 0 && $row_study_role["study_role"] == $row["id"]){
    	            echo '<option value="'.$row['id'].'" selected>'.$row['role'].'</option>'; 
    	        }
    	        else{
    	            echo '<option value="'.$row['id'].'">'.$row['role'].'</option>';
    	        }
    	    }
    	}
	} else {
	    echo '<option value="">Role not found</option>'; 
	}
}

?>