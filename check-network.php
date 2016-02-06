<?php
/**
 * check-network.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 1 feb 2016
 * Time: 21:47
 */

require_once('AppLoader.class.php');

if (!class_exists('SQLite3')) {
  $botIp = $botBasePort = 'Unknown (Settings could not be read by SQLite3)';
  $foundBot = false;
} else {
  \PBPanel\AppLoader::load();

  $dataStore = new \PBPanel\Util\DataStore(true);
  $botConnection = new \PBPanel\Util\BotConnectionHandler($dataStore);

  $foundBot = ($botConnection->testConnection());
  $botIp = $dataStore->getVar('connector', 'botIp', 'Unknown (Not Set)');
  $botBasePort = $dataStore->getVar('connector', 'botBasePort', 'Unknown (Not Set)');
}

$clientIp = (filter_input(INPUT_SERVER, 'REMOTE_ADDR') == '::1' ? 'localhost' : filter_input(INPUT_SERVER, 'REMOTE_ADDR'));
$serverHasInternet = (@file_get_contents('http://www.google.com'));
$contentWriteable = is_writeable(\PBPanel\AppLoader::getBaseDir() . '/app/content');

function yesNoText($state)
{
  return ($state ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>');
}

function validateBotIp($botIp)
{
  $groups = explode('.', $botIp);
  $isIp = true;

  foreach ($groups as $group) {
    $group = intval($group);
    if ($group < 0 || $group > 255) {
      $isIp = false;
      break;
    }
  }


  if ($isIp) {
    return '<span class="text-success">' . $botIp . '</span>';
  } else {
    return '<span class="text-danger">' . $botIp . '</span>';
  }
}

function validateBotBasePort($botBasePort)
{
  $botBasePort = intval($botBasePort);
  if ($botBasePort >= 0 && $botBasePort <= 65535) {
    return '<span class="text-success">' . $botBasePort . '</span>';
  } else {
    return '<span class="text-danger">' . $botBasePort . '</span>';
  }
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="app/css/style_dark.css"
        rel="stylesheet" type="text/css"/>
  <link rel="icon" href="favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
</head>
<body>
<div id="page-wrapper">
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <img alt="PhantomBot Web Panel" src="app/content/static/logo-small.png"/>
      </div>
    </div>
  </nav>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">Checking your network...</h4>
    </div>
    <div class="panel-body">
      <p>
        This page shows you crucial information the Web Panel needs to function proper.<br/>
        If any of these show up red, you should check out the issue or find help on our <a
            href="https://community.phantombot.net">Forum</a>.
      </p>
      <h3>You</h3>
      <p>
        Your Local IP Address: <?= $clientIp ?><br/>
        <span class="text-muted">As seen from the webserver.</span>
      </p>
      <h3>Webserver</h3>
      <p>
        Internet connection: <?= yesNoText($serverHasInternet) ?><br/>
        <span class="text-muted">Is the webserver connected to the internet?</span>
      </p>
      <p>
        Content folder is writeable: <?= yesNoText($contentWriteable) ?><br/>
        <span class="text-info">./app/content</span><span class="text-muted"> needs to be writeable for PHP in order to save settings.</span>
      </p>
      <p>
        PHP <a href="https://www.google.nl/?q=php%20enable%20curl" target="_blank">cUrl</a>
        module: <?= yesNoText(extension_loaded('curl')) ?><br/>
        PHP <a href="https://www.google.nl/?q=php%20enable%20SQLite3" target="_blank">SQLite3</a>
        module: <?= yesNoText(extension_loaded('sqlite3')) ?><br/>
        PHP <a href="https://www.google.nl/?q=php%20enable%20OpenSSL" target="_blank">OpenSSL</a>
        module: <?= yesNoText(extension_loaded('openssl')) ?><br/>
      </p>
      <p>
        Set PhantomBot IP Address: <?= validateBotIp($botIp) ?><br/>
        Set PhantomBot Base Port: <?= validateBotBasePort($botBasePort) ?><br/>
        <span class="text-muted">These are the values you set PhantomBot should be found at.</span><br/>
        <br/>
        Found PhantomBot at the set address: <?= yesNoText($foundBot) ?><br/>
        <span class="text-muted">
          If this is "No" it means something is preventing the webserver from accessing PhantomBot.<br/>
          Check if PhantomBot is running at the set address, that PhantomBot's HTTP server is started (Should be stated in PhantomBot's console) and the information above is correct.
        </span>
      </p>
    </div>
  </div>
  <div class="panel panel-default page-footer">
    <div class="panel-heading">
      PhantomBot Web Panel
      &#xFF0F; <a href="//juraji.nl" target="_blank">juraji</a> &copy;<?= date('Y') ?>
    </div>
  </div>
</div>
</body>
</html>