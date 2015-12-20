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

$alertCSSFields = [
    'followerAlertCSS',
    'hostAlertCSS',
    'subscribeAlertCSS',
    'donationAlertCSS',
];

foreach ($alertCSSFields as $alertCSSField) {
  if (trim($dataStore->getVar('streamalertsettings', $alertCSSField)) == '') {
    $dataStore->setVar('streamalertsettings', $alertCSSField, $functions->getDefaultAlertCSS());
  }
}

?>
<div class="app-part">
  <script src="app/js/codemirror.min.js" type="text/javascript"></script>
  <script src="app/js/cm-css.min.js" type="text/javascript"></script>
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
              '<p>Use (name) in the Alert Text to insert the new follower/hoster\'s name.</p>' .
              '<p>Use <code>//' . \PBPanel\AppLoader::getBaseUrl() . '/stream-alerts-player.php</code> in your OBS browser, to embed the alerts easily in your stream!</p>' .
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
      <hr/>
      <h4 class="collapsible-master">New Subscriber Alerts</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Background <span class="text-muted">(file has to be hosted online!)</span></span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-bg-subscribe"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'subscribeAlertBG') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/subscribeAlertBG', 'setting-alert-bg-subscribe', this)">
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
                <input type="text" class="form-control" id="setting-alert-text-subscribe"
                       placeholder="Alert text"
                       value="<?= $dataStore->getVar('streamalertsettings', 'subscribeAlertText') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/subscribeAlertText', 'setting-alert-text-subscribe', this)">
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
                <input type="text" class="form-control" id="setting-alert-sound-subscribe"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'subscribeAlertSound') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/subscribeAlertSound', 'setting-alert-sound-subscribe', this)">
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
                <textarea class="form-control" id="setting-alert-css-subscribe"
                          placeholder="Custom CSS"><?= $dataStore->getVar('streamalertsettings', 'subscribeAlertCSS') ?></textarea>
              </div>
              <button class="btn btn-primary"
                      onclick="saveToConfig('streamalertsettings/subscribeAlertCSS', 'setting-alert-css-subscribe', this)">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">New Donation Alerts <small>(N.A.)</small></h4>

      <div class="collapsible-content disabled">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <span>Alert Background <span class="text-muted">(file has to be hosted online!)</span></span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-bg-donation"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'donationAlertBG') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/donationAlertBG', 'setting-alert-bg-donation', this)">
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
                <input type="text" class="form-control" id="setting-alert-text-donation"
                       placeholder="Alert text"
                       value="<?= $dataStore->getVar('streamalertsettings', 'donationAlertText') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/donationAlertText', 'setting-alert-text-donation', this)">
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
                <input type="text" class="form-control" id="setting-alert-sound-donation"
                       placeholder="url"
                       value="<?= $dataStore->getVar('streamalertsettings', 'donationAlertSound') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('streamalertsettings/donationAlertSound', 'setting-alert-sound-donation', this)">
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
                <textarea class="form-control" id="setting-alert-css-donation"
                          placeholder="Custom CSS"><?= $dataStore->getVar('streamalertsettings', 'donationAlertCSS') ?></textarea>
              </div>
              <button class="btn btn-primary"
                      onclick="saveToConfig('streamalertsettings/donationAlertCSS', 'setting-alert-css-donation', this)">
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
    var cmSettings = {
          lineNumbers: true,
          mode: 'css',
        },
        onChangeFunc = function (event) {
          event.save();
        };

    CodeMirror.fromTextArea(document.getElementById('setting-alert-css-follower'), cmSettings).on('change', onChangeFunc);
    CodeMirror.fromTextArea(document.getElementById('setting-alert-css-host'), cmSettings).on('change', onChangeFunc);
    CodeMirror.fromTextArea(document.getElementById('setting-alert-css-subscribe'), cmSettings).on('change', onChangeFunc);
  })();
</script>