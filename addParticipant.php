<?php
include 'inc/header.php';
Session::CheckSession();
$sId =  Session::get('roleid');
if ($sId === '1' || $sId === '2' || $sId === '3') { ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addUser'])) {

  $userAdd = $users->addNewParticipant($_POST);
}

if (isset($userAdd)) {
  echo $userAdd;
}


 ?>


 <div class="card ">
   <div class="card-header">
          <h3 class='text-center'>Add New Participant</h3>
        </div>
        <div class="cad-body">



            <div style="width:600px; margin:0px auto">

            <form class="" action="" method="post">
                <div class="form-group pt-3">
                  <label for="anonymous_name">Participant Name (optional) </label>
                  <input type="text" name="anonymous_name"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="dob">Date of Birth (required) </label>
                  <input type="date" name="dob"  class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="weight">Weight (optional)</label>
                  <input type="number" name="weight"  class="form-control" min="0" step="1">
                </div>
                <div class="form-group">
                  <label for="gender">Gender (optional)</label>
                  <select class=form-control name="gender" id="gender">
                      <option selected value=""> </option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Other">Other</option>
                      <option value="Prefer Not To Say">Prefer Not To Say</option>                      
                  </select>
                </div>
                <div class="form-group">
                  <label for="ethnicity">Ethnicity (optional)</label>
                  <input type="text" name="ethnicity" class="form-control">
                </div>
                <div class="form-group">
                  <label for="occupation">Occupation (optional)</label>
                  <input type="text" name="occupation" class="form-control">
                </div>
                <div class="form-group">
                  <label for="education">Education (optional)</label>
                  <input type="text" name="education" class="form-control">
                </div>
                <div class="form-group">
                  <label for="phone_no">Phone Number (optional)</label>
                  <input type="tel" name="phone_no" pattern="\d*" title="Only numbers allowed" class="form-control">
                  <small>Format: 123-456-7890, don't type the hyphens!</small>
                </div>
                <div class="form-group">
                  <label for="email">Participant Email (optional)</label>
                  <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                  <label for="additional_info">Additional Info (optional)</label>
                  <input type="text" name="additional_info" class="form-control">
                </div>
                <div class="form-group">
                  <label for="comments">Comments (optional)</label>
                  <input type="text" name="comments" class="form-control">
                </div>
            
                <div class="form-group">
                  <button type="submit" name="addUser" class="btn btn-success">Register</button>
                </div>


            </form>
          </div>


        </div>
      </div>

<?php
}else{

  header('Location:index.php');



}
 ?>

  <?php
  include 'inc/footer.php';

  ?>
