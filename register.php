<?php
include 'inc/header.php';
include 'database.php';
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $userRegistration = $users->userRegistration($_POST);
}
if (isset($userRegistration)) {
    echo $userRegistration;
}

?>


 <div class="card ">
   <div class="card-header">
          <h3 class='text-center'>User Registration (All Fields Required)</h3>
          <p class='text-center'>After registering, check your email for a link to create a password.</p>
        </div>
        <div class="cad-body">

            <div style="width:600px; margin:0px auto">

            <form class="" action="" method="post">
                <br>
                <div class="form-group pt-3">
                  <label for="name">Your name</label>
                  <input type="text" name="name"  class="form-control" id="name">
                </div>
                <div class="form-group">
                  <label for="username">Your username</label>
                  <input type="text" name="username"  class="form-control" id="username">
                </div>
                <div class="form-group">
                  <label for="email">Email address</label>
                  <input type="email" name="email"  class="form-control" id="email">
                </div>
                <div class="form-group">
                  <label for="mobile">Phone Number</label>
                  <input type="tel" name="mobile" pattern="\d*" title="Only numbers allowed" class="form-control" id="mobile">
                  <small>Format: 123-456-7890, don't type the hyphens!</small>
                <div class="form-group">
                  <div class="form-group">
                    <label for="sel1">Select User Role</label>
                    <select class="form-control" name="roleid" id="sel1">
                      <?php 
                      $sql = mysqli_query($conn, "SELECT id, role FROM tbl_roles WHERE id > 1");
                      while ($row = $sql->fetch_assoc()){
                      echo '<option value="'.$row['id'].'">' . $row['role'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-group">
                    <label for="selAffil">Select Affiliation</label>
                    <select class="form-control" name="affiliationid" id="selAffil">
                      <?php 
                      $sql = mysqli_query($conn, "SELECT id, name FROM Affiliation WHERE id > 0");
                      while ($row = $sql->fetch_assoc()){
                      echo '<option value="'.$row['id'].'">' . $row['name'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div> 
                <div class="form-group">
                  <button type="submit" name="register" class="btn btn-success">Register</button>
                </div>
            </form>
          </div>

        </div>
      </div>
      
<?php
  include 'inc/footer.php';
?>
