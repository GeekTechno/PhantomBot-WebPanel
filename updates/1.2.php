<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 3-12-2015
 * Time: 01:49
 */

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
$config = new Configuration();

if (!is_array($config->sfxSettings)) {
  $config->_saveToConfig([
      'sfxSettings' => [
        'enabled' => 'false',
        'commands' => [],
      ]
  ]);
}
