/**
 * app.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:33
 */
var pBotStorage = {
      keys: {
        twitchCache: 'pbot-twitch-cache',
        chatDefaultState: 'pbot-chat-state',
        panelLogin: 'pbot-login',
        lastUsedPart: 'pbot-last-used-part',
        favoritesMenuItems: 'pbot-favorites-menu-items',
        informationActive: 'pbot-information-active',
        tooltipsActive: 'pbot-tooltips-active',
        musicPlayer: {
          volume: 'pbot-music-player-volume',
          controlsEnabled: 'pbot-music-player-controls',
        },
      },
      set: function (settingName, data) {
        localStorage.setItem(settingName, JSON.stringify(data));
      },
      get: function (settingName, returnValue) {
        return (localStorage.getItem(settingName) ? JSON.parse(localStorage.getItem(settingName)) : (returnValue ? returnValue : false));
      },
    },
    pBotData = {
      config: {
        owner: '',
        botName: '',
        isBotOnline: false,
        token: '',
      },
      login: null,
      touchedCollapsibles: [],
      streamPreviewActive: false,
      informationActive: pBotStorage.get(pBotStorage.keys.informationActive, true),
      tooltipsActive: pBotStorage.get(pBotStorage.keys.tooltipsActive, true),
      musicPlayerControls: {
        controlsEnabled: pBotStorage.get(pBotStorage.keys.musicPlayer.controlsEnabled),
        updateIntervalId: -1,
      },
    };

$(window).ready(function () {
  /* Retrieve ?login data from local storage */
  pBotData.login = pBotStorage.get(pBotStorage.keys.panelLogin);

  if (pBotData.login) {
    /* Set current user in UI */
    $('#current-user').text(pBotData.login.username);

    /* Load panel config */
    getPanelConfig();

    /* Get bot status */
    getBotStatus();

    /* Bind click event to dropdown-toggles to toggle their respective lists */
    bindGlobalEventHandlers();

    /* Load up the favorites menu */
    updateFavoritesMenu();

    /* Set interval to update channel data every 30 minutes */
    setInterval(function () {
      loadChannelData();
      getBotStatus();
    }, 6e5);
  } else {
    logOut();
  }
});

function applyChannelData(channelData) {
  if (channelData.stream != null) {
    $('#stream-status').text('Online');
    //noinspection JSUnresolvedFunction
    if (!channelData.stream.hasOwnProperty('viewers')) {
      channelData.stream.viewers = 0;
    }
    $('#stream-viewer-count').text(channelData.stream.viewers);
  }
  $('#stream-title').text(channelData.channel.status);
  //noinspection JSUnresolvedFunction
  if (!channelData.channel.hasOwnProperty('game')) {
    channelData.channel.game = '';
  }
  $('#stream-game').text(channelData.channel.game);
  //noinspection JSUnresolvedFunction
  if (!channelData.followers.hasOwnProperty('_total')) {
    channelData.followers._total = 0;
  }
  $('#stream-followers').text(channelData.followers._total);

  doBotRequest('getIniValueByKey', function (result) {
    if (result != '0') {
      $('#stream-hosts').text(data[0]);
    }
  }, {uri: 'stream_info', key: 'hosts_amount'});
}

function bindGlobalEventHandlers() {
  bindContextMenu();

  $('.dropdown-toggle').on('click', function () {
    var owner = $(this),
        targetList = owner.next('.dropdown-menu'),
        timer,
        fadeoutTime = 300;
    if (targetList.css('display') == 'block') {
      targetList.fadeOut(fadeoutTime);
    } else {
      $('.dropdown-menu').fadeOut(fadeoutTime);
      if (targetList.hasClass('drop-up')) {
        targetList.fadeIn(fadeoutTime);
      } else {
        targetList
            .fadeIn(fadeoutTime)
            .on('click mouseleave', function () {
              timer = setTimeout(function () {
                targetList
                    .fadeOut(fadeoutTime)
                    .off('click mouseleave mouseenter');
              }, 300);
            })
            .on('mouseenter', function () {
              clearTimeout(timer);
            });
      }
    }
  });

  $('#volume-control-slider').slider({
    min: 0,
    max: 100,
    value: pBotStorage.get(pBotStorage.keys.musicPlayer.volume, 100),
    change: function (event, ui) {
      pBotStorage.set(pBotStorage.keys.musicPlayer.volume, ui.value);
      doQuickCommand('volume ' + ui.value);
    }
  });

  $('.side-tab').on('click', sideTabTabHandler);
}

