<?php
/**
 * update.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:48
 */
define('BASEPATH', realpath(dirname(__FILE__)));

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');

$config = new Configuration();
$updatesDir = array_diff(scandir(BASEPATH . '/updates'), [
    '.',
    '..',
    'current_version.txt',
    'roadmap.txt',
]);

$updateInstalledTo = -1;
$installedUpdates = '';
$notification = [];
$updateNotes = [];

if (touch(BASEPATH . '/app/content/vars/current_version.txt')) {
  $currentUpdate = @file_get_contents(BASEPATH . '/app/content/vars/current_version.txt');
} else {
  throw new Exception('Could not read or create current version file! Make sure your webserver has write access to the "./app/content/vars" folder');
}

foreach ($updatesDir as $updateScript) {
  if (preg_match('/unreleased/i', $updateScript)) {
    continue;
  }
  $updateNo = floatval(str_replace('.php', '', $updateScript));
  if ($updateNo > floatval($currentUpdate)) {
    require_once(BASEPATH . '/updates/' . $updateScript);
    $installedUpdates .= '<li>Installed update: ' . $updateNo . '</li>';
    $updateInstalledTo = $updateNo;
  }
}

if ($updateInstalledTo > -1) {
  @file_put_contents(BASEPATH . '/app/content/vars/current_version.txt', $updateInstalledTo);
  $notification[] = 'Updates Installed!';
  $notification[] = 'You are now on version ' . $updateInstalledTo . '!';
} else {
  $notification[] = 'No updates available.';
  $notification[] = 'You are on version ' . $currentUpdate . '!';
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="app/css/<?= (array_key_exists('theme', $config->paths) ? $config->paths['theme'] : 'style_dark') ?>.css"
        rel="stylesheet" type="text/css"/>
</head>
<body>
<div id="page-wrapper">
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand">PhantomBot Control Panel <br/><span
              class="panel-version text-muted">version <?= ($config->version ? $config->version : 'new install') ?></span></a>
      </div>
    </div>
  </nav>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title"><?= $notification[0] ?></span></h4>
    </div>
    <div class="panel-body">
      <div class="update-info">
        <ul class="list-unstyled">
          <?= $installedUpdates ?>
        </ul>
        <p><?= join('<br /><br />', $updateNotes) ?></p>
        <?= $notification[1] ?>
        <p>
          Proceed to <a href="index.php">login</a>!
        </p>
      </div>
    </div>
  </div>
</div>
</body>
</html>