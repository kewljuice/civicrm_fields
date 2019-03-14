<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldWidget;

use Drupal\civicrm_fields\Utility\CivicrmService;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'civicrm_field_contact' field widget.
 *
 * @FieldWidget(
 *   id = "civicrm_field_contact_inline_widget",
 *   label = @Translation("CiviCRM contact inline widget"),
 *   field_types = {
 *     "civicrm_field_contact",
 *   }
 * )
 */
class ContactInlineWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The CiviCRM API service.
   *
   * @var \Drupal\civicrm_fields\Utility\CivicrmService
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('civicrm.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, CivicrmService $service) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->service = $service;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item =& $items[$delta];
    // Store element(s) for the field.
    $element = [];
    // Fetch default value.
    $default = NULL;
    if (isset($item->contact_id)) {
      $result = $this->service->api('Contact', 'GetSingle', ['contact_id' => $item->contact_id]);
      $default = $result['display_name'] . ' (' . $result['contact_id'] . ')';
    }
    // Autocomplete field for contact_id.
    $element['contact_id'] = [
      '#title' => $this->t('CiviCRM Contact ID'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'civicrm_fields.endpoint',
      '#autocomplete_route_parameters' => [
        'entity' => 'contact',
        'count' => 10,
      ],
      '#default_value' => $default,
    ];
    // Return element(s).
    return $element;
  }

}
