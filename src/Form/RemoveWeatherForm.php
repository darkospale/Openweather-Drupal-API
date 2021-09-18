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

    $form['#attributes']['remove_id'] = $_SESSION['count'];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getCompleteForm()['#attributes']['remove_id'];
    unset($_SESSION['build'][$id]);

    $_SESSION['build'] = array_values($_SESSION['build']);
    unset($_SESSION['form_values']);
  }

}
