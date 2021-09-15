<?php

namespace Drupal\openweathermap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The controller for the weather forecast routes.
 */
class DisplayWeatherController extends ControllerBase
{

  /**
   * Build controller.
   *
   * @return mixed
   *   Build controller.
   */
  public function display()
  {
    if(isset($_SESSION['form_values'])) {
      $data = $_SESSION['form_values'];
      if (isset($data)) {

        // I will probably have to save it to $_SESSION
        // Build is overwritten, need to assert the $times value every time just don't know how currently.
        $times = $_SESSION['count'];
        $build = [];
        $build += [
          $times => [
            \Drupal::service('get_weather')->makeRequest($data),
          ],
        ];

        $build += [
          '#attached' => [
            'library' => [
              'openweathermap/openweathermap.display_weather',
            ],
          ],
//          'actions' => [
//            '#type' => 'submit',
//            '#value' => 'Add another city',
//            '#submit' => $this->redirectPage(),
//          ],
        ];
        // @todo Still left to redirect to form and then add multiple cities
        return $build;
      }
    }
    return $this->redirectPage();
  }

  /**
   * Redirect page function.
   */
  public function redirectPage() {
    $path = '/weather-form';
    $response = new RedirectResponse($path);
    $response->send();
  }


}
