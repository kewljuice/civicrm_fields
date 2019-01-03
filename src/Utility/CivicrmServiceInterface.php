<?php

namespace Drupal\civicrm_fields\Utility;

/**
 * Central interface for implementing CiviCRMService.
 */
interface CivicrmServiceInterface {

  /**
   * Gets results from the CiviCRM api.
   *
   * @param string $entity
   *   The CiviCRM API entity.
   * @param string $action
   *   The CiviCRM API action.
   * @param array $params
   *   The CiviCRM API params.
   *
   * @return array
   *   An array with result(s).
   *
   * @throws \Exception
   */
  public function api($entity, $action, array $params);

}
