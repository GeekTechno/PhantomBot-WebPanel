<?php
/**
 * marathon.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:40
 */

require_once('../../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\ConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$timezoneSettings = $functions->getIniArray('timezone');
$marathonSettings = $functions->getIniRaw('marathon', true);
$lastAnnouncement = null;
$marathonName = null;
$marathonLink = null;
$scheduleTableRows = '';

$marathonSettings = explode(PHP_EOL, $marathonSettings);

if ($marathonSettings) {
  foreach ($marathonSettings as $line) {
    if ($line == '') {
      continue;
    }
    $key = explode('=', $line)[0];
    $value = explode('=', $line)[1];
    switch ($key) {
      case 'lastAnnounce':
        $lastAnnouncement = date('D dS M Y @ h:i a', $value / 1000);
        break;
      case 'name':
        $marathonName = $value;
        break;
      case 'link':
        $marathonLink = $value;
        break;
      default:
        $splitValue = explode(';', $value);
        $scheduleTableRows .= '<tr><td>' . $splitValue[1]
            . '</td><td>' . date('D dS M Y @ h:i a', $splitValue[0] / 1000) . '</td><td>'
            . $templates->botCommandButton('marathon schedule delete '
                . date('m/d H:i', $splitValue[0] / 1000), 'Clear', 'danger btn-sm') . '</td></tr>';
        break;
    }
  }
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Marathon System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('marathonCommand.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-group">
        <?= $templates->botCommandButton('marathon', 'Current Marathon') ?>
        <?= $templates->botCommandButton('marathon clear', 'Clear Marathons') ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Current Marathon</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('marathon name', 'Change marathon name', '[name]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('marathon link', 'Change marathon link', '[url]') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Schedule Marathon</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('marathon schedule add', 'Schedule marathon', '[name] [MM/DD] [HH:MM]') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Timezone</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <?= $templates->botCommandButton('timezone', 'Current Timezone') ?>
            </div>
            <?= $templates->botCommandForm('timezone', 'Change bot timezone', '[timezone]', (array_key_exists('timezone', $timezoneSettings) ? $timezoneSettings['timezone'] : '')) ?>
          </div>
          <div class="col-sm-4 col-sm-offset-4">
            <?= $templates->informationPanel('Marathon commands use the Time-Zone module that\'s included in the script in order to make up for timezone differences.') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4>Current Marathon</h4>

      <p>
        <?= (!$marathonName && !$marathonLink && !$lastAnnouncement ? 'No marathon currently active.' : '') ?>
        <?= ($marathonName ? 'Marathon: ' . $marathonName . '<br />' : '') ?>
        <?= ($marathonLink ? 'Link: <a href="' . $marathonLink . '" target="_blank">' . $marathonLink . '</a><br />' : '') ?>
        <?= ($lastAnnouncement ? 'Last Announcement: ' . $lastAnnouncement . '<br />' : '') ?>
      </p>
      <?= $templates->dataTable('Scheduled Marathons', ['Marathon Name', 'Date', 'Clear'], $scheduleTableRows, true) ?>
    </div>
  </div>
</div>