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
            $results = $this->lookupEntity($typed_string, $count, 'Contact', 'display_name');
          }
          catch (\Exception $e) {
            $this->logger->get('CivicrmFields')
              ->error($e->getMessage());
          }
          break;

        case 'event':
          try {
            $results = $this->lookupEntity($typed_string, $count, 'Event', 'title', ['is_template' => 0]);
          }
          catch (\Exception $e) {
            $this->logger->get('CivicrmFields')
              ->error($e->getMessage());
          }
          break;

        case 'contributionPage':
          try {
            $results = $this->lookupEntity($typed_string, $count, 'ContributionPage', 'title');
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
   * Gets entity from the CiviCRM api.
   *
   * @param string $search
   *   The search keyword.
   * @param string $count
   *   The amount of results.
   * @param string $entity
   *   The API entity.
   * @param string $label
   *   The label.
   * @param array $extra
   *   The extra params.
   *
   * @return array
   *   An array with result(s).
   *
   * @throws \Exception
   */
  protected function lookupEntity($search, $count, $entity, $label, array $extra = []) {
    $results = [];
    // Find CiviCRM entity.
    if (is_numeric($search)) {
      // Search entity by id.
      $params = [
        'id' => $search,
        'return' => ['id', $label],
        'options' => ['limit' => $count],
      ];
    }
    else {
      // Search entity by title.
      $params = [
        'title' => ['LIKE' => "%$search%"],
        'return' => ['id', $label],
        'options' => ['limit' => $count],
      ];
    }
    // Extra filters.
    if (!empty($extra)) {
      $params = array_merge($params, $extra);
    }
    try {
      $founded = $this->service->api($entity, 'Get', $params);
    }
    catch (\Exception $e) {
      $this->logger->get('CivicrmFields')
        ->error($e->getMessage());
    }
    if (!is_null($founded) && !empty($founded)) {
      foreach ($founded['values'] as $found) {
        $results[] = [
          'value' => $found[$label] . ' (' . $found['id'] . ')',
        ];
      }
    }
    // Return found entity.
    return $results;
  }

}
