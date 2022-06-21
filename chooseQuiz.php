<?php
    include 'inc/header.php';
    include_once 'lib/Database.php';
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    if (Session::get('study_ID') == 0) {
        header('Location: view_study');
        exit();
    }
    
    Session::CheckSession();
    Session::requireResearcherOrUser(Session::get('study_ID'), $pdo);
    $active_sql = "SELECT is_active FROM Study WHERE study_ID = " . Session::get('study_ID') . " LIMIT 1;";
    $res = $pdo->query($active_sql);
    if ($res->fetch(PDO::FETCH_ASSOC)['is_active'] == 0) {
        header('Location: view_study');
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['take-ssq-btn']) && Session::CheckPostID($_POST)) {
        $takeSSQMessage = $studies->takeSSQ($_POST);
        if (isset($takeSSQMessage)) {
            echo $takeSSQMessage;
            Session::set('ssq_ID', -1); ?>
            
            <script type="text/javascript">
                const divMsg = document.getElementById("flash-msg");
                if (divMsg.classList.contains("alert-success")){
                    setTimeout(redirectUser, 1000);
                }
            
                function redirectUser(){
                    let form = document.createElement("form");
                    let hiddenInput;

                    let ssqTime = <?= $_POST['ssq_time']; ?>;
                    let quizType = <?= $_POST['quiz_type'] ?>;
                    let targetURL;
                
                    if (quizType === 0){ // 0 represents textual quiz
                        targetURL = "TextQuiz";
                    }
                    else if (quizType === 1){ // 1 represents visual quiz
                        targetURL = "VisualQuiz";
                    }
                    else{
                        targetURL = "404";
                    }
                
                    form.setAttribute("method", "POST");
                    form.setAttribute("action", targetURL);
                    form.setAttribute("style", "display: none");
                    
                    hiddenInput = document.createElement("input");
                    hiddenInput.setAttribute("type", "hidden");
                    hiddenInput.setAttribute("name", "ssq_time");
                    hiddenInput.setAttribute("value", ssqTime);
                    form.appendChild(hiddenInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                    
                    return false;
                };
            </script>
<?php   }
    } ?>

<div class="card">
    <div class="card-header">
        <h3 class="float-left">
            Choose Quiz
        </h3>
        <span class="float-right">
            <a href="session_details" 
               class="btn btn-primary">
               Back
            </a>
        </span>
    </div>
    
    <div class="card-body pr-2 pl-2">
        <h2 class="text-center">Quiz Settings</h2>
        <form action="" method="post">
            <?php 
                $rand = bin2hex(openssl_random_pseudo_bytes(16));
                Session::set("post_ID", $rand);
            ?>
            <input type="hidden" name="randCheck" value="<?php echo $rand; ?>">
            <div style="margin-block: 6px;">
                <small style='color: red'>
                    * Required Field
                </small>
            </div>
            <div class="form-group pt-3">
                <label for="quiz_type" class="required">Quiz Type</label>
                <select class="form-control" name="quiz_type" id="quiz_type" required>
                    <option value="" disabled selected hidden>Choose Quiz Type...</option>
                    <?php
                        $sql = "SELECT id, type FROM SSQ_type;";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            echo "<option value=\"" . $row['id'] . "\">";
                            echo $row['type'];
                            echo "</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ssq_time" class="required">Quiz Time</label>
                <select class="form-control" name="ssq_time" id="ssq_time" required>
                    <option value="" disabled selected hidden>Select Quiz Time...</option>
                    <?php
                        $session_ID = Session::get('session_ID'); 
                        $sql = "SELECT id, name
                                FROM SSQ_times
                                WHERE is_active = 1 AND study_id IN (SELECT study_ID
									 FROM Session
			                         WHERE session_ID = $session_ID) 
			                         AND id NOT IN (SELECT ssq_time
			                                        FROM SSQ
			                                        WHERE session_ID = $session_ID and is_active = 1);";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            echo "<option value=\"" . $row['id'] . "\">";
                            echo $row['name'];
                            echo "</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="take-ssq-btn" class="btn btn-success">Take SSQ</button>
            </div>
        </form>
    </div>
</div>
<?php
    include 'inc/footer.php';
?>