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

$botSettings = $functions->getDbTableArray('settings');
$chatModSettings = $functions->getDbTableArray('chatModerator');

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Chat Moderator
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('chatModerator.js'))?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Moderate</h4>

      <div class="row">
        <div class="col-sm-6">
          <?= $templates->combinedBotCommandForm('', [
              'permit' => 'Permit Link',
              'purge' => 'Purge',
              'timeout' => 'Timeout',
              'ban' => 'Ban',
              'unban' => 'Unban',
          ], 'Moderate', '[username]') ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Auto Moderator Settings</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle('Allow Links', 'doQuickCommand', '[\'chatmod linksallowed\']', '',
              (array_key_exists('linksAllowed', $chatModSettings) && $chatModSettings['linksAllowed'] == 'true')) ?>
          <?= $templates->switchToggle('Allow Youtube Links', 'doQuickCommand', '[\'chatmod youtubelinksallowed\']', '',
              (array_key_exists('youtubeLinksAllowed', $chatModSettings) && $chatModSettings['youtubeLinksAllowed'] == 'true')) ?>
          <?= $templates->switchToggle('Allow Regulars Links', 'doQuickCommand', '[\'chatmod regslinksallowed\']', '',
              (array_key_exists('regsLinksAllowed', $chatModSettings) && $chatModSettings['regsLinksAllowed'] == 'true')) ?>
        </div>
        <div class="spacer"></div>
        <div class="btn-toolbar">
          <?= $templates->switchToggle('Aggressive Link Scanner', 'doQuickCommand', '[\'chatmod linksagressive\']', '',
              (array_key_exists('linksAgressive', $chatModSettings) && $chatModSettings['linksAgressive'] == 'true')) ?>
          <?= $templates->switchToggle('Allow Caps Spam', 'doQuickCommand', '[\'chatmod capsallowed\']', '',
              (array_key_exists('capsAllowed', $chatModSettings) && $chatModSettings['capsAllowed'] == 'true')) ?>
          <?= $templates->switchToggle('Allow Symbol Spam', 'doQuickCommand', '[\'chatmod symbolsallowed\']', '',
              (array_key_exists('symbolsAllowed', $chatModSettings) && $chatModSettings['symbolsAllowed'] == 'true')) ?>
        </div>
        <div class="spacer"></div>
        <div class="btn-toolbar">
          <?= $templates->switchToggle('Allow Repeating Character Chains', 'doQuickCommand', '[\'chatmod repeatcharallowed\']', '',
              (array_key_exists('repeatCharAllowed', $chatModSettings) && $chatModSettings['repeatCharAllowed'] == 'true')) ?>
        </div>
        <div class="spacer"></div>
        <div class="row">
          <div class="col-xs-6">
            <h4>Links:</h4>
            <?= $templates->botCommandForm('chatmod setlinkpermittime', 'Link Permit Timout', '[seconds]', (array_key_exists('linkPermitTime', $chatModSettings) ? $chatModSettings['linkPermitTime'] : '')) ?>
          </div>
          <div class="col-xs-6">
            <h4>Symbols & Grapheme Clusters:</h4>
            <?= $templates->botCommandForm('chatmod setsymbolstriggercount', 'Set Maximum Symbols per message', '[amount]', (array_key_exists('symbolsTriggerCount', $chatModSettings) ? $chatModSettings['symbolsTriggerCount'] : '')) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <h4>Caps:</h4>
            <?= $templates->botCommandForm('chatmod setcapstriggerlength', 'Set Minimim Caps Before Checking', '[amount]', (array_key_exists('capsTriggerLength', $chatModSettings) ? $chatModSettings['capsTriggerLength'] : '')) ?>
            <?= $templates->botCommandForm('chatmod setcapstriggerratio', 'Set Caps count To Message Ratio', '[float 0-1]', (array_key_exists('capsTriggerRatio', $chatModSettings) ? $chatModSettings['capsTriggerRatio'] : '')) ?>
          </div>
          <div class="col-xs-6">
            <h4>Repeating Characters:</h4>
            <?= $templates->botCommandForm('chatmod setrepeatchartriggerlength', 'Set Maximum Repeating Character Chain', '[amount]', (array_key_exists('repeatCharTriggerLength', $chatModSettings) ? $chatModSettings['repeatCharTriggerLength'] : '')) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>