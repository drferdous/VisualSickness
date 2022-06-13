<?php
    include "inc/header.php";
    include_once "lib/Database.php";
    $db = Database::getInstance();
    $pdo = $db->pdo;
    if (isset($_POST['study_ID'])) $study_ID = $_POST['study_ID'];
?>

<div class="card">
    <div class="card-header">
        <h3>Participant List
            <?php
            if (isset($study_ID)) {
                echo "<span class='float-right'><a href='study_details' class='btn btn-primary redirectUser' data-study_ID=" . $study_ID . ">Back</a></span>";
            }?>
        </h3>
    </div>
    <?php
    
        $sql = "SELECT P.anonymous_name, P.dob, P.email, P.phone_no, S.full_name
                FROM Participants AS P JOIN Study AS S ON (P.study_id = S.study_ID)
                WHERE P.is_active AND P.study_id IN (SELECT study_ID
                                FROM Researcher_Study
                                WHERE researcher_ID = " . Session::get("id") . (isset($study_ID) ? " AND study_ID = $study_ID" : "") . ");";
                
        $result = $pdo->query($sql);
        
        if (!$result){
            echo $pdo->errorInfo();
        }
        
    ?>
    
    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered" id="example">
            <thead class="text-center">
                <tr>
                    <th>Participant Name</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Study Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                        <tr>
                            <td><?php echo $row["anonymous_name"]; ?></td>
                            <td><?php echo $row["dob"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["phone_no"]; ?></td>
                            <td><?php echo $row["full_name"]; ?></td>
                        </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(() => {
        $(document).on("click", "a.redirectUser", redirectUser);
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