<?php
/**
 * part-template.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:47
 */
require_once('../../../AppLoader.class.php');
\PBPanel\AppLoader::load();

$session = new \PBPanel\Util\PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\FunctionLibrary($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$pointSettings = $functions->getDbTableArray('pointSettings');
$raffleFiles = array_reverse(preg_split('/\n/', trim($functions->getOtherFile('./addons/raffleSystem/'))));
$previousRaffleFileDataRows = '';

foreach ($raffleFiles as $id => $raffleFile) {
  if ($raffleFile == '') {
    continue;
  }
  $fileContents = preg_split('/\n/', str_replace('New Raffle: ', '', $functions->getOtherFile('./addons/raffleSystem/' . $raffleFile)));
  $previousRaffleFileDataRows .= '<tr>'
      . '<td>' . getRaffleFileDateString($raffleFile) . ($id == 0 ? '<br /><span class="text-muted">(Entire file)</span>' : '') . '</td>'
      . '<td>' . ($id == 0 ? join('<br />', $fileContents) : $fileContents[0]) . '</td>'
      . '</tr>';
}

function getRaffleFileDateString($raffleFile) {
  $raffleFile = str_replace(['raffle_', '.txt'], '', $raffleFile);
  $fileDate = DateTime::createFromFormat('d-m-Y_G:i:s_e', $raffleFile);
  return ($fileDate ? $fileDate->format('d-m-Y H:i:s') : '');
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Raffle System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('raffleSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->botCommandButton('raffle', 'Anounce Current Raffle', 'default') ?>
        <?= $templates->botCommandButton('raffle close', 'Close Current Raffle', 'default') ?>
        <?= $templates->botCommandButton('raffle redraw', 'Redraw Current Raffle Winner', 'default') ?>
      </div>
      <div class="spacer"></div>
      <h4>Start a new raffle</h4>

      <div class="row">
        <div class="col-sm-4">
          <form class="raffle-start-form">
            <div class="form-group">
              <input type="text" class="form-control" name="keyword" placeholder="[keyword]">
            </div>
            <div class="form-group">
              <input type="number" class="form-control" name="cost"
                     placeholder="[cost in <?= $pointSettings['pointNameMultiple'] ?>]*">
            </div>
            <div class="form-group">
              <input type="number" class="form-control" name="time" placeholder="[time in seconds]*">
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="follow"> Followers only
              </label>
            </div>
            <button type="submit" class="btn btn-default btn-block">Start</button>
          </form>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Previous Raffle Files <button class="btn btn-sm btn-default" onclick="loadPartFromStorage()"><span class="fa fa-refresh"></span></button>',
          ['filename'], $previousRaffleFileDataRows, true) ?>
    </div>
  </div>
</div>
<script>
  $(window).ready(function () {
    $('.raffle-start-form').on('submit', function (event) {
      var command = 'raffle start ',
          followCheck = $(event.target[3]),
          keywordField = $(event.target[0]),
          costField = $(event.target[1]),
          timeField = $(event.target[2]);

      event.preventDefault();

      if (followCheck.is(":checked")) {
        command += '-follow ';
      }

      if (keywordField.val().trim() != '') {
        command += '-k ' + keywordField.val();
      } else {
        showGeneralAlert('The keyword field is mandatory!', 'warning');
        return;
      }

      if (costField.val()) {
        command += ' -c ' + costField.val();
      }

      if (timeField.val()) {
        command += ' -t ' + timeField.val();
      }

      doQuickCommand(command);
    })
  });
</script>
