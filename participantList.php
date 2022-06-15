<?php
    include "inc/header.php";
    include_once "lib/Database.php";
    $db = Database::getInstance();
    $pdo = $db->pdo;
    if (isset($_GET['forStudy']) && $_GET['forStudy'] && Session::get('study_ID') != 0) $study_ID = Session::get('study_ID');
    
    if (Study::get("study_ID") == 0){
        header("Location: view_study");
        exit();
    }
?>

<div class="card">
    <div class="card-header">
        <h3>Participant List
            <?php if (isset($study_ID)) { ?>
                <span class='float-right'>
                    <a href='study_details' 
                       class='btn btn-primary redirectUser'> 
                       Back
                    </a>
                </span>
            <?php } ?>
        </h3>
    </div>
    <?php
    
        $sql = "SELECT P.anonymous_name, P.dob, P.email, P.phone_no, S.full_name
                FROM Participants AS P 
                JOIN Study AS S ON (P.study_id = S.study_ID)
                WHERE P.is_active 
                AND P.study_id IN (SELECT study_ID
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
                <?php 
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                        <tr>
                            <td><?php echo $row["anonymous_name"]; ?></td>
                            <td><?php echo $row["dob"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["phone_no"]; ?></td>
                            <td><?php echo $row["full_name"]; ?></td>
                        </tr>
                <?php }
                } else { ?>
                    <tr>
                        <td colspan='100%' class="text-center notFound">No participants found for this study</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(() => {
        if (!document.querySelector('.notFound')) $('#example').DataTable();
    });
</script>
<?php
  include "inc/footer.php";
?>