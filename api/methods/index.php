<?php
$fileList = array_filter(glob(__DIR__ . "/*.php"), function ($filename) {
  return $filename != __DIR__ . '/index.php';
});

foreach ($fileList as $filename) {
  require_once($filename);
}