function bindPartEventHandlers() {
  $('form.bot-command-form').off('submit').on('submit', function (event) {
    var requestParams;
    if (!event.target.attributes.hasOwnProperty('botcommand')) {
      event.target.attributes.botcommand = {value: ''};
    }
    //noinspection JSUnresolvedVariable,CoffeeScriptUnusedLocalSymbols
    var input = $(event.target[0]),
        button = $(event.target[1]),
        buttonOrgHtml = button.html(),
        noReload = event.target.attributes.formnoreload.value;
    if (input.val().length != 0) {
      if (event.target.attributes.botcommand.value == '') {
        requestParams = {command: _cleanInput(input.val())};
      } else {
        requestParams = {command: _cleanInput(event.target.attributes.botcommand.value) + ' ' + _cleanInput(input.val())};
      }
      button.html('<span class="fa fa-cog fa-spin"></span>');
      doBotRequest('command', function (result) {
        showGeneralAlert(result);
        if (noReload == '1') {
          input.val('');
          button.html(buttonOrgHtml);
        } else {
          loadPartFromStorage();
        }
      }, requestParams);
    } else {
      showGeneralAlert();
    }
    event.preventDefault();
  });

  $('form.combined-bot-command-form').off('submit').on('submit', function (event) {
    //noinspection JSUnresolvedVariable
    var command = event.target.attributes.botcommand.value,
        action = event.target[0].selectedOptions[0].value,
        actionArg = event.target[1].value,
        noReload = (event.target.attributes.formnoreload.value == '1'),
        requestParams;

    if (actionArg != '') {
      requestParams = {command: (command != '' ? command + ' ' : '') + action + ' ' + actionArg};
      doBotRequest('command', function (result) {
        showGeneralAlert(result, 'success');
        if (noReload) {
          $(event.target[1]).val('');
        } else {
          loadPartFromStorage();
        }
      }, requestParams);
    } else {
      showGeneralAlert();
    }

    event.preventDefault();
  });

  $('form#sfx-selection').off('submit').on('submit', function (event) {
    //noinspection JSUnresolvedVariable
    var file = event.target[0].selectedOptions[0].value,
        command = event.target[1].value;

    if (file && file != '' && command != '') {
      doBotRequest('saveSfxCommand', function () {
        loadPartFromStorage();
      }, {command: _cleanInput(command), file: file});
    }

    debug(command, file);
    event.preventDefault();
  });

  $('.collapsible-master').on('click', function () {
    var owner = $(this),
        child = owner.next('.collapsible-content');

    if (child.hasClass('disabled')) {
      showGeneralAlert('This feature us currently disabled', 'warning');
      return;
    }

    if (owner.hasClass('open')) {
      child.fadeOut(300);
      for (var i in pBotData.touchedCollapsibles) {
        //noinspection JSUnfilteredForInLoop
        if (pBotData.touchedCollapsibles[i] == owner.text()) {
          //noinspection JSUnfilteredForInLoop
          pBotData.touchedCollapsibles.splice(i, 1);
        }
      }
    } else {
      child.fadeIn(300);
      pBotData.touchedCollapsibles.push(owner.text());
    }
    owner.toggleClass('open');
  });

  $('.auto-complete').on('change input keyup. paste', function (event) {
    //noinspection JSValidateTypes
    var target = $(event.target),
        wrapper = target.parent().parent().parent(),
        autoCompleteId = wrapper.attr('autocompleteid'),
        autoCompleteType = wrapper.attr('autocompleteon'),
        autoCompleteList = $('#ac-list-' + autoCompleteId);

    if (target.val().length > 2) {
      var splitValue = target.val().split(' ');
      $.ajax({
        type: 'POST',
        url: 'app/connectors/autocomplete.php',
        data: {
          'type': autoCompleteType,
          'input': splitValue[splitValue.length - 1],
          'token': pBotData.config.token
        },
        dataType: 'json',
        success: function (data) {
          if (data.length > 0) {
            autoCompleteList.show().html('');
            for (var i in data) {
              autoCompleteList.append(
                  '<li role="button" onclick="insertAutoComplete(\'' + autoCompleteId + '\', \'' + data[i] + '\')">' + data[i] + '</li>'
              )
            }
          } else {
            autoCompleteList.hide().html('');
          }
        }
      });
    } else {
      autoCompleteList.hide().html('');
    }
  })
  ;

  /*
   * Open previously opened collapsibles for current part
   * pBotData.touchedCollapsibles gets reset on changing parts
   */
  for (var i in pBotData.touchedCollapsibles) {
    //noinspection JSUnfilteredForInLoop
    $('.collapsible-master:contains(' + pBotData.touchedCollapsibles[i] + ')')
        .toggleClass('open')
        .next('.collapsible-content').fadeIn(300);
  }

  toggleTooltips(true);
  toggleInformationPanels(true);
}

