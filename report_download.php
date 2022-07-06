<?php
include_once 'lib/Database.php';
include_once 'classes/Crypto.php';
include_once 'lib/Session.php';
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["downloadResults"])){
	header("Location: 404");
	exit();
}

Session::init();

$db = Database::getInstance();
$pdo = $db->pdo;

if (!empty($_POST['study_ID'])) {
    $study_ID = Crypto::decrypt($_POST['study_ID'], hex2bin($_POST['study_iv']));
    if (!empty($_POST['participant_ID'])) {
        $participant_ID = Crypto::decrypt($_POST['participant_ID'], hex2bin($_POST['participant_iv']));
        $times = array();
        $names = array();
        $times_sql = "SELECT id, name FROM ssq_times
                        WHERE study_id = $study_ID
                        AND is_active = 1
                        " . (!empty($_POST['ssq_time']) ? "AND id = {$_POST['ssq_time']}" : "");
        $times_result = $pdo->query($times_sql);
        while ($row = $times_result->fetch(PDO::FETCH_ASSOC)) {
            array_push($times, $row);
        }
        $names_sql = "SELECT id, name FROM session_times
                        WHERE study_id = $study_ID
                        AND is_active = 1
                        " . (!empty($_POST['session_name']) ? "AND id = {$_POST['session_name']}" : "");
        $names_result = $pdo->query($names_sql);
        while ($row = $names_result->fetch(PDO::FETCH_ASSOC)) {
            array_push($names, $row);
        }
    
        $study_str = "session_name," . implode(',', array_map(function ($time) {
            $name_str = preg_replace('/[\s,]/', '_', strtolower($time['name']));
            return "{$name_str}_type,{$name_str}_nausea,{$name_str}_oculomotor,{$name_str}_disorient,{$name_str}_total";
        }, $times));
        $name_sql = "SELECT anonymous_name, iv FROM participants
                     WHERE participant_id = $participant_ID;";
        $name_result = $pdo->query($name_sql);
        $row = $name_result->fetch(PDO::FETCH_ASSOC);
        $participant_name = Crypto::decrypt($row['anonymous_name'], hex2bin($row['iv']));
        foreach ($names as $session_name) {
            $study_str .= "\n{$session_name['name']}";
            foreach ($times as $ssq_time) {
                $row = getSSQs($study_ID, $participant_ID, $session_name['id'], $ssq_time['id'], $pdo);
                if ($row) {
                    $general_discomfort = $row['general_discomfort'];
                    $fatigue = $row['fatigue'];
                    $headache = $row['headache'];
                    $eye_strain = $row['eye_strain'];
                    $difficulty_focusing = $row['difficulty_focusing'];
                    $increased_salivation = $row['increased_salivation'];
                    $sweating = $row['sweating'];
                    $nausea = $row['nausea'];
                    $difficulty_concentrating = $row['difficulty_concentrating'];
                    $fullness_of_head = $row['fullness_of_head'];
                    $blurred_vision = $row['blurred_vision'];
                    $dizziness_with_eyes_open = $row['dizziness_with_eyes_open'];
                    $dizziness_with_eyes_closed = $row['dizziness_with_eyes_closed'];
                    $vertigo = $row['vertigo'];
                    $stomach_awareness = $row['stomach_awareness'];
                    $burping = $row['burping'];
                    
                    $nausea_sum = $general_discomfort + $increased_salivation + $sweating + $nausea + $difficulty_concentrating + $stomach_awareness + $burping;
                    $nausea_score = $nausea_sum * 9.54;
                
                    $oculomotor_sum = $general_discomfort + $fatigue + $headache + $eye_strain + $difficulty_focusing + $difficulty_concentrating + $blurred_vision;
                    $oculomotor_score = $oculomotor_sum * 7.58;
                
                    $disorient_sum = $difficulty_focusing + $nausea + $fullness_of_head + $blurred_vision + $dizziness_with_eyes_open + $dizziness_with_eyes_closed + $vertigo;
                    $disorient_score = $disorient_sum * 13.92;
                
                    $SSQ_Sum = $nausea_sum + $oculomotor_sum + $disorient_sum;
                    $total_score = $SSQ_Sum * 3.74;
                    $study_str .= ",{$row['ssq_type']},$nausea_score,$oculomotor_score,$disorient_score,$total_score";
                } else {
                    $study_str .= ',,,,,,';
                }
            }
            $study_str .= "\n";
        }
        $retStr = "$study_str;FILEBREAK - name: " . preg_replace('/\s/', '_', strtolower($participant_name)) . ";";
        echo $retStr;
    } else {
        echo getFilesForSessions($study_ID, $pdo, !empty($_POST['session_name']) ? $_POST['session_name'] : false, !empty($_POST['ssq_time']) ? $_POST['ssq_time'] : false);
    }
} else {
    $userid = Session::get('id');
    $studies_sql = "SELECT study_id FROM researchers
                    WHERE is_active = 1
                    AND researcher_id = $userid
                    AND study_role <= 3;";
    $studies_result = $pdo->query($studies_sql);
    $studyIDs = array();
    while ($row = $studies_result->fetch(PDO::FETCH_ASSOC)) {
        array_push($studyIDs, $row['study_id']);
    }
    $retStr = "";
    foreach ($studyIDs as $study_ID) {
        $retStr .= getFilesForSessions($study_ID, $pdo);
        $study_name_sql = "SELECT short_name FROM study
                           WHERE study_id = $study_ID;";
        $study_name_result = $pdo->query($study_name_sql);
        $study_name = $study_name_result->fetch(PDO::FETCH_ASSOC)['short_name'];
        $retStr .= ";FOLDERBREAK - name: " . preg_replace('/\s/', '_', strtolower($study_name)) . ";";
    }
    echo $retStr;
}

