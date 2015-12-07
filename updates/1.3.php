<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 3-12-2015
 * Time: 14:12
 */

$ds = new \PBPanel\Util\DataStore();
$oldConfig = [];

if (file_exists(\PBPanel\AppLoader::getBaseDir() . '/app/content/vars/config.php')) {
  $oldConfig = json_decode(file_get_contents(\PBPanel\AppLoader::getBaseDir() . '/app/content/vars/config.php'), JSON_OBJECT_AS_ARRAY);
  $messages[] = '<span class="text-warning">Please delete the "vars" folder in "./app/content", since it are no longer used.</span>';
}

/* Create bot connector table */
if ($ds->getVar('connector', 'botIp') == '') {
  
  $ds->setVar(
      'connector',
      'botIp',
      (array_key_exists('botIp', $oldConfig) ? $oldConfig['botIp'] : '')
  );
  $ds->setVar(
      'connector',
      'botBasePort',
      (array_key_exists('botBasePort', $oldConfig) ? $oldConfig['botBasePort'] : '25000')
  );
  $ds->setVar(
      'connector',
      'botName',
      (array_key_exists('botName', $oldConfig) ? $oldConfig['botName'] : '')
  );
  $ds->setVar(
      'connector',
      'botOauthToken',
      (array_key_exists('botOauthToken', $oldConfig) ? $oldConfig['botOauthToken'] : '')
  );
  $ds->setVar(
      'connector',
      'channelOwner',
      (array_key_exists('channelOwner', $oldConfig) ? $oldConfig['channelOwner'] : '')
  );
}

/* Create panel users table */

if (array_key_exists('panelUsers', $oldConfig)) {
  foreach ($oldConfig['panelUsers'] as $username => $hash) {
    $ds->setVar('users', $username, $hash);
  }
}

/* Create paths table */

$ds->setVar(
    'paths',
    'latestFollower',
    (array_key_exists('latestFollower', $oldConfig['paths']) ? $oldConfig['paths']['latestFollower'] : '/web/latestfollower.txt')
);
$ds->setVar(
    'paths',
    'latestDonation',
    (array_key_exists('latestDonation', $oldConfig['paths']) ? $oldConfig['paths']['latestDonation'] : '/addons/donationchecker/latestdonation.txt')
);
$ds->setVar(
    'paths',
    'youtubeCurrentSong',
    (array_key_exists('youtubeCurrentSong', $oldConfig['paths']) ? $oldConfig['paths']['youtubeCurrentSong'] : '/addons/youtubePlayer/currentsong.txt')
);
$ds->setVar(
    'paths',
    'youtubePlaylist',
    (array_key_exists('youtubePlaylist', $oldConfig['paths']) ? $oldConfig['paths']['youtubePlaylist'] : '/addons/youtubePlayer/requests.txt')
);
$ds->setVar(
    'paths',
    'defaultYoutubePlaylist',
    (array_key_exists('defaultYoutubePlaylist', $oldConfig['paths']) ? $oldConfig['paths']['defaultYoutubePlaylist'] : '/addons/youtubePlayer/defaultPlaylist.txt')
);

/* Create sfx commands table */

if (array_key_exists('sfxSettings', $oldConfig)) {
  foreach ($oldConfig['sfxSettings']['commands'] as $command => $file) {
    $ds->setVar('sfxcommands', $command, $file);
  }
}

/* Create misc table*/

$ds->setVar(
    'misc',
    'theme',
    (array_key_exists('theme', $oldConfig['paths']) ? $oldConfig['paths']['theme'] : 'style_dark')
);
$ds->setVar(
    'misc',
    'sfxEnabled',
    (array_key_exists('sfxSettings', $oldConfig) ? $oldConfig['sfxSettings']['enabled'] : 'false')
);
$ds->setVar(
    'misc',
    'currentVersion',
    '1.3'
);
$ds->setVar(
    'misc',
    'pbCompat',
    '1.6.6'
);