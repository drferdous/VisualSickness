<script type="text/javascript">
    $(document).ready(function(){ //newly added 
    $('#Submit').click(function() {alert('in');
        var txtNameVal = $('#txtName').val(); // assuming this is a input text field
        $.post('checkcode.php', {'txtName' : txtNameVal}, function(data) {
            if(data=='exist') {
              alert("Code has been used too many times! Contact visualsicknessstudy@gmail.com for more information.")
              return false;
            }
            else $('#codeForm').submit();
        });
    });});

    function Validate() {
        var code = document.getElementById("txtName").value;

        var char = code.split('');

        var regexA = /^[A-Za-z0-9]{1}$/
        var isValidA = regexA.test(char[0]);

        var regexB = /[B-DF-HJ-NP-TV-Z]/g
        var isValidB = regexB.test(char[1]);

        var regexC = /^\d+$/
        var isValidC = regexC.test(char[2]);

        var regexD = /^[aeiou]$/i
        var isValidD = regexD.test(char[3]);

        var regexEFGH = /^\d+$/
        var isValidE = regexEFGH.test(char[4]);
        var isValidF = regexEFGH.test(char[5]);
        var isValidG = regexEFGH.test(char[6]);
        var isValidH = regexEFGH.test(char[7]);

        if (!isValidA || !isValidB || !isValidC || !isValidD || !isValidE || !isValidF || !isValidF || !isValidG || !isValidH) {
            alert("Please enter a valid code");
        }
 
        if (isValidA && isValidB && isValidC && isValidD && isValidE && isValidF && isValidF && isValidG && isValidH) {
          if (char[4] == '3' || char[4] == '6' || char[4] == '9') {
            var quiz = 0;
            window.location.replace("https://visualsickness.000webhostapp.com/VisualQuiz.php?code=" + code);
          } else if (char[4] == '2' || char[4] == '5' || char[4] == '8') {
            var quiz = 1;
            window.location.replace("https://visualsickness.000webhostapp.com/TextQuiz.php?code=" + code);
          }
        }
    }
</script>

    <?php
    include 'inc/header.php';

    ?>
    
      <div class="card ">
        <div class="card-header">
          <h3><span class="float-right">Welcome! <strong>
            <span class="badge badge-lg badge-secondary text-white">
</span>

          </strong></span></h3>
        </div>
        <div class="card-body pr-2 pl-2">
          <h1>Ongoing Studies</h1>
          <hr>
          <br>
          <h2>Visual Sickness Study</h2>
          <form id="codeForm">
            <h4>Enter Your Confirmation Code</h4>
            <input type="text" id="txtName"/>
            <input type="button" value="Submit" onclick="Validate()"/>
          </form>
          <p>Not registered for the study? Click <a href="https://docs.google.com/forms/d/e/1FAIpQLScSCdtMj7uQILJpLQPhyVRZa6S5bLZlaPA1ruJ-OV_1gzb8Mw/viewform" target="_blank">here</a></p>
          <hr>
          
        <?php if (Session::get('roleid') == '1' || Session::get('roleid') == '2') { ?>
            <h2>Study Options</h2>
            <p><a href="create_study.php">Create a Study</a></p>
            <p><a href="view_study.php">Manage Studies</a></p>
            <p><a href="create_session.php">Create a Session</a></p            
            </div>
        <?php  } ?>          
      </div>

  <?php
  include 'inc/footer.php';
  ?>