<?php
    include 'inc/header.php';
    if (isset($_GET['success']) && $_GET['success'] === 'true') {
        echo Util::generateSuccessMessage('Your message has been sent!');
    }
    if (isset($_GET['success']) && $_GET['success'] === 'false') {
        echo Util::generateErrorMessage('Something went wrong! Make sure all required fields are filled and try again!');
    }
?>
<div class="card">
    <div class="card-header">
        <h1 class="float-left mb-0">Contact Us</h1>
    </div>
    <div class="card-body">
    <p class="font-italic">We will get back to you within 48 hours. You can also reach out to your local admin
 for help!</p>
        <div class="mt-2 d-flex flex-wrap flex-md-row-reverse flex-column">
            <form class="p-0 p-sm-4 flex-fill" action="inc/support_email" method="POST">
                <div class="form-group">
                    <label for="nameInput" class="required">Name</label>
                    <input class="form-control" id="nameInput" name="name" placeholder="Enter name" required>
                </div>
                <div class="d-flex flex-wrap flex-lg-row flex-column">
                    <div class="form-group mr-0 mr-lg-1 flex-fill">
                        <label for="emailInput" class="required">Email</label>
                        <input type="email" class="form-control" id="emailInput" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="form-group ml-0 ml-lg-1 flex-fill">
                        <label for="phone_no">Phone Number</label>
                        <input name="phone_no" type="tel" class="form-control" id="phone_no" placeholder="Enter phone number">
                    </div>
                </div>
                <div class="form-group">
                    <label for="messageInput" class="required">Message</label>
                    <textarea class="form-control" id="messageInput" name="msg" placeholder="Enter message" rows="4" required></textarea>
                </div>
                <div class="form-group">
                     <input type="submit" class="btn btn-success" name="sendSupportEmail" value="Send">
                </div>
            </form>
            <div class="p-2 mt-4">
                <?php if(Session::get('login')) {
                    $sql = "SELECT name FROM affiliation WHERE affiliation_id = " . Session::get('affiliationid');
                    $result = Database::getInstance()->pdo->query($sql);
                    if ($result) {
                        $admins = Util::getAdminsFromAffiliation(Database::getInstance()->pdo,Session::get('affiliationid'));
                        if($admins) { ?>
                            <div style="max-height: 50vh;overflow-y:auto;min-width:max-content">
                                <h2 class='h3'>Admins</h2>
                                <p>Affiliation: <?= $result->fetch(PDO::FETCH_ASSOC)['name'] ?></p>
                                <ul class="list-unstyled">
                                    <?php
                                        $admins = explode(', ',$admins);
                                        foreach($admins as $admin) { ?>
                                            <li><a href= "mailto:<?php echo $admin; ?>"><?php echo $admin; ?></a></li>
                                    <?php }
                                    ?>
                                </ul>
                            </div>
                            <hr>
                        <?php } 
                    }
                } ?>
                <h2 class='h3'>Support</h2>
                <a href="mailto:visualsicknessstudy@gmail.com">visualsicknessstudy@gmail.com</a>
            </div>
        </div>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>