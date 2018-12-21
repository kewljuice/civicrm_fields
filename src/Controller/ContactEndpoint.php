<?php

namespace Drupal\civicrm_fields\Controller;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactEndpoint extends ControllerBase {

  /**
   * Return JSON response.
   *
   * @return JsonResponse
   *
   * @throws \Exception
   */
  public function fetchResponse(Request $request, $entity, $count) {
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
          } catch (\Exception $e) {
            \Drupal::logger('ContactFormatter')->error($e->getMessage());
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
   *
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
      /** @var \Drupal\civicrm_fields\Utility\CiviCRMServiceInterface $civicrm */
      $civicrm = \Drupal::service('civicrm.service');
      $founded = $civicrm->API('Contact', 'Get', $params);
    } catch (\Exception $e) {
      \Drupal::logger('ContactFormatter')->error($e->getMessage());
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
}
