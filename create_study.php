<?php
include 'inc/header.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_study'])) {

  $insert_study = $users->insert_study($_POST);
}

if (isset($insert_study)) {
  echo $insert_study;
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
          <h3 class='text-center'>Create a Study</h3>
        </div>
        <div class="cad-body">
        <div class="card-body">

            <form class="" action="" method="post">
                <div class="form-group">
                  <label for="full_name">Full Name (required)</label>
                  <input type="text" name="full_name"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="short_name">Short Name (required)</label>
                  <input type="text" name="short_name"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="IRB">IRB (required) </label>
                  <input type="text" name="IRB"  class="form-control">
                </div>
            <div class="form-group">
                 <button type="submit" name="insert_study" class="btn btn-success">Submit</button>
            </div>

            </form>
          </div>


        </div>
      </div>

  <?php
  include 'inc/footer.php';

  ?>