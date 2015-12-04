<?php
/**
 * index.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:47
 */
require_once('AppLoader.class.php');
\PBPanel\AppLoader::load();

$dataStore = new \PBPanel\Util\DataStore();

if (\PBPanel\AppLoader::runInstall($dataStore)) {
  require_once('install.php');
  exit;
}

if (\PBPanel\AppLoader::updateAvailable($dataStore)) {
  require_once('update.php');
  exit;
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
  <script src="app/js/spark-md5.min.js" type="text/javascript"></script>
  <script src="app/js/login.min.js" type="text/javascript"></script>
</head>
<body>
<div id="page-wrapper">
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <img alt="PhantomBot Web Panel" src="app/content/static/logo-small.png" role="button"
             onclick="loadPartFromStorage()"/>
        <span class="panel-version text-muted">version <?= $dataStore->getVar('misc', 'currentVersion') ?></span>
      </div>
    </div>
  </nav>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">Login</h4>
    </div>
    <div class="panel-body">
      <form id="login-form">
        <div class="alert alert-danger" id="login-alert"></div>
        <div class="form-group">
          <input type="text" id="login-username" placeholder="Username" class="form-control"/>
        </div>
        <div class="form-group">
          <input type="password" id="login-password" placeholder="Password" class="form-control"/>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
      </form>
    </div>
  </div>
  <div class="panel panel-default page-footer">
    <div class="panel-body text-muted">
      PhantomBot Control Panel
      <small><?= $dataStore->getVar('misc', 'version') ?></small>
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