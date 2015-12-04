<?php
/**
 * Created by PhpStorm.
 * User: Robin | Juraji
 * Date: 4-12-2015
 * Time: 16:59
 */

require_once('AppLoader.class.php');
\PBPanel\AppLoader::loadUtil('DataStore');

$dataStore = new \PBPanel\Util\DataStore();

$currentVersion = floatval($dataStore->getVar('misc', 'currentVersion', 0.0));
$hasUpdate = \PBPanel\AppLoader::updateAvailable($dataStore);
$messages = [];
$messagesString = '';

if ($hasUpdate) {
  $updateFiles = glob(\PBPanel\AppLoader::getBaseDir() . '/updates/*');
  foreach ($updateFiles as $file) {
    $updateFileVersion = floatval(basename($file, '.php'));
    if ($updateFileVersion > $currentVersion) {
      require_once($file);
      $messages[] = 'Applied update ' . $updateFileVersion;
    }
  }
} else {
  $messages[] = 'There are no updates available!';
}

foreach ($messages as $message) {
  $messagesString .= '<p>' . $message . '</p>';
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="app/css/<?= $dataStore->getVar('misc', 'theme', 'style_dark') ?>.css"
        rel="stylesheet" type="text/css"/>
  <link rel="icon" href="favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
  <script src="//code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
</head>
<body>
<div id="page-wrapper">
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <img alt="PhantomBot Web Panel" src="app/content/static/logo-small.png"/>
        <span class="panel-version text-muted">version <?= $currentVersion ?></span>
      </div>
    </div>
  </nav>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">Update</h4>
    </div>
    <div class="panel-body">
      <?=  $messagesString ?>
      <p>
        Continue to <a href="index.php">Login</a>
      </p>
    </div>
  </div>
  <div class="panel panel-default page-footer">
    <div class="panel-body text-muted">
      PhantomBot Control Panel
      <small><?= $currentVersion ?></small>
      Developed by <a href="//juraji.nl" target="_blank">juraji</a> &copy;<?= date('Y') ?><br/>
      Compatible with <a href="//www.phantombot.net/"
                         target="_blank">PhantomBot <?= $dataStore->getVar('misc', 'pBCompat') ?></a>,
      developed by <a href="//phantombot.net/members/phantomindex.1/" target="_blank">phantomindex</a>,
      <a href="//phantombot.net/members/gloriouseggroll.2/" target="_blank">gloriouseggroll</a> &amp;
      <a href="//phantombot.net/members/gmt2001.28/" target="_blank">gmt2001</a>.
    </div>
  </div>
</div>
</body>
</html>
