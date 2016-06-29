
    <h3>Confirmation Link Expired</h3>
    <p>Click <a href="<?php echo esc_attr( get_option('subscribenow_landing_page') ) . '?' .http_build_query([
      'email' => $_GET['email'],
      'resend' => true
    ]); ?>">here</a> to resend a confirmation link</p>
