<?php

namespace Castoware;

use Dibi\Connection;

class Database
{
  public $db, $mysql;

  function __construct()
  {
    /* MySQL */
    $username = "u466389499_faan_gala";
    $password = "p43dS(Pk0WIVk})I!d$4";
    $dbName = "u466389499_faan_gala";
    $databaseConnection = $this->connectMysql($username, $password, $dbName);
    $this->db = new Connection($databaseConnection);

    /* SQLite */
    // $devPath = "/Users/mike/website-data-repo";
    // $prodPath = "/home/u466389499/domains/castoware.com/data-repo";

    // $dbPath = stristr($_SERVER['DOCUMENT_ROOT'], 'gala-faantastica-dev') ? $devPath : $prodPath;

    // $dbFile = $dbPath . '/admin.db';
    // $databaseConnection = $this->connectSqlite($dbFile);

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
      // 'profiler' => ['file' => __DIR__ . '/sql.log']
    ];
  }
}
