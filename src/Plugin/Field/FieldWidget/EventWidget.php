<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'civicrm_field_event' field widget.
 *
 * @FieldWidget(
 *   id = "civicrm_field_event_widget",
 *   label = @Translation("CiviCRM event widget"),
 *   field_types = {
 *     "civicrm_field_event",
 *   }
 * )
 */
class EventWidget extends WidgetBase implements WidgetInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item =& $items[$delta];
    // Store element(s) for the field.
    $element += [
      '#type' => 'details',
      '#title' => t('CiviCRM Event'),
      '#open' => FALSE,
      '#group' => 'advanced',
    ];
    // Autocomplete field for event_id.
    $element['event_id'] = [
      '#title' => t('CiviCRM Event ID'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'civicrm_fields.endpoint',
      '#autocomplete_route_parameters' => [
        'entity' => 'event',
        'count' => 10,
      ],
      '#default_value' => isset($item->event_id) ? $item->event_id : NULL,
    ];
    // Return element(s).
    return $element;
  }
}