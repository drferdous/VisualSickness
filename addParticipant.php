<?php
include 'inc/header.php';
Session::CheckSession();
$sId =  Session::get('roleid');
if ($sId === '1' || $sId === '2' || $sId === '3') { ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addNewParticipant'])) {

  $userAdd = $users->addNewParticipant($_POST);
}

if (isset($userAdd)) {
  echo $userAdd;
}


 ?>


<div class="card ">
    <div class="card-header">
          <h3 class="text-center">Add New Participant</h3>
    </div>
    
    <div class="cad-body">
            <div style="width:600px; margin:0px auto">

            <form class="" action="" method="post">
                <div class="form-group pt-3">
                  <label for="anonymous_name">Participant Name</label>
                  <input type="text" name="anonymous_name"  class="form-control" id="anonymous_name" required>
                </div>
                <div class="form-group">
                  <label for="age">Age</label>
                  <input type="number" name="age"  class="form-control" id="age" min="1" step="1">
                </div>                
                <div class="form-group">
                  <label for="dob">Date of Birth</label>
                  <input type="date" name="dob"  class="form-control" id="dob" required>
                </div>
                <div class="form-group">
                  <label for="weight">Weight</label>
                  <input type="number" name="weight"  class="form-control" id="weight" min="1" step="1">
                </div>
                <div class="form-group">
                  <label for="gender">Gender</label>
                  <select class=form-control name="gender" id="gender">
                      <option selected value=""> </option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Other">Other</option>
                      <option value="Prefer Not To Say">Prefer Not To Say</option>                      
                  </select>
                </div>
                <div class="form-group">
                  <label for="ethnicity">Race/Ethnicity</label>
                  <select class=form-control name="ethnicity" id="ethnicity"> 
                     <option selected value=""> </option>    
                      <option value="aian">American Indian or Alaska Native</option>
                      <option value="asian">Asian</option>
                      <option value="black">Black or African American</option>
                      <option value="nhopi">Native Hawaiian or Other Pacific Islander</option>
                      <option value="white">White</option>
                      <option value="other">Other</option>
                      <option value="Prefer Not To Answer">Prefer Not To Answer</option> 
                </select>      
                </div>
                <div class="form-group">
                  <label for="occupation">Occupation</label>
                  <input type="text" name="occupation" class="form-control" id="occupation">
                </div>
                <div class="form-group">
                  <label for="education">Education</label>
                  <select class=form-control name="education" id="education"> 
                     <option selected value=""> </option>    
                      <option value="elementary">Elementary School</option>
                      <option value="middle">Middle School</option>
                      <option value="high">High School</option>
                      <option value="twoYear">2 Year College</option>
                      <option value="fourYear">4 Year College</option>
                      <option value="Prefer Not To Answer">Prefer Not To Answer</option> 
                </select>
                </div>
                <div class="form-group">
                  <label for="phone_no">Phone Number</label>
                  <input type="tel" name="phone_no" pattern="\d*" title="Only numbers allowed" class="form-control" id="phone_no">
                  <small>Format: 123-456-7890, don't type the hyphens!</small>
                </div>
                <div class="form-group">
                  <label for="email">Participant Email</label>
                  <input type="email" name="email" class="form-control" id="email">
                </div>
                <div class="form-group">
                  <label for="additional_info">Additional Info</label>
                  <input type="text" name="additional_info" class="form-control" id="additional_info">
                </div>
                <div class="form-group">
                  <label for="comments">Comments</label>
                  <input type="text" name="comments" class="form-control" id="comments">
                </div>
            
                <div class="form-group">
                  <button type="submit" name="addNewParticipant" class="btn btn-success">Register</button>
                </div>


            </form>
          </div>


        </div>
      </div>

<?php
}
else{
    header('Location:index');
}
?>

<?php
  include 'inc/footer.php';
?>
