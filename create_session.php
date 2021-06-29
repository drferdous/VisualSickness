<?php
include 'inc/header.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_session'])) {

  $insert_session = $users->insert_session($_POST);
}

if (isset($insert_session)) {
  echo $insert_session;
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
          <h3 class='text-center'>Create a Session</h3>
        </div>
        <div class="cad-body">
        <div class="card-body">

            <form class="" action="" method="post">
            <div class="form-group">
                 <button type="submit" name="insert_session" class="btn btn-success">Submit</button>
            </div>

            </form>
          </div>


        </div>
      </div>

  <?php
  include 'inc/footer.php';

  ?>