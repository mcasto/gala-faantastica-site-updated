<?php

function getSpinnerImages($db)
{
  $auctionSpinner = $db->fetchAll("SELECT file_location FROM biddable_items");
  $auctionSpinner = array_map(function ($rec) {
    return $rec->file_location;
  }, $auctionSpinner);

  $spinnerPath = dirname(__DIR__, 2) . '/images/auction/spinner';
  $auctionSpinner = array_merge($auctionSpinner, glob($spinnerPath . "/*.{jpg,jpeg,png}", GLOB_BRACE));
  $auctionSpinner = array_map(function ($path) {
    return str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);
  }, $auctionSpinner);

  shuffle($auctionSpinner);

  return $auctionSpinner;
}

function getContents($db, $request, $util)
{
  $path = dirname(__DIR__) . '/site-contents/' . $request->params->language;

  $dirList = glob($path . '/*', GLOB_ONLYDIR);
  $pages = array_map(function ($path) use ($db) {
    $elements = glob($path . '/*.md');
    $ret = json_decode(file_get_contents($path . '/info.json'), true);

    if ($ret['name'] == 'Auction') {
      $ret['spinner'] = getSpinnerImages($db);
    }

    foreach ($elements as $element) {
      $name = pathinfo($element, PATHINFO_FILENAME);
      $ret[$name] = file_get_contents($element);
    }

    if (isset($ret['image_path'])) {
      $imageList = glob($_SERVER['DOCUMENT_ROOT'] . $ret['image_path'] . "/*.{jpg,jpeg,png}", GLOB_BRACE);
      $ret['image_list'] = array_map(function ($image) use ($ret) {
        return $ret['image_path'] . '/' . basename($image);
      }, $imageList);
    }

    return $ret;
  }, $dirList);

  $util->success($pages, true);
}
