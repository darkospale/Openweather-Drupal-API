<?php

namespace Drupal\openweathermap\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * The controller for the weather forecast routes.
 */
class DisplayWeatherController extends ControllerBase {

  /**
   * Display controller.
   *
   * @return array|mixed
   *   Return the controller display.
   */
  public function display() {
    if (isset($_SESSION['form_values'])) {
      if(!empty($_SESSION['form_values'])) {
        $form_class = '\Drupal\openweathermap\Form\RemoveWeatherForm';

        foreach ($_SESSION['form_values'] as $key => $data) {
          $call = [
            $key => $data,
          ];
          $_SESSION['build'] += [
            $key => [
              '#prefix' => '<div class="weather">',
              '#suffix' => '</div>',
              \Drupal::service('get_weather')->makeRequest($call),
              '#attached' => [
                'library' => [
                  'openweathermap/openweathermap.display_weather',
                ],
              ],
              'form' => [
                $key => \Drupal::formBuilder()->getForm($form_class),
              ],
            ],
//            $_SESSION['key'] = $key,
          ];
        }

        return $_SESSION['build'];
      }
    }
    $build = [
      '#theme' => 'openweathermap_controller_theme',
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
      '#markup' => $this->t('You haven\'t made any weather requests.'),
    ];

    return $build;
  }

}
