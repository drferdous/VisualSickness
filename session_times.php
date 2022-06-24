<?php
include_once 'lib/Database.php';
include_once 'classes/Crypto.php';
include_once 'lib/Session.php';
Session::init();
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['iv'])){
    header("Location: 404");
    exit();
}

$db = Database::getInstance();
$pdo = $db->pdo;

if (isset($_POST['participant_id']) && !empty($_POST['participant_id']) && isset($_POST['iv'])) {
    $id = Crypto::decrypt($_POST['participant_id'], hex2bin($_POST['iv']));
    
	// Fetch session time choices after participant id is selected
	$sql = "SELECT name, id
            FROM session_times
            WHERE is_active = 1
            AND study_id = " . Session::get('study_ID') . " 
            AND id NOT IN (SELECT session_time
                           FROM session WHERE is_active = 1
                           AND study_id = " . Session::get('study_ID') . " 
                           AND participant_id = " . $id . ");";
	
	$result = $pdo->query($sql);
	if ($result->rowCount() > 0) { ?>
	    <option value="" selected hidden disabled>Please Choose...</option> 
	    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
    	    <option value="<?=$row['id'] ?>"><?= $row['name'] ?></option>
    	<?php }
	} else { ?>
	    <option class="timesNotFound" value="" selected hidden disabled>No remaining sessions available for this participant</option>
	<?php }
} ?>