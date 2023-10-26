<?php

use Castoware\Sendgrid;

function submitOrder($db, $request, $util)
{
  $rec = (array) $request->body;
  $items = $rec['items'];
  $rec['items'] = json_encode($rec['items']);
  $rec['order_date'] = date("Y-m-d");

  $itemList = json_decode(file_get_contents(dirname(__DIR__) . '/buy-now/items.json'));

  $items = array_map(function ($item) use ($itemList) {
    $rec = array_filter($itemList, function ($buyNowItem) use ($item) {
      return $buyNowItem->id == $item->id;
    });
    $rec = array_shift($rec);
    return [
      'item_name' => $rec->item_name_en,
      'quantity' => $item->quantity,
      'price' => $item->quantity * $rec->price
    ];
  }, $items);

  try {
    $db->query("INSERT INTO buy_now_orders %v", $rec);
    $id = $db->getInsertId();

    // mc-todo: try to send email, update record with send_status for $id
    // mc-todo: add buy now orders to admin

    $subject = "New Order From " . $rec['name'];
    $body = json_encode([
      'name' => $rec['name'],
      'email' => $rec['email'],
      'phone' => $rec['phone'],
      'items' => $items
    ], JSON_PRETTY_PRINT);

    // $sg = new Sendgrid();
    // $response = $sg->sendEmail('no-reply@castoware.com', 'NO REPLY', 'reinrosemary@gmail.com', 'Rosemary Rein', $subject, $body);

    // $db->query("UPDATE buy_now_orders SET %a WHERE id=?", [
    //   'send_status' => json_encode($response, JSON_PRETTY_PRINT)
    // ], $id);

    $util->success(['id' => $id]);
  } catch (Exception $e) {
    $message = $e->getMessage();
    error_log(print_r(['rec' => $rec, 'error' => $$message], true));
    $util->fail($message);
  }

  $util->success($request->body);
}
