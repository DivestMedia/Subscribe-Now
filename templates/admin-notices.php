<?php
switch ($_GET['notice']) {
  case 'resend-success':
  $class = 'notice-info';
  $message = 'Confirmation Link sent successfully to ' . (isset($_GET['email']) ? '<b>' . $_GET['email'] . '</b>' : 'selected email address');
  break;
  case 'resend-failed':
  $class = 'notice-error';
  $message = 'Confirmation Link failed sending to ' . (isset($_GET['email']) ? '<b>' . $_GET['email'] . '</b>' : 'selected email address');
  break;
  break;
  case 'delete-success':
  $class = 'notice-info';
  $message = 'Subscriber Deleted successfully';
  break;
  case 'delete-failed':
  $class = 'notice-error';
  $message = 'Failed to delete selected subscriber';
  break;
  default:
  $class = 'notice-warning';
  $message = 'Admin Notice';
  break;
}

?>
<div class="notice <?=$class?> is-dismissible">
  <p>
    <?=$message?>
  </p>
</div>
