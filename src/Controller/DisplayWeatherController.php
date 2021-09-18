<?php

namespace Drupal\openweathermap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The controller for the weather forecast routes.
 */
class DisplayWeatherController extends ControllerBase {

  /**
   * Build controller.
   *
   * @return mixed
   *   Build controller.
   */
  public function display() {
    if (isset($_SESSION['form_values'])) {
      $form_class = '\Drupal\openweathermap\Form\RemoveWeatherForm';

      $times = $_SESSION['count'];
      $data = $_SESSION['form_values'];
      $_SESSION['build'] += [
        $times => [
          '#prefix' => '<div class="weather">',
          '#suffix' => '</div>',
          \Drupal::service('get_weather')->makeRequest($data),
          '#attached' => [
            'library' => [
              'openweathermap/openweathermap.display_weather',
            ],
          ],
          'form' => [
            $times => \Drupal::formBuilder()->getForm($form_class),
          ],
        ],
      ];
      $_SESSION['times'] = $times;

      $_SESSION['build'][$times]['form'][$times]['#attributes']['remove_id'] = $_SESSION['times'];

      return $_SESSION['build'];
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
