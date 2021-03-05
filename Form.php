<?php

/**
 * JSON Form Generator for Bootstrap.
 * HTML Form Generator from JSON format
 *
 * Based on the idea of Arash Soleimani <arash@leomoon.com> json_form_generator
 * date 2021-02-24
 *
 * @author Gert Massheimer <deltaos@web.de>
 * @package json_form_generator_for_bootstrap
 * @version 1.0
 *
 */
class Form
{
  private $json; // JSON Content
  private array $elProps; // Element Properties
  private string $inputElement; // Form Element

  /**
   * Class Constructor.
   * Formcode is either a JSON file or a JSON formatted string
   *
   * @param string|null $formCode - json content as extern file or intern variable (code)
   * @throws ErrorException|JsonException
   */
  public function __construct(?string $formCode = null)
  {
    if (!$formCode) {
      $this->error("ERROR: No JSON file or JSON code given to interpret!");
      exit;
    }

    // Prepare custom ErrorHandler
    set_error_handler(
      static function ($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
      }
    );

    // Check if $formCode ends with '.json' then assume it's a file
    if (strtolower(substr($formCode, -strlen('.json'))) === '.json') {
      try {
        $formCode = file_get_contents($formCode);
      } catch (Exception $e) {
        $this->error("ERROR: Couldn't get content of JSON file!");
        $this->error("Error Description: <span style='color:#198754'>" . $e->getMessage() . "</span>");
        exit;
      }
    }
    try {
      $formCode = json_decode($formCode, true, 512, JSON_THROW_ON_ERROR);
    } catch (Exception $e) {
      $this->error("ERROR: Can't decode JSON file or string!");
      $this->error("Error Description: <span style='color:#198754'>" . $e->getMessage() . "</span>");
      exit; // Exit here -> finally is NOT executed
    } finally {
      $this->json = $formCode;
    }
    restore_error_handler();
  }

  /**
   * Render HTML tags from JSON and print it.
   * @throws JsonException
   */
  public function show(): void
  {
    $properties = $this->json['properties'] ?? null;

    if (!$properties) {
      $this->error("Error: Could not find <span style='color:#198754'>&#123;\"properties\"&#125;</span>!");
      exit;
    }

    $html = '<form';
    $html .= $this->checkArray($this->json);
    $html .= ">\n";
    foreach ($properties as $prop => $propValue) {
      $this->inputElement = json_encode($prop, JSON_THROW_ON_ERROR);
      $this->elProps = $propValue;
      $html .= $this->parse();
    }

    $html .= "</form>\n";
    echo $html;
  }

  /**
   * Parse field array.
   * Specifies the HTML tag to generate based on the type
   *
   * @return string
   * @throws JsonException
   */
  private function parse(): string
  {
    $el = $this->elProps; $inputEl = "<span style='color:#198754'>&#123;$this->inputElement&#125;</span>";
    $type = $el['type'] ?? null;

    // Check if type is set
    if (!$type) {
      $this->error("No type specified for JSON element $inputEl!", true);
      return false;
    }

    switch ($type) {
      case 'button':
        case 'submit':
          case 'reset': return $this->button();
      case 'textarea': return $this->textarea();
      case 'select': return $this->select();
      case 'div': return $this->div();
      default: return $this->input();
    }
  }

  /**
   * Generate Input tag.
   *
   * @return string
   * @throws JsonException
   */
  public function input(): string
  {
    $el = $this->elProps; $inputEl = "<span style='color:#198754'>&#123;$this->inputElement&#125;</span>";
    $type = $el['type'] ?? null;

    // One of them has to be set at least: label and/or placeholder
    if (!isset($el['label']) && !isset($el['placeholder']) && $type !== 'hidden') {
      $this->error("No label or placeholder specified for JSON element $inputEl!", true);
      return false;
    }
    // Check if value is set for hidden input
    if (!isset($el['value']) && $type === 'hidden') {
      $this->error("Input type hidden but no value is set for JSON element $inputEl!", true);
      return false;
    }
    // Create input with label
    $tag = $this->labelWrap('before') . "\n";
    $tag .= "<input";
    $tag .= $this->checkArray($el);
    $tag .= ">\n";
    $tag .= $this->labelWrap('after');
    return $tag;
  }

