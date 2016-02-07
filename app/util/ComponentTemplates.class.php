<?php

/**
 * ComponentTemplates.class.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:45
 */
namespace PBPanel\Util;

class ComponentTemplates
{
  const TOOLTIP_POS_BOTTOM = 'bottom';
  const TOOLTIP_POS_EVENT = 'event';
  const TOOLTIP_POS_LEFT = 'left';
  const TOOLTIP_POS_RIGHT = 'right';
  const TOOLTIP_POS_TOP = 'top';

  protected $partFile;
  protected $partUrl;
  protected $sideTabIndex;

  public function __construct()
  {
    $callingFile = preg_split('/\/|\\\/', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'));
    $partFile = array_pop($callingFile);
    $partFolder = array_pop($callingFile);
    $this->partFile = $partFile;
    $this->partUrl = $partFolder . '/' . $partFile;

    $this->sideTabIndex = -1;
  }

  /**
   * @param string $command
   * @param string $buttonText
   * @param string $buttonClass
   * @param string $confirmMessage
   * @param bool $noReload
   * @return string
   */
  public function botCommandButton($command, $buttonText, $buttonClass = 'default', $confirmMessage = '', $noReload = false)
  {
    return '<button type="button" class="btn btn-' . $buttonClass . '" onclick="doQuickCommand(\'' . $command . '\', \'' . $confirmMessage
    . ($noReload ? ', true' : '') . '\');">' . $buttonText . '</button>';
  }

  /**
   * @param string $command
   * @param string $description
   * @param string $inputPlaceHolder
   * @param null|string $inputValue
   * @param string $buttonText
   * @param bool $disabled
   * @param bool $small
   * @param bool $noReload
   * @param null|string $autoComplete
   * @return string
   */
  public function botCommandForm($command, $description, $inputPlaceHolder = '[username]', $inputValue = null, $buttonText = null, $disabled = false, $small = false, $noReload = false, $autoComplete = null)
  {
    $autoCompeteId = $this->randomId();

    return '<form class="bot-command-form' . ($autoComplete ? ' auto-complete' : '') . '" botcommand="'
    . $command . '" formnoreload="' . ($noReload ? '1' : '0') . '"' . ($autoComplete ? ' autocompleteon="' . $autoComplete . '" autocompleteid="' . $autoCompeteId . '"' : '') . '>
          <div class="form-group' . ($small ? ' form-group-sm' : '') . '">
            <span>' . $description . '</span>
            <div class="input-group">
              <input type="text"' . ($autoComplete ? ' id="ac-field-' . $autoCompeteId . '" autocomplete="off"' : '') . ' class="form-control" placeholder="' . $inputPlaceHolder . '" value="' . $inputValue . '"' . ($disabled ? ' disabled' : '') . '/>
              <span class="input-group-btn">
                <button type="submit" class="btn btn-primary' . ($small ? ' btn-sm' : '') . '"' . ($disabled ? ' disabled' : '') . '>' . ($buttonText ? $buttonText : '<span class="fa fa-paper-plane-o"></span>') . '</button>
              </span>
              ' . ($autoComplete ? '<ul class="ac-list" id="ac-list-' . $autoCompeteId . '"></ul>' : '') . '
            </div>
          </div>
        </form>';
  }

  /**
   * @param string $command
   * @param string $description
   * @param array $options
   * @return string
   */
  public function botCommandFormV2($command, $description, $options = [])
  {
    $options = array_merge(
        [
            'placeholder' => '[username]',
            'value' => null,
            'buttonText' => '<span class="fa fa-paper-plane-o"></span>',
            'disabled' => false,
            'small' => false,
            'noReload' => false,
            'autoComplete' => null,
        ],
        $options
    );
    $autoCompeteId = $this->randomId();

    return '<form class="bot-command-form' . ($options['autoComplete'] ? ' auto-complete' : '') . '" botcommand="'
    . $command . '" formnoreload="' . ($options['noReload'] ? '1' : '0') . '"' . ($options['autoComplete'] ? ' autocompleteon="' . $options['autoComplete'] . '" autocompleteid="' . $autoCompeteId . '"' : '') . '>
          <div class="form-group' . ($options['small'] ? ' form-group-sm' : '') . '">
            <span>' . $description . '</span>
            <div class="input-group">
              <input type="text"' . ($options['autoComplete'] ? ' id="ac-field-' . $autoCompeteId . '" autocomplete="off"' : '') . ' class="form-control" placeholder="' . $options['placeholder'] . '" value="' . $options['value'] . '"' . ($options['disabled'] ? ' disabled' : '') . '/>
              <span class="input-group-btn">
                <button type="submit" class="btn btn-primary' . ($options['small'] ? ' btn-sm' : '') . '"' . ($options['disabled'] ? ' disabled' : '') . '>' . $options['buttonText'] . '</button>
              </span>
              ' . ($options['autoComplete'] ? '<ul class="ac-list" id="ac-list-' . $autoCompeteId . '"></ul>' : '') . '
            </div>
          </div>
        </form>';
  }

  /**
   * @param $command
   * @param $options
   * @param $description
   * @param null $textInputPlaceHolder
   * @param null $textInputValue
   * @param null $buttonText
   * @param bool|false $small
   * @param bool|false $noReload
   * @return string
   */
  public function combinedBotCommandForm($command, $options, $description, $textInputPlaceHolder = null, $textInputValue = null, $buttonText = null, $small = false, $noReload = false)
  {
    $optionsString = '';
    foreach ($options as $value => $option) {
      $optionsString .= '<option value="' . $value . '">' . $option . '</option>';
    }
    return '<form class="combined-bot-command-form" botcommand="' . $command . '" formnoreload="' . ($noReload ? '1' : '0') . '">
          <div class="form-group' . ($small ? ' form-group-sm' : '') . '">
            <span>' . $description . '</span>
            <div class="input-group">
              <div class="input-group-addon input-group-addon-select">
                <select class="form-control" name="action">' . $optionsString . '</select>
              </div>
              <input type="text" class="form-control" name="actionarg" placeholder="' . $textInputPlaceHolder . '" value="' . $textInputValue . '" />
            <span class="input-group-btn">
              <button class="btn btn-primary">' . ($buttonText ? $buttonText : '<span class="fa fa-paper-plane-o"></span>') . '</button>
            </span>
            </div>
          </div>
        </form>';
  }

  /**
   * @param string $title
   * @param array $headers
   * @param string $dataRows
   * @param bool $disableHeightLimit
   * @param string $class
   * @param array $filters
   * @param bool $collapsible
   * @return string
   */
  public function dataTable($title, $headers, $dataRows, $disableHeightLimit = true, $class = '', $filters = [], $collapsible = false)
  {
    $headerRows = '';
    $filterButtons = '';
    foreach ($headers as $header) {
      $headerRows .= '<th>' . $header . '</th>';
    }
    if (count($filters) > 0) {
      $filterButtons .= '<div class="btn-group btn-group-sm">';
      foreach ($filters as $filter) {
        $filterButtons .= '<div class="btn ' . ($filter['active'] ? 'btn-success' : 'btn-default') . '" onclick="'
            . 'openPart(\'' . $this->partUrl . '\', {' . $filter['name'] . ': \'' . $filter['value'] . '\'})'
            . '">'
            . $filter['display']
            . '</div>';
      }
      $filterButtons .= '</div>';
    }

    return
        ($collapsible ? '<h4 class="collapsible-master">' . $title . '</h4>' : '<h4>' . $title . '</h4>')
        . $filterButtons
        . ($collapsible ? '<div class="collapsible-content">' : '')
        . '<div class="data-table' . ($disableHeightLimit ? ' full-height' : '') . ($class != '' ? ' ' . $class : '') . '">
            <table class="table table-striped">
              <thead>
              <tr>' . $headerRows . '</tr>
              </thead>
              <tbody>' . ($dataRows ? $dataRows : '<tr><td colspan="' . count($headers) . '">No Data</td></tr>') . '</tbody>
            </table>
          </div>'
        . ($collapsible ? '</div>' : '');
  }

  /**
   * @param string $template
   * @param string $text
   * @param array $options
   * @return string
   */
  public function addTooltip($template, $text, $options = [])
  {
    //$position = 'top', $offsetX = 0, $offsetY = 0, $replaceInRow = 0, $appendToBody = false
    $s = array_merge([
        'position' => ComponentTemplates::TOOLTIP_POS_TOP,
        'offsetX' => 0,
        'offsetY' => 0,
        'replaceInRow' => 0,
        'appendToBody' => false,
    ], $options);

    if (trim($template) != '') {
      $elementArray = [];
      preg_match_all('(<.*>)', $template, $elementArray);
      $elementArray[0][$s['replaceInRow']] =
          preg_replace('/class="([a-z0-9\s_-]+)"/i', 'class="$1 has-tooltip"', $elementArray[0][$s['replaceInRow']], 1);
      $elementArray[0][$s['replaceInRow']] =
          preg_replace('/(<[a-z0-9]+)/i', '$1 tooltip="' . htmlspecialchars($text) . '" tooltip-position="'
              . $s['position'] . '" tooltip-offset="' . $s['offsetX'] . ',' . $s['offsetY'] . '"' . ($s['appendToBody'] ? ' tooltip-to-body="1"' : ''),
              $elementArray[0][$s['replaceInRow']], 1);

      return join('', $elementArray[0]);
    } else {
      return '';
    }
  }

  /**
   * @param $content
   * @param bool $static
   * @return string
   */
  public function informationPanel($content, $static = false)
  {
    if ($static) {
      return '<div class="panel panel-info information-panel-static"><div class="panel-heading"><div class="panel-title"><span class="fa fa-info-circle"></span></div></div><div class="panel-body">' . $content . '</div></div>';
    } else {
      return '<div class="panel panel-info information-panel"><div class="panel-heading"><div class="panel-title"><span class="fa fa-info-circle"></span></div></div><div class="panel-body">' . $content . '</div></div>';
    }
  }

  /**
   * @param mixed $data
   * @param string $faIcon
   * @param string $iconColorType
   * @param $tooltipText
   * @param string $id
   * @param bool $active
   * @return string
   */
  public function streamInfoBanner($data, $faIcon, $iconColorType, $tooltipText, $id = '', $active = true)
  {
    if ($active) {
      $banner = '<div class="pull-right info-banner-space-left" onclick="loadChannelData(true)"><span class="fa fa-' . $faIcon . ' text-' . $iconColorType . '"></span> <span'
          . ($id != '' ? ' id="' . $id . '"' : '') . ' class="text-muted">' . $data . '</span></div>';
      return $this->addTooltip($banner, $tooltipText, ['offsetX' => 12.75]);
    } else {
      return '';
    }
  }

  /**
   * @param string $label
   * @param string $onChangeJsFnc
   * @param string $onChangeParamsJsArray
   * @param string $id
   * @param bool $initialState
   * @param bool $small
   * @param bool $noBackground
   * @param bool $noFalse
   * @param bool $disabled
   * @param string $name
   * @return string
   */
  public function switchToggle($label, $onChangeJsFnc, $onChangeParamsJsArray = '[]', $id = 'switch-toggle', $initialState = false, $small = false, $noBackground = false, $noFalse = false, $disabled = false, $name = 'switch-toggle')
  {
    if ($id == 'switch-toggle' || $id == '' || $id == null) {
      $id = 'switch-toggle-' . $this->randomId();
    }
    if ($name == 'switch-toggle' || $name == '' || $name == null) {
      $name = $id;
    }
    return '<div class="switch-toggle' . ($small ? ' st-sm' : '') . ($noBackground ? ' st-no-bg' : '') . ($noFalse ? ' no-false' : '') . '">
          <div class="switch-label-text">' . $label . '</div>
          <div class="switch">
            <input type="checkbox" name="' . $name . '" class="switch-checkbox" id="' . $id . '" onchange="switchToggleCallback('
    . ($onChangeJsFnc != null || $onChangeJsFnc != '' ? $onChangeJsFnc : 'null') . ', '
    . ($onChangeParamsJsArray != null || $onChangeParamsJsArray != '' ? $onChangeParamsJsArray : '[]') . ')" ' . ($initialState ? 'checked' : '')
    . ' ' . ($disabled ? 'disabled' : '') . '>
            <label class="switch-label" for="' . $id . '"></label>
          </div>
        </div>';
  }

  /**
   * @param string $text
   * @param bool $small
   * @param bool $noBackground
   * @return string
   */
  public function switchToggleText($text, $small = false, $noBackground = false)
  {
    return '<div class="switch-toggle' . ($small ? ' st-sm' : '') . ($noBackground ? ' st-no-bg' : '') . '">
          <div class="switch-label-text">' . $text . '</div>
        </div>';
  }

  public function toggleFavoriteButton()
  {
    $button = '<button class="btn btn-default btn-sm" onclick="updateFavoritesMenu(\'' . ucwords(str_replace(['-', '.php', 'custom'], [' ', '', ''], $this->partFile))
        . '\', \'' . $this->partUrl . '\')"><span class="fa fa-star-o"></span></button>';

    return $this->addTooltip($button, 'Toggle favorite', ['position' => ComponentTemplates::TOOLTIP_POS_RIGHT]);
  }

  /**
   * @param string $command
   * @param string $expression
   * @param string $trueOption
   * @param string $falseOption
   * @return string
   */
  public function _wrapInJsToggledDoQuickCommand($command, $expression, $trueOption, $falseOption)
  {
    return '(function(){(' . $expression . '?doQuickCommand(\'' . $command . ' ' . $trueOption . '\'):doQuickCommand(\'' . $command . ' ' . $falseOption . '\'))})';
  }

  /**
   * @param string $name
   * @param string $frameUrl
   * @param string $faIcon
   * @param bool|false $activateBtn
   * @return string
   */
  public function sideTab($name, $frameUrl, $faIcon, $activateBtn = false)
  {
    ++$this->sideTabIndex;
    return '<div class="side-tabs-wrapper">
              <iframe></iframe>
              ' . ($activateBtn ? '<button class="btn btn-success btn-sm"><span class="fa fa-check"></span></button>' : '') . '
              ' . ($activateBtn ? '<div class="notice">Click the button in the upper right to activate the ' . $name . '!</div>' : '') . '
              <div class="side-tab" tab-src="' . $frameUrl . '" style="top:' . (43 * $this->sideTabIndex) . 'px" title="' . $name . '">
                <span class="fa ' . $faIcon . '"></span>
              </div>
            </div>';
  }

  /**
   * @return string
   */
  public function randomId()
  {
    $randomIdSeed = str_shuffle('abcdefghijklmnipqrstuvwzyxabcdefghijklmnipqrstuvwzyxabcdefghijklmnipqrstuvwzyx');
    return substr($randomIdSeed, rand(0, 72), 5);
  }

  /**
   * @param boolean $status
   * @return string
   */
  public function moduleActiveIndicator($status)
  {
    if ($status) {
      return '<span class="text-success pull-right"><span class="fa fa-check-circle"></span> Module Active</span>';
    } else {
      return $this->addTooltip('<span class="text-danger pull-right"><span class="fa fa-exclamation-circle"></span> Module Inactive</span>',
          'Enable this module in Extras->Modules Manager to use it.', ['position' => ComponentTemplates::TOOLTIP_POS_LEFT, 'offsetX' => -5]);
    }
  }
} 