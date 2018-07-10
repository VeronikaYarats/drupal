<?php

namespace Drupal\custom_entity\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldItemInterface;

/**
 * @FieldType(
 *   id = "default_entity_duration",
 *   label = @Translation("Default Entity Duration"),
 *   description = @Translation("This field stores a duration"),
 *   default_formatter = "default_entity_duration_f",
 *   default_widget = "default_entity_duration_w",
 * )
 */

class DefaultEntityDurationField extends FieldItemBase
{
  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    $properties['start_date'] = DataDefinition::create('integer')
      ->setLabel(t('Start date'))
      ->setDescription(t('Start date'))
      ->setSetting('unsigned', TRUE);

    $properties['end_date'] = DataDefinition::create('integer')
      ->setLabel(t('End date'))
      ->setDescription(t('End date'))
      ->setSetting('unsigned', TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $fieldDefinition)
  {
    $columns = [
      'start_date' => [
        'description' => 'Start date',
        'type' => 'int',
      ],
      'end_date' => [
        'description' => 'End date',
        'type' => 'int',
      ]
    ];
    $schema['columns'] = $columns;

    return $schema;
  }
}
