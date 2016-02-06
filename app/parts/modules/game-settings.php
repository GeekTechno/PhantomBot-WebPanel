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
$heistSettings = $functions->getDbTableArray('adventureSettings');
$rouletteSettings = $functions->getDbTableArray('roulette');

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Chat Game Settings
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Play</h4>

      <div class="btn-toolbar">
        <?= $templates->botCommandButton('adventure 500', 'Adventure (500 ' . $pointSettings['pointNameMultiple'] . ')') ?>
        <?= $templates->botCommandButton('random', 'Random') ?>
        <?= $templates->botCommandButton('roll', 'Roll') ?>
        <?= $templates->botCommandButton('roulette', 'Roulette') ?>
        <?= $templates->botCommandButton('slot', 'Slot Machine') ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Adventure Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('adventure set jointime', 'Set Join Period (Seconds)', '[seconds]', (array_key_exists('joinTime', $heistSettings) ? $heistSettings['joinTime'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('adventure set cooldown', 'Set Cool Down (Seconds)', '[seconds]', (array_key_exists('coolDown', $heistSettings) ? $heistSettings['coolDown'] : '')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('adventure set minbet', 'Set Minimum Entry Bet', '[points]', (array_key_exists('minBet', $heistSettings) ? $heistSettings['minBet'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('adventure set maxbet', 'Set Maximum Entry Bet', '[points]', (array_key_exists('maxBet', $heistSettings) ? $heistSettings['maxBet'] : '')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('adventure set gainpercent', 'Set Gain Percentage', '[0-100]', (array_key_exists('gainPercent', $heistSettings) ? $heistSettings['gainPercent'] : '')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Roulette Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?=$templates->botCommandForm('roulettetimeouttime', 'Set Timeout Time (seconds)', '[seconds]', (array_key_exists('timeoutTime', $rouletteSettings) ? $rouletteSettings['timeoutTime'] : ''))?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>