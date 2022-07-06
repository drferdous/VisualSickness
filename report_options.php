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
            <select class="form-control form-select" name="session_id" id="session_id">

            <?php if($result->rowCount() === 0) { ?>
                    <option value="" disabled hidden selected>There are no sessions available!</option>
            <?php } else {?>
                <option value="" selected>All Sessions</option>
            <?php } 
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) { 
                    $enc_id = Crypto::encrypt($row['id'], $iv); ?>
                    <option value="<?= $enc_id ?>;<?= bin2hex($iv) ?>"><?php echo $row['name'];?></option>
                <?php } ?>
            </select> 
        </div>
        
        <div class="form-group">
            <label for="participant_id">Participants</label>
            <?php 
                $sql = "SELECT anonymous_name, iv, dob, participant_id 
                        FROM participants 
                        WHERE is_active = 1 AND study_id = 97";
                $result = $pdo->query($sql); ?>
            <select class="form-control form-select" name="participant_id" id="participant_id">

            <?php if($result->rowCount() === 0) { ?>
                    <option value="" disabled hidden selected>There are no participants available!</option>
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
            <select class="form-control form-select" name="SSQ_id" id="SSQ_id">
            <?php if($result->rowCount() === 0) { ?>
                    <option value="" disabled hidden selected>There are no SSQ Times available!</option>
            <?php } else {?>
                <option value="" selected>All SSQ Times</option>
            <?php } 
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {  
                    $enc_id = Crypto::encrypt($row['id'], $iv);?>
                    <option value="<?= $enc_id ?>;<?= bin2hex($iv) ?>"><?php echo $row['name'];?></option>
                <?php } ?>
            </select>
        </div>

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