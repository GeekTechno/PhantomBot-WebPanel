console.log(alertSettings);
var connection,
    alertQueue = [];

function playAlert(alert) {
  var t,
      audio,
      body = $('body'),
      settings = alertSettings[alert.type],
      alertTemplate = $('<div class="stream-alert in">' +
          '<div class="alert-text">' +
          settings.textTemplate.replace('(name)', alert.username) +
          '</div>' +
          '</div>');
  if (settings.customCss.trim() != '') {
    body.prepend($('<style>' + settings.customCss + '</style>'));
  }
  if (settings.soundFile.trim() != '') {
    //noinspection JSUnresolvedFunction
    audio = new Audio(settings.soundFile);
    audio.play();
  }
  alertTemplate.css({
    'background-image': 'url(\'' + settings.bgImage + '\')',
  });
  body.append(alertTemplate);
  t = setTimeout(function () {
    alertTemplate
        .removeClass('in')
        .addClass('out')
        .delay(1e3)
        .queue(function () {
          $(this).remove();
        });
    clearTimeout(t);
  }, 1e4);
}

$(document).ready(function () {
  $('body').css({
    'background-color': alertSettings.bgColor,
  });

  connection = new WebSocket('ws://' + botAddress);

  connection.onmessage = function (e) {
    var data = e.data.split('|');

    console.log(data);
    if (data[0].match(/^TwitchFollowEvent.*/) && data[1] == 'FOLLOW') {

      alertQueue.push({
        type: 'follow',
        username: data[0].split(':')[1],
      });
    }

    if (data[0].match(/^TwitchHostEvent.*/) && data[1] == 'HOST') {

      alertQueue.push({
        type: 'host',
        username: data[0].split(':')[1],
      });
    }
  };

  setInterval(function () {
    if (alertQueue.length > 0) {
      playAlert(alertQueue.shift());
    }
  }, 11e3);
});