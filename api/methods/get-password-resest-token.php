<?php

use Castoware\Sendgrid;

function getPasswordResetToken($db, $request, $util)
{
  $email = $request->body->email;
  $user = $db->fetch("SELECT * FROM admin_users WHERE email=?", $email);
  if (!$user) $util->fail("Invalid e-mail");

  $token = uniqid();
  $db->query("UPDATE admin_users SET reset_token=? WHERE id=?", $token, $user->id);

  $sg = new Sendgrid();
  $response = $sg->sendEmail("no-reply@castoware.com", "No Reply", $email, "Password Reset", "Password Reset for FAAN Gala Website", "Go to https://gala-en.castoware.com/password-reset/" . $token . " to create a new password.");

  $util->success();
}
