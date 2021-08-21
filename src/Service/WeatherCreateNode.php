<?php

namespace Drupal\openweathermap\Service;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class WeatherCreateNode{
  public function createNode(){
  $is_ajax = \Drupal::request()->isXmlHttpRequest();

  dsm($is_ajax);
  
  do{
    $user_storage = \Drupal::entityTypeManager()->getStorage('user');
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');

    $currentUser = User::load(\Drupal::currentUser()->id());

    $uid = 1;
    if ($user = $currentUser) {
      $uid = $user->id();
    }

    $node = $node_storage->create([
      'uid' => $uid,
      'type' => 'weather_info',
      'title' => 'Title', // "Country: $country, City: $city, Date & Time: $date_time"
      'field_value' => 'Value'
    ]);
    $node->save();
  } while($is_ajax);
  }
}