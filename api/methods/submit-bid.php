<?php

use Castoware\Sendgrid;

function submitBid($db, $request, $util)
{
  try {
    $rec = (array) $request->body;

    $item = $db->fetch("SELECT * FROM biddable_items WHERE item_number=?", $rec['item_number']);
    $itemName = $item->item_name_es;

    $subject = "Bid on Item #" . $rec['item_number'];
    $body = $rec['name'] . " submitted a maximum bid of " . $rec['highest_bid'] . " for $itemName (Item #" . $rec['item_number'] . ")";

    $sg = new Sendgrid();
    $response = $sg->sendEmail('no-reply@castoware.com', 'NO REPLY', 'reinrosemary@gmail.com', 'Rosemary Rein', $subject, $body);

    $rec['send_status'] = json_encode($response, JSON_PRETTY_PRINT);
    $rec['date'] = date("Y-m-d H:i:s");

    $db->query("INSERT INTO online_bids %v", $rec);
    $util->success($rec);
  } catch (Exception $e) {
    $util->fail($e->getMessage());
  }
}
