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
      <h4 class="collapsible-master">General Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <span>Background Color <span class="text-muted">(for Chroma keying)</span></span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-bg-color"
                       placeholder="<?= $dataStore->getVar('misc', 'streamAlertBG', '#FFFFFF') ?>"
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
              <span>Window Size <span class="text-muted">(use format "width=[IN PIXELS],height=[IN PIXELS]")</span></span>

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
                       placeholder="/path/to/file or url"
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
              <span>Alert Sound</span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-sound-follower"
                       placeholder="/path/to/file or url"
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
          <div class="col-sm-4">
            <div class="form-group">
              <span>Custom CSS</span>

              <div class="form-group">
                <textarea class="form-control" id="setting-alert-cdd-follower"
                          placeholder="Custom CSS"><?= $dataStore->getVar('streamalertsettings', 'followerAlertCSS') ?></textarea>
              </div>
              <button class="btn btn-primary"
                      onclick="saveToConfig('streamalertsettings/followerAlertCSS', 'setting-alert-cdd-follower', this)">
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
                       placeholder="/path/to/file or url"
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
              <span>Alert Sound</span>

              <div class="input-group">
                <input type="text" class="form-control" id="setting-alert-sound-host"
                       placeholder="/path/to/file or url"
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
          <div class="col-sm-4">
            <div class="form-group">
              <span>Custom CSS</span>

              <div class="form-group">
                <textarea class="form-control" id="setting-alert-cdd-host"
                          placeholder="Custom CSS"><?= $dataStore->getVar('streamalertsettings', 'hostAlertCSS') ?></textarea>
              </div>
              <button class="btn btn-primary"
                      onclick="saveToConfig('streamalertsettings/hostAlertCSS', 'setting-alert-cdd-host', this)">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
