<?php
/* composer require sendgrid/sendgrid */
/* make sure to set up & verify sender email at https://app.sendgrid.com/settings/sender_auth */

namespace Castoware;

use Exception;

class Sendgrid
{
  private $key, $cipher;

  function __construct($key = null)
  {
    $this->key = $key ?? bin2hex(openssl_random_pseudo_bytes(256));
    $this->cipher = "aes-256-gcm";
  }

  function encrypt($plaintext)
  {
    $cipher = $this->cipher;
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($plaintext, $cipher, $this->key, $options = 0, $iv, $tag);
    return $this->key . bin2hex($tag) . bin2hex($iv) . $ciphertext;
  }

  function decrypt($cipherHash)
  {
    $key = substr($cipherHash, 0, 512);
    $tag = hex2bin(substr($cipherHash, 512, 32));
    $iv = hex2bin(substr($cipherHash, 544, 24));
    $ciphertext = substr($cipherHash, 568);

    return openssl_decrypt($ciphertext, $this->cipher, $key, $options = 0, $iv, $tag);
  }

  function setupKeyFile($apiKey, $fileLocation)
  {
    $hash = $this->encrypt($apiKey);
    file_put_contents($fileLocation, $hash);
  }

  function apiKey($keyFile)
  {
    return $this->decrypt(file_get_contents($keyFile));
  }

  function sendEmail($replyTo, $replyToName, $to, $toName, $subject, $body)
  {
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom('gala-faantastica@castoware.com', 'Gala Faantastica Site');
    $email->setReplyTo($replyTo, $replyToName);
    $email->setSubject($subject);
    $email->addTo($to, $toName);
    $email->addContent('text/html', $body);

    $sendgrid = new \SendGrid($this->apiKey(__DIR__ . '/sendgrid.key'));

    try {
      $response = $sendgrid->send($email);

      $sendStatus = [
        'statusCode' => $response->statusCode(),
        'headers' => $response->headers(),
        'body' => $response->body()
      ];


      return $sendStatus;
    } catch (Exception $e) {
      error_log(print_r(['sendFail' => ['message' => $e->getMessage(), 'from' => $replyToName . " <" . $replyTo . ">", 'subject' => $subject, 'body' => $body, 'attempted' => date("Y-m-d")]], true));
      return ['statusCode' => false, 'data' => ['message' => $e->getMessage(), 'from' => $replyToName . " <" . $replyTo . ">", 'subject' => $subject, 'body' => $body, 'attempted' => date("Y-m-d")]];
    }
  }
}

/*
  1. get api key from sendgrid
  2. uncomment following code
  3. set $apiKey = api key
  4. set $fileLocation = the location of the sendgrid.key file (as specified in config)
  5. run this script
  6. recomment code below
*/

// $apiKey="";
// $fileLocation="";
// $crypt = new sendgrid();
// $crypt->setupKeyFile($apiKey, $fileLocation);