function insertAutoComplete(autoCompleteId, value) {
  var field = $('#ac-field-' + autoCompleteId),
      fieldValues = field.val().split(' '),
      autoCompleteList = $('#ac-list-' + autoCompleteId);

  fieldValues[fieldValues.length - 1] = value;
  field.val(fieldValues.join(' '));
  autoCompleteList.hide().html('');
}

function channelDataCache(data) {
  if (data) {
    data.pbotCacheEndTime = Date.now() + 3e5;
    pBotStorage.set(pBotStorage.keys.twitchCache, data);
  } else {
    return pBotStorage.get(pBotStorage.keys.twitchCache, false);
  }
}

function doBotRequest(action, callback, params) {
  if (!pBotData.config.isBotOnline) {
    return;
  }
  $.ajax({
    type: 'POST',
    url: 'app/connectors/connect.php',
    data: $.extend({action: action}, params, pBotData.login),
    dataType: 'json',
    success: function (data) {
      if (data[1] == 200) {
        if (callback) {
          callback.apply(this, [data[0], data[1]]);
        }
      } else {
        showGeneralAlert('Bot Request failed!', 'danger');
        console.log('Bot Request failed...', data);
      }
    },
  });
}

function doQuickCommand(command, confirmAction, noReload) {
  if (confirmAction && confirmAction != '') {
    if (!confirm(confirmAction)) {
      return;
    }
  }
  doBotRequest('command', function (result) {
    showGeneralAlert(result);
    if (!noReload) {
      loadPartFromStorage();
    }
  }, {command: _cleanInput(command)});
}

function getPanelConfig() {
  $.ajax({
    type: "POST",
    url: 'app/connectors/connect.php',
    data: $.extend({action: 'getConfig'}, pBotData.login),
    dataType: 'json',
    success: function (data) {
      if (data[1] == 200) {
        pBotData.config = data[0];
        loadChannelData();
        /* Apply UI Settings */
        toggleMusicPlayerControls(true);
        toggleTooltips(true);
        toggleChat(true);
        loadPartFromStorage();

      } else {
        showLoginAlert(data[3])
      }
    },
  });
}

function loadChannelData(skipCache) {
  var cache = channelDataCache();
  if (!skipCache && cache && cache.pbotCacheEndTime > Date.now()) {
    console.log('Stream info loaded from local cache!', cache);
    applyChannelData(cache);
  } else {
    var newCache = {
      stream: null,
      channel: null,
      followers: null
    };
    $.ajax({
      url: 'https://api.twitch.tv/kraken/channels/' + pBotData.config.owner + '/follows?limit=1',
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        newCache.followers = data;
        $.ajax({
          url: 'https://api.twitch.tv/kraken/streams/' + pBotData.config.owner,
          type: 'GET',
          dataType: 'json',
          success: function (data) {
            if (data.stream != null) {
              newCache.stream = data.stream;
              newCache.channel = data.stream.channel;
              channelDataCache(newCache);
              applyChannelData(newCache);
            } else {
              $.ajax({
                url: 'https://api.twitch.tv/kraken/channels/' + pBotData.config.owner,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                  newCache.channel = data;
                  channelDataCache(newCache);
                  applyChannelData(newCache);
                }
              });
            }
          }
        });
      }
    });
  }
}

