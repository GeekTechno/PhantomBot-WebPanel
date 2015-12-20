<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 3-12-2015
 * Time: 17:08
 */
namespace PBPanel;

class AppLoader
{
  /**
   * @return string
   *
   * Get the absolute path to the installation
   * E.g. /var/www/panel
   */
  public static function getBaseDir()
  {
    return dirname(__FILE__);
  }

  /**
   * @return string
   *
   * The absolute path to the document root, as is defined in the server configuration
   */
  public static function getDocRoot()
  {
    return filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
  }

  /**
   * @return string
   *
   * The path relative to example.com/
   */
  public static function getBaseUrl()
  {
    return filter_input(INPUT_SERVER, 'HTTP_HOST') . str_replace(self::getDocRoot(), '', self::getBaseDir());
  }

  /**
   * Autoload Panel Classes
   */
  public static function load()
  {
    $files = glob(self::getBaseDir() . '/app/util/*');
    foreach ($files as $file) {
      require_once($file);
    }
  }

  /**
   * @param string $utilName
   *
   * load specific class
   */
  public static function loadUtil($utilName)
  {
    require_once(self::getBaseDir() . '/app/util/' . $utilName . '.class.php');
  }

  /**
   * @param \PBPanel\Util\DataStore $dataStore
   * @return bool
   */
  public static function runInstall($dataStore)
  {
    return ($dataStore->getVar('connector', 'botIp') == '');
  }

  /**
   * @param \PBPanel\Util\DataStore $dataStore
   * @return bool
   */
  public static function updateAvailable($dataStore)
  {
    $currentVersion = floatval($dataStore->getVar('misc', 'currentVersion', 0.0));
    $latestUpdateAvailable = 0.0;
    $updateFiles = glob(self::getBaseDir() . '/updates/*');

    foreach ($updateFiles as $file) {
      $fileVersion = floatval(basename($file, '.php'));
      if ($fileVersion > $latestUpdateAvailable) {
        $latestUpdateAvailable = $fileVersion;
      }
    }

    return ($latestUpdateAvailable > $currentVersion);
  }
}