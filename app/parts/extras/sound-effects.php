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
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$sfxFiles = $functions->getSfxFiles();
$sfxCommands = $dataStore->getTableAsArray('sfxcommands');
$sfxSelectOptions = '';
$sfxTableDataRows = '';

if (count($sfxFiles) > 0) {
  foreach ($sfxFiles as $sfxFile) {
    $sfxSelectOptions .= '<option value="' . $sfxFile['path'] . '">' . $sfxFile['fileName'] . '</option>';
  }
} else {
  $sfxSelectOptions .= '<option>NO FILES!</option>';
}


ksort($sfxCommands);

foreach ($sfxCommands as $command => $file) {
  $sfxTableDataRows .= '<tr><td>' . $templates->botCommandButton($command, '!' . $command, 'default btn-sm')
      . '</td><td>' . $file . '</td><td><button class="btn btn-danger" onclick="deleteSfx(\'' . $command . '\')"><span class="fa fa-trash"></span></button></td></tr>';
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Sound Effect Settings
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <button class="btn btn-primary" onclick="openPopup('sound-effects-player.php', 'PhantomBot WebPanel Sound Effects')">Open Sfx Player</button>
      </div>
      <hr/>
      <h4 class="collapsible-master">Add New Sound Effect</h4>

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
        </div>
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->informationPanel(
                '<p>This feature only works on the latest <a href="https://github.com/GloriousEggroll/PhantomBot" target="_blank">Phantombot</a> "nightly"!</p>' .
                '<p>Place your audio files (mp3, wav or ogg) in "app/content/sfx" and they will be listed automatically.</p>' .
                '<p>Select a sfx from the dropdown, enter a command and click the send button to save it.</p>'.
                '<p>You will have to open the Sfx Host using the "Open Sfx Host" Button"</p>' .
                '<p class="text-warning">Currently there\'s no volume control. This will be added later on!</p>'
            ) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Sfx Commands', ['command', 'file', ''], $sfxTableDataRows, true) ?>
    </div>
  </div>
</div>