function getFilesForSessions($study_ID, $pdo, $session_name = false, $ssq_time_choice = false) {
    $names = array();
    $names_sql = "SELECT id, name FROM session_times
                    WHERE study_id = $study_ID
                    AND is_active = 1
                    " . ($session_name ? "AND id = $session_name" : "");
    $names_result = $pdo->query($names_sql);
    while ($row = $names_result->fetch(PDO::FETCH_ASSOC)) {
        array_push($names, $row);
    }
    $retStr = "";
    foreach ($names as $session_name) {
        $times = array();
        $times_sql = "SELECT id, name FROM ssq_times
                        WHERE study_id = $study_ID
                        AND is_active = 1
                        " . ($ssq_time_choice ? "AND id = $ssq_time_choice" : "");
        $times_result = $pdo->query($times_sql);
        while ($row = $times_result->fetch(PDO::FETCH_ASSOC)) {
            array_push($times, $row);
        }
    
        $session_str = "participant_name," . implode(',', array_map(function ($time) {
            $name_str = preg_replace('/[\s,]/', '_', strtolower($time['name']));
            return "{$name_str}_type,{$name_str}_nausea,{$name_str}_oculomotor,{$name_str}_disorient,{$name_str}_total";
        }, $times));
        $participant_sql = "SELECT anonymous_name, iv, participant_id FROM participants
                            WHERE study_id = $study_ID
                            AND is_active = 1;";
        $participant_result = $pdo->query($participant_sql);
        $participants = array();
        while ($row = $participant_result->fetch(PDO::FETCH_ASSOC)) {
            array_push($participants, $row);
        }
        foreach ($participants as $participant) {
            $participant_name = Crypto::decrypt($participant['anonymous_name'], hex2bin($participant['iv']));
            $participant_ID = $participant['participant_id'];
            $session_str .= "\n" . preg_replace('/[\s,]/', '_', strtolower($participant_name));
            foreach ($times as $ssq_time) {
                $row = getSSQs($study_ID, $participant_ID, $session_name['id'], $ssq_time['id'], $pdo);
                if ($row) {
                    $general_discomfort = $row['general_discomfort'];
                    $fatigue = $row['fatigue'];
                    $headache = $row['headache'];
                    $eye_strain = $row['eye_strain'];
                    $difficulty_focusing = $row['difficulty_focusing'];
                    $increased_salivation = $row['increased_salivation'];
                    $sweating = $row['sweating'];
                    $nausea = $row['nausea'];
                    $difficulty_concentrating = $row['difficulty_concentrating'];
                    $fullness_of_head = $row['fullness_of_head'];
                    $blurred_vision = $row['blurred_vision'];
                    $dizziness_with_eyes_open = $row['dizziness_with_eyes_open'];
                    $dizziness_with_eyes_closed = $row['dizziness_with_eyes_closed'];
                    $vertigo = $row['vertigo'];
                    $stomach_awareness = $row['stomach_awareness'];
                    $burping = $row['burping'];
                    
                    $nausea_sum = $general_discomfort + $increased_salivation + $sweating + $nausea + $difficulty_concentrating + $stomach_awareness + $burping;
                    $nausea_score = $nausea_sum * 9.54;
                
                    $oculomotor_sum = $general_discomfort + $fatigue + $headache + $eye_strain + $difficulty_focusing + $difficulty_concentrating + $blurred_vision;
                    $oculomotor_score = $oculomotor_sum * 7.58;
                
                    $disorient_sum = $difficulty_focusing + $nausea + $fullness_of_head + $blurred_vision + $dizziness_with_eyes_open + $dizziness_with_eyes_closed + $vertigo;
                    $disorient_score = $disorient_sum * 13.92;
                
                    $SSQ_Sum = $nausea_sum + $oculomotor_sum + $disorient_sum;
                    $total_score = $SSQ_Sum * 3.74;
                    $session_str .= ",{$row['ssq_type']},$nausea_score,$oculomotor_score,$disorient_score,$total_score";
                } else {
                    $session_str .= ',,,,,,';
                }
            }
            $session_str .= "\n";
        }
        $retStr .= "$session_str;FILEBREAK - name: " . preg_replace('/\s/', '_', strtolower($session_name['name'])) . ";";
    }
    return $retStr;
}