  /**
   * Add label or wrap input with label.
   *
   * @param string $position - label closes before or after the input
   * @return string
   */
  private function labelWrap(string $position): string
  {

    $wrap = ''; $el = $this->elProps;

    $type          = $el['type'];
    $id            = $el['id'] ?? null;
    $label         = $el['label'] ?? null;
    $ariaLabel     = isset($el['aria-label']) && $el['aria-label'] === true ?? null;
    $labelClass    = $el['labelClass'] ?? null;
    $divClass      = $el['divClass'] ?? null;
    $help          = $el['help'] ?? null;
    $helpID        = $el['aria-describedby'] ?? null;
    $helpClass     = $el['helpClass'] ?? null;
    $feedback      = $el['feedback'] ?? null;
    $feedbackID    = $el['feedbackID'] ?? null;
    $feedbackClass = $el['feedbackClass'] ?? null;

    $box = ($type === 'checkbox' || $type === 'radio') ?? null;

    if (!$box) { // Handle everything but checkbox and radio
      if ($position === 'before') {
        $wrap .= $divClass ? "\n<div class='$divClass'>\n" : '';
        // No need for label if aria-label is set
        if ($label && !$ariaLabel) {
          $wrap .= $ariaLabel;
          $wrap .= '<label';
          $wrap .= $id ? " for='$id'" : '';
          $wrap .= $labelClass ? " class='$labelClass'" : '';
          $wrap .= ">$label";
          $wrap .= $id ? "</label>\n" : '';
        }
      } else { // position = after
        if ($feedback) { // Add feedback from form validation
          $wrap .= $this->textAddition($id, $ariaLabel, $feedback, $feedbackID, $feedbackClass);
        }
        if ($help) { // Add help text
          $wrap .= $this->textAddition($id, $ariaLabel, $help, $helpID, $helpClass);
        }
        if ($label && !$ariaLabel) {
          $wrap .= !$id ? "</label>\n" : '';
        }
        $wrap .= $divClass ? "</div>\n" : '';
      }
      // Checkbox and radio has label text always after the input
    } else if ($position === 'before') {
      $wrap .= $divClass ? "\n<div class='$divClass'>\n" : '';
      if (!$id) {
        $wrap .= "<label";
        $wrap .= $labelClass ? " class='$labelClass'" : '';
        $wrap .= ">";
      }
    } else { // position = after
      if ($id) {
        $wrap .= "<label for='$id'";
        $wrap .= $labelClass ? " class='$labelClass'" : '';
        $wrap .= ">";
      }
      $wrap .= "$label</label>\n";
      $wrap .= $divClass ? "</div>\n" : '';
    }
    return $wrap;
  }

  /**
   * Generate Select tag.
   *
   * @return string
   * @throws JsonException
   */
  private function select(): string
  {
    $el = $this->elProps; $inputEl = "<span style='color:#198754'>&#123;$this->inputElement&#125;</span>";
    // Check if aria-label is true but no label given
    if (!isset($el['label'])) {
      $this->error("No label specified for JSON element $inputEl!", true);
      return false;
    }
    // Remove attribute "type"
    $selectData = $this->elProps;
    unset($selectData['type']);
    // Create select
    $tag = $this->labelWrap('before');
    $tag .= '<select';
    $tag .= $this->checkArray($selectData);
    $tag .= ">\n";
    if (isset($el['options'])) {
      foreach ($el['options'] as $value => $label) {
        $selected = $label[1] ? "selected" : '';
        $tag .= "<option value='$value' $selected>$label[0]</option>\n";
      }
    }
    $tag .= "</select>\n";
    $tag .= $this->labelWrap('after');
    return $tag;
  }

