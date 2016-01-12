<?php
/**
 * Created by PhpStorm.
 * User: Robin | Juraji
 * Date: 14-12-2015
 * Time: 15:34
 */

require_once('../AppLoader.class.php');
\PBPanel\AppLoader::load();

$dataStore = new \PBPanel\Util\DataStore();
$connection = new \PBPanel\Util\BotConnectionHandler($dataStore);
$functions = new \PBPanel\Util\Functions($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$fileName = filter_input(INPUT_GET, 'file');
$logDataRows = '';
if ($fileName) {
  $logFileContents = preg_split('/\n/', trim($functions->getOtherFile($fileName)));

  foreach ($logFileContents as $line) {
    $logDataRows .= '<tr><td>' . $line . '</td></tr>';
  }
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title></title>
  <link href="app/css/<?= $dataStore->getVar('misc', 'theme', 'style_dark') ?>.css"
        rel="stylesheet" type="text/css"/>
  <link rel="icon" href="../favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon"/>
  <script src="//code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
  <script src="//code.jquery.com/ui/1.11.3/jquery-ui.min.js" type="text/javascript"></script>
</head>
<body>
<div class="panel panel-primary">
  <div class="panel-heading">
    <h4><?= $fileName ?></h4>
  </div>
</div>
<?= $templates->dataTable('', [], $logDataRows) ?>
</body>
</html>