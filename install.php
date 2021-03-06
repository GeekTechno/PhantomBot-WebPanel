<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 4-12-2015
 * Time: 18:31
 */

require_once('AppLoader.class.php');
\PBPanel\AppLoader::loadUtil('DataStore');

$dataStore = new \PBPanel\Util\DataStore();
$done = false;
$input = filter_input_array(INPUT_POST);

if ($dataStore->getVar('connector', 'botIp') != '') {
  $done = true;
}

if (count($input) == 7 && !$done) {
  $dataStore->setVar('users', $input['username'], $input['password']);
  $dataStore->setVar('connector', 'botIp', $input['botIp']);
  $dataStore->setVar('connector', 'botBasePort', $input['botBasePort']);
  $dataStore->setVar('connector', 'botName', $input['botName']);
  $dataStore->setVar('connector', 'botOauthToken', $input['botOauthToken']);
  $dataStore->setVar('connector', 'channelOwner', $input['channelOwner']);
  $done = true;
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
  <script src="app/js/install.min.js" type="text/javascript"></script>
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
      <h4 class="panel-title">Install</h4>
    </div>
    <div class="panel-body">
      <div class="row<?= ($done ? ' hidden' : '') ?>">
        <div class="col-xs-8">
          <h2>Hey!</h2>

          <p>
            Before you can start using the PhantomBot Web Panel, I&rsquo;m going to need some information about your
            PhantomBot installation first.
          </p>

          <p class="text-muted">
            Notice: This panel needs <a href="http://phantombot.net/forums/#current-builds.10" target="_blank">PhantomBot</a>.<br />
            It is not built in!
          </p>
          <form action="install.php" method="post">
            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label>WebPanel Username</label>
                  <input type="text" name="username" class="form-control"/>
                </div>
              </div>
              <div class="col-xs-6">
                <div class="form-group">
                  <label>WebPanel Password</label>
                  <input type="password" name="password" class="form-control"/>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>PhantomBot IP Address</label>
              <input type="text" placeholder="Ip adress or Url" name="botIp" class="form-control"/>

              <p class="text-muted">
                This is generally the Ip address of the machine running PhantomBot.
              </p>
            </div>
            <div class="form-group">
              <label>PhantomBot Webserver Base Port</label>
              <input type="number" placeholder="Default: 25000" name="botBasePort" class="form-control" value="25000"/>

              <p class="text-muted">
                This is by default "25000". Only change it if you have entered a custom port at the PhantomBot
                installation!
              </p>
            </div>
            <div class="form-group">
              <span>Username For Bot</span>
              <input type="text" placeholder="MyLovelyBot" name="botName" class="form-control"/>

              <p class="text-muted">
                The username of the account you used for PhantomBot.
              </p>
            </div>
            <div class="form-group">
              <label>Bot Account Oauth token</label>
              <input type="text" placeholder="oauth:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" name="botOauthToken"
                     class="form-control"/>

              <p class="text-muted">
                This can be found in &quot;botlogin.txt&quot; in the installation folder of PhantomBot. (Use the &quot;oauth&quot;
                one)
              </p>
            </div>
            <div class="form-group">
              <span>Channel Owner Username</span>
              <input type="text" placeholder="MyLovelySelf" name="channelOwner" class="form-control"/>
            </div>
            <button class="btn btn-primary btn-block">Continue</button>
          </form>
        </div>
      </div>
      <div class="<?= ($done ? '' : 'hidden') ?>">
        <h2>All Done!</h2>
        Proceed to the <a href="update.php">updater</a>.
      </div>
    </div>
  </div>
  <div class="panel panel-default page-footer">
    <div class="panel-heading">
      PhantomBot Web Panel
      <small><?= $dataStore->getVar('misc', 'currentVersion') ?></small>
      &#xFF0F; <a href="//juraji.nl" target="_blank">juraji</a> &copy;<?= date('Y') ?>
      &#xFF0F; Compatible with <a href="//www.phantombot.net/" target="_blank">PhantomBot <?= $dataStore->getVar('misc', 'pbCompat') ?></a>
    </div>
  </div>
</div>
</body>
</html>

