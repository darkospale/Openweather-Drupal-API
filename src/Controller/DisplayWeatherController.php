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
  public function display() {
    if (isset($_SESSION['form_values'])) {

      $_SESSION['count']++;
      $_SESSION['build']++;

      if ($_SESSION['count'] == 1) {
          $times = $_SESSION['count'];
          $data[$times] = $_SESSION['form_values'];
          $_SESSION['build'] = [
            $times => [
              '#prefix' => '<div class="weather">',
              '#suffix' => '</div>',
              \Drupal::service('get_weather')->makeRequest($data[$times]),
              '#attached' => [
                'library' => [
                  'openweathermap/openweathermap.display_weather',
                ],
              ],
            ],
          ];
        }
        if ($_SESSION['count'] == 2) {
          $times = $_SESSION['count'];
          $data[$times] = $_SESSION['form_values'];
          $_SESSION['build'] += [
            $times => [
              '#prefix' => '<div class="weather">',
              '#suffix' => '</div>',
              \Drupal::service('get_weather')->makeRequest($data[$times]),
              '#attached' => [
                'library' => [
                  'openweathermap/openweathermap.display_weather',
                ],
              ],
            ],
          ];
        }
//        $form_class = '\Drupal\openweathermap\Form\DisplayWeatherForm';

        if ($_SESSION['count'] == 3) {
          $times = $_SESSION['count'];
          $data[$times] = $_SESSION['form_values'];
          $_SESSION['build'] += [
            $times => [
              '#prefix' => '<div class="weather">',
              '#suffix' => '</div>',
              \Drupal::service('get_weather')->makeRequest($data[$times]),
              '#attached' => [
                'library' => [
                  'openweathermap/openweathermap.display_weather',
                ],
              ],
//              'form' => \Drupal::formBuilder()->getForm($form_class),
            ],
          ];
        }

//          'actions' => [
//            '#type' => 'submit',
//            '#value' => 'Add another city',
//            '#submit' => $this->redirectPage(),
//          ],

        return $_SESSION['build'];
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
