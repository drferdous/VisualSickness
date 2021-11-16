<?php
include 'inc/header.php';
include 'database.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeResearcher'])) {
    $removeResearcher = $users->removeResearcher($_POST);
    echo $removeResearcher;
}

if (isset($addResearcher)) {
  echo $removeResearcher;
}

 ?>
 
      <div class="card ">
        <div class="card-header">
   
 </span>
        <h3>Remove A Researcher </h3> 
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
                    
                  <label for="researcher_ID">Remove A Member:</label>
                      <select class="form-control" name="researcher_ID" id="researcher_ID">
                        <option value="" selected hidden disabled>Member Name</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                 <button type="submit" name="removeResearcher" class="btn btn-success">Submit</button>
            </div>


            </form>

        </div>


      </div>
      
 <script type="text/javascript">
    $(document).on("click", "a", redirectUser);
    
    function redirectUser(){
        let form = document.createElement("form");
        let hiddenInput = document.createElement("input");
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "study_ID");
        hiddenInput.setAttribute("value", $(this).attr("data-study_ID"));
        
        form.append(hiddenInput);
        document.body.append(form);
        
        form.submit();
        
        return false;
    }
    
    $(document).ready(function() {
        $('#study_ID').change(function() {
            var study_ID = $(this).val();
            $.ajax({
              url :"researcherremoval",
              type:"POST",
              cache:false,
              data:{study_ID:study_ID},
              success:function(data){
                  $("#researcher_ID").html(data);
              }
            });	
        });
    });
 </script>      


  <?php
  include 'inc/footer.php';

  ?>