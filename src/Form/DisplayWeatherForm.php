<?php

namespace Drupal\openweathermap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The settings form for the 'Openweathermap' module.
 */
class DisplayWeatherForm extends FormBase {

  /**
   * City manager service.
   *
   * @var \Drupal\openweathermap\CityManagerInterface
   */
  protected $cityManager;

  /**
   * All countries from the 'city.list.json' file.
   *
   * @var array
   */
  protected $countries;

  /**
   * @var Drupal\openweathermap\Service\WeatherService
   */
  protected $weatherService;

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return \Drupal\openweathermap\Form\DisplayWeatherForm
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->cityManager = $container->get('city_manager');
    $instance->countries = $instance->cityManager->getCountries();
    $instance->weatherService = $container->get('get_weather');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openweathermap.weather_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Attaching the select2 library in order to be able to select countries and cities from the city.list.json
    $form['#attached']['library'] = 'openweathermap/openweathermap.select2_for_settings_form';

    // @todo I think this has got some work to do because it does not remember the selection
    // Update city value if country is selected
    if (isset($form_state->getUserInput()['country'])) {

      $country_code = $form_state->getUserInput()['country'];
      $city_id = $form_state->getUserInput()['city'];
    }
    else {
      $country_code = $form_state->getValue('country');
      $city_id = $form_state->getValue('city');
    }

    if (isset($form_state->getUserInput()['city'])) {

      $city_id = $form_state->getUserInput()['city'];
      $lat = $form_state->getUserInput()['lat'];
      $lon = $form_state->getUserInput()['lon'];
    }
    else {
      $city_id = $form_state->getValue('city');
      $lat = $form_state->getValue('lat');
      $lon = $form_state->getValue('lon');
    }

    if (isset($form_state->getUserInput()['units_measurement'])) {
      $units_measurement = $form_state->getUserInput()['units_measurement'];
    }
    else {
      $units_measurement = $form_state->getValue('units_measurement');
    }

    $cities = $this->cityManager->getCitiesByCountryCode($country_code);

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
      '#validated' => TRUE,
    ];

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

    $form['lat_lon_container'] = [
      '#type' => 'fieldset',
      '#prefix' => '<div id="edit-lat-lon-wrapper">',
      '#suffix' => '</div>',
      '#attributes' => [
        'style' => 'display: none;',
      ],
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
      '#default_value' => $units_measurement,
      '#required' => TRUE,
    ];

    $form['update_container'] = [
      '#type' => 'fieldset',
    ];

    $form['update_container']['update_periodically'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update periodically'),
      '#description' => $this->t('If it is set, then a weather forecast data will be updated at the specified time interval.'),
      '#default_value' => $form_state->getValue('update_periodically'),
    ];

    $form['update_container']['update_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Update interval in minutes'),
      '#min' => 1,
      '#default_value' => $form_state->getValue('update_interval'),
      '#states' => [
        'enabled' => [
          ':input[name="update_periodically"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search for the location'),
    ];

    return $form;
  }

  /**
   * Function that redirects user to the /weather page.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state of an array.
   */
  public function redirectPage() {
    $path = '/weather';
    $response = new RedirectResponse($path);
    $response->send();
  }

  /**
   * Function that gives user the ajax callback of cities when country is selected.
   *
   * @param $form
   *   Form array.
   * @param $form_state
   *   Form state array.
   *
   * @return mixed
   */
  public function changeCountryAjaxCallback($form, $form_state) {
    return $form['city'];
  }

  /**
   * Function that gives user the ajax callback of latitude and longited when city is selected.
   *
   * @param $form
   *   Form array.
   * @param $form_state
   *   Form state array.
   *
   * @return mixed
   */
  public function changeCityLatLonAjaxCallback($form, $form_state) {
    $lat = $this->cityManager->getLatByCity($form['city']['#value']);
    $lon = $this->cityManager->getLonByCity($form['city']['#value']);
    $form['lat_lon_container']['lat']['#value'] = $lat;
    $form['lat_lon_container']['lon']['#value'] = $lon;

    return $form['lat_lon_container'];
  }

  /**
   *
   * Implement the validator because we were forced to set '#validated' => TRUE.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state array.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (empty($form_state->getValue('city'))) {
      $form_state->setErrorByName('city', $this->t('The "City" field is required.'));
    }
    if (empty($form_state->getValue('country'))) {
      $form_state->setErrorByName('country', $this->t('The "Country" field is required.'));
    }
    if (empty($form_state->getValue('units_of_measurement'))) {
      $form_state->setErrorByName('units_of_measurement', $this->t('The "Units of measurement" field is required.'));
    }
  }

  /**
   * Submit Form function.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state array.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if (!isset($_SESSION['count'])) {
      $_SESSION['count'] = 0;
    }
    if (!isset($_SESSION['build'])) {
      $_SESSION['build'] = [];
    }
//    if (count($_SESSION['build']) > 6) {
      $_SESSION['count']++;
      $_SESSION['form_values'] = $form_state;
      $this->redirectPage();
//    }
  }

}
