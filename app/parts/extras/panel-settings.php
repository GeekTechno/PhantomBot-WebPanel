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
$functions = new \PBPanel\Util\FunctionLibrary($dataStore, $connection);
$templates = new \PBPanel\Util\ComponentTemplates();

$botSettings = $functions->getDbTableArray('settings');
$currentTheme = $dataStore->getVar('misc', 'theme', 'style_dark');
$themeFiles = new \PBPanel\Util\SortedDirectoryIterator(\PBPanel\AppLoader::getBaseDir() . '/app/css', false);
$themesOptions = '';

/* @var \DirectoryIterator $themeFile */
foreach ($themeFiles as $themeFile) {
  if ($themeFile->getExtension() == 'css' && strpos($themeFile->getBasename(), 'style_') === 0) {
    $themesOptions .= '<option value="' . $themeFile->getBasename('.css') . '">' . ucfirst(str_replace('style_', '', $themeFile->getBasename('.css'))) . '</option>';
  }
}

?>
<script>
  $('#toggle-information-button').prop('checked', pBotStorage.get(pBotStorage.keys.informationActive, true));
  $('#toggle-tooltips-button').prop('checked', !pBotStorage.get(pBotStorage.keys.tooltipsActive, false));
  $('#toggle-chat-default-button').prop('checked', pBotStorage.get(pBotStorage.keys.chatDefaultState, false));
</script>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        PhantomBot Panel Preferences
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>General</h4>

      <div class="btn-toolbar">
        <?= $templates->switchToggle('Show Information Panels', 'toggleInformationPanels', '[false]', 'toggle-information-button') ?>
        <?= $templates->switchToggle('Show Tooltips', 'toggleTooltips', '[false]', 'toggle-tooltips-button') ?>
        <?= $templates->switchToggle('Show Chat By Default', 'toggleChatDefaultState', '[false]', 'toggle-chat-default-button') ?>
      </div>
      <div class="spacer"></div>
      <div class="row">
        <div class="col-xs-4">
          <form id="theme-selector">
            <span>UI Theme</span>

            <div class="input-group">
              <select class="form-control">
                <?= $themesOptions ?>
              </select>

              <div class="input-group-btn">
                <button type="submit" class="btn btn-primary"><span class="fa fa-check"></span></button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <hr/>
      <h4>Panel Users</h4>

      <div class="btn-toolbar">
        <button class="btn btn-default" onclick="openPart('static/panel-users.php')">Manage</button>
      </div>
      <hr/>
      <h4>Misc PhantomBot Settings</h4>

      <div class="row">
        <div class="col-sm-8">
          <div class="btn-toolbar">
            <?= $templates->switchToggle('Whisper Mode', 'doQuickCommand', '[\'togglewhispermode\']', '', (array_key_exists('whisperMode', $botSettings) && $botSettings['whisperMode'] == 'true')) ?>
            <?= $templates->switchToggle('Enable Event/Error Logging', $templates->_wrapInJsToggledDoQuickCommand('log', (array_key_exists('loggingEnabled', $botSettings) && $botSettings['loggingEnabled'] == 'true' ? 'true' : 'false'), 'enable', 'disable'), '[]', '', (array_key_exists('loggingEnabled', $botSettings) && $botSettings['loggingEnabled'] == 'true')) ?>
          </div>
        </div>
        <div class="col-sm-4">
          <?= $templates->informationPanel('The bot is able to whisper some messages intended for a specific user to that user, instead of posting it in the chat.') ?>
        </div>
      </div>
      <hr/>
      <h4>Connector Settings</h4>

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>PhantomBot IP address</label>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-bot-ip"
                     placeholder="<?= $dataStore->getVar('connector', 'botIp') ?>"
                     value="<?= $dataStore->getVar('connector', 'botIp') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary" onclick="saveToConfig('connector/botIp', 'setting-bot-ip', this)">Save
                </button>
              </span>
            </div>

            <p class="text-muted">This is generally the Ip address of the machine running PhantomBot.</p>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>PhantomBot webserver base port</label>

            <div class="input-group">
              <input type="number" class="form-control" id="setting-bot-base-port"
                     placeholder="<?= $dataStore->getVar('connector', 'botBasePort') ?>"
                     value="<?= $dataStore->getVar('connector', 'botBasePort') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('connector/botBasePort', 'setting-bot-base-port', this)">
                  Save
                </button>
              </span>
            </div>

            <p class="text-muted">
              This is by default "25000". Only change it if you have entered a custom port at the PhantomBot
              installation!
            </p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Username for bot</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-bot-name"
                     placeholder="<?= $dataStore->getVar('connector', 'botName') ?>"
                     value="<?= $dataStore->getVar('connector', 'botName') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary" onclick="saveToConfig('connector/botName', 'setting-bot-name', this)">
                  Save
                </button>
              </span>
            </div>

            <p class="text-muted">
              The username of the account you used for PhantomBot.
            </p>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Bot account Oauth token</label>

            <div class="input-group">
              <input type="password" class="form-control" id="bot-oauth"
                     placeholder="oauth:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
                     value="<?= $dataStore->getVar('connector', 'botOauthToken') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('connector/botOauthToken', 'setting-bot-oauth', this)">
                  Save
                </button>
              </span>
            </div>

            <p class="text-muted">
              This can be found in &quot;botlogin.txt&quot; in the installation folder of PhantomBot. (Use the &quot;oauth&quot;
              one)
            </p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Channel owner username</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-bot-owner"
                     placeholder="<?= $dataStore->getVar('connector', 'channelOwner') ?>"
                     value="<?= $dataStore->getVar('connector', 'channelOwner') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('connector/channelOwner', 'setting-bot-owner', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <hr/>
      <h4>Bot Add-on Paths</h4>

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Latest follower file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-follower"
                     placeholder="<?= $dataStore->getVar('paths', 'latestFollower') ?>"
                     value="<?= $dataStore->getVar('paths', 'latestFollower') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/latestFollower', 'setting-path-follower', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <span>Latest donation file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-donation"
                     placeholder="<?= $dataStore->getVar('paths', 'latestDonation') ?>"
                     value="<?= $dataStore->getVar('paths', 'latestDonation') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/latestDonation', 'setting-path-donation', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Current song file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-current-song"
                     placeholder="<?= $dataStore->getVar('paths', 'youtubeCurrentSong') ?>"
                     value="<?= $dataStore->getVar('paths', 'youtubeCurrentSong') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/youtubeCurrentSong', 'setting-path-current-song', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <span>Latest song requests file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-playlist"
                     placeholder="<?= $dataStore->getVar('paths', 'youtubePlaylist') ?>"
                     value="<?= $dataStore->getVar('paths', 'youtubePlaylist') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/youtubePlaylist', 'setting-path-playlist', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Default playlist file <span class="text-muted(txt!)"></span></span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-default-playlist"
                     placeholder="<?= $dataStore->getVar('paths', 'defaultYoutubePlaylist') ?>"
                     value="<?= $dataStore->getVar('paths', 'defaultYoutubePlaylist') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/defaultYoutubePlaylist', 'setting-path-default-playlist', this)">
                  Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    $('#theme-selector')
        .submit(function (event) {
          doBotRequest('saveToConfig', function () {
            location.reload();
          }, {settingPath: 'misc/theme', setting: event.target[0].selectedOptions[0].value.trim()});
          event.preventDefault();
        })
        .find('option').filter('[value=<?= $dataStore->getVar('misc', 'theme', 'style_dark') ?>]').attr('selected', true);
  })();
</script>