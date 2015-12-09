<?php
/**
 * bank-heist.php
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
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$bankHeistTimers = $functions->getIniArray('bankheist_timers', true);
$bankHeistStrings = $functions->getIniArray('bankheist_strings', true);
$bankHeistRatios = $functions->getIniArray('bankheist_ratios', true);
$bankHeistChances = $functions->getIniArray('bankheist_chances', true);

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Bank Heist
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('bankHeistSystem.js')) ?>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->botCommandButton('bankheist start', 'Start Bank Heist') ?>
        <?= $templates->switchToggle('Toggle Bankheist', 'doQuickCommand', '[\'bankheist toggle\']', '', (array_key_exists('bankheistToggle', $botSettings) ? filter_var($botSettings['bankheistToggle'], FILTER_VALIDATE_BOOLEAN) : false)) ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Session Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist signupminutes', 'Sign up time', '[minutes]', (array_key_exists('signupMinutes', $bankHeistTimers) ? $bankHeistTimers['signupMinutes'] : '1')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist heistminutes', 'Session Length', '[minutes]', (array_key_exists('heistMinutes', $bankHeistTimers) ? $bankHeistTimers['heistMinutes'] : '30')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist maxbet', 'Maximum Bet', '[minutes]', (array_key_exists('bankheistmaxbet', $botSettings) ? $botSettings['bankheistmaxbet'] : '1000')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Chances</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist chances50', '50% Chance', '[percent]', (array_key_exists('chances50', $bankHeistChances) ? $bankHeistChances['chances50'] : 'GENERATED')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist chances40', '40% Chance', '[percent]', (array_key_exists('chances40', $bankHeistChances) ? $bankHeistChances['chances40'] : 'GENERATED')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist chances30', '30% Chance', '[percent]', (array_key_exists('chances30', $bankHeistChances) ? $bankHeistChances['chances30'] : 'GENERATED')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist chances20', '20% Chance', '[percent]', (array_key_exists('chances20', $bankHeistChances) ? $bankHeistChances['chances20'] : 'GENERATED')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist chances10', '10% Chance', '[percent]', (array_key_exists('chances10', $bankHeistChances) ? $bankHeistChances['chances10'] : 'GENERATED')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Ratios</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist ratio50', '50% ratio', '[ratio]', (array_key_exists('ratio50', $bankHeistRatios) ? $bankHeistRatios['ratio50'] : '2.75')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist ratio40', '40% ratio', '[ratio]', (array_key_exists('ratio40', $bankHeistRatios) ? $bankHeistRatios['ratio40'] : '2.25')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist ratio30', '30% ratio', '[ratio]', (array_key_exists('ratio30', $bankHeistRatios) ? $bankHeistRatios['ratio30'] : '2')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist ratio20', '20% ratio', '[ratio]', (array_key_exists('ratio20', $bankHeistRatios) ? $bankHeistRatios['ratio20'] : '1.7')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('bankheist ratio10', '10% ratio', '[ratio]', (array_key_exists('ratio10', $bankHeistRatios) ? $bankHeistRatios['ratio10'] : '1.5')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Other Messages:</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist stringnojoin', 'No players joined message', '[message]', (array_key_exists('stringNoJoin', $bankHeistStrings) ? $bankHeistStrings['stringNoJoin'] : 'No one joined the bankheist! The banks are safe for now.')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist stringstarting', 'Heist starting message', '[message]', (array_key_exists('stringStarting', $bankHeistStrings) ? $bankHeistStrings['stringStarting'] : 'Alright guys, check your guns. We are storming into the Bank through all entrances. Let\'s get the cash and get out before the cops get here.')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist stringflawless', 'Heist executed flawlessly message', '[message]', (array_key_exists('stringFlawless', $bankHeistStrings) ? $bankHeistStrings['stringFlawless'] : 'The crew executed the heist flawlessly and scored (pointname) from the vault without leaving a trace!')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist stringcasualties', 'Heist casualties message', '[message]', (array_key_exists('stringCasualties', $bankHeistStrings) ? $bankHeistStrings['stringCasualties'] : 'The crew suffered a few losses engaging the local security team. The remaining crew got away scoring (pointname) from the vault before backup arrived.')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist stringpayouts', 'Heist payouts message', '[message]', (array_key_exists('stringPayouts', $bankHeistStrings) ? $bankHeistStrings['stringPayouts'] : 'The heist payouts are: ')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist stringalldead', 'Heist all dead message', '[message]', (array_key_exists('stringAllDead', $bankHeistStrings) ? $bankHeistStrings['stringAllDead'] : 'The security team killed everyone in the heist!')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist banksclosed', 'Heist banks closed message', '[message]', (array_key_exists('stringAllDead', $bankHeistStrings) ? $bankHeistStrings['stringAllDead'] : 'The security team killed everyone in the heist!')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist enterabet', 'Heist enter a bet message', '[message]', (array_key_exists('enterABet', $bankHeistStrings) ? $bankHeistStrings['enterABet'] : 'You must enter a bet! For example !bankheist (amount)')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist affordbet', 'Heist user cannot afford message', '[message]', (array_key_exists('affordBet', $bankHeistStrings) ? $bankHeistStrings['affordBet'] : 'You must enter a bet you can afford and is not 0')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist alreadybet', 'Heist user already bet message', '[message]', (array_key_exists('alreadyBet', $bankHeistStrings) ? $bankHeistStrings['alreadyBet'] : 'you have already placed a bet of $1')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist startedheist', 'Heist started message', '[message]', (array_key_exists('startedHeist', $bankHeistStrings) ? $bankHeistStrings['startedHeist'] : 'has started a bankheist! To join in type !bankheist (amount)')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist joinedheist', 'Heist joined heist message', '[message]', (array_key_exists('joinedHeist', $bankHeistStrings) ? $bankHeistStrings['joinedHeist'] : ', you have joined in on the bank heist!')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist banksopen', 'Heist banks open message', '[message]', (array_key_exists('banksOpen', $bankHeistStrings) ? $bankHeistStrings['banksOpen'] : 'The banks are now open for the taking! Use !bankheist (amount) to bet.')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist heistcancelled', 'Heist canceled message', '[message]', (array_key_exists('heistCancelled', $bankHeistStrings) ? $bankHeistStrings['heistCancelled'] : 'has cleared all previous bankheists. A new bankheist will start in ')) ?>
          </div>
          <div class="col-sm-8">
            <?= $templates->botCommandForm('bankheist bettoolarge', 'Heist bet too large message', '[message]', (array_key_exists('betTooLarge', $bankHeistStrings) ? $bankHeistStrings['betTooLarge'] : 'The maximum amount allowed is $1')) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>