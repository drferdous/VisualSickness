<?php
    include_once 'classes/Crypto.php';
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST)){
        header("Location: 404");
        exit();
    }
    
    include "lib/Session.php";
    include_once "lib/Database.php";
    
    Session::init();
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    $idToSearch = Session::get('id');
    $activeStatus = $_POST["activeStatus"];
    $sqlActiveStatus;
    $sql;
    
    if ($activeStatus === "active"){
        $sqlActiveStatus = " AND Study.is_active = 1;";
    }
    else{
        $sqlActiveStatus = ";";
    }
    
    if (Session::get("roleid") === '1'){
        $sql = "SELECT study_ID, full_name, created_at, is_active
                FROM Study
                WHERE created_by IN (SELECT id FROM tbl_users WHERE affiliationid = " . Session::get("affiliationid") . ")";
    }
    else{
        $sql = "SELECT Study.study_ID, Study.full_name, Study.created_at, Study.is_active, RS.study_role 
                FROM Researcher_Study AS RS 
                JOIN Study ON (RS.study_ID = Study.study_ID) 
                WHERE RS.researcher_ID = " . $idToSearch . "
                AND RS.is_active = 1";
    }
    
    $sql = $sql . $sqlActiveStatus;
    
    $result = $pdo->query($sql);
    ?>
    
<thead class="text-center">
    <tr>
        <th>Study Name</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
</thead>
    
<tbody id="study-contents">
<?php
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
            <tr>
                <?php
                    $encrypted = Crypto::encrypt($row['study_ID'], $iv);
                    $role_sql = "SELECT study_role FROM Researcher_Study WHERE study_ID = " . $row['study_ID'] . " AND  researcher_ID = " . Session::get("id") . " AND is_active = 1;";
                
                    $role_result = $pdo->query($role_sql);
                    $role = $role_result->fetch(PDO::FETCH_ASSOC);
                    
                    
                ?>
                <td class="redirectUserBtns" data-study_ID="<?= $encrypted ?>" data-IV="<?= bin2hex($iv) ?>"><a class="link" href="study_details"><?php echo $row['full_name']; ?></a></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <div class="redirectUserBtns" data-study_ID="<?= $encrypted ?>" data-IV="<?= bin2hex($iv) ?>">
                            
                    <?php if(isset($role['study_role']) && $row['is_active'] == 1){ ?>
                            <a class="btn btn-success btn-sm my-1" href="create_session">Create Session</a>
                    <?php } ?>
                    
                        <a class="btn btn-success btn-sm my-1" href="session_list">Session List</a>   
                    </div>
                </td>
            </tr>
    <?php } 
    } else {?>
        <td colspan="100%" class="text-center notFound">
            You have no studies!
        </td>
    <?php }?>
</tbody>