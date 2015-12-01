<?php
/**
 * commands.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:42
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../../..');

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');
require_once(BASEPATH . '/app/php/classes/ComponentTemplates.class.php');
require_once(BASEPATH . '/app/php/classes/PanelSession.class.php');

$session = new PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$groups = $functions->getIniArray('groups');
$customCommandsIni = $functions->getIniArray('command');
$commandAliasIni = $functions->getIniArray('aliases');
$commandPriceIni = $functions->getIniArray('pricecom');
$commandPermIni = $functions->getIniArray('permcom');
$defaultCommands = [];
$pointNames = [
    (array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points'),
    (array_key_exists('pointNameSingle', $botSettings) ? $botSettings['pointNameSingle'] : 'point'),
];
$customCommandsTableRows = '';
$defaultCommandsTableRows = '';

foreach ($customCommandsIni as $command => $message) {
  $commandAliases = [];
  foreach ($commandAliasIni as $alias => $originalCommand) {
    if ($originalCommand == $command) {
      $commandAliases[] = '!' . $alias;
    }
  }
  if (preg_match('/\/me\s|\/w\s/i', $message, $msgType)) {
    if ($msgType[0] == '/me ') {
      $msgClass = 'me';
    } else {
      $msgClass = 'whisper';
    }
  } else {
    $msgClass = '';
  }
  if (array_key_exists($command, $commandPermIni)) {
    $perm = $groups[getGroupId($commandPermIni[$command])];
  } elseif (array_key_exists($command . '_recursive', $commandPermIni)) {
    $perm = $groups[getGroupId($commandPermIni[$command . '_recursive'])] . '+';
  } else {
    $perm = 'Viewer';
  }
  if (preg_match('/\([0-9]\)|\([.]{3}\)/i', $message)) {
    $actor = $templates->botCommandForm($command, '', '!' . $command);
  } else {
    $actor = $templates->botCommandButton($command, '!' . $command, 'default btn-block');
  }
  if (array_key_exists($command, $commandPriceIni)) {
    if (intval($commandPriceIni[$command]) < 1 || intval($commandPriceIni[$command]) > 1) {
      $price = $commandPriceIni[$command] . ' ' . $pointNames[0];
    } else {
      $price = $commandPriceIni[$command] . ' ' . $pointNames[1];
    }
  } else {
    $price = '0 ' . $pointNames[0];
  }
  $customCommandsTableRows .= '<tr>'
      . $templates->addTooltip('<td class="command-actor">' . $actor . '</td>',
          '<span class="message ' . $msgClass . '">' . $message . '</span>',
          ['position' => ComponentTemplates::TOOLTIP_POS_RIGHT, 'offsetY' => (strlen($message) < 50 ? 17 : (strlen($message) > 90 ? -17 : 0))]
      )
      . '<td>'
      . '<span class="text-muted">Group:</span>&nbsp;' . $perm . '<br />'
      . '<span class="text-muted">Price:</span>&nbsp;' . $price
      . '</td>'
      . '<td>' . join(', ', $commandAliases) . '</td>'
      . '<td class="actions">' . $templates->botCommandButton('delcom ' . $command, '<span class="fa fa-trash"></span>', 'danger', 'Are you sure you want to delete !' . $command . '?') . '</td>'
      . '</tr>';
}

array_walk($commandPermIni, function ($value) use ($defaultCommands) {
  $defaultCommands[] = str_replace('_recursive', '', $value);
});
$defaultCommands = array_unique(array_merge($defaultCommands, array_keys($commandPriceIni), array_values($commandAliasIni)));
sort($defaultCommands);
foreach ($defaultCommands as $command) {
  if (array_key_exists($command, $commandAliasIni) || array_key_exists($command, $customCommandsIni)) {
    continue;
  }
  $commandAliases = [];
  foreach ($commandAliasIni as $alias => $originalCommand) {
    if ($originalCommand == $command) {
      $commandAliases[] = '!' . $alias;
    }
  }
  if (array_key_exists($command, $commandPermIni)) {
    $perm = $groups[getGroupId($commandPermIni[$command])];
  } elseif (array_key_exists($command . '_recursive', $commandPermIni)) {
    $perm = $groups[getGroupId($commandPermIni[$command . '_recursive'])] . '+';
  } else {
    $perm = '(not set in permcom)';
  }
  if (array_key_exists($command, $commandPriceIni) && $commandPriceIni[$command] != '') {
    if (intval($commandPriceIni[$command]) < 1 || intval($commandPriceIni[$command]) > 1) {
      $price = $commandPriceIni[$command] . ' ' . $pointNames[0];
    } else {
      $price = $commandPriceIni[$command] . ' ' . $pointNames[1];
    }
  } else {
    $price = '0 ' . $pointNames[0];
  }
  $defaultCommandsTableRows .= '<tr>'
      . '<td class="command-actor">' . '!' . $command . '</td>'
      . '<td class="price">' . $price . '</td>'
      . '<td>' . $perm . '</td>'
      . '<td>' . join('<br />', $commandAliases) . '</td>'
      . '</tr>';
}

function getGroupId($group)
{
  if (!is_numeric($group)) {
    switch (strtolower($group)) {
      case 'caster':
        return '0';
      case 'administrator':
        return '1';
      case 'moderator':
        return '2';
      case 'subscriber':
        return '3';
      case 'donator':
        return '4';
      case 'hoster':
        return '5';
      case 'regular':
        return '6';
      case 'viewer':
        return '7';
      default:
        return $group;
    }
  }
  return $group;
}

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Bot Commands
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('addCommand.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <?= $templates->botCommandForm('', 'Run command', '[command] [params]') ?>
      <hr/>
      <h4 class="collapsible-master">Command Creation</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-6">
            <?= $templates->botCommandForm('addcom', 'Add command', '[command] [message]') ?>
            <?= $templates->botCommandForm('editcom', 'Modify command', '[command] [message]') ?>
          </div>
          <div class="col-sm-6">
            <div class="toggled-notice panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">Command Creation Tags</h4>
              </div>
              <div class="panel-body">
                <ul>
                  <li>(sender) - displays the user of the command</li>
                  <li>(count) - displays the amount of times the command has been used</li>
                  <li>(random) - chooses a random person in the channel</li>
                  <li>(code) - generates a 8 character code using A-Z and 1-9</li>
                  <li>(#) - generates a random number 1-100</li>
                  <li>(1) - this targets the first argument in a command.</li>
                  <li>(2) - this targets the second argument in a command.</li>
                  <li>(file path/to/file) - replace this tag with the contents of the given file.</li>
                  <li>(customapi requesturl) - replace this tag with the result of the request call.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Command Attributes</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-6">
            <?= $templates->botCommandForm('aliascom', 'Add alias', '[command] [alias]') ?>
            <?= $templates->botCommandForm('coolcom', 'Add cooldown', '[command] [cooldown]') ?>
            <?= $templates->botCommandForm('pricecom', 'Price', '[command] [amount]') ?>
            <?= $templates->botCommandForm('permcom', 'Permission', '[command] [group] [mode]') ?>
            <?= $templates->botCommandForm('rewardcom', 'Reward', '[command] [amount]') ?>
          </div>
          <div class="col-sm-6">
            <?= $templates->informationPanel('<p>!Permcom uses a "<b>mode</b>" system, which is for targeting what groups has access to the command.<br/>
                <ul>
                  <li>1: Setting the mode as "<b>1</b>" will set the command to be accessible by <cite>ONLY</cite> the chosen
                    group.
                  </li>
                  <li>2: Setting the mode as "<b>2</b>" will set the command to be accessible by the chosen group you and
                    any group ranked higher.
                  </li>
                </ul>
                <p><b>Alias:</b> Alias means another word for the same command. If you set !points with an alias as wallet, the !points command will respond to !wallet.</p>') ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Custom Commands', ['Command', 'Attributes', 'Aliases', ''], $customCommandsTableRows, true, 'custom-commands') ?>
      <hr/>
      <?= $templates->dataTable('Default Command Settings', ['Command', 'price', 'Permissions', 'Aliases'], $defaultCommandsTableRows, true, 'custom-commands') ?>
    </div>
  </div>
</div>