<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'civicrm_field_contact' formatter.
 *
 * @FieldFormatter(
 *   id = "civicrm_field_contact_vcard_formatter",
 *   label = @Translation("CiviCRM contact VCard formatter"),
 *   field_types = {
 *     "civicrm_field_contact"
 *   }
 * )
 */
class ContactVCardFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $settings = $this->getSettings();
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Display value(s) for the field.
    $elements = [];
    foreach ($items as $delta => $item) {
      if ($item->get('contact_id')->getValue() != NULL) {
        $cid = $item->get('contact_id')->getValue();
        $results = NULL;
        try {
          /** @var \Drupal\civicrm_fields\Utility\CiviCRMServiceInterface $civicrm */
          $civicrm = \Drupal::service('civicrm.service');
          $results = $civicrm->API('Contact', 'GetSingle', ['contact_id' => $cid]);
        } catch (\Exception $e) {
          \Drupal::logger('ContactVCardFormatter')->error($e->getMessage());
        }
        if (!is_null($results) && !empty($results)) {
          $elements[] = [
            '#theme' => 'contact_vcard',
            '#item' => $results,
            '#cache' => [
              'max-age' => 0,
            ],
            '#attached' => [
              'library' => 'civicrm_fields/contact-vcard',
            ],
          ];
        }
      }
    }
    return $elements;
  }
}
