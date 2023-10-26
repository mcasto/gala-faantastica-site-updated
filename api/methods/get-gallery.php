<?php
function getGallery($db, $request, $util)
{
  $galleryPath = dirname(__DIR__, 2) . '/images/gallery';
  $albumList = glob($galleryPath . '/*', GLOB_ONLYDIR);
  $albums = array_map(function ($album) {
    $info = json_decode(file_get_contents($album . '/info.json'));
    $images = glob($album . "/*.{jpg,jpeg,png}", GLOB_BRACE);
    $images = array_map(function ($image) {
      return str_replace($_SERVER['DOCUMENT_ROOT'], '', $image);
    }, $images);

    return [
      'en' => $info->en,
      'es' => $info->es,
      'images' => $images
    ];
  }, $albumList);

  $util->success($albums);
}
