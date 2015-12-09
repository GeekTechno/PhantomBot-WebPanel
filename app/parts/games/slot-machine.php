<?php
/**
 * slot-machine.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:41
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

$slotMachineSettings = $functions->getIniArray('slotMachine');
$priceComSettings = $functions->getIniArray('pricecom');
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Slot Machine Command
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('slotMachineCommand.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-8">
          <div class="btn-toolbar">
            <?= $templates->botCommandButton('slot', 'Play The Slots') ?>
            <?= $templates->botCommandButton('d !chat  NEW!!! The Slot Machine! type !slot to try your luck!', 'Hype The Slots') ?>
            <?= $templates->switchToggle('Toggle Cool Down Messages', 'doQuickCommand', '[\'slot CooldownMessages toggle\']',
                null, (array_key_exists('slotCMessages', $slotMachineSettings) && $slotMachineSettings['slotCMessages'] == 1)) ?>
          </div>
        </div>
        <div class="col-sm-4">
          <?= $templates->informationPanel('<p>Win points by hitting 3 of the same emotes in a row!</p>Emote 1 is most likely to triple while emote 7 is least likely to!') ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">General Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('slot time', 'Set Slot Cool Down', '[seconds]', (array_key_exists('slotTimer', $slotMachineSettings) ? $slotMachineSettings['slotTimer'] : '0')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('slot bonus', 'Set Slot Bonus', '[amount]', (array_key_exists('slotBonus', $slotMachineSettings) ? $slotMachineSettings['slotBonus'] : '1')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('slot jackpot', 'Set Slot Jackpot', '[amount]', (array_key_exists('slotJackpot', $slotMachineSettings) ? $slotMachineSettings['slotJackpot'] : '300')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('pricecom !slot', 'Set Slot Cost', '[amount]', (array_key_exists('slot', $priceComSettings) ? $priceComSettings['slot'] : '')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Slot Emotes</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-8">
            <div class="row">
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot emote 1', 'Emote 1', '[emote]', (array_key_exists('slotEmote1', $slotMachineSettings) ? $slotMachineSettings['slotEmote1'] : 'Kappa')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot emote 2', 'Emote 2', '[emote]', (array_key_exists('slotEmote2', $slotMachineSettings) ? $slotMachineSettings['slotEmote2'] : 'KappaPride')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot emote 3', 'Emote 3', '[emote]', (array_key_exists('slotEmote3', $slotMachineSettings) ? $slotMachineSettings['slotEmote3'] : 'BloodTrail')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot emote 4', 'Emote 4', '[emote]', (array_key_exists('slotEmote4', $slotMachineSettings) ? $slotMachineSettings['slotEmote4'] : 'MrDestructoid')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot emote 5', 'Emote 5', '[emote]', (array_key_exists('slotEmote5', $slotMachineSettings) ? $slotMachineSettings['slotEmote5'] : '<3')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot emote 6', 'Emote 6', '[emote]', (array_key_exists('slotEmote6', $slotMachineSettings) ? $slotMachineSettings['slotEmote6'] : 'deIlluminati')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot emote 7', 'Emote 7', '[emote]', (array_key_exists('slotEmote7', $slotMachineSettings) ? $slotMachineSettings['slotEmote7'] : 'VaultBoy')) ?>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <?= $templates->informationPanel('<p>You can replace the 7 emotes the slot machine uses.</p>Turbo &amp; subscription emotes will not work, unless you give the bot either Turbo or paid subscription emotes.') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Slot Rewards</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-8">
            <div class="row">
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot reward 1', 'Reward 1', '[amount]', (array_key_exists('slotEmoteReward1', $slotMachineSettings) ? $slotMachineSettings['slotEmoteReward1'] : '20')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot reward 2', 'Reward 2', '[amount]', (array_key_exists('slotEmoteReward2', $slotMachineSettings) ? $slotMachineSettings['slotEmoteReward2'] : '20')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot reward 3', 'Reward 3', '[amount]', (array_key_exists('slotEmoteReward3', $slotMachineSettings) ? $slotMachineSettings['slotEmoteReward3'] : '40')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot reward 4', 'Reward 4', '[amount]', (array_key_exists('slotEmoteReward4', $slotMachineSettings) ? $slotMachineSettings['slotEmoteReward4'] : '50')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot reward 5', 'Reward 5', '[amount]', (array_key_exists('slotEmoteReward5', $slotMachineSettings) ? $slotMachineSettings['slotEmoteReward5'] : '60')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot reward 6', 'Reward 6', '[amount]', (array_key_exists('slotEmoteReward6', $slotMachineSettings) ? $slotMachineSettings['slotEmoteReward6'] : '70')) ?>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <?= $templates->informationPanel('<p>Set the reward per emote.</p>For instance: If a viewer gets a combination of three of the first emote, reward 1 will be paid out.') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Slot Half Rewards</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle('Toggle Half Rewards', 'doQuickCommand', '[\'slot halfreward toggle\']',
              null, (array_key_exists('slotHalfRewards', $slotMachineSettings) ? $slotMachineSettings['slotHalfRewards'] == 1 : true)) ?>
        </div>
        <div class="spacer"></div>
        <div class="row">
          <div class="col-sm-8">
            <div class="row">
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot halfreward 3', 'Half reward 3', '[amount]', (array_key_exists('slotDoubleEmoteReward3', $slotMachineSettings) ? $slotMachineSettings['slotDoubleEmoteReward3'] : 3)) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot halfreward 4', 'Half reward 4', '[amount]', (array_key_exists('slotDoubleEmoteReward4', $slotMachineSettings) ? $slotMachineSettings['slotDoubleEmoteReward4'] : '3')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot halfreward 5', 'Half reward 5', '[amount]', (array_key_exists('slotDoubleEmoteReward5', $slotMachineSettings) ? $slotMachineSettings['slotDoubleEmoteReward5'] : '5')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot halfreward 6', 'Half reward 6', '[amount]', (array_key_exists('slotDoubleEmoteReward6', $slotMachineSettings) ? $slotMachineSettings['slotDoubleEmoteReward6'] : '10')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot halfreward 7', 'Half reward 7', '[amount]', (array_key_exists('slotDoubleEmoteReward7', $slotMachineSettings) ? $slotMachineSettings['slotDoubleEmoteReward7'] : '60')) ?>
              </div>
              <div class="col-sm-6">
                <?= $templates->botCommandForm('slot seereward', 'Seeing Emote 7', '[amount]', (array_key_exists('slotSeeingEmoteReward7', $slotMachineSettings) ? $slotMachineSettings['slotSeeingEmoteReward7'] : '10')) ?>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <?= $templates->informationPanel('<p>Set the half reward per emote.</p>For instance: If a viewer gets a combination of two of the first emote, reward 1 will be paid out.') ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>