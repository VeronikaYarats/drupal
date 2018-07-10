<?php

namespace Drupal\custom_entity\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'default_entity_duration_f' formatter.
 *
 * @FieldFormatter(
 *   id = "default_entity_duration_f",
 *   label = @Translation("Default Entity Duration - Formatter"),
 *   description = @Translation("Default Entity Duration - Formatter"),
 *   field_types = {
 *     "default_entity_duration",
 *   }
 * )
 */
class DefaultEntityDurationFormatter extends  FormatterBase
{
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
        $elements = [];

       foreach ($items as $delta => $item) {
            $startDate = $item->start_date ? date('Y-m-d', $item->start_date) : '';
            $endDate = $item->end_date ? date('Y-m-d', $item->end_date) : '';
            $elements[$delta] = [
                '#markup' => 'from ' . $startDate . ' to ' . $endDate
              ];
          }

    return $elements;
  }
}
