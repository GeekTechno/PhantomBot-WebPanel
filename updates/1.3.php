<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 3-12-2015
 * Time: 14:12
 */

$oldConfig = [];

if (file_exists(\PBPanel\AppLoader::getBaseDir() . '/app/content/vars/config.php')) {
  $oldConfig = json_decode(file_get_contents(\PBPanel\AppLoader::getBaseDir() . '/app/content/vars/config.php'), JSON_OBJECT_AS_ARRAY);
  $messages[] = '<span class="text-warning">Please delete the "vars" folder in "./app/content", since it are no longer used.</span>';
}

/* Create bot connector table */
if ($dataStore->getVar('connector', 'botIp') == '') {
  
  $dataStore->setVar(
      'connector',
      'botIp',
      (array_key_exists('botIp', $oldConfig) ? $oldConfig['botIp'] : '')
  );
  $dataStore->setVar(
      'connector',
      'botBasePort',
      (array_key_exists('botBasePort', $oldConfig) ? $oldConfig['botBasePort'] : '25000')
  );
  $dataStore->setVar(
      'connector',
      'botName',
      (array_key_exists('botName', $oldConfig) ? $oldConfig['botName'] : '')
  );
  $dataStore->setVar(
      'connector',
      'botOauthToken',
      (array_key_exists('botOauthToken', $oldConfig) ? $oldConfig['botOauthToken'] : '')
  );
  $dataStore->setVar(
      'connector',
      'channelOwner',
      (array_key_exists('channelOwner', $oldConfig) ? $oldConfig['channelOwner'] : '')
  );
}

/* Create panel users table */

if (array_key_exists('panelUsers', $oldConfig)) {
  foreach ($oldConfig['panelUsers'] as $username => $hash) {
    $dataStore->setVar('users', $username, $hash);
  }
}

/* Create paths table */

$dataStore->setVar(
    'paths',
    'latestFollower',
    (array_key_exists('latestFollower', $oldConfig['paths']) ? $oldConfig['paths']['latestFollower'] : '')
);
$dataStore->setVar(
    'paths',
    'latestDonation',
    (array_key_exists('latestDonation', $oldConfig['paths']) ? $oldConfig['paths']['latestDonation'] : '')
);
$dataStore->setVar(
    'paths',
    'youtubeCurrentSong',
    (array_key_exists('youtubeCurrentSong', $oldConfig['paths']) ? $oldConfig['paths']['youtubeCurrentSong'] : '')
);
$dataStore->setVar(
    'paths',
    'youtubePlaylist',
    (array_key_exists('youtubePlaylist', $oldConfig['paths']) ? $oldConfig['paths']['youtubePlaylist'] : '')
);
$dataStore->setVar(
    'paths',
    'defaultYoutubePlaylist',
    (array_key_exists('defaultYoutubePlaylist', $oldConfig['paths']) ? $oldConfig['paths']['defaultYoutubePlaylist'] : '')
);

/* Create sfx commands table */

if (array_key_exists('sfxSettings', $oldConfig)) {
  foreach ($oldConfig['sfxSettings']['commands'] as $command => $file) {
    $dataStore->setVar('sfxcommands', $command, $file);
  }
}

/* Create misc table*/

$dataStore->setVar(
    'misc',
    'theme',
    (array_key_exists('theme', $oldConfig['paths']) ? $oldConfig['paths']['theme'] : 'style_dark')
);
$dataStore->setVar(
    'misc',
    'sfxEnabled',
    (array_key_exists('sfxSettings', $oldConfig) ? $oldConfig['sfxSettings']['enabled'] : 'false')
);
$dataStore->setVar(
    'misc',
    'currentVersion',
    '1.3'
);
$dataStore->setVar(
    'misc',
    'pbCompat',
    '1.6.6'
);