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
            <select title="<?= $result->rowCount() ? "All Sessions" : "There are no sessions available!"?>" multiple data-live-search="true" class="form-control form-select selectpicker" name="session_id" id="session_id" <?= !$result->rowCount() ? "disabled" : "" ?>>
		<?php $session_names = array();
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
            <select title="<?= $result->rowCount() ? "All Participants" : "There are no participants available!"?>" multiple data-live-search="true" class="form-control form-select selectpicker" name="participant_id" id="participant_id" <?= !$result->rowCount() ? "disabled" : "" ?>>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) {  
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
            <select title="<?= $result->rowCount() ? "All SSQ Times" : "There are no SSQ Times available!"?>" multiple data-live-search="true" class="form-control form-select selectpicker" name="SSQ_id" id="SSQ_id" <?= !$result->rowCount() ? "disabled" : "" ?>>
		<?php $time_names = array();
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		    array_push($time_names, $row['name']); ?>
                    <option value="<?= $row['id'] ?>"><?php echo $row['name'];?></option>
                <?php } ?>
            </select>
        </div>
        <?php if($_POST['button_id'] == 'session_time') { ?>
            <span class="float-left">
                <button type="button" id="session_time" class="btn btn-outline-primary active">Session Time</button>
                <button type="button" id="ssq_time" class="btn btn-outline-primary">SSQ Time</button>
            </span>
        <?php } else {?>
            <span class="float-left">
                <button type="button" id="session_time" class="btn btn-outline-primary">Session Time</button>
                <button type="button" id="ssq_time" class="btn btn-outline-primary active">SSQ Time</button>
            </span>
        <?php } ?>


        <div><canvas id="study_graph"></canvas></div>
        <script>
	    $('.selectpicker').selectpicker();

	    if (selectFuncs.length) selectFuncs.forEach((f) => $(document).off('click', f));
            selectFuncs = [
                function (e) {
                    if ($(e.target).is('#session_id.selectpicker ~ *, #session_id.selectpicker ~ * *')) {
                        session_selected = !session_selected;
                    } else session_selected = false;
                    const menu = $('#session_id.selectpicker').siblings('.dropdown-menu');
                    menu.toggleClass('show', session_selected);
                    menu.find('.inner').toggleClass('show', session_selected);
                    menu.find('.check-mark').attr('class', 'check-mark bs-ok-default');
                }, function (e) {
                    if (
                        $(e.target).is(
                            '#participant_id.selectpicker ~ *, #participant_id.selectpicker ~ * *'
                        )
                    ) {
                        part_selected = !part_selected;
                    } else part_selected = false;
                    const menu = $('#participant_id.selectpicker').siblings('.dropdown-menu');
                    menu.toggleClass('show', part_selected);
                    menu.find('.inner').toggleClass('show', part_selected);
                    menu.find('.check-mark').attr('class', 'check-mark bs-ok-default');
                }, function (e) {
                    if (
                        $(e.target).is(
                            '#SSQ_id.selectpicker ~ *, #SSQ_id.selectpicker ~ * *'
                        )
                    ) {
                        ssq_selected = !ssq_selected;
                    } else ssq_selected = false;
                    const menu = $('#SSQ_id.selectpicker').siblings('.dropdown-menu');
                    menu.toggleClass('show', ssq_selected);
                    menu.find('.inner').toggleClass('show', ssq_selected);
                    menu.find('.check-mark').attr('class', 'check-mark bs-ok-default');
                }
            ];
	    selectFuncs.forEach((f) => $(document).on('click', f));

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
	    /*chart = new Chart(document.querySelector('#study_graph'), {
		type: 'line',
		data,
		options: {
		    plugins: {
			title: {
			    display: true,
			    text: '<?php /*$sql = "SELECT full_name FROM study WHERE study_id = $id";
					$res = $pdo->query($sql);
					echo preg_replace("/'/", "\\'", $res->fetch(PDO::FETCH_ASSOC)['full_name']); */?>',
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
	    });*/
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
