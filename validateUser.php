<?php
    include "inc/header.php";
    Session::CheckSession();
    
    if (strcmp(Session::get("roleid"), "1") !== 0){
        header("Location: 404");
        exit();
    }
?>

<div class="card">
    <div class="card-header">
        <h3>User Details</h3>
    </div>
    <div class="card-body pr-2 pl-2">
        <table id="example"
               class="table table-striped table-bordered">
            <tbody class="text-center">
                <tr>
                    <td>Name</td>
                    <td>Andrew Michael</td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td>AmazingAndrewM</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>michaea3@tcnj.edu</td>
                </tr>
                <tr>
                    <td>Registration Role:</td>
                    <td>User</td>
                </tr>
                <tr>
                    <td colspan="2">Validate User</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
    include "inc/footer.php";
?>