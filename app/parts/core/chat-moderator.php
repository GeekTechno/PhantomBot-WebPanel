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

$botSettings = $functions->getDbTableArray('settings');

// Merge user settings with default
$chatModSettings = array_merge(
    [
        'linksAllowed' => 'true',
        'youtubeLinksAllowed' => 'true',
        'regsLinksAllowed' => 'true',
        'linksAgressive' => 'false',
        'linkPurgeMessage' => '',
        'linkPermitTime' => 60,
        'capsAllowed' => 'true',
        'capsTriggerRatio' => 0,
        'capsTriggerLength' => 10,
        'capsPurgeMessage' => '',
        'symbolsAllowed' => 'true',
        'symbolsTriggerCount' => 0,
        'symbolsPurgeMessage' => '',
        'repeatCharAllowed' => 'true',
        'repeatCharTriggerLength' => 0,
        'repeatCharPurgeMessage' => '',
    ],
    $functions->getDbTableArray('chatModerator')
);
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Chat Moderator
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Moderate</h4>

      <div class="row">
        <div class="col-sm-6">
          <?= $templates->combinedBotCommandForm(
              '',
              [
                  'permit' => 'Permit Link',
                  'purge' => 'Purge',
                  'timeout' => 'Timeout',
                  'ban' => 'Ban',
                  'unban' => 'Unban',
              ],
              'Moderate',
              '[username]'
          ) ?>
        </div>
      </div>
      <hr/>
      <h4>Auto Moderator Settings</h4>

      <?= $templates->switchToggle(

          'Allow Links',
          'doQuickCommand',
          '[\'chatmod linksallowed\']',
          '',
          $functions->strToBool($chatModSettings['linksAllowed'])
      ) ?>
      <?= $templates->switchToggle(
          'Allow Youtube Links',
          'doQuickCommand',
          '[\'chatmod youtubelinksallowed\']',
          '',
          $functions->strToBool($chatModSettings['youtubeLinksAllowed'])
      ) ?>
      <?= $templates->switchToggle(
          'Allow Regulars Links',
          'doQuickCommand',
          '[\'chatmod regslinksallowed\']',
          '',
          $functions->strToBool($chatModSettings['regsLinksAllowed'])
      ) ?>
      <?= $templates->switchToggle(
          'Aggressive Link Scanner',
          'doQuickCommand',
          '[\'chatmod linksagressive\']',
          '',
          $functions->strToBool($chatModSettings['linksAgressive'])
      ) ?>
      <?= $templates->switchToggle(
          'Allow Caps Spam',
          'doQuickCommand',
          '[\'chatmod capsallowed\']',
          '',
          $functions->strToBool($chatModSettings['capsAllowed'])
      ) ?>
      <?= $templates->switchToggle(
          'Allow Symbol Spam',
          'doQuickCommand',
          '[\'chatmod symbolsallowed\']',
          '',

          $functions->strToBool($chatModSettings['symbolsAllowed'])
      ) ?>
      <?= $templates->switchToggle(
          'Allow Repeating Character Chains',
          'doQuickCommand',
          '[\'chatmod repeatcharallowed\']',
          '',
          $functions->strToBool($chatModSettings['repeatCharAllowed'])
      ) ?>
      <div class="spacer"></div>
      <div class="row">
        <div class="col-xs-6">
          <h4>Links:</h4>
          <?= $templates->botCommandForm(
              'chatmod setlinkpurgemessage',
              'Purged Link Message',
              '[message]',
              $chatModSettings['linkPurgeMessage']
          ) ?>
          <?= $templates->botCommandForm(
              'chatmod setlinkpermittime',
              'Link Permit Timout',
              '[seconds]',
              $chatModSettings['linkPermitTime']
          ) ?>
        </div>
        <div class="col-xs-6">
          <h4>Symbols & Grapheme Clusters:</h4>
          <?= $templates->botCommandForm(
              'chatmod setsymbolspurgemessage',
              'Purged Symbol Spam Message',
              '[message]',
              $chatModSettings['symbolsPurgeMessage']
          ) ?>
          <?= $templates->botCommandForm(
              'chatmod setsymbolstriggercount',
              'Set Maximum Symbols per message',
              '[amount]',
              $chatModSettings['symbolsTriggerCount']
          ) ?>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
          <h4>Caps:</h4>
          <?= $templates->botCommandForm(
              'chatmod setcapspurgemessage',
              'Purged Caps Spam Message',
              '[message]',
              $chatModSettings['capsPurgeMessage']
          ) ?>
          <?= $templates->botCommandForm(
              'chatmod setcapstriggerlength',
              'Set Minimim Caps Before Checking',
              '[amount]',
              $chatModSettings['capsTriggerLength']
          ) ?>
          <?= $templates->botCommandForm(
              'chatmod setcapstriggerratio',
              'Set Caps count To Message Ratio',
              '[float 0-1]',
              $chatModSettings['capsTriggerRatio']
          ) ?>
        </div>
        <div class="col-xs-6">
          <h4>Repeating Characters:</h4>
          <?= $templates->botCommandForm(
              'chatmod setrepeatcharpurgemessage',
              'Purged Repeating Characters Message',
              '[message]',
              $chatModSettings['repeatCharPurgeMessage']
          ) ?>
          <?= $templates->botCommandForm(
              'chatmod setrepeatchartriggerlength',
              'Set Maximum Repeating Character Chain',
              '[amount]',
              $chatModSettings['repeatCharTriggerLength']
          ) ?>
        </div>
      </div>
    </div>
  </div>
</div>
