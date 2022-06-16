<?php
    include "inc/header.php";
    include_once "lib/Database.php";
    $db = Database::getInstance();
    $pdo = $db->pdo;
    if (Session::get('study_ID') == 0) {
        header('Location: view_study');
    }
    $study_ID = Session::get('study_ID');
    Session::requireResearcherOrUser($study_ID, $pdo);
?>

<div class="card">
    <div class="card-header">
        <h3>Researcher List
            <?php
            if (isset($study_ID)) {
                echo "<span class='float-right'><a href='study_details' class='btn btn-primary redirectUser' data-study_ID=" . $study_ID . ">Back</a></span>";
            }?>
        </h3>
    </div>
    <?php
    
        $sql = "SELECT user.name, user.email, user.mobile, role.role FROM tbl_users as user JOIN Researcher_Study as study ON (user.id = study.researcher_ID) JOIN tbl_roles as role ON (role.id = study.study_role) WHERE study.is_active = 1 AND study.study_ID = $study_ID;";
                
        $result = $pdo->query($sql);
        
        if (!$result){
            echo $pdo->errorInfo();
        }
        
    ?>
    
    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered" id="example">
            <thead class="text-center">
                <tr>
                    <th>Researcher Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                        <tr>
                            <td><?php echo $row["name"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["mobile"]; ?></td>
                            <td><?php echo $row["role"]; ?></td>
                        </tr>
                <?php }
                } else { ?>
                    <tr>
                        <td colspan='100%' class="text-center notFound">No researchers found for this study</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(() => {
        $(document).on("click", "a.redirectUser", redirectUser);
        if (!document.querySelector('.notFound')) $('#example').DataTable();
    });
    
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
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
</script>
<?php
  include "inc/footer.php";
?>