function loadPartFromStorage() {
  var partUrl = pBotStorage.get(pBotStorage.keys.lastUsedPart, 'static/dashboard.php');
  setActiveMenuItem(partUrl);
  $('#part-window').load('app/parts/' + partUrl, {
    token: pBotData.config.token,
    username: pBotData.login.username
  }, bindPartEventHandlers);
}

//noinspection JSUnusedGlobalSymbols
function logOut() {
  localStorage.removeItem(pBotStorage.keys.twitchCache);
  localStorage.removeItem(pBotStorage.keys.panelLogin);
  location.replace('index.php');
}

//noinspection JSUnusedGlobalSymbols
function openPart(partUrl, filters) {
  var partWindow = $('#part-window');
  pBotData.touchedCollapsibles = [];
  setActiveMenuItem(partUrl);
  partWindow.load('app/parts/static/loading.html', null, function () {
    if (filters) {
      partWindow.load(
          'app/parts/' + partUrl,
          $.extend(
              filters,
              {
                token: pBotData.config.token,
                username: pBotData.login.username
              }
          ),
          bindPartEventHandlers
      );
    } else {
      partWindow.load(
          'app/parts/' + partUrl,
          {
            token: pBotData.config.token,
            username: pBotData.login.username
          },
          bindPartEventHandlers
      );
    }
    pBotStorage.set(pBotStorage.keys.lastUsedPart, partUrl);
  });
}

function saveToConfig(settingPath, inputId, button) {
  var setting = $('#' + inputId).val();

  doBotRequest('saveToConfig', function (status) {
    console.log(status);
    $(button)
        .removeClass('btn-primary')
        .addClass('btn-success')
        .text('Saved')
        .prop('disabled', true);
    var timer = setTimeout(function () {
      $(button)
          .removeClass('btn-success')
          .addClass('btn-primary')
          .text('Save')
          .prop('disabled', false);
      clearTimeout(timer);
    }, 3e3)
  }, {settingPath: settingPath, setting: setting.trim()});
}

function setActiveMenuItem(partUrl) {
  $('.active').removeClass('active');
  $('#menu-parent-' + partUrl.replace('static/', '').replace(/([a-z]+).*/i, '$1')).addClass('active');
  $('#menu-favorites-' + partUrl.replace(/.*\/|-|\.php/ig, '')).addClass('active');
}

function sideTabTabHandler(e) {
  var targetTab = $(e.target),
      tabWrapper = targetTab.parent(),
      iframe = tabWrapper.find('iframe'),
      activateBtn = tabWrapper.find('button');

  tabWrapper.toggleClass('open');
  targetTab.toggleClass('active');

  if (activateBtn.length > 0) {
    activateBtn
        .off('click')
        .on('click', function () {
          if (activateBtn.hasClass('btn-success')) {
            iframe.attr('src', targetTab.attr('tab-src'));
          } else {
            iframe.attr('src', '');
          }
          targetTab
              .toggleClass('pulse')
          ;

          activateBtn
              .toggleClass('btn-success')
              .toggleClass('btn-danger')
          ;

          activateBtn
              .find('span')
              .toggleClass('fa-check')
              .toggleClass('fa-close')
          ;

          tabWrapper
              .find('.notice')
              .toggleClass('hidden')
          ;
        })
    ;
  } else {
    if (tabWrapper.hasClass('open')) {
      iframe.attr('src', targetTab.attr('tab-src'));
    } else {
      iframe.attr('src', '');
    }
  }
}

