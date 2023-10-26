<?php

use Image\Image;

function getThumb($db, $request, $util)
{
  $image = $_SERVER['DOCUMENT_ROOT'] . $request->query->image;
  $i = getimagesize($image);
  [$width, $height] = $i;
  $mime = $i['mime'];

  $im = new Image();

  $maxHeight = 400;
  $width = floor(($maxHeight * $width) / $height);
  $height = $maxHeight;

  $thumbFile = sys_get_temp_dir() . "/" . uniqid();

  $im->load($image)
    ->resize($width, $height)
    ->save($thumbFile);

  header("Content-Type: " . $mime);
  readfile($thumbFile);
}
