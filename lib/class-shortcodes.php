<?php

function subscribenow_form(){
  wp_enqueue_script('subscribenow-ajax');
  ?>
  <form id="subscribenow" method="POST" action="">
    <h3>Stay up to date</h3>
    <p>
      Join the weekly newsletter and never miss out on new tips, tutorials, and more.
    </p>
    Email Address: <input type="email" id="email" name="email"><br>
    <button class="btn btn-success" type="submit">Subscribe</button>
    <?php wp_nonce_field( 'ajax-subscription-nonce', 'security' ); ?>
  </form>
  <?php
}
add_shortcode('subscribenow-form', 'subscribenow_form');
