<?php
include 'inc/header.php';
include_once 'lib/Database.php';
Session::CheckSession();
$db = Database::getInstance();
$pdo = $db->pdo;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addResearcher'])) {
    $addResearcher = $studies->addResearcher($_POST);
}

if (isset($addResearcher)) {
  echo $addResearcher;
}
?>

<div class="card">
    <div class="card-header">
        <h3><span class="float-left">Add A Researcher</span>
            <a href="study_details" 
               class="btn btn-primary float-right redirectUser" 
               data-study_ID= "<?php echo $_POST['study_ID']; ?>">
               Back
            </a>
        </h3>  
    </div>
    <div class="card-body pr-2 pl-2">
        <form class="" action="" method="post">
            <div class="form-group">
                <div class="form-group">
                    <input type="hidden" name="study_ID" value="<?php echo $_POST['study_ID']; ?>">
                    <!-- <br> -->
                    <label for="researcher_ID" class="required">Add A Member</label>
                    <select class="form-control" name="researcher_ID" id="researcher_ID">
                        <option value="" selected hidden disabled>Member Name</option>
                        <?php
                            $sql = "SELECT id, name, email
                                    FROM tbl_users
                                    WHERE NOT id IN (SELECT researcher_ID 
                                                     FROM Researcher_Study
                                                     WHERE study_ID = " . $_POST['study_ID'] . ")
                                    AND isActive = 1;";
                            $result = $pdo->query($sql);
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row["name"] . " (" . $row["email"] . ")"; ?></option>
                        <?php } ?>
                    </select>
                    <br>
                    <label for="study_role" class="required">Select Study Role</label>
                    <select class="form-control" name="study_role" id="study_role">
                        <option value="" selected hidden disabled>Study Role</option>
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
     $(document).on("click", "a.redirectUser", redirectUser);
     
     function redirectUser(){
         let form = document.createElement("form");
         let hiddenInput = document.createElement("input");
         
         form.setAttribute("method", "POST");
         form.setAttribute("action", $(this).attr("href"));
         form.setAttribute("style", "display: none");
         
         hiddenInput.setAttribute("type", "hidden");
         hiddenInput.setAttribute("name", "study_ID");
         hiddenInput.setAttribute("value", $(this).attr("data-study_ID"));
         
         form.appendChild(hiddenInput);
         document.body.append(form);
         form.submit();
         
         return false;
     }
     
     $(document).ready(function() {
         $('#researcher_ID').change(function() {
             var researcher_ID = $(this).val();
            $.ajax({
              url :"researcherrole",
              type:"POST",
              cache:false,
              data:{researcher_ID:researcher_ID},
              success:function(data){
                  $("#study_role").html(data);
              }
            });	
         });
     });
 </script>

<?php
  include 'inc/footer.php';
?>