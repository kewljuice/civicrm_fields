<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'civicrm_field_event' field type.
 *
 * @FieldType(
 *   id = "civicrm_field_event",
 *   label = @Translation("CiviCRM field_event"),
 *   category = @Translation("CiviCRM"),
 *   default_widget = "civicrm_field_event_widget",
 *   default_formatter = "civicrm_field_event_default_formatter"
 * )
 */
class EventField extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    // Store value(s) for the field.
    $schema['columns'] = [
      'event_id' => [
        'type' => 'varchar',
        'description' => 'CiviCRM Event ID.',
        'length' => 256,
        'not_null' => FALSE,
      ],
    ];
    // Return schema.
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Details about field properties.
    $properties['event_id'] = DataDefinition::create('any')
      ->setLabel(t('CiviCRM Event ID'));
    // Return properties.
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    // How to determine if field is empty.
    $item = $this->getValue();
    // Check if field is set.
    if (!isset($item['event_id']) || empty($item['event_id'])) {
      return TRUE;
    }
    // Subtract contact_id from default value.
    if (preg_match('!\(([^\)]+)\)!', $item['event_id'], $match)) {
      $item['event_id'] = $match[1];
      $this->setValue($item);
    }
    // Check if event_id exists in CiviCRM.
    try {
      /** @var \Drupal\civicrm_fields\Utility\CiviCRMServiceInterface $civicrm */
      $civicrm = \Drupal::service('civicrm.service');
      $results = $civicrm->api('Event', 'GetSingle', ['id' => $item['event_id']]);
    }
    catch (\Exception $e) {
      \Drupal::logger('EventField')->error($e->getMessage());
    }
    if (empty($results)) {
      return TRUE;
    }
    // defaults.
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    /* Alter values. */
  }

}
