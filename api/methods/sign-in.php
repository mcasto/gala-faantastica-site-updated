<?php
function signIn($db, $request, $util)
{
  $user = $db->fetch(
    "SELECT * FROM admin_users WHERE email=?",
    $request->body->email
  );

  if (!$user) $util->fail("Invalid email");
  if (!password_verify($request->body->password, $user->pass_hash)) $util->fail("Invalid password");

  $token = uniqid();
  $db->query("UPDATE admin_users SET token=? WHERE id=?", $token, $user->id);

  $util->success(
    [
      'token' => $token,
      'donations' => $db->fetchAll("SELECT * FROM donations")
    ]
  );
}
