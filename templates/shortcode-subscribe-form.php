<form id="subscribenow" method="POST" action="">
    <h3>Stay up to date</h3>
    <p>
        Join the weekly newsletter and never miss out on new tips, tutorials, and more.
    </p>
    Email Address: <input type="email" id="email" name="email"><br>
    <button class="btn btn-success" type="submit">Subscribe</button>
    <?php wp_nonce_field( 'ajax-subscription-nonce', 'security' ); ?>
    <p class="status">

    </p>
</form>
