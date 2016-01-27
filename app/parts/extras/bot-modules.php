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
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$moduleSettingsIni = $functions->getDbTableArray('modules');
$modulesTableRows = '';
$moduleNameReplacements = [
    './',
    '_enabled',
];
$NOModulesActive = 0;

uksort($moduleSettingsIni, function ($a, $b) use ($moduleNameReplacements) {
  return strcasecmp(
      str_replace($moduleNameReplacements, '', $a),
      str_replace($moduleNameReplacements, '', $b)
  );
});

foreach ($moduleSettingsIni as $fullPath => $active) {
  if (strpos($fullPath, 'lang-') > -1) {
    continue;
  }
  $moduleName = ucfirst(str_replace($moduleNameReplacements, '', $fullPath));
  $moduleFullPath = str_replace('_enabled', '', $fullPath);
  $active = ($active == 1 || strpos($moduleFullPath, 'util') > -1);

  if ($active) {
    $NOModulesActive++;
  }


  $toggleButton = $templates->switchToggle('', $templates->_wrapInJsToggledDoQuickCommand(
      'module', ($active ? 'true' : 'false'), 'disable ' . $moduleFullPath, 'enable ' . $moduleFullPath
  ), null, null, $active, false, true, false, (strpos($moduleFullPath, 'util') > -1 || strpos($moduleFullPath, 'lang') > -1));

  $modulesTableRows .= '<tr><td>' . $templates->switchToggleText($moduleName, false, true) . '</td><td>' . $toggleButton . '</td></tr>';
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Module Manager
        <?= $templates->toggleFavoriteButton() ?>
        <span class="text-info pull-right"><span class="fa fa-info-circle"></span> <?= count($moduleSettingsIni) ?>
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