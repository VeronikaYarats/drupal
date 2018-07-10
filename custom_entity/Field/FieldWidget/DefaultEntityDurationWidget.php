<?php

namespace Drupal\custom_entity\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Plugin implementation of the  'default_entity_duration_w' widget.
 *
 * @FieldWidget(
 *   id = "default_entity_duration_w",
 *   label = @Translation("Default Entity Duration - Widget"),
 *   description = @Translation("Default Entity Duration - Widget"),
 *   field_types = {
 *     "default_entity_duration",
 *   }
 * )
 */
class DefaultEntityDurationWidget extends WidgetBase
{
  const INVALID_DURATION_ERROR_MESSAGE = 'End date must go after start date';

  const INVALID_DATE_FORMAT_ERROR_MESSAGE = 'Incorrect date format';

  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta, array $element,
    array &$form,
    FormStateInterface $form_state
  ) {
    $type = $this->getSetting('date_format');

    $element['start_date'] = [
      '#type' => $type,
      '#title' => $this->t('Start date'),
      '#description' => $this->t('Select start date.'),
      '#default_value' =>
        $items[$delta]->start_date ?
          date('Y-m-d', $items[$delta]->start_date) : '',
      '#prefix' => '<span class="error-message"></span>',
    ];

    $element['end_date'] = [
      '#type' => $type,
      '#title' => t('End date'),
      '#description' => t('Select end date'),
      '#default_value' =>
        $items[$delta]->end_date ?
          date('Y-m-d', $items[$delta]->end_date) : '',
      '#prefix' => '<span class="error-message"></span>',
      '#element_validate' => [
        [$this, 'validateDuration'],
      ]
    ];


    if ($type != 'textfield')
      return $element;

    /** If type is textfield add ajax validation for elements */
    $element['start_date']['#ajax'] = [
      'callback' => [$this, 'validateAjax'],
      'event' => 'change',
    ];
    array_unshift($element['start_date']['#element_validate'],
      [$this, 'validateDateFormat']);

    $element['end_date']['#ajax'] = [
      'callback' => [$this, 'validateAjax'],
      'event' => 'change',
    ];
    array_unshift($element['end_date']['#element_validate'],
      [$this, 'validateDateFormat']);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return ['date_format' => 'date'] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['date_format'] = [
      '#title' => $this->t('Format of the date'),
      '#type' => 'select',
      '#options' => [
        'date' => $this->t('Calendar'),
        'textfield' => $this->t('Text')
      ],
      '#default_value' => $this->getSetting('date_format'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values,
    array $form,
    FormStateInterface $form_state
  ) {
    $values = parent::massageFormValues($values, $form, $form_state);
    foreach ($values as &$item) {
      if (isset($item['start_date'])) {
        $item['start_date'] = strtotime($item['start_date']);
      }

      if (isset($item['end_date'])) {
        $item['end_date'] = strtotime($item['end_date']);
      };
    }
    return $values;
  }

  /**
   * Validate date format by Ajax.
   *
   * @param array $element
   * @return AjaxResponse
   */
  public function validateAjax(array &$form, FormStateInterface $form_state)
  {
    $response = new AjaxResponse();
    $element = $form_state->getTriggeringElement();
    if(!strtotime($element['#value'])) {
      $response->addCommand(new HtmlCommand(
        '.error-message',
        $this->t(self::INVALID_DATE_FORMAT_ERROR_MESSAGE)));
    }

    return $response;
  }

  /**
   * Validate date format and duration
   *
   * @param $element
   * @param FormStateInterface $formState
   * @param $form
   */
  public function validateDuration(array $element, FormStateInterface $formState, $form)
  {
    $dates = $this->getElementsByNames(['start_date', 'end_date'], $form);
    if (strtotime($dates['start_date']['#value']) > strtotime($dates['end_date']['#value']))
      $formState->setError($dates['end_date'],
        $this->t(self::INVALID_DURATION_ERROR_MESSAGE));
    }

  /**
   * Validate date format
   *
   * @param array $form
   * @param FormStateInterface $formState
   */
  public function validateDateFormat(array $form, FormStateInterface $formState)
  {
    $dates = $this->getElementsByNames(['start_date', 'end_date'], $form);
    foreach ($dates as $date)
      $this->validateDate($date, $formState);
  }

  /**
   * Retrieve array of elements by name.
   *
   * @param array $names
   * @param array $form
   * @return array ['name' => $element]
   */
  private function getElementsByNames($names, $form)
  {
    $elements = [];
    foreach ($names as $name) {
      $elements[$name] = $form[$this->fieldDefinition->getName()]['widget'][0][$name];
    }
    return $elements;
  }

  /**
   * @param $element
   * @param FormStateInterface $formState
   */
  private function validateDate($element, FormStateInterface $formState)
  {
    if (!empty($element['#value']) && !strtotime($element['#value']))
      $formState->setError($element, self::INVALID_DATE_FORMAT_ERROR_MESSAGE);
  }
}
