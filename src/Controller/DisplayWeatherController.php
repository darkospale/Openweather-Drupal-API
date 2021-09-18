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
      $count = $_SESSION['count'];
      $form_class = '\Drupal\openweathermap\Form\RemoveWeatherForm';

      foreach($_SESSION['form_values'] as $key => $data) {
        $call = [
          $key => $data,
        ];
        $_SESSION['build'] += [
          $count => [
            '#prefix' => '<div class="weather">',
            '#suffix' => '</div>',
            \Drupal::service('get_weather')->makeRequest($call),
            '#attached' => [
              'library' => [
                'openweathermap/openweathermap.display_weather',
              ],
            ],
            'form' => [
              $count => \Drupal::formBuilder()->getForm($form_class),
            ],
          ],
        ];
        $_SESSION['times'] = $count;

        $_SESSION['build'][$count]['form'][$count]['#attributes']['remove_id'] = $_SESSION['times'];
      }
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
