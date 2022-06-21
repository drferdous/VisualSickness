<?php
include 'inc/header.php';
Session::CheckSession();

if (Session::get('study_ID') == 0) {
    header('Location: view_study');
    exit();
}
Session::requirePIorRA(Session::get('study_ID'), Database::getInstance()->pdo);

if (!isset($_POST['referrer'])) $referrer = ltrim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '/');
else $referrer = $_POST['referrer'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addNewParticipant']) && Session::CheckPostID($_POST)){
    $userAdd = $studies->addNewParticipant($_POST);
}

if (isset($userAdd)) {
  echo $userAdd;?>
    <script type="text/javascript">
        const divMsg = document.getElementById("flash-msg");
        if (divMsg.classList.contains("alert-success")){
            setTimeout(function(){
                location.href = '<?php echo $referrer; ?>';
            }, 1000);
        }
    </script>
<?php } ?>
<div class="card">
    <div class="card-header">
          <h3 class="text-center float-left">Add New Participant</h3>
          <span class="float-right"> <a href='<?= $referrer ?>' class="btn btn-primary redirectUser">Back</a></span>
    </div>
    <div class="card-body">
            <div style="max-width:600px; margin:0px auto">
            <form class="" action="" method="post">
                <?php 
                    $rand = bin2hex(openssl_random_pseudo_bytes(16));
                    Session::set("post_ID", $rand);
                ?>
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <input type="hidden" name="referrer" value="<?= $referrer ?>">
                <div style="margin-block: 6px;">
                    <small style='color: red'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group pt-3">
                  <label for="anonymous_name" class="required">Participant Name</label>
                  <input type="text" name="anonymous_name" value="<?= Util::getValueFromPost('anonymous_name', $_POST); ?>" class="form-control" id="anonymous_name" required>
                </div>
                <div class="form-group">
                  <label for="age" class="required">Age</label>
                  <input type="number" name="age" value="<?= Util::getValueFromPost('age', $_POST); ?>" class="form-control" id="age" min="1" step="1">
                </div>                
                <div class="form-group">
                  <label for="dob" class="required">Date of Birth</label>
                  <input type="date" name="dob" value="<?= Util::getValueFromPost('dob', $_POST); ?>" class="form-control" id="dob" required>
                </div>
                <div class="form-group">
                  <label for="weight">Weight</label>
                  <input type="number" name="weight" value="<?= Util::getValueFromPost('weight', $_POST); ?>" class="form-control" id="weight" min="1" step="1">
                </div>
                <div class="form-group">
                  <label for="gender" class="required">Gender</label>
                  <select class=form-control name="gender" id="gender" required>
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Male' ? 'selected' : '' ?> value="Male">Male</option>
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Female' ? 'selected' : '' ?> value="Female">Female</option>
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Other' ? 'selected' : '' ?> value="Other">Other</option>
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Prefer Not To Answer' || Util::getValueFromPost('gender', $_POST) === '' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option>                      
                  </select>
                </div>
                <div class="form-group">
                  <label for="ethnicity" class="required">Race/Ethnicity</label>
                  <select class=form-control name="ethnicity" id="ethnicity" required>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'aian' ? 'selected' : '' ?> value="aian">American Indian or Alaska Native</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'asian' ? 'selected' : '' ?> value="asian">Asian</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'black' ? 'selected' : '' ?> value="black">Black or African American</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'nhopi' ? 'selected' : '' ?> value="nhopi">Native Hawaiian or Other Pacific Islander</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'white' ? 'selected' : '' ?> value="white">White</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'other' ? 'selected' : '' ?> value="other">Other</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'Prefer Not To Answer' || Util::getValueFromPost('ethnicity', $_POST) === '' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option> 
                  </select>      
                </div>
                <div class="form-group">
                  <label for="occupation">Occupation</label>
                  <input type="text" name="occupation" class="form-control" id="occupation">
                </div>
                <div class="form-group">
                  <label for="education" class="required">Education</label>
                  <select class=form-control name="education" id="education" required> 
                      <option <?= Util::getValueFromPost('education', $_POST) === 'elementary' ? 'selected' : '' ?> value="elementary">Elementary School</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'middle' ? 'selected' : '' ?> value="middle">Middle School</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'high' ? 'selected' : '' ?> value="high">High School</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'twoYear' ? 'selected' : '' ?> value="twoYear">2 Year College</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'fourYear' ? 'selected' : '' ?> value="fourYear">4 Year College</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'Prefer Not To Answer' || Util::getValueFromPost('education', $_POST) === '' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="phone_no">Phone Number</label>
                  <input type="tel" name="phone_no" pattern="\d*" title="Only numbers allowed" class="form-control" id="phone_no" value="<?= Util::getValueFromPost('phone_no', $_POST); ?>">
                  <small>Format: 123-456-7890, don't type the hyphens!</small>
                </div>
                <div class="form-group">
                  <label for="email">Participant Email</label>
                  <input type="email" name="email" class="form-control" id="email" value="<?= Util::getValueFromPost('email', $_POST); ?>">
                </div>
                <div class="form-group">
                  <label for="comments">Comments</label>
                  <input type="text" name="comments" class="form-control" id="comments" value="<?= Util::getValueFromPost('comments', $_POST); ?>">
                </div>
                <div class="form-group">
                  <button type="submit" name="addNewParticipant" class="btn btn-success">Register</button>
                </div>
            </form>
        </div>


    </div>
</div>

<?php
  include 'inc/footer.php';
?>
