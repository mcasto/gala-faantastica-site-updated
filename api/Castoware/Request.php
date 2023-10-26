<?php

namespace Castoware;

class Request
{
  public $auth, $headers, $body, $post, $files, $params, $query;

  function __construct()
  {
    $this->headers = function_exists('getallheaders') ? (object) getallheaders() : false;
    $this->auth = $this->headers->authorization
      ?? $this->headers->Authorization
      ?? null;
    $this->body = json_decode(file_get_contents("php://input"));
    $this->post = (object) $_POST;
    $this->files = (object) $_FILES;
    $this->query = (object) $_REQUEST;
  }
}
