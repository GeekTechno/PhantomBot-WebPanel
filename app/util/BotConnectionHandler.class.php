<?php

/**
 * BotConnectionHandler.class.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */
namespace PBPanel\Util;

class BotConnectionHandler
{
  private $dataStore;
  private $curl;

  /**
   * @param DataStore $dataStore
   */
  public function __construct($dataStore)
  {
    $this->dataStore = $dataStore;
  }

  public function testConnection()
  {
    $this->init();
    curl_setopt($this->curl, CURLOPT_HEADER, true);
    curl_setopt($this->curl, CURLOPT_NOBODY, true);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, false);
    $result = curl_exec($this->curl);
    $err = curl_error($this->curl);
    $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    $eno = curl_errno($this->curl);
    $this->close();

    return array($result, $status, $eno, $err);
  }

  /**
   * @param string $uri
   */
  private function init($uri = '')
  {
    if (!empty($uri) && substr($uri, 0, 1) != '/') {
      $uri = '/' . $uri;
    }

    $this->curl = curl_init($this->dataStore->getVar('connector', 'botIp') . $uri);
    curl_setopt($this->curl, CURLOPT_PORT, $this->dataStore->getVar('connector', 'botBasePort'));
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_USERAGENT, 'Chrome/44.0.2403.52 PhantomPanel/1.0');

    if (defined('CURLOPT_IPRESOLVE')) {
      curl_setopt($this->curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }
  }

  /**
   *
   */
  private function close()
  {
    curl_close($this->curl);
  }

  /**
   * @param string $message
   * @param string $user
   * @return array
   */
  public function send($message, $user = '')
  {
    if ($user == '') {
      $user = $this->dataStore->getVar('connector', 'channelOwner');
    }
    $this->init();
    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    $curlPass = str_replace("oauth:", "", $this->dataStore->getVar('connector', 'botOauthToken'));
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('user: ' . $user, 'message: ' . urlencode($message), 'password: ' . $curlPass));
    $result = curl_exec($this->curl);
    $err = curl_error($this->curl);
    $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    $eno = curl_errno($this->curl);

    $this->close();

    return array(($result == 'event posted' ? 'Executed: ' . $message : $result), $status, $eno, $err);
  }

  /**
   * @param string $uri
   * @return array
   */
  public function get($uri)
  {
    $this->init($uri);

    $curlPass = str_replace("oauth:", "", $this->dataStore->getVar('connector', 'botOauthToken'));
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('password: ' . $curlPass));
    $result = curl_exec($this->curl);
    $eno = curl_errno($this->curl);
    $err = curl_error($this->curl);
    $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    $this->close();

    return array($result, $status, $eno, $err);
  }
} 