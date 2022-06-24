<?php
    include 'inc/header.php';
    include_once 'lib/Database.php';
    Session::CheckSession();
    
    $db = Database::getInstance();
    $pdo = $db->pdo;
    
    if (Session::get("roleid") !== "1"){
        header("Location: 404");
        exit();
    }
    
    $localId = Session::get('id');
    if (isset($_POST["user_ID"]) && isset($_POST["iv"])){
        $iv = hex2bin($_POST["iv"]);
        $user_ID = Crypto::decrypt($_POST["user_ID"], $iv);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["removeUser"]) && Session::CheckPostID($_POST)){
        $sql = "UPDATE users
                SET status = 2,
                    updated_by = $localId,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = " . $user_ID . ";";
        $result = $pdo->query($sql);
        if (!$result){
            echo $pdo->errorInfo();
            exit();
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deactivateUser"]) && Session::CheckPostID($_POST)){
        $sql = "UPDATE users
                SET status = 0,
                    updated_by = $localId,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = " . $user_ID . "
                LIMIT 1;";
        $result = $pdo->query($sql);
        if (!$result){
            echo $pdo->errorInfo();
            exit();
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["activateUser"]) && Session::CheckPostID($_POST)){
        $sql = "UPDATE users
                SET status = 1,
                    updated_by = $localId,
                    updated_at = CURRENT_TIMESTAMP
                WHERE user_id = " . $user_ID . "
                LIMIT 1;";
        $result = $pdo->query($sql);
        if (!$result){
            echo $pdo->errorInfo();
            exit();
        }
    }

$rand = bin2hex(openssl_random_pseudo_bytes(16));
Session::set("post_ID", $rand);
?>

<div class="card" style="overflow-x: hidden">
    <div class="card-header">
        <h3><i class="fas fa-users mr-2"></i>User list <span class="float-right">Welcome! <strong>
            <span class="badge badge-lg badge-secondary text-white">
            <?php
                $name = Session::get("name");
                if (isset($name)) {
                    echo $name;
                }
            ?>
            </span>
        </strong></span></h3>
    </div>
    <div class="card-body pr-2 pl-2">
        <table id="example" class="table table-striped table-bordered table-responsive" style="width:100%;display:table">
            
        </table>
    <div class="form-check form-switch float-right" style="margin-left: 2rem">
        <input class="form-check-input" type="checkbox" id="show-deactivated-users" unchecked>
        <label class="form-check-label" for="show-deactivated-users">Show Deactivated Users Only</label>
    </div>
    <div class="form-check form-switch float-right">
        <input class="form-check-input" type="checkbox" id="show-pending-users" unchecked>
        <label class="form-check-label" for="show-pending-users">Show Users Pending Admin Approval Only</label>
    </div>
</div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $("#example").on("click", "a.profilePage", goToProfilePage);
        $("#example").on("click", "a.userAction", doUserAction);
        $("#example").on("click", "#validateUser", validateUser);
        $("#show-pending-users").on("click", showPendingUsers);
        $("#show-deactivated-users").on("click", showDeactivatedUsers);
    });
    
    let showOnlyPending = false;
    let showOnlyDeactivated = false;
    
    function goToProfilePage(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", $(this).parent().parent().attr("data-user_ID"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "iv");
        hiddenInput.setAttribute("value", $(this).parent().parent().attr("data-iv"));
        form.appendChild(hiddenInput);
        
        if ($(this).get(0).hasAttribute("data-purpose")){
            hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", "purpose");
            hiddenInput.setAttribute("value", $(this).attr("data-purpose"));
            form.appendChild(hiddenInput);
        }
        
        document.body.appendChild(form);
        form.submit(); 
        
        return false;
    };
    
    function validateUser(){
        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", $(this).parent().parent().attr("data-user_ID"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "iv");
        hiddenInput.setAttribute("value", $(this).parent().parent().attr("data-iv"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "randCheck");
        hiddenInput.setAttribute("value", "<?php echo $rand; ?>");
        
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
    
    let overrideDT = true;
    
    function showUsers(isFirstTime=false) {
        $.ajax({
            url: "load_users",
            method: "POST",
            cache: false,
            data:{
                showPendingUsers: showOnlyPending,
                showDeactivatedUsers: showOnlyDeactivated
            },
            success: function(data){
                $("#example").html(data);
                if (!isFirstTime && !overrideDT){
                    $("#example").DataTable().destroy();
                }
                overrideDT = false;
                if ($("#example td.notFound").length === 0) {
                    $('#example').DataTable();
                    $('#example').parent().css('overflow-x', 'auto');
                }
                else overrideDT = true;
                $("#example a.profilePage[data-user_ID]").on("click", goToProfilePage);
                $("#example a.userAction[data-user_ID]").on("click", doUserAction);
                $("#example").on("click", "#validateUser", validateUser);
                $("#show-pending-users").on("click", showPendingUsers);
                $("#show-deactivated-users").on("click", showDeactivatedUsers);
            }
        });
    }
    
    function showPendingUsers(){
        showOnlyPending = $(this).prop('checked');
        showUsers();
    }
    
    function showDeactivatedUsers(){
        showOnlyDeactivated = $(this).prop('checked');
        showUsers();
    }
    
    function doUserAction(){
        let userAction;
        if ($(this).hasClass("removeUser")){
            userAction = "removeUser";
            if (!confirm("Are you sure you want to remove this user?")){
                return false;
            }
        }
        if ($(this).hasClass("deactivateUser")){
            userAction = "deactivateUser";
            if (!confirm("Are you sure you want to deactivate this user?")){
                return false;
            }
        }
        if ($(this).hasClass("activateUser")){
            userAction = "activateUser";
            if (!confirm("Are you sure you want to activate this user?")){
                return false;
            }
        }

        let form = document.createElement("form");
        let hiddenInput;
        
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "user_ID");
        hiddenInput.setAttribute("value", $(this).parent().parent().attr("data-user_ID"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "iv");
        hiddenInput.setAttribute("value", $(this).parent().parent().attr("data-iv"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", userAction);
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "randCheck");
        hiddenInput.setAttribute("value", "<?php echo $rand; ?>");
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
        
        return false;
    }
    showUsers(true);
</script>
<?php
    include 'inc/footer.php';
?>
