<?php
function passwordReset($db, $request, $util)
{
  $user = $db->fetch("SELECT * FROM admin_users WHERE reset_token=?", $request->body->token);

  if (!$user) $util->fail("Invalid reset token");

  $db->query(
    "UPDATE admin_users SET %a WHERE id=?",
    [
      'reset_token' => null, 'pass_hash' => password_hash($request->body->password, PASSWORD_DEFAULT)
    ],
    $user->id
  );

  $util->success();
}
