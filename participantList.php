<?php
    include "inc/header.php";
    include_once "lib/Database.php";
    $db = Database::getInstance();
    $pdo = $db->pdo;
    if (isset($_GET['forStudy']) && $_GET['forStudy'] && Session::get('study_ID') != 0) $study_ID = Session::get('study_ID');
    
    if (isset($study_ID)) {
        $access_sql = "SELECT study_role FROM Researcher_Study
                    WHERE researcher_ID = " . Session::get("id") . "
                    AND study_ID = $study_ID;";
        $access_result = $pdo->query($access_sql);
        if (Session::get('roleid') == 1 && !$access_result->rowCount()) {
            header('Location: participantList');
            exit();
        }
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
    
        $sql = "SELECT P.anonymous_name, P.dob, P.email, P.phone_no, S.full_name, P.iv
                FROM Participants AS P 
                JOIN Study AS S ON (P.study_id = S.study_ID)
                WHERE P.is_active = 1 
                AND P.study_id IN (SELECT study_ID
                                   FROM Researcher_Study
                                   WHERE researcher_ID = " . Session::get("id") . (isset($study_ID) ? " AND study_ID = $study_ID" : "") . ");";
        $result = $pdo->query($sql);
        
        if (!$result){
            echo $pdo->errorInfo();
        }
        
    ?>
    
    <div class="card-body pr-2 pl-2">
        <table class="table table-striped table-bordered table-responsive" style="display: table" id="example">
            <thead class="text-center">
                <tr>
                    <th>Name</th>
                    <th>DOB</th>
                    <th>Study</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                        <?php
                        $iv = hex2bin($row['iv']);
                        $name = Crypto::decrypt($row['anonymous_name'], $iv); 
                        ?>
                        <tr>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $row["dob"]; ?></td>
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