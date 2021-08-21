<?php

namespace Drupal\openweathermap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The settings form for the 'Openweathermap' module.
 */
class DisplayWeatherForm extends FormBase {

  /**
   * The configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * All countries from the 'city.list.json' file.
   *
   * @var array
   */
  protected $countries;

  /**
   * City manager service.
   *
   * @var \Drupal\openweathermap\CityManagerInterface
   */
  protected $cityManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->cityManager = $container->get('city_manager');
    $instance->config = $instance->config('openweathermap.settings');
    $instance->countries = $instance->cityManager->getCountries();
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openweathermap_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attached']['library'] = 'openweathermap/openweathermap.select2_for_settings_form';

    // Update city value if country is selected
    if (isset($form_state->getUserInput()['country'])) {

      $country_code = $form_state->getUserInput()['country'];
      $city_id = NULL;
    }
    else {
      $country_code = $this->config->get('country_code');
      $city_id = $this->config->get('city_id');
    }

    // Update latitude and longitude value if city is selected
    if (isset($form_state->getUserInput()['city'])) {

      $city_id = $form_state->getUserInput()['city'];
      $lat = NULL;
      $lon = NULL;
    }
    else {
      $city_id = $this->config->get('city_id');
      $lat = $this->config->get('lat');
      $lon = $this->config->get('lon');
    }

    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => $this->countries,
      '#size' => 7,
      '#default_value' => $country_code,
      '#required' => TRUE,
      '#ajax' => [
        'event' => 'change',
        'callback' => '::changeCountryAjaxCallback',
        'wrapper' => 'city-select-wrapper',
      ],
    ];

    $cities = $this->cityManager->getCitiesByCountryCode($country_code);

    $form['city'] = [
      '#type' => 'select',
      '#title' => $this->t('City'),
      '#options' => $cities,
      '#size' => 7,
      '#required' => TRUE,
      '#default_value' => $city_id,
      '#prefix' => '<div id="city-select-wrapper">',
      '#suffix' => '</div>',
      '#ajax' => [
        'event' => 'change',
        'callback' => '::changeCityLatLonAjaxCallback',
        'wrapper' => 'edit-lat-lon-wrapper',
      ],
      // To prevent the error message: "An illegal choice has been detected...".
      '#validated' => TRUE,
    ];

    // $lat = $this->cityManager->getLatByCity($city_id);
    // $lon = $this->cityManager->getLonByCity($city_id);

    $form['lat_lon_container'] = [
      '#type' => 'fieldset',
      '#prefix' => '<div id="edit-lat-lon-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['lat_lon_container']['lat'] = [
      '#type' => 'textfield',
      '#default_value' => $lat,
      '#validated' => TRUE,
    ];

    $form['lat_lon_container']['lon'] = [
      '#type' => 'textfield',
      '#default_value' => $lon,
      '#validated' => TRUE,
    ];

    dsm($form['lat_lon_container']);

    // OVO RADI SIGURNO ALI POSLE DRUGOG SAVEA

    $form['units_container'] = [
      '#type' => 'fieldset',
    ];

    $form['units_container']['units_of_measurement'] = [
      '#type' => 'select',
      '#title' => $this->t('Units of measurement'),
      '#options' => [
        'standard' => $this->t('standard'),
        'metric' => $this->t('metric'),
        'imperial' => $this->t('imperial'),
      ],
      '#default_value' => $this->config->get('units_of_measurement'),
      '#required' => TRUE,
    ];

    $form['update_container'] = [
      '#type' => 'fieldset',
    ];

    $form['update_container']['update_periodically'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update periodically'),
      '#description' => $this->t('If it is set, then a weather forecast data will be updated at the specified time interval.'),
      '#default_value' => $this->config->get('update_periodically'),
    ];

    $form['update_container']['update_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Update interval in minutes'),
      '#min' => 1,
      '#default_value' => $this->config->get('update_interval'),
      '#states' => [
        'enabled' => [
          ':input[name="update_periodically"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function changeCountryAjaxCallback($form, $form_state) {
    return $form['city'];
  }

  /**
   * {@inheritdoc}
   */
  public function changeCityLatLonAjaxCallback($form, $form_state){
    $lat = $this->cityManager->getLatByCity($form['city']['#value']);
    $lon = $this->cityManager->getLonByCity($form['city']['#value']);
    $form['lat_lon_container']['lat']['#value'] = $lat;
    $form['lat_lon_container']['lon']['#value'] = $lon;

    return $form['lat_lon_container'];
  }

  /**
   * Implement the validator because we were forced to set '#validated' => TRUE.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (empty($form_state->getValue('city'))) {
      $form_state->setErrorByName('city', $this->t('The "City" field is required.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config->set('country_code', $form_state->getValue('country'));
    $this->config->set('city_id', $form_state->getValue('city'));
    $this->config->set('lat', $form_state->getValue('lat'));
    $this->config->set('lon', $form_state->getValue('lon'));
    $this->config->set('update_periodically', $form_state->getValue('update_periodically'));
    $this->config->set('update_interval', $form_state->getValue('update_interval'));

    $this->config->save();
  }

}
