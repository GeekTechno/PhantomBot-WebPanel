<?php
/**
 * install.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */
require_once('classes/Configuration.class.php');

$config = new Configuration(true);
$input = filter_input_array(INPUT_POST);
$nextInstallationStep = 1;

if (is_array($input) && array_key_exists('currentStep', $input)) {
  $nextInstallationStep = $input['currentStep'] + 1;
  $currentStep = $input['currentStep'];
  unset($input['currentStep']);
  switch ($currentStep) {
    case 1:
      if (!$config->_saveToConfig($input)) {
        saveError();
      }
      break;
    case 2:
      if (!$config->_saveToConfig(['panelUsers' => [$input['username'] => $input['psswd']]])) {
        saveError();
      }
      break;
  }
}

function saveError()
{
  echo 'Could not save your settings to the configuration file! Make sure that the installation directory is writeable by your webserver!';
  exit;
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="../css/<?= (array_key_exists('theme', $config->paths) ? $config->paths['theme'] : 'style_dark') ?>.css"
        rel="stylesheet" type="text/css"/>
  <script src="../js/jquery-1.11.3.min.js" type="text/javascript"></script>
  <script src="../js/spark-md5.min.js" type="text/javascript"></script>
  <script src="../js/install.min.js" type="text/javascript"></script>
</head>
<body>
<div id="page-wrapper">
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand">PhantomBot Control Panel <br/><span
              class="panel-version text-muted">version <?= $config->version ?></span></a>
      </div>
    </div>
  </nav>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">Installation <span class="text-muted">Step <?= $nextInstallationStep ?></span></h4>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <form action="<?= filter_input(INPUT_SERVER, 'PHP_SELF') ?>" method="post" name="step1"
                style="display:<?= ($nextInstallationStep == 1 ? 'block' : 'none') ?>">
            <h2>Hey!</h2>

            <p>
              Before you can start using the PhantomBot Web Panel, I&rsquo;m going to need some information about the
              PhantomBot installation first.
            </p>

            <div class="form-group">
              <label>PhantomBot Webserver Address</label>
              <input type="text" placeholder="Ip adress or Url" name="botIp" class="form-control"/>

              <p class="text-muted">
                This is generally the Ip address of the PC running PhantomBot.<br/>
                (Use "localhost" if you have PhantomBot running on the same PC as this webserver)
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
            <input type="hidden" name="currentStep" value="<?= $nextInstallationStep ?>"/>
            <button class="btn btn-primary btn-block">Continue</button>
          </form>
          <form action="install.php" method="post" name="step2"
                style="display:<?= ($nextInstallationStep == 2 ? 'block' : 'none') ?>">
            <p>
              Create a user account for the PhantomBot Web Panel.
            </p>

            <div class="form-group">
              <span>Set Your Username</span>
              <input type="text" placeholder="Username" name="username" class="form-control"/>
            </div>
            <div class="form-group">
              <span>Set Your Password</span>
              <input type="password" placeholder="Password" name="psswd" class="form-control"/>
            </div>
            <input type="hidden" name="currentStep" value="<?= $nextInstallationStep ?>"/>
            <button class="btn btn-primary btn-block">Continue</button>
          </form>
          <div class="text-center" style="display:<?= ($nextInstallationStep == 3 ? 'block' : 'none') ?>">
            <h2>All Done!</h2>

            <p>
              Your settings have been saved!
            </p>

            <p>
              Proceed to <a href="../../update.php">The updater</a>!
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default page-footer">
    <div class="panel-body text-muted">
      PhantomBot Control Panel
      <small><?= $config->version ?></small>
      Developed by <a href="//juraji.nl" target="_blank">Juraji</a> &copy;<?= date('Y') ?><br/>
      Compatible with <a href="//www.phantombot.net/" target="_blank">PhantomBot <?= $config->pBCompat ?></a>,
      developed by <a href="//phantombot.net/members/phantomindex.1/" target="_blank">PhantomIndex</a>,
      <a href="//phantombot.net/members/gloriouseggroll.2/" target="_blank">GloriousEggroll</a> &amp;
      <a href="//phantombot.net/members/gmt2001.28/" target="_blank">gmt2001</a>.
    </div>
  </div>
</div>
</body>
</html>