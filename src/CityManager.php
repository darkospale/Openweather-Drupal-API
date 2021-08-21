<?php

namespace Drupal\openweathermap;

use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\File\FileSystemInterface;

/**
 * Helper for extraction a data from the 'city.list.json' file.
 */
class CityManager implements CityManagerInterface {

  /**
   * Country manager service.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * File system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Array of cities data from the 'city.list.json' file.
   *
   * @var array
   */
  protected $cityList;

  /**
   * Constructs a new CityManager object.
   */
  public function __construct(CountryManagerInterface $country_manager, FileSystemInterface $file_system) {
    $this->countryManager = $country_manager;
    $this->fileSystem = $file_system;
    $path = $this->fileSystem->realpath(drupal_get_path('module', 'openweathermap'));
    $json_data = file_get_contents($path . '/city.list.json');
    $this->cityList = json_decode($json_data, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getCountries() {
    $countries = [];
    // We use Drupal country list to get country names, because
    // city.list.json file contains only country codes.
    $drupal_country_list = $this->countryManager->getList();

    foreach ($this->cityList as $city) {

      $country_code = $city['country'];
      // city.list.json file has empty strings instead of some country codes,
      // therefore validate that a country code exists in Drupal's country list.
      if (isset($drupal_country_list[$country_code])) {

        if (!array_key_exists($country_code, $countries)) {

          $country_name = $drupal_country_list[$country_code];
          $countries[$country_code] = $country_name;
        }
      }
    }

    asort($countries);

    return $countries;
  }

  /**
   * {@inheritdoc}
   */
  public function getCitiesByCountryCode($country_code) {
    $cities = [];

    foreach ($this->cityList as $city) {

      if ($city['country'] == $country_code) {
        // The 'city.list.json' file has issues, for instance, the city with
        // the id=713155 has the name '-'. To prevent this cases validate that
        // a city name is started from a letter.
        if (ctype_alpha($city['name'][0])) {
          $cities[$city['id']] = $city['name'];
        }
      }
    }

    asort($cities);

    return $cities;
  }

  /**
   * {@inheritdoc}
   */
  public function getCityId($country_code, $city_name) {

    foreach ($this->cityList as $city) {

      if ($city['country'] == $country_code && $city['name'] == $city_name) {
        return $city['id'];
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getLatByCity($city_id){
    $lat = '';

    foreach($this->cityList as $city){

      if($city['id'] == $city_id){
        $lat = $city['coord']['lat'];
        return $lat;
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getLonByCity($city_id){
    $lon = '';

    foreach($this->cityList as $city){

      if($city['id'] == $city_id){
        $lon = $city['coord']['lon'];
        return $lon;
      }
    }
  }

}
