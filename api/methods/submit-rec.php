<?php

use Castoware\Sendgrid;

function submitRec($db, $request, $util)
{
  $table = $request->body->table;
  $rec = (array) $request->body->rec;

  $sg = new Sendgrid();

  $to = "info@faanecuador.org";
  $toName = "FAAN Auction Donations";

  if ($table == 'contacts') {
    $replyTo = $rec['email'];
    $replyToName = $rec['name'];
    $subject = $rec['subject'];
    $body = $rec['message'];
  }

  if ($table == 'donations') {
    $replyTo = $rec['email'];
    $replyToName = $rec['name'];
    $subject = "Auction Donation Submission";
    $body = json_encode($request->body, JSON_PRETTY_PRINT);
  }

  $response = $sg->sendEmail($replyTo, $replyToName, $to, $toName, $subject, $body);

  $rec['send_status'] = json_encode($response, JSON_PRETTY_PRINT);
  $rec['date'] = date("Y-m-d H:i:s");

  $db->query("INSERT INTO %n %v", $table, $rec);
  $util->success($request->body);
}
