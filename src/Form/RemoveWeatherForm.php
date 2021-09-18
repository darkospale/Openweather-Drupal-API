<?php

namespace Drupal\openweathermap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
    $key = $_SESSION['key'];
    $remove_id = $_SESSION['build'][$key]['form'][$key]['#attributes']['remove_id'];
    unset($_SESSION['build'][$key]);
    unset($_SESSION['form_values'][$remove_id]);
  }

}
