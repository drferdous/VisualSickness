<?php
include 'inc/header.php';
Session::CheckLogin();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

  $register = $users->userRegistration($_POST);
}

if (isset($register)) {
  echo $register;
}


 ?>  

<?php
$servername='localhost';
$username='id16175630_admin';
$password='_TYp9G@HXf+U=OrW';
$dbname = "id16175630_visualsickness";
$conn=mysqli_connect($servername,$username,$password,"$dbname");
if(!$conn){
   die('Could not Connect My Sql:' .mysql_error());
}
?>


 <div class="card ">
   <div class="card-header">
          <h3 class='text-center'>User Registration (All Fields Required)</h3>
        </div>
        <div class="cad-body">

            <div style="width:600px; margin:0px auto">

            <form class="" action="" method="post">
                <br>
                <div class="form-group pt-3">
                  <label for="name">Your name</label>
                  <input type="text" name="name"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="username">Your username</label>
                  <input type="text" name="username"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="email">Email address</label>
                  <input type="email" name="email"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="mobile">Phone Number</label>
                  <input type="tel" name="mobile" pattern="\d*" title="Only numbers allowed" class="form-control">
                  <small>Format: 123-456-7890, don't type the hyphens!</small>
 
                </div>                
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                  <label for="confirm_password">Confirm Your Password</label>
                  <input type="password" name="confirm_password" class="form-control">
                </div>
                <div class="form-group">
                  <div class="form-group">
                    <label for="sel1">Select User Role</label>
                    <select class="form-control" name="roleid" id="roleid">
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
                  <button type="submit" name="register" class="btn btn-success">Register</button>
                </div>


            </form>
          </div>


        </div>
      </div>



  <?php
  include 'inc/footer.php';
  
  ?>