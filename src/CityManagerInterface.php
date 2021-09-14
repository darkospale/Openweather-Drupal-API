<?php

namespace Drupal\openweathermap;

/**
 * The interface for the city_manager service.
 */
interface CityManagerInterface {

  /**
   * Get all countries from the 'city.list.json' file.
   *
   * @return array
   *   The array with country names keyed by country id.
   */
  public function getCountries();

  /**
   * Get info about all cities of a country from the 'city.list.json' file.
   *
   * @param string $country_code
   *   A country code.
   *
   * @return array
   *   An array with info about all cities of a country of city.
   */
  public function getCitiesByCountryCode($country_code);

  /**
   * Get a city id from the 'city.list.json' file.
   *
   * @param string $country_code
   *   A country code.
   * @param string $city_name
   *   A name of a city.
   *
   * @return string|bool
   *   A a city id or FALSE if a city was not found.
   */
  public function getCityId($country_code, $city_name);
}
