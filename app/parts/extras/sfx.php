<?php
/**
 * part-template.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:47
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

$sfxFiles = $functions->getSfxFiles();
$sfxSelectOptions = '';
$sfxTableDataRows = '';

foreach ($sfxFiles as $sfxFile) {
  $sfxSelectOptions .= '<option value="' . $sfxFile['path'] . '">' . $sfxFile['fileName'] . '</option>';
}

ksort($config->sfxSettings['commands']);

foreach ($config->sfxSettings['commands'] as $command => $file) {
  $sfxTableDataRows .= '<tr><td>!' . $command . '</td><td>' . $file . '</td><td><button class="btn btn-danger" onclick="deleteSfx(\'' . $command . '\')"><span class="fa fa-trash"></span></button></td></tr>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Sfx Settings
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <!--suppress HtmlUnknownTarget -->
        <a href="sfx-host.php" target="_blank">
          <button class="btn btn-primary">Open Sfx Host</button>
        </a>
        <?= $templates->switchToggle('Toggle Sfx', 'toggleSfx', '[\'' . ($config->sfxSettings['enabled'] == 'true' ? 'false' : 'true') . '\']', null, $config->sfxSettings['enabled'] == 'true') ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Add New Sfx</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-8">
            <form id="sfx-selection">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-addon input-group-addon-select">
                    <select class="form-control" name="file">
                      <?= $sfxSelectOptions ?>
                    </select>
                  </div>
                  <input type="text" class="form-control" name="command" placeholder="[command]"/>
                  <span class="input-group-btn">
                    <button class="btn btn-primary"><span class="fa fa-paper-plane-o"></span></button>
                  </span>
                </div>
              </div>
            </form>
          </div>
          <div class="col-sm-4">
            <?= $templates->informationPanel('<p>Place your audio files (mp3, wav or ogg) in "app/content/sfx" and they will be listed automatically.</p>'
                . '<p>Select a sfx from the dropdown, enter a command and click the send button to save it.</p>'
                . '<p>You will have to open the Sfx Host using the "Open Sfx Host" Button"</p>'
            . '<p class="text-warning">Currently there\'s no volume control. This will be added later on!</p>') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Sfx Commands', ['command', 'file', ''], $sfxTableDataRows, true) ?>
    </div>
  </div>
</div>
