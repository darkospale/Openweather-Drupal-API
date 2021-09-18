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
   *   Country code.
   *
   * @return array
   *   Array with info about all cities of a country of city.
   */
  public function getCitiesByCountryCode($country_code);

  /**
   * Get a city ID from the 'city.list.json' file.
   *
   * @param string $country_code
   *   Country code.
   * @param string $city_name
   *   Name of a city.
   *
   * @return string|bool
   *   Get a city ID or FALSE if a city was not found.
   */
  public function getCityId($country_code, $city_name);
}
