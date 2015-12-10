$(window).ready(function () {
  var display = $('#sfx-display'),
      sfxHistory = $('#sfx-history'),
      connection = new WebSocket(getProtocol() + botAddress),
      audio;

  connection.onmessage = function (e) {
    var message = e.data.split('|'),
        event = message[0].split(':'),
        userGroup = (userGroups[event[1].toLowerCase()] ? userGroups[event[1].toLowerCase()] : "7"),
        now = new Date();

    if (event[0] == 'CommandEvent' && sfxCommands[message[1]]) {
      if (!commandPermissions[message[1].toLowerCase()] || !commandPermissions[message[1].toLowerCase()] == userGroup) {
        //noinspection JSUnresolvedFunction
        audio = new Audio(sfxCommands[message[1]]);

        audio.addEventListener('play', function () {
          display.text('Playing "' + sfxCommands[message[1]] + '" for !' + message[1]);
        });

        audio.addEventListener('ended', function () {
          display.text('Waiting for commands...');
        });

        audio.play();

        sfxHistory.append($('<div>[' + now.toLocaleDateString('en-GB').replace(/\s[0-9]{4}/, '').replace(/([a-z]{3})[a-z]+/i, '$1') + ' '
            + now.toLocaleTimeString() + '] ' + event[1] + ' triggered <span class="text-success">!' + message[1] +
            '</span></div>'));
      } else {
        sfxHistory.append($('<div>[' + now.toLocaleDateString('en-GB').replace(/\s[0-9]{4}/, '').replace(/([a-z]{3})[a-z]+/i, '$1') + ' '
            + now.toLocaleTimeString() + '] ' + event[1] + ' does not have permission to trigger <span class="text-success">!' + message[1] +
            '</span></div>'));
      }
    }
  };

  function getProtocol() {
    return (window.location.protocol == 'http:' ? 'ws://' : 'wss://');
  }
});