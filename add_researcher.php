<?php
include 'inc/header.php';
include 'database.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addResearcher'])) {
    $addResearcher = $users->addResearcher($_POST);
}

if (isset($addResearcher)) {
  echo $addResearcher;
}


 ?>


      <div class="card ">
        <div class="card-header">
   
 </span>
        <h3>Add New Researcher <span class="float-right"> <a href="remove_researcher.php" class="btn btn-primary">Remove A Researcher</a> </h3> 
        </div>
        <div class="card-body pr-2 pl-2">
           <form class="" action="" method="post">
            <div class="form-group">
                <div class="form-group">
                  <label for="study_id">Choose a Study:</label>
                      <select class="form-control" name="study_ID" id="study_ID">
                      <?php 
                      $sql = mysqli_query($conn, "SELECT study_ID, full_name, created_by FROM Study WHERE created_by = " . Session::get('id'));
                      while ($row = $sql->fetch_assoc()){
                     echo '<option value="'.$row['study_ID'].'">' . $row['full_name'] . "</option>";
                      }
                      ?>
                    </select>
                    <br>
                  <label for="researcher_ID">Add A Member:</label>
                      <select class="form-control" name="researcher_ID" id="researcher_ID">
                          <option value = "">Member Name</option>
                      <?php 
                      $sql = mysqli_query($conn, "SELECT id, username FROM tbl_users");
                      while ($row = $sql->fetch_assoc()){
                     echo '<option value="'.$row['id'].'">' . $row['username'] . "</option>";
                      }
                      ?>
                    </select>
                    <br>
                    <label for="study_role">Select Study Role:</label>
                    <select class="form-control" name="study_role" id="study_role">
                        <option value="">Study Role</option>
                      <?php 
                      // $sql = mysqli_query($conn, "SELECT id, role FROM tbl_roles WHERE id > 1");
                      // while ($row = $sql->fetch_assoc()){
                          
                      // echo '<option value="'.$row['id'].'">' . $row['role'] . "</option>";
                      // }
                      ?>       
                    </select> 
                </div>
            </div>
            <div class="form-group">
                 <button type="submit" name="addResearcher" class="btn btn-success">Submit</button>
            </div>


            </form>

        </div>


      </div>
      
 <script type="text/javascript">
      // Researcher ID dependent ajax
      $("#researcher_ID").on("change",function(){
        var researcher_ID = $(this).val();
        $.ajax({
          url :"researcherrole.php",
          type:"POST",
          cache:false,
          data:{researcher_ID:researcher_ID},
          success:function(data){
              $("#study_role").html(data);
          }
        });			
 </script> 
 
 <script type="text/javascript">
     $(document).ready(function() {
         $('#researcher_ID').change(function() {
             alert($(this).val());
         });
     });
 </script>

  <?php
  include 'inc/footer.php';

  ?>