<?php
include 'inc/header.php';
Session::CheckSession();

if (Session::get('study_ID') == 0) {
    header('Location: study_list');
    exit();
}
Session::requirePIorRA(Session::get('study_ID'), Database::getInstance()->pdo);

if (!isset($_POST['referrer'])) {
    if (isset($_SERVER['HTTP_REFERER'])) $referrer = ltrim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '/') . '?' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    else $referrer = 'study_details';
}
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
                location.href = '<?= $referrer ?>';
            }, 1000);
        }
    </script>
<?php } ?>

<div class="card">
    <div class="card-header">
          <h1 class="text-center float-left">Add New Participant</h1>
          <span class="float-right"> <a href='<?= $referrer ?>' class="btn btn-primary backBtn">Back</a></span>
    </div>
    <div class="card-body">
            <div style="max-width:600px; margin:0px auto">
            <form class="" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
                <?php 
                    $rand = bin2hex(openssl_random_pseudo_bytes(16));
                    Session::set("post_ID", $rand);
                ?>
                <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
                <input type="hidden" name="referrer" value="<?= $referrer ?>">
                <div style="margin-block: 6px;">
                    <small class='required-msg'>
                        * Required Field
                    </small>
                </div>
                <div class="form-group pt-3">
                  <label for="anonymous_name" class="required">Participant Name</label>
                  <input type="text" name="anonymous_name" value="<?= Util::getValueFromPost('anonymous_name', $_POST); ?>" class="form-control" id="anonymous_name" required>
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
                  <select class="form-control form-select" name="gender" id="gender" size="1">
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Male' ? 'selected' : '' ?> value="Male">Male</option>
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Female' ? 'selected' : '' ?> value="Female">Female</option>
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Other' ? 'selected' : '' ?> value="Other">Other</option>
                      <option <?= Util::getValueFromPost('gender', $_POST) === 'Prefer Not To Answer' || Util::getValueFromPost('gender', $_POST) === '' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option>                      
                  </select>
                </div>
                <div class="form-group">
                  <label for="ethnicity" class="required">Race/Ethnicity</label>
                  <select class="form-control form-select" name="ethnicity" id="ethnicity" size="1">
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'American Indian or Alaska Native' ? 'selected' : '' ?> value="American Indian or Alaska Native">American Indian or Alaska Native</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'Asian' ? 'selected' : '' ?> value="Asian">Asian</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'Black or African American' ? 'selected' : '' ?> value="Black or African American">Black or African American</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'Native Hawaiian or Other Pacific Islander' ? 'selected' : '' ?> value="Native Hawaiian or Other Pacific Islander">Native Hawaiian or Other Pacific Islander</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'White' ? 'selected' : '' ?> value="White">White</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'Other1' ? 'selected' : '' ?> value="Other">Other</option>
                      <option <?= Util::getValueFromPost('ethnicity', $_POST) === 'Prefer Not To Answer' || Util::getValueFromPost('ethnicity', $_POST) === '' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option> 
                  </select>      
                </div>
                <div class="form-group">
                  <label for="occupation">Occupation</label>
                  <input type="text" name="occupation" class="form-control" id="occupation">
                </div>
                <div class="form-group">
                  <label for="education" class="required">Highest Level of Education</label>
                  <select class="form-control form-select" name="education" id="education" size="1"> 
                      <option <?= Util::getValueFromPost('education', $_POST) === 'Elementary School' ? 'selected' : '' ?> value="Elementary School">Elementary School</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'Middle School' ? 'selected' : '' ?> value="Middle School">Middle School</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'High School' ? 'selected' : '' ?> value="High School">High School</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === '2 Year College' ? 'selected' : '' ?> value="2 Year College">2 Year College</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === '4 Year College' ? 'selected' : '' ?> value="4 Year College">4 Year College</option>
                      <option <?= Util::getValueFromPost('education', $_POST) === 'Prefer Not To Answer' || Util::getValueFromPost('education', $_POST) === '' ? 'selected' : '' ?> value="Prefer Not To Answer">Prefer Not To Answer</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="phone_no">Phone Number</label>
                  <input type="tel" name="phone_no" class="form-control" id="phone_no" value="<?= Util::getValueFromPost('phone_no', $_POST); ?>">
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
                  <button id="submitBtn" type="submit" name="addNewParticipant" class="btn btn-success">Register</button>
                </div>
            </form>
        </div>


    </div>
</div>
<?php
  include 'inc/footer.php';
?>