function getBotStatus() {
  $.ajax({
    type: 'POST',
    url: 'app/connectors/connect.php',
    data: $.extend({action: 'testBotConnection'}, pBotData.login),
    dataType: 'json',
    success: function (data) {
      if (data[2] == 52) {
        pBotData.config.isBotOnline = true;
      } else {
        console.warn('Bot is offline!\nDisabling bot requests...\nChecking again in 10 minutes...\n' + data[3]);
      }
    },
  });
}

function bindContextMenu(replace) {
  var body = $('body'),
      favorites = pBotStorage.get(pBotStorage.keys.favoritesMenuItems, []),
      contextItems = [
        {title: 'Quick Access'},
        {title: 'Dashboard', cmd: 'part:static/dashboard.php'}
      ];
  favorites.sort(function (a, b) {
    return a.itemName > b.itemName;
  });
  for (var i in favorites) {
    contextItems.push({
      title: favorites[i].itemName,
      cmd: 'part:' + favorites[i].itemPath,
    });
  }

  contextItems.push(
      {title: 'Toggle Chat', cmd: 'func:toggleChat'},
      {title: 'Toggle Player Controls', cmd: 'func:toggleMusicPlayerControls'}
  );

  if (replace) {
    body.contextmenu("replaceMenu", contextItems);
  } else {
    body.contextmenu({
      menu: contextItems,
      select: function (event, ui) {
        if (ui.cmd) {
          if (ui.cmd.match(/^func:/)) {
            window[ui.cmd.replace('func:', '')]();
          }
          if (ui.cmd.match(/^part:/)) {
            window.scrollTo(0, 0);
            openPart(ui.cmd.replace('part:', ''));
          }
        }
      }
    });
  }
}

function showGeneralAlert(message, severity) {
  var alertHost = $('#general-alert');
  if (!message) {
    message = 'You forgot to enter a value!';
  }
  if (!severity) {
    severity = 'info';
  }
  alertHost
      .addClass('alert-' + severity)
      .text(message)
      .fadeIn(500);
  var t = setTimeout(function () {
    alertHost.animate({
      right: '-100%',
    }, {
      duration: 1500,
      complete: function () {
        $(this)
            .hide()
            .css({
              right: '1em',
            })
            .removeClass('alert-' + severity);
      }
    });
    clearTimeout(t);
  }, 3e3);
}

function toggleInformationPanels(fromPageLoad) {
  var panels = $('.information-panel');
  if (fromPageLoad) {
    panels.toggleClass('hidden', !pBotData.informationActive);
  } else {
    pBotData.informationActive = !pBotData.informationActive;
    panels.toggleClass('hidden', pBotData.informationActive);
    pBotStorage.set(pBotStorage.keys.informationActive, pBotData.informationActive);
    loadPartFromStorage();
  }
}

//noinspection CoffeeScriptUnusedLocalSymbols
function toggleMusicPlayerControls(fromLoad, button) {
  if (fromLoad) {

  } else {
    pBotData.musicPlayerControls.controlsEnabled = !pBotData.musicPlayerControls.controlsEnabled;
    pBotStorage.set(pBotStorage.keys.musicPlayer.controlsEnabled, pBotData.musicPlayerControls.controlsEnabled);
  }
  var body = $('body'),
      enabledClassName = 'music-controls-enabled';

  if (!pBotData.musicPlayerControls.controlsEnabled) {
    if (pBotData.musicPlayerControls.updateIntervalId != -1) {
      clearInterval(pBotData.musicPlayerControls.updateIntervalId);
    }
    body.removeClass(enabledClassName);
    $(button).html('<span class="fa fa-eject"></span>&nbsp;Show Music Player Controls');
  } else {
    body.addClass(enabledClassName);
    pBotData.musicPlayerControls.updateIntervalId = setInterval(updateMusicPlayerState, 5e3);
    $(button).html('<span class="fa fa-eject text-success"></span>&nbsp;Hide Music Player Controls');
  }
}

function toggleTooltips(fromLoad) {
  if (fromLoad) {
    bindTooltips(pBotData.tooltipsActive);
  } else {
    bindTooltips(pBotData.tooltipsActive);
    pBotData.tooltipsActive = !pBotData.tooltipsActive;
    pBotStorage.set(pBotStorage.keys.tooltipsActive, pBotData.tooltipsActive);
    loadPartFromStorage();
  }
}

