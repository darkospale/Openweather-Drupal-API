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
      if (!empty($_SESSION['form_values'])) {
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
                  $key => 'openweathermap/openweathermap.display_weather',
                ],
              ],
            ],
          ];
          if ($data->getValue('update_periodically') == 1) {
            // Minutes to seconds to miliseconds.
            $interval = $data->getValue('update_interval') * 60 * 1000;
            $_SESSION['build'][$key]['#attached']['drupalSettings']['interval'] = $interval;
          }
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
