<?php

/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 3-12-2015
 * Time: 13:09
 */
namespace PBPanel\Util;

class DataStore
{
  /* @var \SQLite3 $db */
  private $db;

  public function __construct()
  {
    if (!is_writable(\PBPanel\AppLoader::getBaseDir() . '/app/content/')) {
      die('The webserver needs read/write permissions on "' . \PBPanel\AppLoader::getBaseDir() . '/app/content/' . '" and it\'s contents!');
    }

    $this->db = new \SQLite3(\PBPanel\AppLoader::getBaseDir() . '/app/content/dataStore.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE, null);
    register_shutdown_function([$this, 'shutdown']);
  }

  /**
   * @param string $table
   * @return array
   */
  public function getTableAsArray($table)
  {
    $array = [];

    if (in_array($table, $this->listTables())) {
      $res = $this->db->query('SELECT * FROM `' . $table . '`;');

      while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $array[$row['key']] = $row['value'];
      }
    }

    return $array;
  }

  /**
   * @param string $table
   * @param string $key
   * @param string $default
   * @return string
   */
  public function getVar($table, $key, $default = '')
  {
    if (in_array($table, $this->listTables())) {
      $stmt = $this->db->prepare('SELECT value FROM `' . $table . '` WHERE key=:key;');
      $stmt->bindValue(':key', $key, SQLITE3_TEXT);
      $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

      if ($res) {
        return $res['value'];
      }
    }
    return $default;
  }

  public function getRowById($table, $id)
  {
    if (in_array($table, $this->listTables())) {
      $stmt = $this->db->prepare('SELECT key, value FROM `' . $table . '` WHERE id=:id;');
      $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
      $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

      if ($res) {
        return [
            'key' => $res['key'],
            'value' => $res['value'],
        ];
      }
    }
    return [
        'key' => null,
        'value' => null,
    ];
  }

  /**
   * @param string $table
   * @param string $key
   * @param string $value
   * @return bool
   */
  public function setVar($table, $key, $value)
  {
    $this->createTable($table);
    $stmt = $this->db->prepare('INSERT OR REPLACE INTO `' . $table . '` (id, key, value) VALUES ((SELECT id FROM `' . $table . '` WHERE key=:key), :key, :value)');
    $stmt->bindValue(':key', $key, SQLITE3_TEXT);
    $stmt->bindValue(':value', $value, SQLITE3_TEXT);

    return $stmt->execute();
  }

  /**
   * @param string $table
   * @param string $key
   * @return \SQLite3Result
   */
  public function delVar($table, $key)
  {
    if (in_array($table, $this->listTables())) {
      $stmt = $this->db->prepare('DELETE FROM `' . $table . '` WHERE key=:key;');
      $stmt->bindValue(':key', $key, SQLITE3_TEXT);
      return $stmt->execute();
    }
    return false;
  }

  /**
   * @param string $tableName
   * @return bool
   */
  public function createTable($tableName)
  {
    if (!in_array($tableName, $this->listTables())) {
      $this->db->exec('CREATE TABLE `' . $tableName . '` (id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, `key`	TEXT NOT NULL, `value` TEXT);');
    }
  }

  /**
   * @return array
   */
  public function listTables()
  {
    $array = [];
    $res = $this->db->query('SELECT name FROM sqlite_master WHERE type=\'table\';');

    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
      $array[] = $row['name'];
    }

    return $array;
  }

  public function shutdown()
  {
    $this->db->close();
  }

  /**
   * @param mixed $arg
   * @return int
   */
  protected function getArgType($arg)
  {
    switch (gettype($arg)) {
      case 'double':
        return SQLITE3_FLOAT;
      case 'integer':
        return SQLITE3_INTEGER;
      case 'boolean':
        return SQLITE3_INTEGER;
      case 'NULL':
        return SQLITE3_NULL;
      case 'string':
        return SQLITE3_TEXT;
      default:
        throw new \InvalidArgumentException('Argument is of invalid type ' . gettype($arg));
    }
  }

  /**
   * @param \SQLiteException $e
   */
  protected function printStackTrace($e)
  {
    echo $e->getMessage() . '<br /><pre>' . $e->getTraceAsString() . '</pre>';
  }
}