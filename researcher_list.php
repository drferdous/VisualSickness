<?php
    include "inc/header.php";
    include_once "lib/Database.php";
    $db = Database::getInstance();
    $pdo = $db->pdo;
    if (Session::get('study_ID') == 0) {
        header('Location: study_list');
    }
    $study_ID = Session::get('study_ID');
    $affil_sql = "SELECT users.affiliation_id FROM users
                    JOIN study ON users.user_id = study.created_by
                    WHERE study.study_ID = $study_ID LIMIT 1;";
    $affil_result = $pdo->query($affil_sql);
    $role_sql = "SELECT study_role FROM researchers WHERE study_id = " . Session::get("study_ID") . " AND  researcher_id = " . Session::get("id") . " AND is_active = 1;";
    $role_result = $pdo->query($role_sql);
    $role = $role_result->fetch(PDO::FETCH_ASSOC);
    $active_sql = "SELECT is_active 
                   FROM study 
                   WHERE study_id = $study_ID;";

    $active_result = $pdo->query($active_sql);
    $active = $active_result->fetch(PDO::FETCH_ASSOC);
    
    if (!(Session::get('roleid') == 1 && $affil_result->fetch(PDO::FETCH_ASSOC)['affiliation_id'] == Session::get('affiliationid'))) {
        Session::requireResearcherOrUser($study_ID, $pdo);
    }
?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Researcher List</h1>
        <span class='float-right'>
            <?php if(isset($role['study_role']) && $role['study_role'] == 2 && $active['is_active'] == 1){ ?>
                <a href='edit_researchers' class='btn btn-primary mx-2'>Edit Researchers</a>
            <?php } ?>
            <a href='study_details' class='backBtn btn btn-primary'>Back</a>
        </span>
    </div>
    <?php
    
        $sql = "SELECT name, email, mobile, role.role FROM users JOIN researchers as researcher ON (user_id = researcher.researcher_id) JOIN user_roles as role ON (role.id = researcher.study_role) WHERE researcher.is_active = 1 AND researcher.study_id = $study_ID;";
                
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
                                <a href="mailto:<?= $row["email"] ?>"<i class="fa fa-envelope mr-2 text-decoration-none"><span class="d-none">Email <?= $row["name"] ?></span></i></a>
                                <a href="tel:<?= $row["mobile"] ?>"<i class="fa fa-phone mr-2 text-decoration-none"><span class="d-none">Call <?= $row["name"] ?></span></i></a>
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