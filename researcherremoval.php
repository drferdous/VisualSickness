<?php
include_once 'lib/Database.php';
$db = Database::getInstance();
$pdo = $db->pdo;

if (isset($_POST['study_ID']) && !empty($_POST['study_ID'])) {

	// Fetch all users in a study after study id is selected
    $sql = "SELECT id, name FROM tbl_users, Researcher_Study WHERE researcher_ID = id AND study_ID = ".$_POST['study_ID']."";	
	$result = $pdo->query($sql);
	
	if ($result->rowCount() > 0) { 
		echo '<option value="" selected hidden disabled>Member Name</option>'; 
    	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';     	    
    	}
	} else {
	    echo '<option value="">Member not found</option>'; 
	}
}

?>