function getSSQs($study_ID, $participant_ID, $session_name, $ssq_time, $pdo) {
    
    $sql = "SELECT 
            ssq.general_discomfort,
            ssq.fatigue,
            ssq.headache,
            ssq.eye_strain,
            ssq.difficulty_focusing,
            ssq.increased_salivation,
            ssq.sweating,
            ssq.nausea,
            ssq.difficulty_concentrating,
            ssq.fullness_of_head,
            ssq.blurred_vision,
            ssq.dizziness_with_eyes_open,
            ssq.dizziness_with_eyes_closed,
            ssq.vertigo,
            ssq.stomach_awareness,
            ssq.burping,
            ssq_times.name AS ssq_time,
            ssq_type.type AS ssq_type,
            session_times.name AS session_name
        FROM ssq
            JOIN ssq_times ON ssq.ssq_time = ssq_times.id
            JOIN ssq_type ON ssq.ssq_type = ssq_type.id
            JOIN session ON session.session_id = ssq.session_id
            JOIN session_times ON session_times.id = session.session_time
        WHERE ssq.is_active = 1
        AND ssq_times.is_active = 1
        AND session_times.is_active = 1
        AND ssq.session_id IN (
            SELECT session.session_id FROM session
                JOIN study ON session.study_id = study.study_id
            WHERE session.is_active = 1
            AND study.study_id = $study_ID
            AND session.participant_id = $participant_ID
            AND session.session_time = $session_name
        )
        AND ssq_times.id = $ssq_time";
    $result = $pdo->query($sql);
    return $result->fetch(PDO::FETCH_ASSOC);
}