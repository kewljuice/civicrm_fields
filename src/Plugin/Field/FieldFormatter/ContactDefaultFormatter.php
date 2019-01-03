<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldFormatter;

use Drupal\civicrm_fields\Utility\CivicrmService;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'civicrm_field_contact' formatter.
 *
 * @FieldFormatter(
 *   id = "civicrm_field_contact_default_formatter",
 *   label = @Translation("CiviCRM contact default formatter"),
 *   field_types = {
 *     "civicrm_field_contact"
 *   }
 * )
 */
class ContactDefaultFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The CiviCRM API service.
   *
   * @var \Drupal\civicrm_fields\Utility\CivicrmService
   */
  protected $service;

  /**
   * The logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('civicrm.service'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, CivicrmService $service, LoggerChannelFactoryInterface $logger) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->service = $service;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Display value(s) for the field.
    $elements = [];
    foreach ($items as $item) {
      if ($item->get('contact_id')->getValue() != NULL) {
        $cid = $item->get('contact_id')->getValue();
        $results = NULL;
        try {
          $results = $this->service->api('Contact', 'GetSingle', ['contact_id' => $cid]);
        }
        catch (\Exception $e) {
          $this->logger->get('ContactDefaultFormatter')
            ->error($e->getMessage());
        }
        if (!is_null($results) && !empty($results)) {
          $elements[] = [
            '#type' => 'markup',
            '#markup' => $results['display_name'],
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
