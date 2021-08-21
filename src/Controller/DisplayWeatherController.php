<?php

namespace Drupal\openweathermap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\openweathermap\CityManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\user\Entity\User;

/**
 * The controller for the weather forecast routes.
 */
class DisplayWeatherController extends ControllerBase {

  /**
   * Country manager service.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * City manager service.
   *
   * @var \Drupal\openweathermap\CityManagerInterface
   */
  protected $cityManager;

  /**
   * Language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * API weather endpoint.
   *
   * @var string
   */
  protected $apiWeatherEndpoint;

  /**
   * API air pollution endpoint.
   *
   * @var string
   */
  protected $apiAirPollutionEndpoint;

  /**
   * API key.
   *
   * @var string
   */
  protected $apiKey;

  /**
   * Units of measurement, see https://openweathermap.org/current#data .
   *
   * @var string
   */
  protected $units;

  /**
   * Latitude
   *
   * @var string
   */
  protected $lat;

  /**
   * Longitude
   *
   * @var string
   */
  protected $lon;

  /**
   * Current language.
   *
   * @var string
   */
  protected $language;

  /**
   * Country code.
   *
   * @var string
   */
  protected $countryCode;

  /**
   * Service URL for requesting weather forecast data.
   *
   * @var string
   */
  protected $urlWeather;

  /**
   * Service URL for requesting air pollution forecast data.
   *
   * @var string
   */
  protected $urlAirPollution;

  /**
   * Constructs a new DisplayWeather object.
   */
  public function __construct(CountryManagerInterface $country_manager, CityManagerInterface $city_manager, LanguageManagerInterface $language_manager, ConfigFactoryInterface $config_factory) {
    $this->countryManager = $country_manager;
    $this->cityManager = $city_manager;
    $this->languageManager = $language_manager;
    $this->config = $config_factory->get('openweathermap.settings');
    $this->apiWeatherEndpoint = $this->config->get('api_weather_endpoint');
    $this->apiAirPollutionEndpoint = $this->config->get('api_air_pollution_endpoint');
    $this->apiKey = $this->config->get('api_key');
    $this->units = $this->config->get('units_of_measurement');
    $this->lat = $this->config->get('lat');
    $this->lon = $this->config->get('lon');
    $this->language = $this->languageManager->getCurrentLanguage()->getId();
  }

  /**
   * {@inheritdoc}
   */
  public function displayAirPollution(){
    $args = "?&lat={$this->lat}" . "&lon={$this->lon}" . "&appid={$this->apiKey}";
    $this->urlAirPollution = $this->apiAirPollutionEndpoint . $args;

    return $this->urlAirPollution;
  }

  /**
   * {@inheritdoc}
   */
  public function displayWeatherDefaultLocation() {
    $this->countryCode = $this->config->get('country_code');

    $city_id = $this->config->get('city_id');

    $args = "?id={$city_id}" . "&units={$this->units}" . "&appid={$this->apiKey}" . "&lang={$this->language}";
    $this->urlWeather = $this->apiWeatherEndpoint . $args;

    $this->displayAirPollution();
    return $this->response();
  }

  /**
   * {@inheritdoc}
   */
  public function displayWeatherByCityName($city) {
    $this->countryCode = NULL;

    $args = "?q={$city}" . "&units={$this->units}" . "&appid={$this->apiKey}" . "&lang={$this->language}";
    $this->urlWeather = $this->apiWeatherEndpoint . $args;

    $this->displayAirPollution();
    return $this->response();
  }

  /**
   * To test this method, "Brest" city can be used.
   *
   * Brest city is presents in Belarus, Germany and France. Possible urls are:
   * /weather/Brest/by
   * /weather/Brest/de
   * /weather/Bresr/fr
   * .
   */
  public function displayWeatherByCityNameAndCountryCode($city, $country_code) {
    $this->countryCode = strtoupper($country_code);

    $args = "?q={$city},{$country_code}" . "&units={$this->units}" . "&appid={$this->apiKey}" . "&lang={$this->language}";
    $this->urlWeather = $this->apiWeatherEndpoint . $args;

    $this->displayAirPollution();
    return $this->response();
  }

  /**
   * {@inheritdoc}
   */
  protected function response() {

    $createNode = \Drupal::service('create_node')->createNode();

    if ($this->apiKey == '') {

      $this->messenger()
        ->addWarning("Sorry, but the weather service hasn't been configured yet. Please contact your system administrator.");

      return $this->redirect('<front>');
    }

    // Get a country name to display instead of a country code.
    if ($this->countryCode) {
      $country_name = $this->countryManager->getList()[$this->countryCode]->__toString();
    }
    else {
      $country_name = '';
    }

    $build['#theme'] = 'openweathermap_weather_page';
    $build['#attached']['library'] = 'openweathermap/openweathermap.display_weather';

    $build['#attached']['drupalSettings'] = [
      'urlWeather' => $this->urlWeather,
      'urlAirPollution' => $this->urlAirPollution,
      'country_name' => $country_name,
      'lat' => $this->lat,
      'lon' => $this->lon,
      'units_of_measurement' => $this->units,
      'update_periodically' => $this->config->get('update_periodically'),
      // Translate minutes to milliseconds.
      'update_interval' => $this->config->get('update_interval') * 60000,
      'weather_characteristics' => $this->config->get('weather_characteristics'),
      'air_pollution_characteristics' => $this->config->get('air_pollution_characteristics'),
    ];

    return $build;
  }

}
