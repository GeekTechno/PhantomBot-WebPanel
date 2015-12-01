<?php

/**
 * ConnectionHandler.class.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */
class ConnectionHandler
{
  private $config;
  private $curl;

  /**
   * @param Configuration $config
   */
  public function ConnectionHandler($config)
  {
    $this->config = $config;
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

    $this->curl = curl_init($this->config->botIp . $uri);
    curl_setopt($this->curl, CURLOPT_PORT, $this->config->botBasePort);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
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
   * @return array
   */
  public function send($message)
  {
    $this->init();
    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    $curlPass = str_replace("oauth:", "", $this->config->botOauthToken);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('user: ' . $this->config->channelOwner, 'message: ' . urlencode($message), 'password: ' . $curlPass));
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

    $curlPass = str_replace("oauth:", "", $this->config->botOauthToken);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('password: ' . $curlPass));
    $result = curl_exec($this->curl);
    $eno = curl_errno($this->curl);
    $err = curl_error($this->curl);
    $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    $this->close();

    return array($result, $status, $eno, $err);
  }
} 