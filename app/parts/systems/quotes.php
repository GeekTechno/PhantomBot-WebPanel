<?php
/**
 * quotes.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:44
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

$botSettings = $functions->getIniArray('settings');
$quotes = $functions->getIniArray('quotes');
$quotesTableRows = '';

unset($quotes['num_quotes']);
foreach ($quotes as $quoteId => $quoteMessage) {
  $quotesTableRows .= '<tr><td>' . str_replace('quote_', '', $quoteId) . '</td><td>' . $quoteMessage . '</td></tr>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Quote System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('quoteCommand.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('quote', 'Summon quote', '[#id]') ?>
        </div>
      </div>
      <hr/>
      <h4>Quote Settings</h4>

      <div class="row">
        <div class="col-sm-8">
          <?= $templates->botCommandForm('addquote', 'Add quote', '[message]') ?>
        </div>
        <div class="col-sm-8">
          <?= $templates->botCommandForm('editquote', 'Edit quote', '#[id] [message]') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('delquote', 'Delete quote', '[#id]') ?>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Quotes', ['#id', 'Message'], $quotesTableRows, true) ?>
    </div>
  </div>
</div>