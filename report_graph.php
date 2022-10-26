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
    if (!empty($_POST['study_ID'])) {
        $study_ID = Crypto::decrypt($_POST['study_ID'], hex2bin($_POST['study_iv']));
    }
    else exit();
    if (!empty($_POST['participants'])) {
        $participants = array_map(function($obj) {
            return Crypto::decrypt(
                $obj['id'], 
                hex2bin($obj['iv'])
            );
        }, $_POST['participants']);
    }

    $sessions = array();
    if(isset($_POST['sessions'])) {
	$sql = "SELECT name FROM session_times WHERE study_id = $study_ID AND is_active = 1 AND id IN (" . implode(", ", $_POST["sessions"]) . ")";
    } else {
        $sql = "SELECT name FROM session_times WHERE study_id = $study_ID AND is_active = 1";
    }
    $result = $pdo->query($sql);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        array_push($sessions,$row['name']);
    }

    $ssq_times = array();
    if(isset($_POST['ssq_times'])) {
	$sql = "SELECT name FROM ssq_times WHERE study_id = $study_ID AND is_active = 1 AND id IN (" . implode(", ", $_POST["ssq_times"]) . ")";
    } else {
        $sql = "SELECT name FROM ssq_times WHERE study_id = $study_ID AND is_active = 1";
    }
    $result = $pdo->query($sql);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        array_push($ssq_times,$row['name']);
    }

if($_POST['button_id'] == 'session_time') {
?>  
    <script>
    labels = [
        <?php foreach ($sessions as $key => $name) { ?>
            '<?= $name ?>',
        <?php } ?>
    ];

    data = {
    labels,
    datasets: [
        <?php foreach ($ssq_times as $key => $time_name) { ?>
                {
            label: '<?= $time_name ?>',
            backgroundColor: 'hsl(<?= $key * 30 ?>, 100%, 50%)',
            borderColor: 'hsl(<?= $key * 30 ?>, 100%, 50%)',
            data: [
            <?php foreach ($sessions as $key => $session_name) {
                $sql = "SELECT ssq.*
                    FROM `ssq` as ssq
                    JOIN session ON session.session_id = ssq.session_id
                    JOIN session_times ON session_times.id = session.session_time
                    JOIN ssq_times ON ssq_times.id = ssq.ssq_time
                    WHERE session_times.name = '$session_name'"   
                    . (isset($participants) ? ("AND session.participant_id IN (" . implode(', ', $participants) . ") ") : "") .            
                    "AND session.study_id = $study_ID
                    AND ssq_times.name = '$time_name'";
                        $result = $pdo->query($sql);
                $total = 0;
                $numRows = $result->rowCount();
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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
                        $ssq_score = $SSQ_Sum * 3.74;
                $total += $ssq_score;
                }
                $avg = $numRows === 0 ? 0 : $total / $numRows;
                echo $avg; ?>,
                    <?php } ?>
            ],
        },
                <?php } ?>
    ],
    };

    if (chart) chart.destroy();
    chart = new Chart(document.querySelector('#study_graph'), {
        type: 'line',
        data,
        options: {
            plugins: {
            title: {
                display: true,
                text: '<?php $sql = "SELECT full_name FROM study WHERE study_id = $study_ID";
                    $res = $pdo->query($sql);
                    echo preg_replace("/'/", "\\'", $res->fetch(PDO::FETCH_ASSOC)['full_name']); ?>',
            },
            },
            scales: {
            y: {
                title: {
                display: true,
                color: 'black',
                text: 'Average SSQ score',
                },
            },
            x: {
                title: {
                display: true,
                color: 'black',
                text: 'Session Time',
                },
            },
            },
        },
        });
    </script>
<?php } else { ?>
    <script>

        labels = [
            <?php foreach ($ssq_times as $key => $name) { ?>
                '<?= $name ?>',
            <?php } ?>
        ];

        data = {
        labels,
        datasets: [
            <?php foreach ($sessions as $key => $time_name) { ?>
                    {
                label: '<?= $time_name ?>',
                backgroundColor: 'hsl(<?= $key * 30 ?>, 100%, 50%)',
                borderColor: 'hsl(<?= $key * 30 ?>, 100%, 50%)',
                data: [
                <?php foreach ($ssq_times as $key => $ssq_name) {
                    $sql = "SELECT ssq.*
                        FROM `ssq` as ssq
                        JOIN session ON session.session_id = ssq.session_id
                        JOIN session_times ON session_times.id = session.session_time
                        JOIN ssq_times ON ssq_times.id = ssq.ssq_time
                        WHERE ssq_times.name = '$ssq_name'"   
                        . (isset($participants) ? ("AND session.participant_id IN (" . implode(', ', $participants) . ") ") : "") .            
                        "AND session.study_id = $study_ID
                        AND session_times.name = '$time_name'";
                            $result = $pdo->query($sql);
                    $total = 0;
                    $numRows = $result->rowCount();
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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
                            $ssq_score = $SSQ_Sum * 3.74;
                    $total += $ssq_score;
                    }
                    $avg = $numRows === 0 ? 0 : $total / $numRows;
                    echo $avg; ?>,
                        <?php } ?>
                ],
            },
                    <?php } ?>
        ],
        };
        if (chart) chart.destroy();
        chart = new Chart(document.querySelector('#study_graph'), {
            type: 'line',
            data,
            options: {
                plugins: {
                title: {
                    display: true,
                    text: '<?php $sql = "SELECT full_name FROM study WHERE study_id = $study_ID";
                        $res = $pdo->query($sql);
                        echo preg_replace("/'/", "\\'", $res->fetch(PDO::FETCH_ASSOC)['full_name']); ?>',
                },
                },
                scales: {
                y: {
                    title: {
                    display: true,
                    color: 'black',
                    text: 'Average SSQ score',
                    },
                },
                x: {
                    title: {
                    display: true,
                    color: 'black',
                    text: 'SSQ Time',
                    },
                },
                },
            },
            });
    </script>
<?php } ?>
