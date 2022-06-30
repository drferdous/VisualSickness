<?php
    include 'inc/header.php';
?>
<div class="card">
    <div class="card-header">
        <h3 class="float-left">Contact Us</h3>
    </div>
    <div class="card-body">
        <div class="mt-2 d-flex flex-wrap">
            <div class="p-2" style="margin-bottom:2rem;flex:1;">
                    <?php
                    if(Session::get('login')) {
                        $sql = "SELECT name FROM affiliation WHERE affiliation_id = " . Session::get('affiliationid');
                        $result = Database::getInstance()->pdo->query($sql);
                        if ($result) {
                            $admins = Util::getAdminsFromAffiliation(Database::getInstance()->pdo,Session::get('affiliationid'));
                            if($admins) { ?>
                                <div style="max-height: 50vh;overflow-y:auto;min-width:max-content">
                                    <h3>Admins</h3>
                                    <h5>Affiliation: <?= $result->fetch(PDO::FETCH_ASSOC)['name'] ?></h5>
                                    <ul class="list-unstyled">
                                        <?php
                                            $admins = explode(',',$admins);
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
                <h3>Support</h3>
                <a href="mailto:visualsicknessstudy@gmail.com">visualsicknessstudy@gmail.com</a>
            </div>
            <form style="flex:1 1 50%" class="p-4">
                <div class="form-group">
                    <label for="nameInput" class="required">Name</label>
                    <input class="form-control" id="nameInput" name="name" placeholder="Enter name" required>
                </div>
                <div class="d-flex flex-wrap">
                    <div class="form-group" style="flex:1;margin-inline-end:5px;min-width:250px">
                        <label for="emailInput" class="required">Email</label>
                        <input type="email" class="form-control" id="emailInput" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="form-group" style="flex:1;margin-inline-start:5px;min-width:250px">
                        <label for="phoneInput">Phone Number</label>
                        <input type="tel" class="form-control" id="phoneInput" placeholder="Enter phone number">
                    </div>
                </div>
                <div class="form-group">
                    <label for="messageInput" class="required">Message</label>
                    <textarea class="form-control" id="messageInput" name="msg" placeholder="Enter message" rows="4" required></textarea>
                </div>
                <div class="form-group">
                     <input type="submit" class="btn btn-success" name="submit" value="Send">
                </div>
            </form>
        </div>
    </div>
</div>

<?php
  include 'inc/footer.php';
?>