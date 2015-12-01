<?php
/**
 * 0.9.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 14 okt 2015
 * Time: 12:09
 */

/**
 * Changes: Use config for PhantomBot add-on file output paths
 */

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
$config = new Configuration();

if (!array_key_exists('latestFollower', $config->paths)) {
  $config->_saveToConfig([
      'paths' => [
          'latestFollower' => '/web/latestfollower.txt',
          'latestDonation' => '/addons/donationchecker/latestdonation.txt',
          'youtubeCurrentSong' => '/addons/youtubePlayer/currentsong.txt',
          'youtubePlaylist' => '/addons/youtubePlayer/requests.txt',
      ]
  ]);
}
