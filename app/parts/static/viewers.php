<?php

require_once('../../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST,
    'token'))
) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$minTimeSec = 3600;
$viewersDataRows = '';

$groups = $functions->getDbTableArray('groups');
$psName = ucfirst($functions->getDbTableValueByKey('pointSettings', 'pointNameMultiple'));

$followers = $functions->getDbTableArray('followed');
$viewerGroups = $functions->getDbTableArray('group');
$lastSeen = $functions->getDbTableArray('lastseen');
$viewerRanks = $functions->getDbTableArray('viewerRanks');
$viewerPoints = $functions->getDbTableArray('points');
$viewerTime = $functions->getDbTableArray('time');
$incRaids = $functions->getDbTableArray('incommingRaids');

if ($psName == '0') {
  $psName = 'Points';
}

$viewers = array_filter(array_keys($viewerTime), function ($username) use ($viewerTime, $minTimeSec) {
  return (intval($viewerTime[$username]) > $minTimeSec);
});

sort($viewers);
foreach ($viewers as $username) {
  $viewersDataRows .= '<tr>
                        <td>
                          <table>
                            <tr><td>' . $username . '</td></tr>
                            <tr><td class="text-muted">' . (array_key_exists($username, $viewerGroups) ? $groups[$viewerGroups[$username]] : $groups[7]) . '</td></tr>
                            <tr><td>' . (array_key_exists($username, $viewerRanks) ? '<span class="text-muted">' . $viewerRanks[$username] . '</span>' : '') . '</td></tr>
                          </table>
                        </td>
                        <td>
                          <table>
                            <tr><td class="text-muted">Last Seen:&nbsp;</td><td>' . (array_key_exists($username, $lastSeen) ? $functions->secondsToDate(round($lastSeen[$username] / 1000)) : 'Unknown') . '</td></tr>
                            <tr><td class="text-muted">Recorded Time:&nbsp;</td><td>' . $functions->secondsToTime($viewerTime[$username]) . '</td></tr>
                            <tr><td class="text-muted">' . $psName . ':&nbsp;</td><td>' . (array_key_exists($username, $viewerPoints) ? $viewerPoints[$username] : 0) . '</td></tr>
                            <tr>
                              <td>' . (array_key_exists($username, $followers) && $functions->strToBool($followers[$username]) ? '<span class="text-success">Follows you</span>' : '<span class="text-danger">Does not follow you</span>') . '</td>
                              <td>' . (array_key_exists($username, $incRaids) ? '<span class="text-success">Raided you ' . $incRaids[$username] . ' times!</span>' : '') . '</td>
                            </tr>
                          </table>
                        </td>
                      </tr>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Viewer Info
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="text-info">
        Showing viewers with a minimal of <?= ($minTimeSec / 3600) ?> hours of recorded time.
      </div>
      <?= $templates->dataTable(
          'Recorded Viewers <span class="text-muted">(' . count($viewers) . ')</span>',
          ['username', 'info'],
          $viewersDataRows
      ) ?>
    </div>
  </div>
