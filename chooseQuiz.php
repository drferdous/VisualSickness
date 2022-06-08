<?php
    include 'inc/header.php';
    include_once 'lib/Database.php';
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    Session::CheckSession();
    Session::set('session_ID', intval($_POST['session_ID']));
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['take-ssq-btn'])) {
        $takeSSQMessage = $studies->takeSSQ($_POST);
        if (isset($takeSSQMessage)){
            echo $takeSSQMessage; ?>
            
            <script type="text/javascript">
                $(document).ready(function(){
                    const divMsg = document.getElementById("flash-msg");
                    if (divMsg.classList.contains("alert-success")){
                        setTimeout(redirectUser, 2000);
                    }
                
                    function redirectUser(){
                        let form = document.createElement("form");
                        let hiddenInput;

                        let sessionID = <?php echo Session::get('session_ID'); ?>;
                        let quizType = <?php echo $_POST['quiz_type']; ?>;
                        let ssqTime = <?php echo $_POST['ssq_time']; ?>;
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
                        hiddenInput.setAttribute("name", "session_ID");
                        hiddenInput.setAttribute("value", sessionID);
                        form.appendChild(hiddenInput);
                        
                        hiddenInput = document.createElement("input");
                        hiddenInput.setAttribute("type", "hidden");
                        hiddenInput.setAttribute("name", "quiz_type");
                        hiddenInput.setAttribute("value", quizType);
                        form.appendChild(hiddenInput);
                        
                        hiddenInput = document.createElement("input");
                        hiddenInput.setAttribute("type", "hidden");
                        hiddenInput.setAttribute("name", "ssq_time");
                        hiddenInput.setAttribute("value", ssqTime);
                        form.appendChild(hiddenInput);
                        
                        hiddenInput = document.createElement("input");
                        hiddenInput.setAttribute("type", "hidden");
                        hiddenInput.setAttribute("name", "is_first_time");
                        hiddenInput.setAttribute("value", "true");
                        form.appendChild(hiddenInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                        
                        return false;
                    };
                });
            </script>
<?php   }
    } ?>

<div class="card ">
    <div class="card-header">
        <h3><span class="float-right">Welcome! 
            <strong><span class="badge badge-lg badge-secondary text-white">
            <?php
                $username = Session::get('username');
                if (isset($username)) {
                    echo $username;
                }
            ?>
            </span></strong>
        </span></h3>
    </div>
    
    <div class="card-body pr-2 pl-2">
        <h2 class="text-center">Quiz Settings</h2>
        <form action="<?php echo "chooseQuiz"; ?>" method="post">
            <div class="form-group pt-3">
                <label for="quiz_type">Quiz Type</label>
                <select class="form-control" name="quiz_type" id="quiz_type">
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
                <label for="ssq_time">Quiz Time</label>
                <select class="form-control" name="ssq_time" id="ssq_time">
                    <option value="" disabled selected hidden>Select Quiz Time...</option>
                    <?php
                        $sql = "SELECT id, name FROM SSQ_times;";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                            echo "<option value=\"" . $row['id'] . "\">";
                            echo $row['name'];
                            echo "</option>";
                        }
                    ?>
                </select>
            </div>
            <input type="hidden" name="session_ID", value="<?php echo Session::get('session_ID'); ?>">
            <div class="form-group">
                <button type="submit" name="take-ssq-btn" class="btn btn-success">Take SSQ</button>
            </div>
        </form>
    </div>
</div>


<?php
    include 'inc/footer.php';
?>