<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 3-12-2015
 * Time: 14:12
 */

/* Setup misc table*/

$dataStore->setVar(
    'misc',
    'theme',
    'style_dark'
);

$dataStore->setVar(
    'misc',
    'currentVersion',
    '2.0'
);

$dataStore->setVar(
    'misc',
    'pbCompat',
    '2.0'
);

$dataStore->setVar(
    'paths',
    'latestFollower',
    '/addons/followHandler/latestFollower.txt'
);

$dataStore->setVar(
    'paths',
    'youtubeCurrentSong',
    '/addons/youtubePlayer/currentSong.txt'
);

$dataStore->setVar(
    'paths',
    'defaultYoutubePlaylist',
    '/addons/youtubePlayer/defaultPlaylist.txt'
);

$dataStore->setVar(
    'paths',
    'latestDonation',
    '/addons/donationchecker/latestdonation.txt'
);

$dataStore->setVar(
    'paths',
    'youtubePlaylist',
    '/addons/youtubePlayer/requestQueue.txt'
);