<?php

/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 23-11-2015
 * Time: 08:21
 */
class PanelSession
{
  public function PanelSession()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public function createToken()
  {
    $_SESSION['PART_LOAD_TOKEN'] = bin2hex(openssl_random_pseudo_bytes(16));
  }

  public function checkSessionToken($token)
  {
    if ($this->getSessionToken() == $token) {
      return true;
    } else {
      session_destroy();
      return false;
    }
  }

  public function getSessionToken()
  {
    return (isset($_SESSION['PART_LOAD_TOKEN']) ? $_SESSION['PART_LOAD_TOKEN'] : false);
  }
}