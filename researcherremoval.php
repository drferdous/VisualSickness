<?php
include_once 'database.php';

if (isset($_POST['study_ID']) && !empty($_POST['study_ID'])) {

	// Fetch all users in a study after study id is selected
    $sql = "SELECT id, name FROM tbl_users, Researcher_Study WHERE researcher_ID = id AND study_ID = ".$_POST['study_ID']."";	
	//$sql = "SELECT researcher_ID FROM Researcher_Study WHERE study_ID = ".$_POST['study_ID']."";
	echo "<script>alert('$sql');</script>";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) { 
		echo '<option value="" selected hidden disabled>Member Name</option>'; 
    	while ($row = $result->fetch_assoc()) {
                echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';     	    
    	        // echo '<option value="'.$row['researcher_ID'].'">'.$row['name'].'</option>'; 
    	}
	} else {
	    echo '<option value="">Member not found</option>'; 
	}
}

?>