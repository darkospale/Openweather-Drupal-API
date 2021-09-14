<?php

namespace Drupal\openweathermap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The controller for the weather forecast routes.
 */
class DisplayWeatherController extends ControllerBase {

  /**e
   * {@inheritdoc}
   */
  public function display() {
//    if (isset($data)) {
      $data = $_SESSION['form_values'];
      $build = \Drupal::service('get_weather')->makeRequest($data);

      $build[] = [
        '#theme' => 'openweathermap',
        '#attached' => [
          'library' => [
            'openweathermap/openweathermap.display_weather',
          ],
        ],
        'actions' => [
          '#type' => 'actions',
          'submit' => [
            '#type' => 'submit',
            '#value' => 'Add another city',
          ],
        ],
      ];
      // @todo Still left to redirect to form and then add multiple cities
      return $build;
//    }
//    return $this->redirectPage();
  }

  public function redirectPage() {
    $path = '/weather-form';
    $response = new RedirectResponse($path);
    $response->send();
  }

}
