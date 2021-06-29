<?php
include 'inc/header.php';
Session::CheckSession();

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

<?php

$sql = "SELECT * FROM Study WHERE study_ID = " . $_GET["id"] . " LIMIT 1;";
$result = mysqli_query($conn, $sql); 
            
while($row = mysqli_fetch_assoc($result)) {
    $study_ID = $row['study_ID'];
    $full_name = $row['full_name'];
    $short_name = $row['short_name'];      
    $IRB = $row['IRB'];                        
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateStudy'])) {
  $updateStudy = $users->updateStudy($study_ID, $_POST);

}

if (isset($updateStudy)) {
  echo $updateStudy;
}

 ?>

      <div class="card ">
        <div class="card-header">
          <h3><span class="float-right">Welcome! <strong>
            <span class="badge badge-lg badge-secondary text-white">
   
 </span>
          </strong></span></h3>
        </div>
        <div class="card-body pr-2 pl-2">
        <h3>Edit a Study <span class="float-right"> <a href="#" onclick="history.go(-1)" class="btn btn-primary">Reload Page</a> <a href="view_study.php" class="btn btn-primary">Study List</a> </h3>            
          <p>To see the changes made, click the reload page button ONCE or simply head back to the Study List. <span class="float-right"></p>          
        
           <form class="" action="" method="POST">
                <div class="form-group">
                  <label for="full_name">Full Name </label>
                  <input type="text" name="full_name" value="<?php echo $full_name;?>"  class="form-control">
                </div>
                <div class="form-group">
                  <label for="short_name">Short Name</label>
                  <input type="text" name="short_name" value="<?php echo $short_name;?>" class="form-control">
                </div>
                <div class="form-group">
                  <label for="IRB">IRB</label>
                  <input type="text" name="IRB" value="<?php echo $IRB;?>" class="form-control">
                </div>
                </div>
            </div>
            <div class="form-group">
                 <button type="submit" name="updateStudy" class="btn btn-success">Submit</button>
            </div>


            </form>

        </div>


      </div>




  <?php
  include 'inc/footer.php';

  ?>