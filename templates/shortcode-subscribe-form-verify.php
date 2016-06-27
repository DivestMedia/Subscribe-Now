<form id="subscribenow" method="POST" action="">
    <h3>Few more steps to complete your subscription</h3>
    <p>
        Please provide the following details to continue.
    </p>
    Email: <input type="text" name="email" value="<?=$_GET['email']?>" readonly><br>
    Full Name *: <input type="text" name="fullname" maxlength="255" required=""><br>
    Nickname: <input type="text" name="nickname" maxlength="255"><br>
    Contact No: <input type="text" name="contact" maxlength="255"><br>
    <button class="btn btn-success" type="submit">Complete Subscription</button>
    <?php wp_nonce_field( 'ajax-subscription-nonce', 'security' ); ?>
    <p class="status">

    </p>
</form>
