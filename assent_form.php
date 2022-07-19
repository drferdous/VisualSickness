<?php
    include 'inc/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Assent Form</h1>
    </div>
    <div class="card-body">
        <p class="font-italic">This is  a research study. Research studies help us learn new things. We want to learn how much you know about the Simulator Sickness Questionnaire. You get to decide if you want to take part. You can say ‘Yes’ or ‘No.’ No one will get upset if you say ‘No.’ If you   say   yes,   you   will   answer   some   questions.   Some   questions   will   ask   about   your   demographics.   Some questions will ask about how you are feeling. Do not worry. Your parents will help you. You can say ‘No’ anytime. This study will not help you directly. It helps us. We will learn important things. Someday, it will help other kids.</p>

        If you have a question, talk to:
        <br>
        * Sharif Mohammad Shahnewaz Ferdous (Phone: 609-771-2789, Email: <a href="mailto:sharif.shahnewaz@tcnj.edu">sharif.shahnewaz@tcnj.edu</a>)
        <br>
        * Dr. Sandy Gibson, (Phone: 609-771-2136 or Email: <a href="mailto:irbchair@tcnj.edu">irbchair@tcnj.edu</a>)
        <hr>
        <form method="POST" id="form" onsubmit="return redirectForm()">
            <div style="margin-block: 6px;">
                <small class='required-msg'>
                    * Required Field
                </small>
            </div>
            <input id="code" type="hidden" name="code" value="<?= $_POST["code"] ?>">
            <div class="form-group">
                <label for="nameInput" class="required" style="font-weight: bold">If you want to take part in the research, write your name below. This shows you read this page.</label>
                <input class="form-control" id="nameInput" name="name" placeholder="Enter name" required>
            </div>
            <div class="form-group">
                <label for="dateInput" class="required" style="font-weight: bold">Print today's date.</label>
                <input class="form-control" type="date" id="dateInput" name="date" value="<?= Util::getValueFromPost('dob', $_POST); ?>" required>
            </div>
            <div class="form-group">
                <label for="emailInput" class="required" style="font-weight: bold">What is your parent's email address?</label>
                <input class="form-control" id="emailInput" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                 <input type="submit" class="btn btn-success" name="sendAdultConsent" value="Send">
            </div>
        </form>
    </div>
</div>
<script>
    const redirectForm = () => {
        const attr = $('#form').attr('action');
        if (typeof attr !== 'undefined' && attr !== false) {
            return true;
        }

        const name = $('#nameInput').val();
        const date = $('#dateInput').val();
        const email = $('#emailInput').val();

        $.ajax({
            url: 'send_PDF.php',
            type: 'POST',
            cache: false,
            data: {name,
                   date,
                   email,
                   documentName : 'assent'
                  },
            success: function(data) {
                if (+$('#code').val().charAt(4) % 3 === 0) {
                    $('#form').attr('action', 'code_visual_quiz');
                } else if (+$('#code').val().charAt(4) % 3 === 2) {
                    $('#form').attr('action', 'code_text_quiz');
                }
                $('#form').submit();
            }
        });
        return false;
    }
</script>


<?php
  include 'inc/footer.php';
?>