/**
 * login.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:34
 */
$(document).ready(function () {
  var loginData = (localStorage.getItem('pbot-login') ? JSON.parse(localStorage.getItem('pbot-login')) : false);
  if (loginData) {
    login(loginData);
  }

  $('#login-form').submit(function (event) {
    login({
      username: $('#login-username').val(),
      password: SparkMD5.hash($('#login-password').val(), false),
    });
    event.preventDefault();
  });
});

function login(loginData) {
  $.ajax({
    type: 'POST',
    url: 'app/connectors/login.php',
    data: loginData,
    dataType: 'json',
    success: function (data) {
      if (data[1] == 200) {
        localStorage.setItem('pbot-login', JSON.stringify(loginData));
        window.location.replace('control-panel.php');
      } else {
        showLoginAlert(data[3])
      }
    },
  });
}

function showLoginAlert(message) {
  $('#login-alert').text(message).show();
}