<?php
/**
 * modules.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:39
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

$botModules = $functions->getDbTableArray('modules');
$modulesTableRows = '';
$NOModules = 0;
$NOModulesActive = 0;

ksort($botModules);

foreach ($botModules as $modulePath => $status) {
  if (strpos($modulePath, './lang') > -1) {
    continue;
  }

  $NOModules++;
  if ($functions->strToBool($status)) {
    $NOModulesActive++;
  }

  $toggle = $templates->switchToggle(
      '',
      $templates->_wrapInJsToggledDoQuickCommand(
          'module',
          ($functions->strToBool($status) ? 'true' : 'false'),
          'disable ' . $modulePath,
          'enable ' . $modulePath
      ),
      null,
      null,
      $functions->strToBool($status),
      false,
      true,
      false,
      (strpos($modulePath, './core') > -1)
  );

  $modulesTableRows .= '<tr><td>' . $modulePath . '</td><td>' . $toggle . '</td></tr>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Module Manager
        <?= $templates->toggleFavoriteButton() ?>
        <span class="text-info pull-right"><span class="fa fa-info-circle"></span> <?= $NOModules ?>
          Known Module's, <?= $NOModulesActive ?>
          Active</span>
      </h3>
    </div>
    <div class="panel-body">
      <?= $templates->informationPanel('<p>
        Note: Not all modules are listed here.<br/>
        Unlisted modules can be enabled/disabled by using "Manually Enable/Disable Modules".<br/>
        After manually enabling the module, the module will become listed here.
      </p>

      <p>
        Note: Each module page has a module active indication. Here\'s a list of possible module statuses:
      </p>
      <ul>
        <li>"<span class="text-success"><span class="fa fa-check-circle"></span> Module Activated</span>" - Module is active
        </li>
        <li>"<span class="text-danger"><span class="fa fa-exclamation-circle"></span> Module Inactive</span>" - Module is inactive
        </li>
      </ul>') ?>
      <hr/>
      <h4 class="collapsible-master">Manually Enable/Disable Modules</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('module enable ', 'Enable module', '[./path/to/script.js]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('module disable ', 'Enable module', '[./path/to/script.js]') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->informationPanel('<p>
          &quot;./path/to/script.js&quot; is the path relative to the "scripts/" directory.<br/>
          E.g. &quot;./commands/addCommand.js&quot;
        </p>') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Modules', ['Module', 'Toggle'], $modulesTableRows, true) ?>
    </div>
  </div>
</div>