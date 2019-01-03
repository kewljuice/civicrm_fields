<?php

namespace Drupal\civicrm_fields\Controller;

use Drupal\civicrm_fields\Utility\CivicrmService;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CivicrmFields.
 */
class CivicrmFields extends ControllerBase {

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
  public static function create(ContainerInterface $container) {
    $service = $container->get('civicrm.service');
    $logger = $container->get('logger.factory');
    return new static($service, $logger);
  }

  /**
   * Constructs a \Drupal\civicrm_fields\Controller\CivicrmFields object.
   *
   * @param \Drupal\civicrm_fields\Utility\CivicrmService $service
   *   The CiviCRM API service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger factory service.
   */
  public function __construct(CivicrmService $service, LoggerChannelFactoryInterface $logger) {
    $this->service = $service;
    $this->logger = $logger;
  }

  /**
   * Return JSON response.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An JsonResponse with result(s).
   *
   * @throws \Exception
   */
  public function response(Request $request, $entity, $count) {
    $results = [];
    // Get the typed string from the URL, if exists.
    if ($input = $request->query->get('q')) {
      $typed_string = Tags::explode($input);
      $typed_string = strtolower(array_pop($typed_string));
      // Apply logic for other CiviCRM entity.
      switch ($entity) {
        case 'contact':
          try {
            $results = $this->lookupContact($typed_string, $count);
          }
          catch (\Exception $e) {
            $this->logger->get('CivicrmFields')
              ->error($e->getMessage());
          }
          break;

        case 'event':
          try {
            $results = $this->lookupEvent($typed_string, $count);
          }
          catch (\Exception $e) {
            $this->logger->get('CivicrmFields')
              ->error($e->getMessage());
          }
          break;
      }
    }
    // Return response.
    return new JsonResponse($results);
  }

  /**
   * Gets results from the CiviCRM api.
   *
   * @param string $search
   *   The search keyword.
   * @param string $count
   *   The amount of results.
   *
   * @return array
   *   An array with result(s).
   *
   * @throws \Exception
   */
  protected function lookupContact($search, $count) {
    $results = [];
    // Find CiviCRM contacts.
    if (is_numeric($search)) {
      // Search contact by contact_id.
      $params = [
        'contact_id' => $search,
        'return' => ['id', 'display_name'],
        'options' => ['limit' => $count],
      ];
    }
    else {
      // Search contact by display_name.
      $params = [
        'display_name' => $search,
        'return' => ['id', 'display_name'],
        'options' => ['limit' => $count],
      ];
    }
    try {
      $founded = $this->service->api('Contact', 'Get', $params);
    }
    catch (\Exception $e) {
      $this->logger->get('CivicrmFields')
        ->error($e->getMessage());
    }
    if (!is_null($founded) && !empty($founded)) {
      foreach ($founded['values'] as $found) {
        $results[] = [
          'value' => $found['contact_id'],
          'label' => $found['display_name'] . ' (' . $found['contact_id'] . ')',
        ];
      }
    }
    // Return found contacts.
    return $results;
  }

  /**
   * Gets results from the CiviCRM api.
   *
   * @param string $search
   *   The search keyword.
   * @param string $count
   *   The amount of results.
   *
   * @return array
   *   An array with result(s).
   *
   * @throws \Exception
   */
  protected function lookupEvent($search, $count) {
    $results = [];
    // Find CiviCRM events.
    if (is_numeric($search)) {
      // Search contact by contact_id.
      $params = [
        'id' => $search,
        'return' => ['id', 'title'],
        'options' => ['limit' => $count],
      ];
    }
    else {
      // Search contact by display_name.
      $params = [
        'title' => $search,
        'return' => ['id', 'title'],
        'options' => ['limit' => $count],
      ];
    }
    try {
      $founded = $this->service->api('Event', 'Get', $params);
    }
    catch (\Exception $e) {
      $this->logger->get('CivicrmFields')
        ->error($e->getMessage());
    }
    if (!is_null($founded) && !empty($founded)) {
      foreach ($founded['values'] as $found) {
        $results[] = [
          'value' => $found['id'],
          'label' => $found['title'] . ' (' . $found['id'] . ')',
        ];
      }
    }
    // Return found events.
    return $results;
  }

}
