<?php

namespace Drupal\civicrm_fields\Utility;

use Drupal\civicrm\Civicrm;

/**
 * Class CiviCRMService.
 */
class CiviCRMService implements CiviCRMServiceInterface {

  /**
   * The CiviCRM service.
   *
   * @var \Drupal\civicrm\Civicrm
   */
  protected $civicrm;

  /**
   * Constructs a new CiviCrmApi object.
   *
   * @param \Drupal\civicrm\Civicrm $civicrm
   *   The CiviCRM service.
   *
   * @throws \Exception
   */
  public function __construct(Civicrm $civicrm) {
    try {
      $this->civicrm = $civicrm;
      $this->civicrm->initialize();
    } catch (\Exception $e) {
      \Drupal::logger('civicrm_services')->error($e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function API($entity, $action, $params) {
    $results = [];
    try {
      $results = civicrm_api3($entity, $action, $params);
    } catch (\Exception $e) {
      \Drupal::logger('civicrm_services')->error($e->getMessage());
    }
    return $results;
  }
}
