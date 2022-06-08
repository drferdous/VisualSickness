<?php
include_once 'lib/Database.php';
$db = Database::getInstance();
$pdo = $db->pdo;

if (isset($_POST['researcher_ID']) && !empty($_POST['researcher_ID'])) {

	// Fetch user role choices after researcher id is selected
	$sql = "SELECT id, role FROM tbl_roles WHERE id >= ( SELECT roleid from tbl_users where id = ".$_POST['researcher_ID'].")";
	$result = $pdo->query($sql);
	
	if ($result->rowCount() > 0) { 
		echo '<option value="" selected hidden disabled>Study Role</option>'; 
    	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    	    if($row['id']>1){
    	        echo '<option value="'.$row['id'].'">'.$row['role'].'</option>'; 
    	    }
    	}
	} else {
	    echo '<option value="">Role not found</option>'; 
	}
}

?>