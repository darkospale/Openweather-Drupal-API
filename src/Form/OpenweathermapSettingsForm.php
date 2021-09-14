<?php

namespace Drupal\openweathermap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The settings form for the 'Openweathermap' module.
 */
class OpenweathermapSettingsForm extends ConfigFormBase {

  /**
   * Config factory class onfiguration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Create function.
   *
   * @param ContainerInterface $container
   *   Container Interface.
   *
   * @return ConfigFormBase|OpenweathermapSettingsForm
   *   Settings.
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->config = $instance->config('openweathermap.settings');
    return $instance;
  }

  /**
   * Config names.
   *
   * @return string[]
   *   Config.
   */
  protected function getEditableConfigNames() {
    return [
      'openweathermap.settings',
    ];
  }

  /**
   * Config form.
   *
   * @return string
   *   Form ID.
   */
  public function getFormId() {
    return 'openweathermap_settings_form';
  }

  /**
   * Build config form.
   *
   * @param array $form
   *   Form.
   * @param FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Return form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['api_container'] = [
      '#type' => 'fieldset',
    ];

    $form['api_container']['api_weather_endpoint'] = [
      '#type' => 'url',
      '#title' => $this->t('API Weather Endpoint'),
      '#size' => 60,
      '#default_value' => $this->config->get('api_weather_endpoint'),
      '#required' => TRUE,
    ];

    $form['api_container']['api_air_pollution_endpoint'] = [
      '#type' => 'url',
      '#title' => $this->t('API Air Pollution Endpoint'),
      '#size' => 60,
      '#default_value' => $this->config->get('api_air_pollution_endpoint'),
      '#required' => TRUE,
    ];

    $form['api_container']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $this->config->get('api_key'),
      '#size' => 60,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit form.
   *
   * @param array $form
   *   Form.
   * @param FormStateInterface $form_state
   *   Form state.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config->set('api_weather_endpoint', $form_state->getValue('api_weather_endpoint'));
    $this->config->set('api_air_pollution_endpoint', $form_state->getValue('api_air_pollution_endpoint'));
    $this->config->set('api_key', $form_state->getValue('api_key'));

    $this->config->save();
  }

}
