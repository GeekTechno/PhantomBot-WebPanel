<?php
/**
 * Functions.class.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */
namespace PBPanel\Util;

class FunctionLibrary
{
  /* @var DataStore $dataStore */
  private $dataStore;
  /* @var BotConnectionHandler $connection */
  private $connection;
  /* @var PanelSession $session */
  private $session;

  /**
   * @param DataStore $dataStore
   * @param BotConnectionHandler $connection
   */
  public function __construct($dataStore, $connection)
  {
    $this->dataStore = $dataStore;
    $this->connection = $connection;
    $this->session = new PanelSession();
  }

  /**
   * @param string $username
   * @param string $md5Password
   * @return bool
   */
  public function isValidUser($username, $md5Password)
  {
    $userHash = $this->dataStore->getVar('users', $username);
    if ($userHash && $md5Password == $userHash) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @return array
   */
  public function getPartsList()
  {
    $directoryContents = [];
    $currentPath = '';
    $it = new SortedDirectoryIterator('./app/parts');

    /* @var \SplFileInfo $file */
    foreach ($it as $file) {
      if ($file->isDir()) {
        $currentPath = $file->getBasename();
        $directoryContents[$file->getBasename()] = [];
      } else if ($currentPath != '') {
        $directoryContents[$currentPath][] = [
            'isCustom' => (strpos($file->getBasename(), 'custom') > -1),
            'partFile' => $file->getBasename(),
            'partName' => ucwords(trim(str_replace(['-', 'custom'], [' ', ''], $file->getBasename('.php')))),
        ];
      }
    }
    unset($directoryContents['static']);
    return array_reverse($directoryContents);
  }

  public function getCurrentTitle()
  {
    $currentTitle = $this->getOtherFile($this->dataStore->getVar('paths', 'youtubeCurrentSong'));
    if ($currentTitle) {
      $this->sendBackOk($this->cleanYTVideoTitle($currentTitle));
    } else {
      $this->sendBackError('getCurrentTitle', 418, 418, 'Failed to load file');
    }
  }

  /**
   * @param string $uri
   * @param bool $internal
   * @return bool|string
   */
  public function getOtherFile($uri, $internal = true)
  {
    $result = $this->connection->get($uri);
    if ($result[1] != 200) {
      $result = $this->connection->get('/..' . $uri);
    }
    if ($result[1] == 200) {
      if ($internal) {
        return $result[0];
      } else {
        $this->sendBackOk($result[0]);
      }
    } else {
      if ($internal) {
        return false;
      } else {
        $this->sendBackError($uri, $result[1], $result[2], $result[3]);
      }
    }
    return false;
  }

  /**
   * @param mixed $result
   * @param int $status
   */
  public function sendBackOk($result, $status = 200)
  {
    echo json_encode([$result, $status, 0, '']);
  }

  /**
   * @param string $action
   * @param int $status
   * @param int $errorNo
   * @param string $error
   */
  public function sendBackError($action, $status = 500, $errorNo = 500, $error = '')
  {
    echo json_encode([$action, $status, $errorNo, $error]);
  }

  /**
   * @param string $YTVideoTitle
   * @return string
   */
  public function cleanYTVideoTitle($YTVideoTitle)
  {
    return str_replace([
        '(OUT NOW!) ',
        '【Trap】',
        '(Official Video)',
        '(HQ)',
        '"',
    ], '', $YTVideoTitle);
  }

  /**
   * @param string $dbName
   * @param string $key
   * @param bool $partialKey
   * @return string
   */
  public function getDbTableValueByKey($dbName, $key, $partialKey = false)
  {
    $ini = $this->getDbTableArray($dbName, true);
    if ($ini && $partialKey) {
      foreach ($ini as $iniKey => $iniValue) {
        if (strpos($iniKey, $key) > -1) {
          return $iniValue;
        }
      }
      return '';
    } else {
      return ($ini && array_key_exists($key, $ini) ? $ini[$key] : '0');
    }
  }

  /**
   * @param string $dbName
   * @param bool $internal
   * @return array
   */
  public function getDbTableArray($dbName, $internal = true)
  {
    $iniStringResult = $this->getDbTableRaw($dbName, true);
    $iniArray = [];
    if ($iniStringResult) {
      $iniArray = @parse_ini_string($iniStringResult, null, INI_SCANNER_RAW);
      if ($internal) {
        ksort($iniArray);
        return $iniArray;
      } else {
        if ($iniArray) {
          $this->sendBackOk($iniArray);
        } else {
          $this->sendBackError('Could not parse ini');
        }
      }
    } else {
      if ($internal) {
        return $iniArray;
      } else {
        $this->sendBackError('Could not retrieve ini');
      }
    };
    return $iniArray;
  }

  /**
   * @param string $dbName
   * @param bool $internal
   * @return bool|string
   */
  public function getDbTableRaw($dbName, $internal = true)
  {
    $dbName = preg_replace('/inistore|[\/.]|ini/i', '', $dbName);
    $iniStringResult = $this->connection->get('/inistore/' . $dbName . '.ini');
    if ($iniStringResult[1] == 200) {
      if ($internal) {
        return $iniStringResult[0];
      } else {
        $this->sendBackOk($iniStringResult[0]);
      }
    }
    return false;
  }

  public function getMusicPlayerPlaylist($requestsFilePath)
  {
    $parsedList = '';
    $requestsFile = $this->getOtherFile($requestsFilePath);
    $requestsFile = preg_split('/[\r\n]+/i', $requestsFile);
    foreach ($requestsFile as $key => $line) {
      if (trim($line) != '') {
        if ($key == 1) {
          $parsedList .= '<li>Last: ' . $this->cleanYTVideoTitle(preg_replace('/^[a-z0-9_-]{11}\s/i', '', trim($line))) . '</li>';
        } else {
          $parsedList .= '<li>' . $this->cleanYTVideoTitle(preg_replace('/^[a-z0-9_-]{11}\s[0-9.]{2,3}/i', '. ', trim($line))) . '</li>';
        }
      }
    }
    return $parsedList;
  }

  /**
   * @param string $requestUri
   */
  public function execExternalApi($requestUri)
  {
    $result = @file_get_contents($requestUri);
    if ($result) {
      $this->sendBackOk($result);
    } else {
      $this->sendBackError('Could not execute Api request');
    }
  }

  public function getJSConfig()
  {
    $this->sendBackOk([
        'owner' => strtolower($this->dataStore->getVar('connector', 'channelOwner')),
        'botName' => strtolower($this->dataStore->getVar('connector', 'botName')),
    ]);
  }

  public function getConfig()
  {
    $this->sendBackOk([
        'botAdress' => strtolower($this->dataStore->getVar('connector', 'botIp')),
        'owner' => strtolower($this->dataStore->getVar('connector', 'channelOwner')),
        'botName' => strtolower($this->dataStore->getVar('connector', 'botName')),
        'token' => $this->session->getSessionToken(),
    ]);
  }

  /**
   * @param int $seconds
   * @return string
   */
  public function secondsToTime($seconds)
  {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime('@' . $seconds);

    if ($seconds > 86400) {
      $output = $dtF->diff($dtT)->format('%a days, %h hours and %i minutes');
    } elseif ($seconds > 3600) {
      $output = $dtF->diff($dtT)->format('%h hours and %i minutes');
    } else {
      $output = $dtF->diff($dtT)->format('%i minutes');
    }

    if (($seconds % 60) > 0) {
      $output .= ' and ' . $dtF->diff($dtT)->format('%s seconds');
    }

    return $output;
  }

  /**
   * @param int $seconds
   * @return string
   */
  public function secondsToDate($seconds)
  {
    $dtT = new \DateTime("@$seconds");
    return $dtT->format('D dS F Y H:i');
  }

  public function strToBool($value)
  {
    return ($value == 'true');
  }

  /**
   * @param string $moduleName
   * @return bool
   */
  public function getModuleStatus($moduleName)
  {
    return $this->strToBool($this->getDbTableValueByKey('modules', $moduleName, true));
  }
}