  /**
   * Generate Textarea tag.
   *
   * @return string
   * @throws JsonException
   */
  private function textarea(): string
  {
    $el = $this->elProps; $inputEl = "<span style='color:#198754'>&#123;$this->inputElement&#125;</span>";
    // Check if aria-label is true but no label given
    if (!isset($el['label'])) {
      $this->error("No label specified for JSON element $inputEl!", true);
      return false;
    }
    // Remove attributes "type" and "pattern"
    $textareaData = $el;
    unset($textareaData['type'], $textareaData['pattern']);
    // If no rows are specified set a min height of 5 rows
    $textareaData['rows'] ??= $textareaData['rows'] = 5;
    // Create the textarea
    $tag = $this->labelWrap('before');
    $tag .= '<textarea';
    $tag .= $this->checkArray($textareaData);
    $tag .= ">";
    $tag .= ($el['value'] ?? '');
    $tag .= "</textarea>\n";
    $tag .= $this->labelWrap('after');
    return $tag;
  }

  /**
   * Generate Button tag.
   *
   * @return string
   * @throws JsonException
   */
  private function button(): string
  {
    $el = $this->elProps; $inputEl = "<span style='color:#198754'>&#123;$this->inputElement&#125;</span>";
    // Check if aria-label is true but no label given
    if (!isset($el['label'])) {
      $this->error("No label specified for JSON element $inputEl!", true);
      return false;
    }
    // Create the button
    $tag = '<button';
    $tag .= $this->checkArray($this->elProps);
    $tag .= ">" . $el['label'];
    $tag .= "</button>\n";
    return $tag;
  }

  /**
   * Generate Div tag.
   *
   * @return string
   */
  private function div(): string
  {
    $tag = ''; $divData = $this->elProps;
    // Remove "type" attribute
    unset($divData['type']);
    // Create the div
    if ($this->elProps['open']) {
      $tag .= '<div';
      $tag .= $this->checkArray($divData);
      $tag .= ">\n";
    } else {
      $tag .= "</div>\n";
    }
    return $tag;
  }

