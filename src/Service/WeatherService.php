<?php

namespace Drupal\openweathermap\service;

use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\Renderer;

/**
 * WeatherService.
 */
class WeatherService {

  /**
   * API Weather Endpoint.
   *
   * @var string
   */
  protected $api_key;

  /**
   * API Weather Endpoint.
   *
   * @var string
   */
  protected $api_weather_endpoint;

  /**
   * API Weather Endpoint.
   *
   * @var string
   */
  protected $api_air_pollution_endpoint;

  /**
   * Renderer used to render email content.
   *
   * @var Renderer
   *   Render all the email content and variables.
   */
  protected $renderer;

  /**
   * Country Manager Interface.
   *
   * @var CountryManagerInterface
   *   Use it here for displaying the full country name on page and email.
   */
  protected $countryManager;

  /**
   * Constructor.
   *
   * @param Renderer $renderer
   *   Renderer class.
   *
   * @param CountryManagerInterface $countryManager
   *   Country Manager class.
   */
  public function __construct(Renderer $renderer, CountryManagerInterface $countryManager) {
    $this->renderer = $renderer;
    $this->countryManager = $countryManager;
  }

  /**
   * Function that makes the API request calls.
   *
   * @param $data
   *   Data that is processed from DisplayWeatherForm class.
   *
   * @return array
   */
  public function makeRequest($data) {
    $config = \Drupal::config('openweathermap.settings');
    $this->api_key = $config->get('api_key');
    $this->api_weather_endpoint = $config->get('api_weather_endpoint');
    $this->api_air_pollution_endpoint = $config->get('api_air_pollution_endpoint');

    foreach ($data as $call) {
      $params = $call->getValues();
      $city = $params['city'];
      $lat = $params['lat'];
      $lon = $params['lon'];
      $units_of_measurement = $params['units_of_measurement'];

      // Decode JSON for weather information.
      $weather_endpoint = $this->api_weather_endpoint . '?id=' . $city . '&appid=' . $this->api_key . '&units=' . $units_of_measurement;
      $request = \Drupal::httpClient()->request('GET', $weather_endpoint);
      $content = $request->getBody()->getContents();
      $build = json_decode($content);

      // Decode JSON for air pollution endpoint.
      $air_pollution_endpoint = $this->api_air_pollution_endpoint . '?lat=' . $lat . '&lon=' . $lon . '&appid=' . $this->api_key;
      $request_air_pollution = \Drupal::httpClient()->request('GET', $air_pollution_endpoint);
      $content_air_pollution = $request_air_pollution->getBody()->getContents();
      $build_air_pollution = json_decode($content_air_pollution);

      // Call the function that formats the values depending on what system has been selected.
      $formatted_data = \Drupal::service('form_data')->formData($units_of_measurement, $build, $build_air_pollution);
      // Convert country name acronym to full name
      $country_name = $this->countryManager->getList()[$build->sys->country];

      $value['country_image'] = [
        '#markup' => '<img src=/sites/default/files/weather-icons/flags/' . strtolower($build->sys->country) . '.png><br>',
      ];
      $value['country'] = [
        '#markup' => $country_name,
      ];
      $value['city'] = [
        '#markup' => '<h2>' . $build->name . '</h2><br>',
      ];
      $value['main'] = [
        '#markup' => 'Weather: ' . $build->weather[0]->main . '<br>',
      ];
      $value['description'] = [
        '#markup' => 'Description: ' . $build->weather[0]->description . '<br>',
      ];
      $value['icon'] = [
        '#markup' => '<img src=/sites/default/files/weather-icons/icons/' . $build->weather[0]->icon . '.png><br>',
      ];
      $value['temp'] = [
        '#markup' => 'Temperature: ' . $build->main->temp . $formatted_data['temp_sign'] . '<br>',
      ];
      $value['feels_like'] = [
        '#markup' => 'Feels like: ' . $build->main->feels_like . $formatted_data['temp_sign'] . '<br>',
      ];
      $value['temp_min'] = [
        '#markup' => 'Min. temp: ' . $build->main->temp_min . $formatted_data['temp_sign'] . '<br>',
      ];
      $value['temp_max'] = [
        '#markup' => 'Max. temp: ' . $build->main->temp_max . $formatted_data['temp_sign'] . '<br>',
      ];
      $value['pressure'] = [
        '#markup' => 'Pressure: ' . $build->main->pressure . ' mbar<br>',
      ];
      $value['humidity'] = [
        '#markup' => 'Humidity: ' . $build->main->humidity . ' %<br>',
      ];
      $value['wind_speed'] = [
        '#markup' => 'Wind speed: ' . $formatted_data['speed'] . $formatted_data['speed_sign'] . '<br>',
      ];
      $value['wind_deg'] = [
        '#markup' => '<img src=/sites/default/files/weather-icons/wind/' . $formatted_data['wind_icon'] . '><br>',
      ];
      $value['wind_direction'] = [
        '#markup' => 'Wind direction: ' . $formatted_data['wind_direction'] . '<br>',
      ];
      $value['aqi'] = [
        '#markup' => '<h2>Air pollution index: ' . $build_air_pollution->list[0]->main->aqi . ' - ' . $formatted_data['index_description'] . '</h2><br>',
      ];
      $value['co'] = [
        '#markup' => 'Carbon monoxide: ' . $build_air_pollution->list[0]->components->co . ' μg/m3<br>',
      ];
      $value['no'] = [
        '#markup' => 'Nitrogen monoxide: ' . $build_air_pollution->list[0]->components->no . ' μg/m3<br>',
      ];
      $value['no2'] = [
        '#markup' => 'Nitrogen dioxide: ' . $build_air_pollution->list[0]->components->no2 . ' μg/m3<br>',
      ];
      $value['o3'] = [
        '#markup' => 'Ozone: ' . $build_air_pollution->list[0]->components->o3 . ' μg/m3<br>',
      ];
      $value['so2'] = [
        '#markup' => 'Sulphur dioxide: ' . $build_air_pollution->list[0]->components->so2 . ' μg/m3<br>',
      ];
      $value['pm2_5'] = [
        '#markup' => 'Fine particles matter: ' . $build_air_pollution->list[0]->components->pm2_5 . ' μg/m3<br>',
      ];
      $value['pm10'] = [
        '#markup' => 'Coarse particulate matter: ' . $build_air_pollution->list[0]->components->pm10 . ' μg/m3<br>',
      ];
      $value['nh3'] = [
        '#markup' => 'Ammonia: ' . $build_air_pollution->list[0]->components->nh3 . ' μg/m3<br>',
      ];
//      $value['map'] = [
//        '#markup' => '<div id="map"></div>',
//      ];
      // Send the lat and lon data to javascript
//      $value['#attached']['drupalSettings']['lat'] = $build->coord->lat;
//      $value['#attached']['drupalSettings']['lon'] = $build->coord->lon;

      // Call a service for creating a node.
      \Drupal::service('create_node')->createNode($value);

      // If the update periodically checkbox is checked, send mails and refresh page @todo
      if ($params['update_periodically'] == 1) {
        $module = 'openweathermap';
        $key = 'openweathermap_mail_theme';
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $user = \Drupal::currentUser()->getEmail();
        $date = date('H:i:s d.m.Y.', time());
        $title = 'Country: ' . $value['country']['#markup'] . ', City: ' . $value['city']['#markup'] . ', Date: ' . $date;

        $params = [
          'headers' => [
            'Content-Type' => 'text/html; charset=UTF-8;',
            'Content-Transfer-Encoding' => '8Bit',
          ],
          'from' => 'Openweathermap <openweathermap@openweathermap.com>',
          'subject' => $title,
        ];

        $build = [
          '#theme' => 'openweathermap_mail_theme',
          '#body' => $value,
        ];

        $params['body'] = $this->renderer->executeInRenderContext(new RenderContext(), function () use ($build) {
          return $this->renderer->render($build);
        });

        $mailManager = \Drupal::service('plugin.manager.mail');
        $mailManager->mail($module, $key, $user, $langcode, $params, NULL, TRUE);

        sleep(2);
      }

      return $value;
    }
  }

}
