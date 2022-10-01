<?php
    include_once 'lib/Database.php';
    include_once 'classes/Crypto.php';
    include_once 'lib/Session.php';
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["study_id"])){
        header("Location: 404");
        exit();
    }

    Session::init();

    $db = Database::getInstance();
    $pdo = $db->pdo;

    if (isset($_POST['study_id']) && !empty($_POST['study_id']) && isset($_POST['iv'])) {
        $id = Crypto::decrypt($_POST['study_id'], hex2bin($_POST['iv'])); ?>
        <div class="form-group">
            <label for="session_id">Sessions</label>
            <?php 
                $sql = "SELECT name, id
                        FROM session_times WHERE study_id = " . $id . " AND is_active = 1";
                $result = $pdo->query($sql); ?>
            <select class="form-control form-select" name="session_id" id="session_id" <?= !$result->rowCount() ? "disabled" : "" ?>>

            <?php if($result->rowCount() === 0) { ?>
                    <option value="" hidden selected>There are no sessions available!</option>
            <?php } else {?>
                <option value="" selected>All Sessions</option>
            <?php } 
		$session_names = array();
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) { 
		    array_push($session_names, $row['name']); ?>
                    <option value="<?= $row['id'] ?>"><?php echo $row['name'];?></option>
                <?php } ?>
            </select> 
        </div>
        
        <div class="form-group">
            <label for="participant_id">Participants</label>
            <?php 
                $sql = "SELECT anonymous_name, iv, dob, participant_id 
                        FROM participants 
                        WHERE is_active = 1 AND study_id = $id";
                $result = $pdo->query($sql); ?>
            <select class="form-control form-select" name="participant_id" id="participant_id" <?= !$result->rowCount() ? "disabled" : "" ?>>

            <?php if($result->rowCount() === 0) { ?>
                    <option value="" hidden selected>There are no participants available!</option>
            <?php } else {?>
                <option value="" selected>All Participants</option>
            <?php } 
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {  
                    $enc_id = Crypto::encrypt($row['participant_id'], $iv_participant);
                    $iv = hex2bin($row['iv']);
                    $name = Crypto::decrypt($row['anonymous_name'], $iv); ?>
                    <option value="<?= $enc_id ?>;<?= bin2hex($iv_participant) ?>"><?php echo $name . " - " . $row['dob']; ?></option>
                <?php } ?>
            </select> 
        </div>

        <div class="form-group">
            <label for="SSQ_id">SSQ Times</label>
            <?php 
                $sql = "SELECT name, id
                        FROM ssq_times 
                        WHERE study_id = " . $id . " AND is_active = 1";
                $result = $pdo->query($sql); ?>
            <select class="form-control form-select" name="SSQ_id" id="SSQ_id" <?= !$result->rowCount() ? "disabled" : "" ?>>
            <?php if($result->rowCount() === 0) { ?>
                    <option value="" hidden selected>There are no SSQ Times available!</option>
            <?php } else {?>
                <option value="" selected>All SSQ Times</option>
            <?php }
		$time_names = array();
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		    array_push($time_names, $row['name']); ?>
                    <option value="<?= $row['id'] ?>"><?php echo $row['name'];?></option>
                <?php } ?>
            </select>
        </div>

        <div><canvas id="study_graph"></canvas></div>
        <script>
            labels = [
                <?php foreach ($session_names as $key => $name) { ?>
                    '<?= $name ?>',
                <?php } ?>
            ];
	    data = {
		labels,
		datasets: [
		    <?php foreach ($time_names as $key => $time_name) { ?>
                	{
			    label: '<?= $time_name ?>',
			    backgroundColor: 'hsl(<?= $key * 30 ?>, 100%, 50%)',
			    borderColor: 'hsl(<?= $key * 30 ?>, 100%, 50%)',
			    data: [
				<?php foreach ($session_names as $key => $session_name) {
				    $sql = "SELECT ssq.*
					    FROM `ssq` as ssq
					    JOIN session ON session.session_id = ssq.session_id
					    JOIN session_times ON session_times.id = session.session_time
					    JOIN ssq_times ON ssq_times.id = ssq.ssq_time
					    WHERE session_times.name = '$session_name'
						AND session.study_id = $id
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
	    chart = new Chart(document.querySelector('#study_graph'), {
		type: 'line',
		data,
		options: {
		    plugins: {
			title: {
			    display: true,
			    text: '<?php $sql = "SELECT full_name FROM study WHERE study_id = $id";
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

<?php }  else { ?>
    <div class="form-group">
        <label for="session_id">Sessions</label>
        <select class="form-control form-select" name="session_id" id="session_id" disabled>
            <option value="" selected hidden disabled>Session</option>
        </select>
    </div>
    <div class="form-group">
        <label for="participant_id">Participants</label>
        <select class="form-control form-select" name="participant_id" id="participant_id" disabled>
            <option value="" selected hidden disabled>Participant</option>
        </select> 
    </div>
    <div class="form-group">
        <label for="SSQ_id">SSQ Times</label>
        <select class="form-control form-select" name="SSQ_id" id="SSQ_id" disabled>
            <option value="" selected hidden disabled>SSQ Time</option>
        </select> 
    </div>
<?php } ?>