<?php

namespace Castoware;

use Dibi\Connection;

class Database
{
  public $db;

  function __construct()
  {
    /* MySQL */
    // $username= "u466389499_legendary";
    // $password= "nH5Aze3?4[!l1NU45W(S";
    // $dbName= "u466389499_legendary";
    // $databaseConnection = $this->connectMysql($username, $password, $dbName);

    /* SQLite */
    $devPath = "/Users/mike/website-data-repo";
    $prodPath = "/home/u466389499/domains/castoware.com/data-repo";

    $dbPath = stristr($_SERVER['DOCUMENT_ROOT'], 'gala-faantastica-dev') ? $devPath : $prodPath;

    $dbFile = $dbPath . '/admin.db';
    $databaseConnection = $this->connectSqlite($dbFile);

    $this->db = new Connection($databaseConnection);
  }

  function connectSqlite($dbFile)
  {
    return [
      'driver' => 'sqlite',
      'database' => $dbFile
    ];
  }

  function connectMysql($username, $password, $dbName)
  {
    return [
      'driver'   => 'mysqli',
      'host'     => '127.0.0.1',
      'username' => $username,
      'password' => $password,
      'database' => $dbName,
    ];
  }
}
