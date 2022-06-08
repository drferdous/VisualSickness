<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeResearcher'])) {
    $removeResearcher = $studies->removeResearcher($_POST);
    echo $removeResearcher;
}
if (isset($addResearcher)) {
  echo $removeResearcher;
}
?>
 
<div class="card">
    <div class="card-header">
        <h3>Remove A Researcher<span class="float-right"><a href="study_details" class="btn btn-primary" data-study_ID="<?php echo $_POST['study_ID']; ?>">Back</a></span></h3> 
    </div>
    <div class="card-body pr-2 pl-2">
        <form class="" action="" method="post">
            <div class="form-group">
                <div class="form-group">
                    <label for="researcher_ID">Remove a Member:</label>
                    <select class="form-control" name="researcher_ID" id="researcher_ID">
                        <option value="" disabled hidden selected>Member Name</option>
                    <?php
                        $sql = "SELECT id, name
                                FROM tbl_users
                                WHERE id IN (SELECT researcher_ID
                                             FROM Researcher_Study
                                             WHERE study_ID = " . $_POST['study_ID'] . "
                                             AND NOT researcher_ID IN (SELECT created_by
                                                                       FROM Study
                                                                       WHERE study_ID = " . $_POST['study_ID'] . ")
                                             AND NOT researcher_ID = " . Session::get('id') . ");";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <input type="hidden" name="study_ID" value="<?php echo $_POST['study_ID']; ?>">
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