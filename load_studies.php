<?php
    include_once 'classes/Crypto.php';
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["activeStatus"])){
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
    
    $sql = "SELECT Study.study_id, Study.full_name, Study.created_at, Study.is_active, RS.study_role,RS.researcher_ID 
                FROM Researcher_Study AS RS 
                JOIN study ON (RS.study_ID = study.study_id) 
                WHERE Study.created_by IN (SELECT id 
                                           FROM tbl_users 
                                           WHERE affiliationid = ". Session::get('affiliationid') .")
                AND RS.researcher_ID = ".  $idToSearch . "
                AND study.is_active = 1 
                AND RS.is_active = 1
                AND RS.researcher_ID IN(SELECT researcher_ID 
                                        FROM Researcher_Study 
                                        WHERE is_active = 1)  
                ORDER BY RS.study_role";
                
    $result = $pdo->query($sql);
    $studies = array();
    
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
            array_push($studies, $row);
        }
    }
    
    echo "hi";
    
    if (Session::get("roleid") === '1'){
        $sql = "SELECT DISTINCT study.study_id, study.full_name, study.created_at, study.is_active
                FROM Researcher_Study AS RS 
                JOIN study ON (RS.study_ID = Study.study_id) 
                WHERE Study.created_by IN (SELECT id FROM tbl_users WHERE affiliationid = ". Session::get('affiliationid') .")
                AND RS.study_ID NOT IN(SELECT study_ID FROM Researcher_Study WHERE is_active = 1 AND researcher_ID = ".  $idToSearch . ")
                AND study.is_active = 1 
                AND RS.is_active = 1";
                
        $result = $pdo->query($sql);
        
        if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                array_push($studies, $row);
            }
        }
    }
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
    if (!empty($studies)) {
        foreach ($studies as $study){ ?>
            <tr>
                <?php
                    $encrypted = Crypto::encrypt($study['study_ID'], $iv);
                    $role_sql = "SELECT study_role FROM Researcher_Study WHERE study_ID = " . $study['study_ID'] . " AND  researcher_ID = " . Session::get("id") . " AND is_active = 1;";
                
                    $role_result = $pdo->query($role_sql);
                    $role = $role_result->fetch(PDO::FETCH_ASSOC);
                    
                    $timezone = Session::get('time_offset');
                    $time = new DateTime($study['created_at']);
                    if($timezone < 0) {
                        $time->sub(new DateInterval('PT' . abs($timezone) . 'M'));
                    } else {
                        $time->add(new DateInterval('PT' . $timezone . 'M'));
                    } 
                    
                    
                ?>
                <td class="redirectUserBtns" data-study_ID="<?= $encrypted ?>" data-IV="<?= bin2hex($iv) ?>"><a class="link" href="study_details"><?php echo $study['full_name']; ?></a></td>
                <td><?php echo date_format($time,"M d, Y h:i A"); ?></td>
                <td>
                    <div class="redirectUserBtns" data-study_ID="<?= $encrypted ?>" data-IV="<?= bin2hex($iv) ?>">
                            
                    <?php if(isset($study['study_role']) && $study['is_active'] == 1){ ?>
                            <a class="btn btn-success btn-sm my-1" href="create_session">Create Session</a>
                    <?php } 
                           if(isset($study['study_role']) && $study['study_role'] != 1) {?>
                    
                            <a class="btn btn-success btn-sm my-1" href="session_list">Session List</a>  
                            <?php } ?>
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
