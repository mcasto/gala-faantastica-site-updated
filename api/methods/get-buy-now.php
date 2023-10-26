<?php
function getBuyNow($db, $request, $util)
{
  $buyNowPath = dirname(__DIR__) . '/buy-now';

  $items = json_decode(file_get_contents("$buyNowPath/items.json"));

  foreach ($items as $key => $item) {
    $items[$key]->description_en = file_get_contents($buyNowPath . "/" . $item->description_en);
    $items[$key]->description_es = file_get_contents($buyNowPath . "/" . $item->description_es);
  }

  $util->success($items, true);
}
