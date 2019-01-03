<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'civicrm_field_contact' field type.
 *
 * @FieldType(
 *   id = "civicrm_field_contact",
 *   label = @Translation("CiviCRM field_contact"),
 *   category = @Translation("CiviCRM"),
 *   default_widget = "civicrm_field_contact_widget",
 *   default_formatter = "civicrm_field_contact_default_formatter"
 * )
 */
class ContactField extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    // Store value(s) for the field.
    $schema['columns'] = [
      'contact_id' => [
        'type' => 'varchar',
        'description' => 'CiviCRM Contact ID.',
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
    $properties['contact_id'] = DataDefinition::create('any')
      ->setLabel(t('CiviCRM Contact ID'));
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
    if (!isset($item['contact_id'])) {
      return TRUE;
    }
    // Check if contact_id exists in CiviCRM.
    try {
      /** @var \Drupal\civicrm_fields\Utility\CiviCRMServiceInterface $civicrm */
      $civicrm = \Drupal::service('civicrm.service');
      $results = $civicrm->api('Contact', 'GetSingle', ['contact_id' => $item['contact_id']]);
    }
    catch (\Exception $e) {
      \Drupal::logger('ContactField')->error($e->getMessage());
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
