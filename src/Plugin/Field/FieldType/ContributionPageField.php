<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'civicrm_field_contribution_page' field type.
 *
 * @FieldType(
 *   id = "civicrm_field_contribution_page",
 *   label = @Translation("CiviCRM field_contribution_page"),
 *   category = @Translation("CiviCRM"),
 *   default_widget = "civicrm_field_contribution_page_widget",
 *   default_formatter = "civicrm_field_contribution_page_formatter"
 * )
 */
class ContributionPageField extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    // Store value(s) for the field.
    $schema['columns'] = [
      'contribution_page_id' => [
        'type' => 'varchar',
        'description' => 'CiviCRM Contribution ID.',
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
    $properties['contribution_page_id'] = DataDefinition::create('any')
      ->setLabel(t('CiviCRM Contribution ID'));
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
    if (!isset($item['contribution_page_id']) || empty($item['contribution_page_id'])) {
      return TRUE;
    }
    // Subtract contact_id from default value.
    if (preg_match('!\(([^\)]+)\)!', $item['contribution_page_id'], $match)) {
      $item['contribution_page_id'] = $match[1];
      $this->setValue($item);
    }
    // Check if contribution_page_id exists in CiviCRM.
    try {
      /** @var \Drupal\civicrm_fields\Utility\CiviCRMServiceInterface $civicrm */
      $civicrm = \Drupal::service('civicrm.service');
      $results = $civicrm->api('ContributionPage', 'GetSingle', ['id' => $item['contribution_page_id']]);
    }
    catch (\Exception $e) {
      \Drupal::logger('ContributionField')->error($e->getMessage());
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
