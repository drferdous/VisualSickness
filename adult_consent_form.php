<?php
    include 'inc/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Adult Consent Form</h1>
    </div>
    <div class="card-body">
        <p class="font-italic">You are invited to participate in a research study. This form provides you with information about the study. You will also receive a copy of this form to keep for your reference. The Principal investigator or his representative will provide you with any additional information needed and answer any questions you may have. Read the information below and ask questions about anything you do not understand before deciding whether to participate. Your participation is entirely voluntary, and you can refuse to participate or withdraw at any time without penalty or loss of benefits to which you are otherwise entitled.
        </p>
        <hr>
        <h4 style="font-weight: bold">Principal Investigator</h4>
        <p>Sharif Mohammad Shahnewaz Ferdous, Ph.D</p>
        <h4 style="font-weight: bold">IRB Number</h4>
        <p>IRB-2021-0414</p>
        <h4 style="font-weight: bold">Study Title</h4>
        <p>Understanding of the Simulator Sickness Questionnaire</p>
        <h4 style="font-weight: bold">Why is this study being done?</h4>
        <p>We ask you to answer a survey that will help us investigate people's understanding of the Simulator Sickness Questionnaire. The survey is consists of 16 questions about how you are feeling right now.
        </p>
        <h4 style="font-weight: bold">Who else is eligible to participate in the study?</h4>
        <p>Anyone who is at least 7 years old and can read a simple question and answer a survey question is eligible to participate in the study.</p>
        <h4 style="font-weight: bold">What will happen while you are in the study?</h4>
        <p>You will be asked to provide demographic information. You will also be asked to fill up a questionnaire about your general discomfort (nausea, eye strain, dizziness, etc.).</p>
        <h4 style="font-weight: bold">What is the duration of the study?</h4>
        <p>This study will take about 5 minutes.</p>
        <h4 style="font-weight: bold">What are the possible discomforts and risks?</h4>
        <p>None.</p>
        <h4 style="font-weight: bold">What are the possible direct benefits to the participant for taking part in this research?</h4>
        <p>There are no direct benefits to you being in this study.</p>
        <h4 style="font-weight: bold">What are the possible benefits to society from this research?</h4>
        <p>The knowledge gained from this study may help us validate the use of a Simulator Sickness Questionnaire.</p>
        <h4 style="font-weight: bold">Will there be any compensation for participation?</h4>
        <p>There will be no compensation for completing this survey.</p>
        <h4 style="font-weight: bold">How will your privacy and the confidentially of your records be protected?</h4>
        <p>All collected data will be anonymized. The survey and other data will be labeled with a code rather than your name. The data resulting from your participation will be analyzed and used in publications and presentations,but your identity will not be disclosed. Your identifying information (name, email) will always be kept separatefrom your study data and not be shared with anyone.
        </p>
        <h4 style="font-weight: bold">Do you have to be in the study?</h4>
        <p>You do not have to be in this study. You are a volunteer! It is okay if you want to stop at any time and not be included in the study. You do not have to answer any questions you do not want to answer.</p>
        <h4 style="font-weight: bold">Do you have any questions about this study?</h4>
        <p>Phone or email the Principal investigator – Sharif Mohammad Shahnewaz Ferdous. (email: <a href="mailto:sharif.shahnewaz@tcnj.edu">sharif.shahnewaz@tcnj.edu</a>, Phone: 609-771-2789)</p>
        <h4 style="font-weight: bold">Do you have any questions about your rights as a research participant?</h4>
        <p>Phone or email the IRB Chair, Dr. Sandy Gibson, at 609-771-2136 or <a href="mailto:irbchair@tcnj.edu">irbchair@tcnj.edu</a></p>
        <h4 style="font-weight: bold">Statement of Consent</h4>
        <p>I have read this form and decided that I will participate in the project described above. Its general purposes, the particulars of involvement, and possible risks and inconveniences have been explained to my satisfaction. I understand that I can withdraw at any time. My signature also indicates that I am 18 years of age or older and have received a copy of this consent form.
        </p>
        <hr>
        <form method="POST" id="form" onsubmit="return redirectForm()">
            <div style="margin-block: 6px;">
                <small class='required-msg'>
                    * Required Field
                </small>
            </div>
            <input id="code" type="hidden" name="code" value="<?= $_POST["code"] ?>">
            <div class="form-group">
                <label for="nameInput" class="required" style="font-weight: bold">Print your name below if you consent to take a part in this study.</label>
                <input class="form-control" id="nameInput" name="name" placeholder="Enter name" required>
            </div>
            <div class="form-group">
                <label for="dateInput" class="required" style="font-weight: bold">Print today's date.</label>
                <input class="form-control" type="date" id="dateInput" name="date" value="<?= Util::getValueFromPost('dob', $_POST); ?>" required>
            </div>
            <div class="form-group">
                <label for="emailInput" class="required" style="font-weight: bold">Enter your email to receive a signed copy of the consent form.</label>
                <input class="form-control" id="emailInput" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                 <button id="sendAdultConsent" type="submit" class="btn btn-success" name="sendAdultConsent">Send</button>
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

        const Name = $('#nameInput').val();
        const Date = $('#dateInput').val();
        const email = $('#emailInput').val();
	    $('#sendAdultConsent').attr('disabled', 'disabled');
        $('#sendAdultConsent').html(`
        <div class="spinner-border text-dark" role="status">
            <span class="sr-only">Loading...</span>
        </div>`);

        $.ajax({
            url: 'send_PDF',
            type: 'POST',
            cache: false,
            data: {Name,
                   Date,
                   email,
                   documentName : 'adultConsent'
                  },
            success: function(data) {
                console.log(data);
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