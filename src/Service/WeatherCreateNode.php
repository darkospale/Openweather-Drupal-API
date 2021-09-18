<?php

namespace Drupal\openweathermap\Service;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\openweathermap\CityManagerInterface;

class WeatherCreateNode {

  /**
   * Country manager.
   *
   * @var Drupal\Core\Locale\CountryManagerInterface
   *   Country manager class.
   */
  protected $country_manager;

  /**
   * City manager.
   *
   * @var Drupal\openweathermap\CityManagerInterface
   *   City manager class.
   */
  protected $city_manager;

  /**
   * Constructor.
   *
   * @param CountryManagerInterface $country_manager
   *   Country manager.
   * @param CityManagerInterface $city_manager
   *   City manager.
   */
  public function __construct(CountryManagerInterface $country_manager, CityManagerInterface $city_manager){
    $this->countryManager = $country_manager;
    $this->cityManager = $city_manager;
  }

  /**
   * Create node function.
   *
   * @param $data
   *   Weather data.
   * @param $air_pollution_data
   *   Air pollution data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createNode($data){

    $city_name = strip_tags($data['city']['#markup']);
    $date = date('H:i:s d.m.Y.', time());
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $currentUser = User::load(\Drupal::currentUser()->id());
    $uid = 1;
    if ($user = $currentUser) {
      $uid = $user->id();
    }
    $title = 'Country: ' . $data['country']['#markup'] . ', City: ' . $city_name . ', Date: ' . $date;

    $node = $node_storage->create([
      'uid' => $uid,
      'type' => 'weather_info',
      'title' => $title,
      'field_country' => $data['country']['#markup'],
      'field_city' => $city_name,
      'field_description' => strip_tags($data['description']['#markup']),
      'field_temperature' => strip_tags($data['temp']['#markup']),
      'field_feels_like' => strip_tags($data['feels_like']['#markup']),
      'field_minimum_temperature' => strip_tags($data['temp_min']['#markup']),
      'field_maximum_temperature' => strip_tags($data['temp_max']['#markup']),
      'field_pressure' => strip_tags($data['pressure']['#markup']),
      'field_humidity' => strip_tags($data['humidity']['#markup']),
      'field_wind_speed' => strip_tags($data['wind_speed']['#markup']),
      'field_wind_direction' => strip_tags($data['wind_direction']['#markup']),
      'field_air_pollution_index' => strip_tags($data['aqi']['#markup']),
      'field_carbon_monoxide' => strip_tags($data['co']['#markup']),
      'field_nitrogen_monoxide' => strip_tags($data['no']['#markup']),
      'field_nitrogen_dioxide' => strip_tags($data['no2']['#markup']),
      'field_ozone' => strip_tags($data['o3']['#markup']),
      'field_sulphur_dioxide' => strip_tags($data['so2']['#markup']),
      'field_fine_particles_matter' => strip_tags($data['pm2_5']['#markup']),
      'field_coarse_particulate_matter' => strip_tags($data['pm10']['#markup']),
      'field_ammonia' => strip_tags($data['nh3']['#markup']),
    ]);
    $node->save();
  }

}
