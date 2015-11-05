<?php
/**
 * Functions.class.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */
require_once('SortedDirectoryIterator.class.php');

class Functions
{
  private $config;
  private $connection;

  /**
   * @param Configuration $config
   * @param ConnectionHandler $connection
   */
  public function Functions($config, $connection)
  {
    $this->config = $config;
    $this->connection = $connection;
  }

  /**
   * @param string $username
   * @param string $md5Password
   * @return bool
   */
  public function isValidUser($username, $md5Password)
  {
    if (array_key_exists($username, $this->config->panelUsers) && $md5Password == $this->config->panelUsers[$username]) {
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

    /* @var SplFileInfo $file */
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

  /**
   * @param string $error
   * @param int $errorNo
   * @param int $status
   */
  public function sendBackError($error, $status = 500, $errorNo = 500)
  {
    echo json_encode(['', $status, $errorNo, $error]);
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
   * @param string $YTVideoTitle
   * @return string
   */
  public function cleanYTVideoTitle($YTVideoTitle)
  {
    return preg_replace('/^[a-z0-9_-]{11}\s[0-9]+\.\s|\[[a-z0-9\s]+\]\s-\s|\[[a-z\s]+\]/i', '',
        str_replace([
            '(OUT NOW!) ',
            '【Trap】',
            '(Official Video)',
            '(HQ)',
        ], '', $YTVideoTitle));
  }

  public function getCurrentTitle()
  {
    $currentTitle = $this->getOtherFile($this->config->paths['youtubeCurrentSong']);
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
   * @param string $iniName
   * @param bool $internal
   * @return bool|string
   */
  public function getIniRaw($iniName, $internal = true)
  {
    $iniName = preg_replace('/inistore|[\/.]|ini/i', '', $iniName);
    $iniStringResult = $this->connection->get('/inistore/' . $iniName . '.ini');
    if ($iniStringResult[1] == 200) {
      if ($internal) {
        return $iniStringResult[0];
      } else {
        $this->sendBackOk($iniStringResult[0]);
      }
    }
    return false;
  }

  /**
   * @param string $iniName
   * @param bool $internal
   * @return array
   */
  public function getIniArray($iniName, $internal = true)
  {
    $iniStringResult = $this->getIniRaw($iniName, true);
    $iniArray = [];
    if ($iniStringResult) {
      $iniArray = @parse_ini_string($iniStringResult, null, INI_SCANNER_RAW);
      if ($internal) {
        $this->_specialIniArrayCases($iniName, $iniArray);
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
   * @param string $iniName
   * @param string $key
   * @param bool $partialKey
   * @return string
   */
  public function getIniValueByKey($iniName, $key, $partialKey = false)
  {
    $ini = $this->getIniArray($iniName, true);
    if ($ini && $partialKey) {
      foreach ($ini as $iniKey => $iniValue) {
        if (strpos($iniKey, $key) > -1) {
          return $iniValue;
        }
      }
      return '0';
    } else {
      return ($ini && array_key_exists($key, $ini) ? $ini[$key] : '0');
    }
  }

  public function getMusicPlayerPlaylist($requestsFilePath, $internal = false)
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
        'owner'   => strtolower($this->config->channelOwner),
        'botName' => strtolower($this->config->botName),
    ]);
  }

  public function getConfig()
  {
    $this->sendBackOk([
        'botAdress' => strtolower($this->config->botIp),
        'botName'   => strtolower($this->config->botName),
        'owner'     => strtolower($this->config->channelOwner),
    ]);
  }

  /**
   * @param int $seconds
   * @return string
   */
  public function secondsToTime($seconds)
  {
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    if ($seconds > 86400) {
      return $dtF->diff($dtT)->format('%a days, %h hours and %i minutes');
    } elseif ($seconds > 3600) {
      return $dtF->diff($dtT)->format('%h hours and %i minutes');
    } else {
      return $dtF->diff($dtT)->format('%i minutes');
    }
  }

  /**
   * @param string $botTime
   * @return array
   */
  public function botTimeToStandardFormat($botTime)
  {
    $botTimeMatches = [];
    preg_match('/([0-9]{2})-([0-9]{2})-([0-9]{4})\s\@\s([0-9:]+)\s([0-9+]+)/', $botTime, $botTimeMatches);
    $dateTime = new DateTime(
        $botTimeMatches[3] . '-'
        . $botTimeMatches[1] . '-'
        . $botTimeMatches[2] . 'T'
        . $botTimeMatches[4]
        . $botTimeMatches[5]
    );
    return $dateTime->format('D dS M Y @ h:m a');
  }

  /**
   * @param string $scriptName
   * @return int
   */
  public function getModuleStatus($scriptName)
  {
    $modules = $this->getIniArray('/inistore/modules.ini', true);
    foreach ($modules as $moduleFullPath => $active) {
      if (strpos(strtolower($moduleFullPath), strtolower($scriptName)) > -1) {
        return $active;
      }
    }
    return -1;
  }

  /**
   * @param string $iniName
   * @param string $iniArray
   * @return array
   */
  private function _specialIniArrayCases($iniName, &$iniArray)
  {
    $iniName = preg_replace('/inistore|[\/.]|ini/i', '', $iniName);
    switch (strtolower($iniName)) {
      case 'modules':
        $iniArray['./util/chatModerator.js_enabled'] = '1';
        break;
      default:
        break;
    }
  }
}