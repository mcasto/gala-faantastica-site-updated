<?php
function getBiddableItems($db, $request, $util)
{
  $items = $db->fetchAll("SELECT * FROM biddable_items ORDER BY sort_order");

  $items = array_map(function ($item) {
    [$item->width, $item->height] = getimagesize($_SERVER['DOCUMENT_ROOT'] . $item->file_location);

    return (array) $item;
  }, $items);

  $util->success($items, true);
}
