var newUserUuid = 0;
//noinspection JSUnusedGlobalSymbols
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
    showGeneralAlert('Saved changes for ' + username, 'success');
    loadPartFromStorage();
  }, {panelUsername: username, panelPassword: password});
}

function deletePanelUser(username, noReload) {
  if (username) {
    if (!confirm('Are you sure you want to delete ' + username + '?')) {
      return;
    }
    doBotRequest('deletePanelUser', function () {
      if (!noReload) {
        loadPartFromStorage();
      }
      showGeneralAlert('Deleted user ' + username, 'success');
    }, {panelUsername: username});
  } else {
    if (!noReload) {
      loadPartFromStorage();
    }
  }
}