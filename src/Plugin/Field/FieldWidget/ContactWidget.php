<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'civicrm_field_contact' field widget.
 *
 * @FieldWidget(
 *   id = "civicrm_field_contact_widget",
 *   label = @Translation("CiviCRM contact widget"),
 *   field_types = {
 *     "civicrm_field_contact",
 *   }
 * )
 */
class ContactWidget extends WidgetBase implements WidgetInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $item =& $items[$delta];

    // Store element(s) for the field.
    $element += [
      '#type' => 'details',
      '#title' => t('CiviCRM Contact'),
      '#open' => FALSE,
      '#group' => 'advanced',
    ];

    // &todo Fetch value(s).
    $options = ['1' => 'contact 1', '2' => 'contact 2', '100' => 'contact 100'];

    // &todo Render value(s).
    $element['contact_id'] = [
      '#title' => t('CiviCRM Contact ID'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => isset($item->contact_id) ? $item->contact_id : NULL,
    ];

    // Return element(s).
    return $element;
  }
}