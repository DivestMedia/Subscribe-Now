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
        <th scope="row">Default Subscribe Landing Page</th>
        <td><input type="text" name="subscribenow_landing_page" value="<?php echo esc_attr( get_option('subscribenow_landing_page') ); ?>" /></td>
      </tr>
    </table>

    <?php submit_button(); ?>

  </form>
</div>
