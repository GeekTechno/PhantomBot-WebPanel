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
$connection = new \PBPanel\Util\ConnectionHandler($dataStore);
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
  if ($usersDataRowsCurrentPosition == 0 && ($username != $currentValidatedUser)) {
    continue;
  } else {
    $userIsAdmin = true;
  }
  if (!$userIsAdmin && ($username != $currentValidatedUser)) {
    continue;
  }
  $usersDataRows .= '<tr>'
      . '<td><input type="text" placeholder="Username" value="' . $username . '" class="form-control" id="user-username-' . $uuid . '" ' . ($usersDataRowsIsFirst ? 'disabled' : '') . ' /></td>'
      . '<td><input type="text" placeholder="Password" value="' . $md5Password . '" class="form-control" id="user-password-' . $uuid . '" /></td>'
      . '<td><div class="btn-toolbar">'
      . '<button class="btn btn-success" onclick="savePanelUser(\'' . $uuid . '\', \'' . $username . '\', \'' . $md5Password . '\')"><span class="fa fa-save"></span></button>'
      . '<button class="btn btn-danger" onclick="deletePanelUser(\'' . $username . '\')" ' . ($usersDataRowsIsFirst ? 'disabled' : '') . '><span class="fa fa-trash"></span></button>'
      . '</div></td>'
      . '</tr>';
  $usersDataRowsIsFirst = !$usersDataRowsIsFirst;
  ++$usersDataRowsCurrentPosition;
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
      <?= $templates->dataTable('Panel Users', ['Username', 'Password', 'Save'], $usersDataRows, true) ?>
      <div class="btn-toolbar">
        <button class="btn btn-default" onclick="addPanelUser()">Add User</button>
      </div>
    </div>
  </div>
</div>
<script src="../../../app/js/spark-md5.min.js" type="text/javascript"></script>
<script>
  var newUserUuid = 0;
  function addPanelUser() {
    $('.data-table tbody').append($('<tr>'
        + '<td><input type="text" placeholder="Username" class="form-control" id="user-username-new' + newUserUuid + '" /></td>'
        + '<td><input type="password" placeholder="Password" class="form-control" id="user-password-new' + newUserUuid + '" /></td>'
        + '<td><div class="btn-toolbar">'
        + '<button class="btn btn-success" onclick="savePanelUser(\'new' + newUserUuid + '\', \'\', \'\')"><span class="fa fa-save"></span></button>'
        + '<button class="btn btn-danger" onclick="deletePanelUser(null)"><span class="fa fa-trash"></span></button>'
        + '</div></td>'
        + '</tr>'));
    ++newUserUuid;
  }

  function savePanelUser(uuid, oldUsername, oldPass) {
    var username = $('#user-username-' + uuid).val(),
        password = $('#user-password-' + uuid).val();

    console.log(username, password, oldUsername, oldPass);

    if (username.trim() == '' || password.trim() == '') {
      showGeneralAlert('You left a value undefined!', 'danger');
      return;
    }

    if (username != oldUsername) {
      deletePanelUser(oldUsername, true);
    }

    if (password != oldPass) {
      password = SparkMD5.hash(password, false);
    }

    doBotRequest('savePanelUser', function () {
      loadPartFromStorage();
    }, {panelUsername: username, panelPassword: password});
  }

  function deletePanelUser(username, noReload) {
    if (username) {
      doBotRequest('deletePanelUser', function () {
        if (!noReload) {
          loadPartFromStorage();
        }
      }, {panelUsername: username});
    } else {
      if (!noReload) {
        loadPartFromStorage();
      }
    }
  }
</script>
