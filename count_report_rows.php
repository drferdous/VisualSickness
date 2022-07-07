<?php
include_once 'lib/Database.php';
include_once 'classes/Crypto.php';
include_once 'lib/Session.php';
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["study_ID"])){
	header("Location: 404");
	exit();
}
Session::init();
$db = Database::getInstance();
$pdo = $db->pdo;

if (!empty($_POST['study_ID'])) $study_ID = Crypto::decrypt($_POST['study_ID'], hex2bin($_POST['study_iv']));
if (!empty($_POST['participant_ID'])) $participant_ID = Crypto::decrypt($_POST['participant_ID'], hex2bin($_POST['participant_iv']));

$sql = "SELECT COUNT(*) AS count FROM ssq
            JOIN session ON session.session_id = ssq.session_id
            JOIN session_times ON session_times.id = session.session_time
            JOIN ssq_times ON ssq_times.id = ssq.ssq_time
            JOIN participants ON participants.participant_id = session.participant_id
            JOIN researchers ON researchers.study_id = session.study_id
        WHERE session_times.is_active = 1
        AND researchers.is_active = 1
        AND researchers.researcher_id = " . Session::get('id') . "
        AND researchers.study_role <= 3 "
        . (isset($study_ID) ? "AND session.study_id = $study_ID " : "")
        . (!empty($_POST['session_name']) ? "AND session.session_time = {$_POST['session_name']} " : "") .
        "AND session.is_active = 1
        AND ssq_times.is_active = 1
        AND ssq.is_active = 1 "
        . (!empty($_POST['ssq_time']) ? "AND ssq.ssq_time = {$_POST['ssq_time']} " : "")
        . (isset($participant_ID) ? "AND session.participant_id = $participant_ID " : "") . 
        "AND participants.is_active = 1
        AND session.is_active = 1;";
$result = $pdo->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
echo $row['count'];