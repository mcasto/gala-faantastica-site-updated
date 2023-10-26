<?php
function getAdminData($db, $request, $util)
{
  $buyNowPath = dirname(__DIR__) . '/buy-now';

  $items = json_decode(file_get_contents("$buyNowPath/items.json"));

  $buyNow = $db->fetchAll("SELECT * FROM buy_now_orders ORDER BY order_date DESC");

  $buyNow = array_map(function ($order) use ($items) {
    $cart = json_decode($order->items);

    $order->items = array_map(function ($selected) use ($items) {
      $item = array_filter($items, function ($item) use ($selected) {
        return $item->id == $selected->id;
      });
      $item = array_shift($item);
      return [
        'name' => $item->item_name_en,
        'price' => $item->price,
        'quantity' => $selected->quantity,
        'subtotal' => $item->price * $selected->quantity
      ];
    }, $cart);

    $subtotals = array_map(function ($item) {
      return $item['subtotal'];
    }, $order->items);

    $order->total = '$' . array_sum($subtotals);

    return $order;
  }, $buyNow);

  $util->success(
    [
      'donations' => $db->fetchAll("SELECT * FROM donations ORDER BY date DESC"),
      'contacts' => $db->fetchAll("SELECT * FROM contacts ORDER BY date DESC"),
      'online_bids' => $db->fetchAll("SELECT b.id, i.item_name_en AS item_name, b.highest_bid, b.name, b.email, b.phone FROM online_bids b, biddable_items i WHERE i.item_number=b.item_number ORDER BY i.item_name_en, b.highest_bid DESC"),
      'buy_now' => $buyNow
    ],
    true
  );
}