//noinspection JSUnusedGlobalSymbols,CoffeeScriptUnusedLocalSymbols
function toggleChat(fromLoad) {
  //noinspection CoffeeScriptUnusedLocalSymbols
  var body = $('body'),
      chatIframe = $('#chat-iframe'),
      enabledClassName = 'chat-enabled',
      chatEnabled = body.hasClass(enabledClassName);
  if (fromLoad) {
    if (pBotStorage.get(pBotStorage.keys.chatDefaultState)) {
      body.addClass(enabledClassName);
      chatIframe.attr('src', 'http://www.twitch.tv/' + pBotData.config.owner + '/chat?popout=');
    }
    return;
  }
  if (chatEnabled) {
    body.removeClass(enabledClassName);
    chatIframe.attr('src', '');
  } else {
    body.addClass(enabledClassName);
    chatIframe.attr('src', 'http://www.twitch.tv/' + pBotData.config.owner + '/chat?popout=');
  }
}

//noinspection JSUnusedGlobalSymbols
function toggleChatDefaultState() {
  var currentSetting = !pBotStorage.get(pBotStorage.keys.chatDefaultState);
  pBotStorage.set(pBotStorage.keys.chatDefaultState, currentSetting);
}

function updateFavoritesMenu(itemName, itemPath) {
  var favorites = pBotStorage.get(pBotStorage.keys.favoritesMenuItems, []);
  if (itemName && itemPath) {
    var exists;
    for (var i in favorites) {
      //noinspection JSUnfilteredForInLoop
      if (favorites[i].itemName == itemName) {
        exists = true;
        //noinspection JSUnfilteredForInLoop
        favorites.splice(i, 1);
      }
    }
    if (!exists) {
      favorites.push({
        itemName: itemName,
        itemPath: itemPath
      });
    }
    pBotStorage.set(pBotStorage.keys.favoritesMenuItems, favorites);
    setTimeout(updateFavoritesMenu, 10);
  } else {
    var favoritesMenu = $('#favorites-menu');
    favoritesMenu.children('.favorites-item').each(function (i, e) {
      $(e).remove();
    });
    for (i in favorites) {
      //noinspection JSUnfilteredForInLoop
      favoritesMenu.append($('<li class="favorites-item" id="menu-favorites-' + favorites[i].itemName.replace(' ', '').toLocaleLowerCase()
          + '"><a nohref onclick="openPart(\'' + favorites[i].itemPath + '\')" role="button">' + favorites[i].itemName + '</a></li>'));
    }
  }
  bindContextMenu(true);
}

function updateMusicPlayerState() {
  if (pBotData.musicPlayerControls.controlsEnabled) {
    doBotRequest('getCurrentTitle', function (result) {
      $('#current-video-title').html(result);
    });
  }
}

function togglePlayPause() {
  doBotRequest('command', function (result) {
    showGeneralAlert(result, 'success');
  }, {command: 'musicplayer pause'});
}

//noinspection JSUnusedGlobalSymbols
function toggleSfx(state) {
  debug(state);
  doBotRequest('saveToConfig', function () {
    loadPartFromStorage();
  }, {settingPath: 'misc/sfxEnabled', setting: state.toString()});
}

//noinspection JSUnusedGlobalSymbols
function deleteSfx(command) {
  doBotRequest('deleteSfxCommand', function () {
    loadPartFromStorage();
  }, {command: command});
}

function openPopup(address, name, options) {
  if (!name) {
    name = 'PhantomBot WebPanel';
  }
  if (!options) {
    options = 'width=800,height=600';
  }
  window.open(address, name, options);
}

function _cleanInput(input) {
  var search = [/^!/],
      replacements = [''],
      i;
  for (i in search) {
    input = input.replace(search[i], replacements[i])
  }
  return input;
}

//noinspection JSUnusedGlobalSymbols
function debug() {
  console.log.apply(console, arguments);
}
