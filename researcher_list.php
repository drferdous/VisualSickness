<?php
    include "inc/header.php";
    include_once "lib/Database.php";
    $db = Database::getInstance();
    $pdo = $db->pdo;
    if (Session::get('study_ID') == 0) {
        header('Location: view_study');
    }
    $study_ID = Session::get('study_ID');
    $affil_sql = "SELECT users.affiliationid FROM tbl_users AS users
                    JOIN Study AS study ON users.id = study.created_by
                    WHERE study.study_ID = $study_ID LIMIT 1;";
    $affil_result = $pdo->query($affil_sql);
    if (!(Session::get('roleid') == 1 && $affil_result->fetch(PDO::FETCH_ASSOC)['affiliationid'] == Session::get('affiliationid'))) {
        Session::requireResearcherOrUser($study_ID, $pdo);
    }
?>

<div class="card">
    <div class="card-header">
        <h3 class="float-left">Researcher List</h3>
        <?php
        if (isset($study_ID)) {
            echo "<span class='float-right'><a href='study_details' class='btn btn-primary'>Back</a></span>";
        }?>
    </div>
    <?php
    
        $sql = "SELECT user.name, user.email, user.mobile, role.role FROM tbl_users as user JOIN Researcher_Study as study ON (user.id = study.researcher_ID) JOIN tbl_roles as role ON (role.id = study.study_role) WHERE study.is_active = 1 AND study.study_ID = $study_ID;";
                
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
                    <th>Role</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                        <tr>
                            <td><?= $row["name"] ?></td>
                            <td><?= $row["role"] ?></td>
                            <td>
                                <a href="mailto:<?= $row["email"] ?>"<i class="fa fa-envelope mr-2 text-decoration-none" style="color:#519AF8"></i></a>
                                <a href="tel:<?= $row["mobile"] ?>"<i class="fa fa-phone mr-2 text-decoration-none" style="color:#D22B2B"></i></a>
                            </td>
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
        if (!document.querySelector('.notFound')) $('#example').DataTable();
        div = document.createElement('div');
        div.style.overflowX = 'auto';
        const table = document.querySelector('table');
        const parent = table.parentElement
        const index = [...parent.children].indexOf(table);
        div.appendChild(table.cloneNode(true));
        parent.replaceChild(div, table);
    });
</script>
<?php
  include "inc/footer.php";
?>