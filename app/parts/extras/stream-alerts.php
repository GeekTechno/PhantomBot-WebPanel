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

$dataStore->createTable('streamalertsettings');

if (trim($dataStore->getVar('streamalertsettings', 'followerAlertCSS')) == '') {
  $dataStore->setVar('streamalertsettings', 'followerAlertCSS', $functions->getDefaultAlertCSS());
}

if (trim($dataStore->getVar('streamalertsettings', 'hostAlertCSS')) == '') {
  $dataStore->setVar('streamalertsettings', 'hostAlertCSS', $functions->getDefaultAlertCSS());
}

?>
<div class="app-part">
  <script src="/app/js/codemirror.min.js" type="text/javascript"></script>
  <script src="/app/js/css.js" type="text/javascript"></script>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Stream Alerts
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <button class="btn btn-primary"
                onclick="openPopup('stream-alerts-player.php', 'PhantomBot WebPanel Stream Alerts', '<?= $dataStore->getVar('misc', 'alertWindowSize', 'width=550,height=200') ?>')">
          Open Alerts Player
        </button>
      </div>
      <hr/>
      <h4>General Settings</h4>

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Background Color <span class="text-muted">(for Chroma keying)</span></span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-bg-color"
                     placeholder="<?= $dataStore->getVar('misc', 'streamAlertBG', '#FFFFFF | transperant') ?>"
                     value="<?= $dataStore->getVar('misc', 'streamAlertBG') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('misc/streamAlertBG', 'setting-bg-color', this)">
                  Save
                </button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <span>Window Size</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-window-size"
                     placeholder="<?= $dataStore->getVar('misc', 'alertWindowSize', 'width=550,height=200') ?>"
                     value="<?= $dataStore->getVar('misc', 'alertWindowSize') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('misc/alertWindowSize', 'setting-window-size', this)">
                  Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-8">
          <?= $templates->informationPanel(
              '<p>This feature only works on the latest <a href="https://github.com/GloriousEggroll/PhantomBot" target="_blank">Phantombot</a> "nightly"!</p>' .
              '<p>Use (name) in the Alert Text to insert the new follower/hoster\'s name.</p>' .
              '<p>Use <code>//' . \PBPanel\AppLoader::getBaseUrl() . '/stream-alerts-player.php</code> in your OBS browser, to embed the alerts easilly in your stream!</p>' .
              '<p>Leave the sound field empty if you don\'t want to use sound effects on alerts.</p>' .
              '<p>Empty the CSS field and save to reset it\'s original value!</p>' .
              '<p>Donation alerts will be available soon!</p>'
          ) ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Follower Alerts</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Background <span class="text-muted">(file has to be hosted online!)</span></span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-bg-follower"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'followerAlertBG') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/followerAlertBG', 'setting-alert-bg-follower', this)">
                  Save
                </button>
              </span>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Text</span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-text-follower"
                       placeholder="Alert text"
                       value="<?= $dataStore->getVar('streamalertsettings', 'followerAlertText') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/followerAlertText', 'setting-alert-text-follower', this)">
                  Save
                </button>
              </span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Sound <span class="text-muted">(file has to be hosted online!)</span></span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-sound-follower"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'followerAlertSound') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/followerAlertSound', 'setting-alert-sound-follower', this)">
                  Save
                </button>
              </span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <div class="form-group">
              <span>Custom CSS</span>

              <div class="form-group">
                <textarea class="form-control" id="setting-alert-css-follower"
                          placeholder="Custom CSS"><?= $dataStore->getVar('streamalertsettings', 'followerAlertCSS') ?></textarea>
              </div>
              <button class="btn btn-primary"
                      onclick="saveToConfig('streamalertsettings/followerAlertCSS', 'setting-alert-css-follower', this)">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Host Alerts</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Background <span class="text-muted">(file has to be hosted online!)</span></span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-bg-host"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'hostAlertBG') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/hostAlertBG', 'setting-alert-bg-host', this)">
                  Save
                </button>
              </span>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Text</span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-text-host"
                       placeholder="Alert text"
                       value="<?= $dataStore->getVar('streamalertsettings', 'hostAlertText') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/hostAlertText', 'setting-alert-text-host', this)">
                  Save
                </button>
              </span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Sound <span class="text-muted">(file has to be hosted online!)</span></span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-sound-host"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'hostAlertSound') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/hostAlertSound', 'setting-alert-sound-host', this)">
                  Save
                </button>
              </span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <div class="form-group">
              <span>Custom CSS</span>

              <div class="form-group">
                <textarea class="form-control" id="setting-alert-css-host"
                          placeholder="Custom CSS"><?= $dataStore->getVar('streamalertsettings', 'hostAlertCSS') ?></textarea>
              </div>
              <button class="btn btn-primary"
                      onclick="saveToConfig('streamalertsettings/hostAlertCSS', 'setting-alert-css-host', this)">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    CodeMirror.fromTextArea(document.getElementById('setting-alert-css-follower'), {
      lineNumbers: true,
      mode: 'css',
    }).on('change', function (event) {
      console.log(event);
      event.save();
    });
    CodeMirror.fromTextArea(document.getElementById('setting-alert-css-host'), {
      lineNumbers: true,
      mode: 'css',
    }).on('change', function (event) {
      console.log(event);
      event.save();
    });
  })();
</script>