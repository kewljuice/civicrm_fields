<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'civicrm_field_event' formatter.
 *
 * @FieldFormatter(
 *   id = "civicrm_field_event_default_formatter",
 *   label = @Translation("CiviCRM event default formatter"),
 *   field_types = {
 *     "civicrm_field_event"
 *   }
 * )
 */
class EventDefaultFormatter extends FormatterBase {

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
      if ($item->get('event_id')->getValue() != NULL) {
        $eid = $item->get('event_id')->getValue();
        $results = NULL;
        try {
          /** @var \Drupal\civicrm_fields\Utility\CiviCRMServiceInterface $civicrm */
          $civicrm = \Drupal::service('civicrm.service');
          $results = $civicrm->API('Event', 'GetSingle', ['event_id' => $eid]);
        } catch (\Exception $e) {
          \Drupal::logger('EventDefaultFormatter')->error($e->getMessage());
        }
        if (!is_null($results) && !empty($results)) {
          $elements[] = [
            '#type' => 'markup',
            '#markup' => $results['title'],
            '#cache' => [
              'max-age' => 0,
            ],
          ];

        }
      }
    }
    return $elements;
  }
}
