<div class="wrap">
    <h2>Newsletter Subscriptions</h2>
    <div id="member-table">
        <div class="metabox-holder columns-2">
            <div class="meta-box-sortables ui-sortable">
                <form method="post">
                    <?php
                    $this->customers->prepare_items();
                    $this->customers->display(); ?>
                </form>
            </div>
        </div>
        <br class="clear">
    </div>
    <form method="post" action="options.php">
        <?php settings_fields( 'subscribenow-settings-group' ); ?>
        <?php do_settings_sections( 'subscribenow-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" colspan="2">General Settings</th>
            </tr>
            <tr>
                <td width="300px">
                    <label>Default Landing Page</label>
                </td>
                <td>
                    <input type="text" name="subscribenow_landing_page" value="<?php echo esc_attr( get_option('subscribenow_landing_page') ); ?>" style="min-width:300px;"/>
                </td>
            </tr>
            <tr>
                <td width="300px">
                    <label>Confirmation Link Expiration</label><br>
                    <small>Maximum time in hours for a confirmation link to be accessible. Default is 24 hours</small>
                </td>
                <td>
                    <input type="number" name="subscribenow_link_expiry" value="<?php echo esc_attr( get_option('subscribenow_link_expiry') ?: 24 ); ?>" style="min-width:300px;"/>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>

    </form>
    <form method="post" action="options.php">
        <?php settings_fields( 'subscribenow-recaptcha-settings-group' ); ?>
        <?php do_settings_sections( 'subscribenow-recaptcha-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" colspan="2">Google Recaptcha Settings</th>
            </tr>
            <tr>
                <td width="300px">
                    <label>Google Secret Key</label><br>
                    <small>Used by the server to interact with google.</small>
                </td>
                <td><input type="text" name="subscribenow_recaptcha_site_key" value="<?php echo esc_attr( get_option('subscribenow_recaptcha_site_key') ); ?>" / style="min-width:300px;"></td>
            </tr>
            <tr>
                <td width="300px">
                    <label>Google Site Key</label><br>
                    <small>Used by the browser to interact with the users</small>
                </td>
                <td><input type="text" name="subscribenow_recaptcha_client_key" value="<?php echo esc_attr( get_option('subscribenow_recaptcha_client_key') ); ?>" / style="min-width:300px;"></td>
            </tr>
        </table>
        <?php submit_button(); ?>

    </form>
</div>
