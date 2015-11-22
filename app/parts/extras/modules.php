<?php
/**
 * modules.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:39
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../../..');

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');
require_once(BASEPATH . '/app/php/classes/ComponentTemplates.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$moduleSettingsIni = $functions->getIniArray('modules');
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
  $moduleName = ucfirst(str_replace($moduleNameReplacements, '', $fullPath));
  $moduleFullPath = str_replace('_enabled', '', $fullPath);
  $toggleButton = '';
  $active = ($active == 1 || strpos($moduleFullPath, 'util') > -1);

  if ($active) {
    $NOModulesActive++;
  }


  $toggleButton = $templates->switchToggle('', $templates->_wrapInJsToggledDoQuickCommand(
      'module', ($active ? 'true' : 'false'), 'disable ' . $moduleFullPath, 'enable ' . $moduleFullPath
  ), null, null, $active, false, true, false, (strpos($moduleFullPath, 'util') > -1));

  $modulesTableRows .= '<tr><td>' . $templates->switchToggleText($moduleName, false, true) . '</td><td>' . $toggleButton . '</td></tr>';
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Module Manager
        <?= $templates->toggleFavoriteButton() ?>
        <span class="text-info pull-right"><span class="fa fa-info-circle"></span> <?= count($moduleSettingsIni) ?> Known Module's, <?= $NOModulesActive ?>
          Active</span>
      </h3>
    </div>
    <div class="panel-body">
      <p>
        Note: Not all modules are listed here.<br/>
        Unlisted modules can be enabled/disabled by using "!module &lt;enable/disable&gt; &lt;module path&gt;".<br/>
        After using the !module command, the module will become listed here.
      </p>

      <p>
        Note: Each module page has a module active indication. Here's a list of possible module statuses:
      </p>
      <ul>
        <li>"<span class="text-success"><span class="fa fa-check-circle"></span> Module Activated</span>" - Module is known and active</li>
        <li>"<span class="text-success"><span class="fa fa-check-circle"></span> Module Activated**</span>" - Module status is unknown, assuming active.
          (PhantomBot enables modules by default)
        </li>
        <li>"<span class="text-danger"><span class="fa fa-exclamation-circle"></span> Module Inactive</span>" - Module is known and inactive</li>
      </ul>
      <hr/>
      <h4 class="collapsible-master">Manually Enable/Disable Modules</h4>

      <div class="collapsible-content">
        <p>
          Note: &quot;relative_script_path&quot; is the path relative to the "scripts/" directory.<br/>
          E.g. &quot;./commands/addCommand.js&quot;
        </p>

        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('module enable ', 'Enable module', 'relative_script_path') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('module disable ', 'Enable module', 'relative_script_path') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Modules', ['Module', 'Toggle'], $modulesTableRows, true) ?>
    </div>
  </div>
</div>