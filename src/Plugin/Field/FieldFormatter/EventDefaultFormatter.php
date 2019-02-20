<?php

namespace Drupal\civicrm_fields\Plugin\Field\FieldFormatter;

use Drupal\civicrm_fields\Utility\CivicrmService;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class EventDefaultFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

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
      if ($item->get('event_id')->getValue() != NULL) {
        $eid = $item->get('event_id')->getValue();
        $results = NULL;
        try {
          $results = $this->service->api('Event', 'GetSingle', ['event_id' => $eid]);
        }
        catch (\Exception $e) {
          $this->logger->get('EventDefaultFormatter')
            ->error($e->getMessage());
        }
        if (!is_null($results) && !empty($results)) {
          if ($results['is_online_registration']) {
            $link = Link::fromTextAndUrl($results['title'], Url::fromUri('base:civicrm/event/register?id=' . $results['id']));
          }
          else {
            $link = Link::fromTextAndUrl($results['title'], Url::fromUri('base:civicrm/event/info?id=' . $results['id']));
          }
          $elements[] = $link->toRenderable();
        }
      }
    }
    return $elements;
  }

}