  /**
   * Check array data and add parameter to input.
   *
   * @param array $data - element properties
   * @return string
   */
  private function checkArray(array $data): string
  {
    $tag = '';
    $label = $data['label'] ?? null;
    $tag .= isset($data['accept']) ? " accept='" . $data["accept"] . "'" : '';
    $tag .= isset($data['accesskey']) ? " accesskey='" . $data["accesskey"] . "'" : '';
    $tag .= isset($data['action']) ? " action='" . $data["action"] . "'" : '';
    $tag .= isset($data['aria-describedby']) ? " aria-describedby='" . $data["aria-describedby"] . "'" : '';
    $tag .= isset($data['aria-label']) && $data['aria-label'] === true ? " aria-label='$label'" : '';
    $tag .= isset($data['autocomplete']) ? " autocomplete='" . $data["autocomplete"] . "'" : '';
    $tag .= isset($data['autofocus']) ? " autofocus" : '';
    $tag .= isset($data['checked']) ? " checked" : '';
    $tag .= isset($data['class']) ? " class='" . $data["class"] . "'" : '';
    $tag .= isset($data['dir']) ? " dir='" . $data["dir"] . "'" : '';
    $tag .= isset($data['disabled']) ? " disabled" : '';
    $tag .= isset($data['enctype']) ? " enctype='" . $data["enctype"] . "'" : '';
    $tag .= isset($data['id']) ? " id='" . $data["id"] . "'" : '';
    $tag .= isset($data['max']) ? " max='" . $data["max"] . "'" : '';
    $tag .= isset($data['method']) ? " method='" . $data["method"] . "'" : '';
    $tag .= isset($data['min']) ? " min='" . $data["min"] . "'" : '';
    $tag .= isset($data['minlength']) ? " minlength='" . $data["minlength"] . "'" : '';
    $tag .= isset($data['multiple']) ? " multiple" : '';
    $tag .= isset($data['name']) ? " name='" . $data["name"] . "'" : '';
    $tag .= isset($data['novalidate']) ? " novalidate" : '';
    $tag .= isset($data['onblur']) ? " onblur='" . $data["onblur"] . "'" : '';
    $tag .= isset($data['onchange']) ? " onchange='" . $data["onchange"] . "'" : '';
    $tag .= isset($data['onclick']) ? " onclick='" . $data["onclick"] . "'" : '';
    $tag .= isset($data['onfocus']) ? " onfocus='" . $data["onfocus"] . "'" : '';
    $tag .= isset($data['oninput']) ? " oninput='" . $data["oninput"] . "'" : '';
    $tag .= isset($data['oninvalid']) ? " oninvalid='" . $data["oninvalid"] . "'" : '';
    $tag .= isset($data['onreset']) ? " onreset='" . $data["onreset"] . "'" : '';
    $tag .= isset($data['onsearch']) ? " onsearch='" . $data["onsearch"] . "'" : '';
    $tag .= isset($data['onselect']) ? " onselect='" . $data["onselect"] . "'" : '';
    $tag .= isset($data['onsubmit']) ? " onsubmit='" . $data["onsubmit"] . "'" : '';
    $tag .= isset($data['pattern']) ? " pattern='" . $data["pattern"] . "'" : '';
    $tag .= isset($data['placeholder']) ? " placeholder='" . $data["placeholder"] . "'" : '';
    $tag .= isset($data['readonly']) ? " readonly" : '';
    $tag .= isset($data['required']) ? " required" : '';
    $tag .= isset($data['rows']) ? " rows='" . $data["rows"] . "'" : '';
    $tag .= isset($data['size']) ? " size='" . $data["size"] . "'" : '';
    $tag .= isset($data['step']) ? " step='" . $data["step"] . "'" : '';
    $tag .= isset($data['style']) ? " style='" . $data["style"] . "'" : '';
    $tag .= isset($data['tabindex']) ? " tabindex='" . $data["tabindex"] . "'" : '';
    $tag .= isset($data['target']) ? " target='" . $data["target"] . "'" : '';
    $tag .= isset($data['title']) ? " title='" . $data["title"] . "'" : '';
    $tag .= isset($data['type']) ? " type='" . $data["type"] . "'" : '';
    $tag .= isset($data['value']) ? " value='" . $data["value"] . "'" : '';
    return $tag;
  }

  /**
   * Additional text for input.
   * Add feedback or help text
   *
   * @param string|null $id - 'id=...' of input
   * @param string|null $ariaLabel - 'aria-lable=...' of input
   * @param string $text - text to add
   * @param string|null $textID - 'id=...' of div/span
   * @param string|null $textClass - 'class=...' of div/span
   * @return string
   */
  private function textAddition(?string $id, ?string $ariaLabel, string $text, ?string $textID, ?string $textClass): string
  {
    $wrap = $id || $ariaLabel ? "<div" : "<span";
    $wrap .= $textID ? " id='$textID'" : '';
    $wrap .= $textClass ? " class='$textClass'" : '';
    $wrap .= ">$text";
    $wrap .= $id || $ariaLabel ? "</div>\n" : "</span>\n";
    return $wrap;
  }

  /**
   * Show errors.
   *
   * @param string $errorMsg
   * @param bool $details
   * @throws JsonException
   */
  private function error(string $errorMsg, $details = false): void
  {
    $red = "style='color:#dc3545'";
    $blue = "style='color:#0d6efd'";
    $green = "style='color:#198754'";
    echo "<br><span $red>$errorMsg</span>\n";
    if ($details) {
      echo "<br><span $green>$this->inputElement</span>: <span $blue>"
        . json_encode($this->elProps, JSON_THROW_ON_ERROR)
        . "</span><br>\n";
    }
  }
}
