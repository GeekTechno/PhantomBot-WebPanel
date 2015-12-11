$(document).ready(function () {
  var connection,
      alertQueue = [];

  $('body').css({
    'background-color': alertSettings.bgColor,
  });

  connection = new WebSocket(getProtocol() + botAddress);

  connection.onmessage = function (e) {
    var data = e.data.split('|'),
        alert;

    if (data[0].match(/^TwitchFollow.*/) && data[1] == 'FOLLOW') {
      alert = {
        type: 'follow',
        username: data[0].split(':')[1],
      };
      console.log(data[1], alert.username, e);
      alertQueue.push(alert);
    }

    if (data[0].match(/^TwitchHost.*/) && data[1] == 'HOST') {
      alert = {
        type: 'host',
        username: data[0].split(':')[1],
      };
      console.log(data[1], alert.username, e);
      alertQueue.push(alert);
    }

    if (data[0].match(/^TwitchHost.*/) && data[1] == 'SUBSCRIBE') {
      alert = {
        type: 'subscribe',
        username: data[0].split(':')[1],
      };
      console.log(data[1], alert.username, e);
      alertQueue.push(alert);
    }
  };

  setInterval(function () {
    if (alertQueue.length > 0) {
      playAlert(alertQueue.shift());
    }
  }, 11e3);

  function getProtocol() {
    return (window.location.protocol == 'http:' ? 'ws://' : 'wss://');
  }

  function playAlert(alert) {
    var t,
        audio,
        body = $('body'),
        settings = alertSettings[alert.type],
        alertTemplate = $('<div class="stream-alert in">' +
            '<div class="alert-text">' +
            settings.textTemplate.replace('(name)', alert.username) +
            '</div>' +
            '</div>'),
        css = $('<style>' + settings.customCss + '</style>');
    if (settings.customCss.trim() != '') {
      body.prepend(css);
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
            css.remove();
          });
      clearTimeout(t);
    }, 1e4);
  }
});