<?php
include_once 'database.php';

if (isset($_POST['study_ID']) && !empty($_POST['study_ID'])) {

	// Fetch all users in a study after study id is selected
	$sql = "SELECT Researcher_Study.researcher_ID from Researcher_Study where study_ID = ".$_POST['study_ID']."";
	echo "<script>alert('$sql');</script>";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) { 
		echo '<option value="" selected hidden disabled>Member Name</option>'; 
    	while ($row = $result->fetch_assoc()) {
                echo '<option value="'.$row['researcher_ID'].'">'.$row['researcher_ID'].'</option>';     	    
    	        // echo '<option value="'.$row['name'].'">'.$row['researcher_ID'].'</option>'; 
    	}
	} else {
	    echo '<option value="">Member not found</option>'; 
	}
}

?>