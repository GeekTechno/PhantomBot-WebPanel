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
$users = $dataStore->getTableAsArray('users');
$usersDataRows = '';
$usersDataRowsCurrentPosition = 0;
$usersDataRowsIsFirst = true;
$currentValidatedUser = filter_input(INPUT_POST, 'username');
$userIsAdmin = false;

foreach ($users as $username => $md5Password) {
  $uuid = $templates->randomId();
  $usersDataRowsIsFirst = ($username == $currentValidatedUser);
  ++$usersDataRowsCurrentPosition;
  if ($usersDataRowsCurrentPosition == 1 && ($username != $currentValidatedUser)) {
    continue;
  } elseif ($usersDataRowsCurrentPosition == 1) {
    $userIsAdmin = true;
  }
  if (!$userIsAdmin && ($username != $currentValidatedUser)) {
    continue;
  }
  $usersDataRows .= '<tr>'
      . '<td><input type="text" placeholder="Username" value="' . $username . '" class="form-control" id="user-username-' . $uuid . '" ' . ($usersDataRowsIsFirst ? 'disabled' : '') . ' /></td>'
      . '<td><input type="password" placeholder="Password" value="' . $md5Password . '" class="form-control" id="user-password-' . $uuid . '" /></td>'
      . '<td><div class="btn-toolbar">'
      . '<button class="btn btn-success" onclick="savePanelUser(\'' . $uuid . '\', \'' . $username . '\', \'' . $md5Password . '\')"><span class="fa fa-save"></span></button>'
      . '<button class="btn btn-danger" onclick="deletePanelUser(\'' . $username . '\')" ' . ($usersDataRowsIsFirst ? 'disabled' : '') . '><span class="fa fa-trash"></span></button>'
      . '</div></td>'
      . '</tr>';
  $usersDataRowsIsFirst = !$usersDataRowsIsFirst;
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Panel Users
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-8">
          <?= $templates->dataTable('Panel Users', ['Username', 'Password', 'Save'], $usersDataRows, true) ?>
          <?= ($userIsAdmin ? '<div class="btn-toolbar"><button class="btn btn-default" onclick="addPanelUser()">Add User</button></div>' : '')?>
        </div>
        <div class="col-sm-4">
          <?= $templates->informationPanel(
              '<p>You can not change your own username, nor can you delete your account!</p>'
              . '<p>If you change your own password you will have to <a href="#" onclick="logOut()">log out</a> and log back in using your new password to be able to continue!</p>'
              . '<p>Admins be careful, granting someone access to your panel gives them full access to everything!</p>'
              . '<p>Use the users twitch username to have the panel send their username on communication with the bot.'
              . '<br />If the name is not known with the bot the channel-owner\'s username will be used!</p>'
          ) ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="app/js/spark-md5.min.js" type="text/javascript"></script>
<script src="app/js/panel-users.min.js" type="text/javascript"></script>
