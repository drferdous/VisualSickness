<?php
include 'inc/header.php';
include_once 'lib/Database.php';

$db = Database::getInstance();
$pdo = $db->pdo;

Session::CheckSession();

?>

<div class="card overflow-hidden">
    <div class="card-header">
        <h1 class="mb-0">Study List</h1>         
    </div>
    <div class="card-body pr-2 pl-2">
        <br />
        <table class="table table-striped table-bordered table-responsive" style="display: table" id="example">
        </table>
        <br>
        <div class="form-check form-switch float-right">
            <input class="form-check-input" type="checkbox" id="show-studies" checked>
            <label class="form-check-label" for="show-studies">Show Active Studies Only</label>
        </div>
    </div>
</div>

<script type="text/javascript">
    let activeStatus = 'active';
    $(document).ready(function(){
        $(document).on("click", "#show-studies", function() {
            if ($(this).prop("checked")){
                activeStatus = "active";
            }
            else{
                activeStatus = "all";
            }
            getData(false);
        });
        $(document).on("click", ".redirectUserBtns a", redirectUser);
    });
    
    let overrideDT = false;
    
    function getData(isFirstTime = false) {
        $.ajax({
           url: "load_studies",
           method: "POST",
           cache: false,
           data:{
               activeStatus: activeStatus
           },
           success: function(data){
                $("#example").html(data);
                if (!isFirstTime && !overrideDT){
                    $("#example").DataTable().destroy();
                }
                overrideDT = false;
                if ($("#example td.notFound").length === 0) {
                    $('#example').DataTable({order:[]});
                    $('#example').parent().css('overflow-x', 'auto');
                }
                else overrideDT = true;
           }
        });
    }
        
    function redirectUser(){
        let form = document.createElement("form");
            
        form.setAttribute("method", "POST");
        form.setAttribute("action", $(this).attr("href"));
        form.setAttribute("style", "display: none");
            
        let hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "study_ID");
        hiddenInput.setAttribute("value", $(this).parent().attr("data-study_ID"));
        form.appendChild(hiddenInput);
        
        hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", "iv");
        hiddenInput.setAttribute("value", $(this).parent().attr("data-IV"));
        form.appendChild(hiddenInput);
        
        document.body.appendChild(form);
        form.submit();
            
        return false;
    };
    
    getData(true);
</script>
<?php
  include 'inc/footer.php';
?>