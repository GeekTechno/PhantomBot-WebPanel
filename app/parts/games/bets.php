<?php
/**
 * bets.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:40
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../../..');

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');
require_once(BASEPATH . '/app/php/classes/ComponentTemplates.class.php');
require_once(BASEPATH . '/app/php/classes/PanelSession.class.php');


$session = new PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

?>
<div class="app-part">
    <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Bet System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('betSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-group">
        <?= $templates->botCommandButton('bet start', 'Start Default Bet') ?>
        <?= $templates->botCommandButton('bet results', 'Latest Bet Results') ?>
      </div>
      <hr/>
      <h4>Custom Bet</h4>

      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('bet start', 'Start Custom bet', 'option option2 option3 option4') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('bet min', 'Start minimum bet', 'amount') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('bet max', 'Set maximum bet', 'amount') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('bet time', 'Set join time', 'seconds') ?>
        </div>
      </div>
      <hr/>
      <h4>Choose winner</h4>

      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('bet win', 'Select winner', 'option') ?>
        </div>
        <div class="col-sm-4 col-sm-offset-4">
          <?= $templates->informationPanel('Have a viewer decide who wins by having them use "!bet win &lt;option&gt;".')?>
        </div>
      </div>
    </div>
  </div>
</div>