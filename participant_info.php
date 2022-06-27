<?php
include_once 'lib/Database.php';
include_once 'classes/Crypto.php';
include_once 'lib/Session.php';
Session::init();
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['participant_ID'])){
    header("Location: 404");
    exit();
}

$db = Database::getInstance();
$pdo = $db->pdo;

if (isset($_POST['participant_ID']) && !empty($_POST['participant_ID']) && isset($_POST['iv'])) {
    $id = Crypto::decrypt($_POST['participant_ID'], hex2bin($_POST['iv']));
    $sql = "SELECT * FROM participants
            JOIN demographics ON participants.demographics_id = demographics.demographics_id
            WHERE participant_id = $id
            AND is_active = 1;";
    $result = $pdo->query($sql);
    if ($result->rowCount()) {
        $row = $result->fetch(PDO::FETCH_ASSOC); ?>
        <div class="form-group">
            <label for="name" class="required">Participant Name</label>
            <input name="name" class="form-control" id="name" value="<?= Crypto::decrypt($row['anonymous_name'], hex2bin($row['iv'])) ?>" required>
        </div>
        <div class="form-group">
            <label for="dob" class="required">Date of Birth</label>
            <input type="date" name="dob" class="form-control" id="dob" value="<?= $row['dob'] ?>" required>
        </div>
        <div class="form-group">
            <label for="weight">Weight</label>
            <input type="number" name="weight" class="form-control" id="weight" value="<?= $row['weight'] ?>">
        </div>
        <div class="form-group">
            <label for="gender" class="required">Gender</label>
            <select class="form-control" name="gender" id="gender" required size="1">
                <option <?= $row['gender'] === 'Male' ? 'selected' : '' ?> value="Male">Male</option>
                <option <?= $row['gender'] === 'Female' ? 'selected' : '' ?> value="Female">Female</option>
                <option <?= $row['gender'] === 'Other' ? 'selected' : '' ?> value="Other">Other</option>
                <option <?= $row['gender'] === 'Prefer Not To Answer' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option>                      
            </select>
        </div>
        <div class="form-group">
            <label for="race_ethnicity" class="required">Race/Ethnicity</label>
            <select class="form-control" name="race_ethnicity" id="race_ethnicity" required size="1">
                <option <?= $row['race_ethnicity'] === 'American Indian or Alaska Native' ? 'selected' : '' ?> value="American Indian or Alaska Native">American Indian or Alaska Native</option>
                <option <?= $row['race_ethnicity'] === 'Asian' ? 'selected' : '' ?> value="Asian">Asian</option>
                <option <?= $row['race_ethnicity'] === 'Black or African American' ? 'selected' : '' ?> value="Black or African American">Black or African American</option>
                <option <?= $row['race_ethnicity'] === 'Native Hawaiian or Other Pacific Islander' ? 'selected' : '' ?> value="Native Hawaiian or Other Pacific Islander">Native Hawaiian or Other Pacific Islander</option>
                <option <?= $row['race_ethnicity'] === 'White' ? 'selected' : '' ?> value="White">White</option>
                <option <?= $row['race_ethnicity'] === 'Other1' ? 'selected' : '' ?> value="Other">Other</option>
                <option <?= $row['race_ethnicity'] === 'Prefer Not To Answer' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option>
            </select>
        </div>
        <div class="form-group">
            <label for="occupation">Occupation</label>
            <input name="occupation" class="form-control" id="occupation" value="<?= $row['occupation'] ?>">
        </div>
        <div class="form-group">
            <label for="education" class="required">Highest Level of Education</label>
            <select class="form-control" name="education" id="education" required size="1">
                <option <?= $row['education'] === 'Elementary School' ? 'selected' : '' ?> value="Elementary School">Elementary School</option>
                <option <?= $row['education'] === 'Middle School' ? 'selected' : '' ?> value="Middle School">Middle School</option>
                <option <?= $row['education'] === 'High School' ? 'selected' : '' ?> value="High School">High School</option>
                <option <?= $row['education'] === '2 Year College' ? 'selected' : '' ?> value="2 Year College">2 Year College</option>
                <option <?= $row['education'] === '4 Year College' ? 'selected' : '' ?> value="4 Year College">4 Year College</option>
                <option <?= $row['education'] === 'Prefer Not To Answer' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option>
            </select>
        </div>
        <div class="form-group">
            <label for="phone_no">Phone Number</label>
            <input type="tel" name="phone_no" pattern="\d*" title="Only numbers allowed" class="form-control" id="phone_no" value="<?= $row['phone_no'] ?>">
            <small>Format: 123-456-7890, don't type the hyphens!</small>
        </div>
        <div class="form-group">
            <label for="email">Participant Email</label>
            <input type="email" name="email" class="form-control" id="email" value="<?= $row['email'] ?>">
        </div>
        <div class="form-group">
            <label for="comments">Comments</label>
            <input name="comments" class="form-control" id="comments" value="<?= $row['comments'] ?>">
        </div>
    <?php } else { ?>
        <script>location.href = 'participant_list';</script>
    <?php }
} ?>