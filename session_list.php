<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;


Session::CheckSession();

if (isset($_POST["study_ID"]) && isset($_POST["iv"])){
    $iv = hex2bin($_POST['iv']);
    $decrypted = Crypto::decrypt($_POST['study_ID'], $iv);
    Session::setStudyId(intval($decrypted), $pdo);
    header('Location: session_list');
    exit();
}
Session::requireStudyID();
Session::requireResearcherOrUser(Session::get('study_ID'), $pdo);
?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Session List</h1> 
        <span class="float-right"> <a href="study_list" class="backBtn btn btn-primary">Back</a></span>
    </div>
        
    <div class="card-body pr-2 pl-2 overflow-hidden">
    <?php
        $sql = "SELECT DISTINCT session.participant_id FROM session
                JOIN participants ON session.participant_id = participants.participant_id
                JOIN session_times AS time ON time.id = session.session_time
                WHERE session.study_id = " . Session::get('study_ID') . "
                AND participants.is_active = 1
                AND session.is_active = 1
                AND time.is_active = 1";
        $result = $pdo->query($sql);
      ?>
        <br />
            <table class="table table-striped table-bordered" id="example">
               
                <thead class="text-center">
                    <tr>
                        <th>Participant Name</th>
                        <th>Session Names</th>
                    </tr>
                </thead>
                 
                <tbody>
             <?php if ($result->rowCount() > 0){ ?>
                <?php
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <?php if (isset($row['participant_id'])) {
                            $sql_users = "SELECT anonymous_name, iv, participant_id FROM participants WHERE participant_id = " . $row['participant_id'] . " LIMIT 1;";
                            $result_users = $pdo->query($sql_users);
                            
                            if (!$result_users){
                                echo $pdo->errorInfo($conn);
                            }
                            
                            $row_users = $result_users->fetch(PDO::FETCH_ASSOC);
                            
                            $iv = hex2bin($row_users['iv']);
                            $name = Crypto::decrypt($row_users['anonymous_name'], $iv); ?>

                            <td><a class='redirectUser' href='participant_details' data-participant_ID="<?= Crypto::encrypt($row_users['participant_id'], $part_iv) ?>" data-IV="<?= bin2hex($part_iv) ?>"><?= $name ?></a></td>
                        <?php } else { ?>
                            <td>-</td>
                        <?php } ?>
                        <td>
                            <?php
                            $timings_sql = "SELECT time.name AS session_time, S.session_id FROM session AS S
                                            JOIN session_times AS time ON S.session_time = time.id
                                            WHERE S.participant_id = " . $row['participant_id'] . "
                                            AND S.is_active = 1
                                            AND time.is_active = 1
                                            ORDER BY S.start_time";
                            $timings_result = $pdo->query($timings_sql);
                            $first = true;
                            while ($session_row = $timings_result->fetch(PDO::FETCH_ASSOC)) {
                                if (!$first) echo ', '; ?><a class="redirectUser" href="session_details" data-session_ID="<?= Crypto::encrypt($session_row['session_id'], $session_iv) ?>" data-IV="<?= bin2hex($session_iv) ?>"><?= $session_row['session_time'] ?></a><?php $first = false;
                            } ?>
                        </td>
                    </tr>
                <?php } ?>
                 <?php } else { ?>
            <td colspan="100%" class="text-center notFound">
                This study has no sessions!
            </td>
            <?php } ?>
                </tbody>
            </table>
    </div>
</div>

<script>
    $(document).ready(function(){
        if (!document.querySelector('.notFound')) $('#example').DataTable();
        $(document).on("click", "a.redirectUser", redirectUser);
        div = document.createElement('div');
        div.style.overflowX = 'auto';
        const table = document.querySelector('table');
        const parent = table.parentElement
        const index = [...parent.children].indexOf(table);
        div.appendChild(table.cloneNode(true));
        parent.replaceChild(div, table);
        
        function redirectUser(){
            let form = document.createElement("form");
            let hiddenInput;
            
            form.setAttribute("method", "POST");
            form.setAttribute("action", $(this).attr("href"));
            form.setAttribute("style", "display: none");
            if ($(this).attr('data-session_ID')) {
                let hiddenInput = document.createElement("input");
                hiddenInput.setAttribute("type", "hidden");
                hiddenInput.setAttribute("name", "session_ID");
                hiddenInput.setAttribute("value", $(this).attr("data-session_ID"));
                form.appendChild(hiddenInput);
            }
            if ($(this).attr('data-participant_ID')) {
                let hiddenInput = document.createElement("input");
                hiddenInput.setAttribute("type", "hidden");
                hiddenInput.setAttribute("name", "participant_ID");
                hiddenInput.setAttribute("value", $(this).attr("data-participant_ID"));
                form.appendChild(hiddenInput);
            }
            
            
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "iv");
            hiddenInput.setAttribute("value", $(this).attr("data-IV"));
            
            form.appendChild(hiddenInput);
            document.body.appendChild(form);
            form.submit();
            
            return false;
        };
    });
</script>

<?php
  include 'inc/footer.php';
?>