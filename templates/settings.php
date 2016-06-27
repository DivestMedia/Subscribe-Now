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
</div>
