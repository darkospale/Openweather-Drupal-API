<?php

namespace Drupal\openweathermap\Service;

class DataFormat {

  public function formData($units_of_measurement, $build, $build_air_pollution) {
    switch ($units_of_measurement) {
      case 'metric':
        $temp_sign = '°C';
        $build->wind->speed *= 18/5;
        $speed_sign = 'km/h';
        break;
      case 'imperial':
        $temp_sign = '°F';
        $speed_sign = 'mph';
        break;
      case 'default':
        $temp_sign = 'K';
        $speed_sign = 'm/s';
        break;
    }

    // North.
    if($build->wind->deg > 337 || $build->wind->deg <= 22) {
      $wind_icon = 'wind-n.jpg';
      $wind_direction = 'North';
    }
    // North-east.
    else if($build->wind->deg > 23 || $build->wind->deg <= 67) {
      $wind_icon = 'wind-ne.jpg';
      $wind_direction = 'North-east';
    }
    // East.
    else if($build->wind->deg > 68 || $build->wind->deg <= 112) {
      $wind_icon = 'wind-e.jpg';
      $wind_direction = 'East';
    }
    // South-east.
    else if($build->wind->deg > 113 || $build->wind->deg <= 157) {
      $wind_icon = 'wind-se.jpg';
      $wind_direction = 'South-east';
    }
    // South.
    else if($build->wind->deg > 158 || $build->wind->deg <= 202) {
      $wind_icon = 'wind-s.jpg';
      $wind_direction = 'South';
    }
    // South-west.
    else if($build->wind->deg > 203 || $build->wind->deg <= 247) {
      $wind_icon = 'wind-sw.jpg';
      $wind_direction = 'South-west';
    }
    // West.
    else if($build->wind->deg > 248 || $build->wind->deg <= 292) {
      $wind_icon = 'wind-w.jpg';
      $wind_direction = 'West';
    }
    // North-west.
    else if($build->wind->deg > 293 || $build->wind->deg <= 337) {
      $wind_icon = 'wind-nw.jpg';
      $wind_direction = 'North-west';
    }

    switch ($build_air_pollution->list[0]->main->aqi) {
      case 1:
        $index_description = 'Good';
        break;
      case 2:
        $index_description = 'Fair';
        break;
      case 3:
        $index_description = 'Moderate';
        break;
      case 4:
        $index_description = 'Poor';
        break;
      case 5:
        $index_description = 'Very Poor';
        break;
    }
    $formatted_data = [
      'temp_sign' => $temp_sign,
      'speed' => $build->wind->speed,
      'speed_sign' => $speed_sign,
      'wind_icon' => $wind_icon,
      'wind_direction' => $wind_direction,
      'index_description' => $index_description];

    return $formatted_data;
  }

}
