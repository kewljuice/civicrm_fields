<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'civicrm_field_contribution_page' field widget.
 *
 * @FieldWidget(
 *   id = "civicrm_field_contribution_page_widget",
 *   label = @Translation("CiviCRM contribution page widget"),
 *   field_types = {
 *     "civicrm_field_contribution_page",
 *   }
 * )
 */
class ContributionPageWidget extends WidgetBase implements WidgetInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item =& $items[$delta];
    // Store element(s) for the field.
    $element += [
      '#type' => 'details',
      '#title' => $this->t('CiviCRM Contribution'),
      '#open' => FALSE,
      '#group' => 'advanced',
    ];
    // Autocomplete field for contribution_page_id.
    $element['contribution_page_id'] = [
      '#title' => $this->t('CiviCRM Contribution ID'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'civicrm_fields.endpoint',
      '#autocomplete_route_parameters' => [
        'entity' => 'contributionPage',
        'count' => 10,
      ],
      '#default_value' => isset($item->contribution_page_id) ? $item->contribution_page_id : NULL,
    ];
    // Return element(s).
    return $element;
  }

}
