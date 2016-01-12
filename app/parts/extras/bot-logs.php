<?php
/**
 * preferences.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:40
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

$logDataTables = parseDir();
$layers = 0;

function parseDir($dir = '/logs')
{
  global $functions, $templates, $layers;
  $tableRows = '';
  $fileList = preg_split('/\n/', trim($functions->getOtherFile($dir)));
  ++$layers;

  foreach ($fileList as $fileName) {
    if ($fileName == '') {
      break;
    }
    if (preg_match('/(.*)\.[a-z0-9]{3,}/i', $fileName)) {
      $tableRows = '<tr>'
          . '<td>' . $fileName . '</td>'
          . '<td style="width:90px;"><button class="btn btn-default btn-sm" onclick="openPopup(\'pops/log-viewer.php?file='
          . $dir . '/' . $fileName . '\', \'PhantomBot WebPanel Log Viewer\')">View Log</button></td>'
          . '</tr>' . $tableRows;
    } else {
      $tableRows .= '<tr><td colspan="2">' . parseDir($dir . '/' . $fileName) . '</td></tr>';
    }
  }

  if ($dir == '/logs') {
    if ($layers > 1) {
      return '<table class="table table-condensed">' . $tableRows . '</table>';
    } else {
      return '<table class="table table-striped">' . $tableRows . '</table>';
    }
  } else {
    return $templates->dataTable($dir, [], $tableRows, true, '', [], true);
  }
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Bot Logs
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-xs-8">
          <p>
            Underneath you will find all files and folders in "./logs".<br/>
            Click the "View Log" button to open a pop-up with the file's contents.
          </p>
        </div>
        <div class="col-xs-4">
          <button class="btn btn-default pull-right" onclick="loadPartFromStorage()"><span class="fa fa-refresh"></span></button>
        </div>
      </div>
      <hr/>
      <?= $logDataTables ?>
    </div>
  </div>
</div>