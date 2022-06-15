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
    
    $idToSearch = $_POST["idToSearch"];
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
                JOIN Study ON RS.study_ID = Study.study_ID 
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
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <?php
                        $encrypted = Crypto::encrypt($row['study_ID'], $iv);
                    ?>
                    <div class="redirectUserBtns" data-study_ID="<?= $encrypted ?>" data-IV="<?= bin2hex($iv) ?>">
                        <a class="btn-success btn-sm" href="study_details">Study Details</a>
                            
                    <?php if ($row["is_active"] === "1" &&
                              Session::get("roleid") === "1" ||
                             ((isset($row["study_role"]) &&
                             ($row["study_role"] === "2" || $row["study_role"] === "3")))){ ?>
                            <br>
                            <br>
                            <a class="btn-success btn-sm" href="create_session">Create Session</a>
                    <?php } ?>
                    
                    <br>
                    <br>
                    <a class="btn-success btn-sm" href="session_list">Session List</a>   
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