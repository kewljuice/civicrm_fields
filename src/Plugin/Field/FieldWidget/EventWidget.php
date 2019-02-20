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
class EventWidget extends WidgetBase implements ContainerFactoryPluginInterface {

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
    $element += [
      '#type' => 'details',
      '#title' => $this->t('CiviCRM Event'),
      '#open' => FALSE,
      '#group' => 'advanced',
    ];
    // Fetch default value.
    $default = NULL;
    if (isset($item->event_id)) {
      $result = $this->service->api('Event', 'GetSingle', ['id' => $item->event_id]);
      $default = $result['title'] . ' (' . $result['id'] . ')';
    }
    // Autocomplete field for event_id.
    $element['event_id'] = [
      '#title' => $this->t('CiviCRM Event ID'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'civicrm_fields.endpoint',
      '#autocomplete_route_parameters' => [
        'entity' => 'event',
        'count' => 10,
      ],
      '#default_value' => $default,
    ];
    // Return element(s).
    return $element;
  }

}
