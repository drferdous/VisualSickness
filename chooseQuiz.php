<?php
    include 'inc/header.php';
    include 'database.php';
    
    Session::CheckSession();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['take-ssq-btn'])) {
        if (isset($_POST['quiz_type']) && isset($_POST['pre_post'])){
            header("Location: " . $_POST['quiz_type'] . ".php?session_ID=" . Session::get('session_ID') . "&pre_post=" . $_POST['pre_post']);
        }
        else{
            $errorMessage = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Error! Please select a quiz type and quiz time!</strong> </div>';
            echo $errorMessage;
        }
    }
?>

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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?session_ID=" . Session::get('session_ID'); ?>" method="post">
            <div class="form-group pt-3">
                <label for="quiz_type">Quiz Type</label>
                <select class="form-control" name="quiz_type" id="quiz_type">
                    <option value="" disabled selected hidden>Choose Quiz Type...</option>
                    <option value="TextQuiz">Textual Quiz</option>
                    <option value="VisualQuiz">Visual Quiz</option>
                </select>
            </div>
            <div class="form-group">
                <label for="pre_post">Quiz Time</label>
                <select class="form-control" name="pre_post" id="pre_post">
                    <option value="" disabled selected hidden>Select Quiz Time...</option>
                    <?php
                        $sql = "SELECT id, name FROM SSQ_times;";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)){
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