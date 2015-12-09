<?php
/**
 * polls.php
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

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Poll System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('pollSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Start A New Vote</h4>

      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('poll open', 'Start a normal poll', '[option1] [option2]') ?>
          <div class="form-group">
            <?= $templates->botCommandButton('poll close', 'End Current Poll') ?>
          </div>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('poll open -t', 'Start a timed poll', '[seconds] [option1] [option2]') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->informationPanel('To vote type <b>!vote "option"</b> in chat.') ?>
        </div>
      </div>
      <hr/>
      <h4>Poll Results</h4>
      <?= $templates->botCommandButton('poll results', 'Announce Last Poll Results') ?>
    </div>
  </div>
</div>