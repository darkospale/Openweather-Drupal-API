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
  public function createNode($data, $air_pollution_data){

    if ($data->sys->country) {
      $country_name = $this->countryManager->getList()[$data->sys->country]->__toString();
    }
    else {
      $country_name = '';
    }

    $city_name = $this->cityManager->getCityById($data->sys->country, $data->id);

    // $is_ajax = \Drupal::request()->isXmlHttpRequest();

    // dsm($is_ajax);

    $date = date('H:i:s d.m.Y.', time());

  // do{
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');

    $currentUser = User::load(\Drupal::currentUser()->id());
    $uid = 1;
    if ($user = $currentUser) {
      $uid = $user->id();
    }

    // @todo call this from another service.
    // North.
    if($data->wind->deg > 337 || $data->wind->deg <= 22) {
      $wind_icon = 'wind-n.jpg';
      $wind_direction = 'North';
    }
    // North-east.
    else if($data->wind->deg > 23 || $data->wind->deg <= 67) {
      $wind_icon = 'wind-ne.jpg';
      $wind_direction = 'North-east';
    }
    // East.
    else if($data->wind->deg > 68 || $data->wind->deg <= 112) {
      $wind_icon = 'wind-e.jpg';
      $wind_direction = 'East';
    }
    // South-east.
    else if($data->wind->deg > 113 || $data->wind->deg <= 157) {
      $wind_icon = 'wind-se.jpg';
      $wind_direction = 'South-east';
    }
    // South.
    else if($data->wind->deg > 158 || $data->wind->deg <= 202) {
      $wind_icon = 'wind-s.jpg';
      $wind_direction = 'South';
    }
    // South-west.
    else if($data->wind->deg > 203 || $data->wind->deg <= 247) {
      $wind_icon = 'wind-sw.jpg';
      $wind_direction = 'South-west';
    }
    // West.
    else if($data->wind->deg > 248 || $data->wind->deg <= 292) {
      $wind_icon = 'wind-w.jpg';
      $wind_direction = 'West';
    }
    // North-west.
    else if($data->wind->deg > 293 || $data->wind->deg <= 337) {
      $wind_icon = 'wind-nw.jpg';
      $wind_direction = 'North-west';
    }

    // @todo using this.
//    $formatted_data = \Drupal::service('form_data')->formData($units_of_measurement, $build, $build_air_pollution);

    $title = 'Country: ' . $country_name . ', City: ' . $city_name . ', Date: ' . $date;

      $node = $node_storage->create([
        'uid' => $uid,
        'type' => 'weather_info',
        'title' => $title,
        'field_country' => $country_name,
        'field_city' => $city_name,
        'field_date' => $date,
        'field_main' => $data->weather[0]->main,
        'field_description' => $data->weather[0]->description,
        'field_temp' => $data->main->temp,
        'field_feels_like' => $data->main->feels_like,
        'field_temp_min' => $data->main->temp_min,
        'field_temp_max' => $data->main->temp_max,
        'field_pressure' => $data->main->pressure.'mbar',
        'field_humidity' => $data->main->humidity.'%',
        'field_wind_speed' => $data->wind->speed,
        'field_wind_deg' => $data->wind->deg,
        'field_wind_direction' => $wind_direction,
        'field_aqi' => $air_pollution_data->list[0]->main->aqi,
        'field_carbon_monoxide' => $air_pollution_data->list[0]->components->co,
        'field_nitrogen_monoxide' => $air_pollution_data->list[0]->components->no,
        'field_nitrogen_dioxide' => $air_pollution_data->list[0]->components->no2,
        'field_ozone' => $air_pollution_data->list[0]->components->o3,
        'field_sulphur_dioxide' => $air_pollution_data->list[0]->components->so2,
        'field_fine_particles_matter' => $air_pollution_data->list[0]->components->pm2_5,
        'field_coarse_particulate_matter' => $air_pollution_data->list[0]->components->pm10,
        'field_ammonia' => $air_pollution_data->list[0]->components->o3,
      ]);
      $node->save();
    // } while($is_ajax);
  }

}
