<?php
    include 'inc/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Parental/Guardian Permission Form</h1>
    </div>
    <div class="card-body">
        <p class="font-italic">Your child are invited to participate in a research study. This form provides you with information about the study. You will also receive a copy of this form to keep for your reference. The Principal investigator or his representative will provide you with any additional information that may be needed and answer any questions you may have. Read the information below and ask questions about anything you do not understand before deciding whether to allow your child to participate. Your child’s participation is entirely voluntary, and you or your child can refuse to participate or withdraw at any time without penalty or loss of benefits to which you or your child are otherwise entitled.
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
        <h4 style="font-weight: bold">What will happen while your child is in the study?</h4>
        <p>Your child will be asked to provide demographic information. Your child will also be asked to fill up a
questionnaire about their general discomfort (nausea, eye strain, dizziness, etc.).</p>
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
        <h4 style="font-weight: bold">How will your child’s privacy and the confidentiality of your records be protected?</h4>
        <p>All collected data will be anonymized. The survey and other data will be labeled with a code rather than participant’s name. The data resulting from your child’s participation will be analyzed and used in publications and presentations, but your identity will not be disclosed. Your identifying information (name, email) will always be kept separate from your study data and not be shared with anyone.
        </p>
        <h4 style="font-weight: bold">Does your child have to be in the study?</h4>
        <p>They do not have to be in this study. They are a volunteer! It is okay if they want to stop at any time and not be included in the study. They do not have to answer any questions they do not want to answer.</p>
        <h4 style="font-weight: bold">Do you have any questions about this study?</h4>
        <p>Phone or email the Principal investigator – Sharif Mohammad Shahnewaz Ferdous. (email: <a href="mailto:sharif.shahnewaz@tcnj.edu">sharif.shahnewaz@tcnj.edu</a>, Phone: 609-771-2789)</p>
        <h4 style="font-weight: bold">Do you have any questions about your rights as a research participant?</h4>
        <p>Phone or email the IRB Chair, Dr. Sandy Gibson, at 609-771-2136 or <a href="mailto:irbchair@tcnj.edu">irbchair@tcnj.edu</a></p>
        <h4 style="font-weight: bold">Statement of Consent</h4>
        <p>I have read this form and decided that I will participate in the project described above. Its general purposes, the particulars of involvement, and possible risks and inconveniences have been explained to my satisfaction. I understand that I can withdraw at any time. My signature also indicates that I am 18 years of age or older and have received a copy of this consent form.
        </p>
        <hr>
        <form action="" method="POST" id="form" onsubmit="return redirectForm()">
            <div style="margin-block: 6px;">
                <small class='required-msg'>
                    * Required Field
                </small>
            </div>
            <input id="code" type="hidden" name="code" value="<?= $_POST["code"] ?>">
            <div class="form-group">
                <label for="childNameInput" class="required" style="font-weight: bold">I have read this form and decided that I allow (name of child) _________________________________ to participate in the project described above. Its general purposes, the particulars of my child’s involvement, and possible risks and inconveniences have been explained to my satisfaction. I understand that I can withdraw at any time. My signature also indicates that I have received a copy of this permission form.</label>
                <input class="form-control" id="childNameInput" name="childName" placeholder="Enter child name" required>
            </div>
            <div class="form-group">
                <label for="nameInput" class="required" style="font-weight: bold">Please type your name below to agree to the above-mentioned form.</label>
                <input class="form-control" id="nameInput" name="name" placeholder="Enter name" required>
            </div>
            <div class="form-group">
                <label for="emailInput" class="required" style="font-weight: bold">Enter your email to receive a copy of the completed form.</label>
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
        const attr = $('#codeForm').attr('action');
        if (typeof attr !== 'undefined' && attr !== false) {
            return true;
        }

        const name = $('#nameInput').val();
        const date = $('#dateInput').val();
        const email = $('#emailInput').val();
        const childName = $('#childNameInput').val();

        $.ajax({
            url: 'send_PDF.php',
            type: 'POST',
            cache: false,
            data: {name,
                   date,
                   email,
                   childName,
                   documentName : 'parent_guardianPermission'
                  },
            success: function(data) {
                $('#form').attr('action', 'assent_form');
                $('#form').submit();
            }
        });
        return false;
    }
</script>

<?php
  include 'inc/footer.php';
?>