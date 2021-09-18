<?php

namespace Drupal\openweathermap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RemoveWeatherForm extends FormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'remove_weather_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove this city'),
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $times = $_SESSION['times'];
    $remove_id = $_SESSION['build'][$times]['form'][$times]['#attributes']['remove_id'];
    if($times == $remove_id) {
      unset($_SESSION['build'][$times]);
    }
  }

}
