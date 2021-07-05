<?php
include_once 'database.php';

if (isset($_POST['researcher_ID']) && !empty($_POST['researcher_ID'])) {

	// Fetch user role choices after researcher id is selected
	$sql = "SELECT id, role FROM tbl_roles WHERE id >= ( SELECT roleid from tbl_users where id = ".$_POST['researcher_ID'].")";
	$result = $conn->query($sql);
	
	//mysqli_query($conn, 
	if ($result->num_rows > 0) { 
		echo '<option value="">Study Role</option>'; 
    	while ($row = $result->fetch_assoc()) {
    	    if($row['id']>1){
    	        echo '<option value="'.$row['id'].'">'.$row['role'].'</option>'; 
    	    }
    	}
	} else {
	    echo '<option value="">Role not found</option>'; 
	}
}

?>