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

$quotes = $functions->getDbTableArray('quotes');
$quotesDataRows = '';

foreach ($quotes as $id => $quote) {
  $quote = json_decode($quote);
  $quotesDataRows .= '<tr><td>' . $templates->botCommandButton('quote ' . $id, $id) . '</td><td>' . $quote[0] . '</td><td>' . $quote[1] . '</td><td>' . $functions->secondsToDate(floor($quote[2] / 1000)) . '</td></tr>';
}

?>
<!--suppress HtmlUnknownTarget -->
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Quote System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('quoteSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->botCommandButton('quote', 'Get Random Quote') ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Manage Quotes</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandFormV2('addquote', 'Add a Quote', [
                'placeholder' => '[username] [quote...]',
                'autoComplete' => 'user'
            ]) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('delquote', 'Delete a Quote', '[quoteId]') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Quotes', ['id', 'Username', 'Text', 'Date'], $quotesDataRows) ?>
    </div>
  </div>
</div>