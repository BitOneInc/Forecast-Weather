<?php 
/*
Plugin Name: Wunderground Forecast Data
Plugin URI: http://blackreit.com
Description: Search forecast data from Wunderground API
Author: Matthew M. Emma
Version: 1.0
Author URI: http://www.blackreit.com
*/
$WPForecastWunderground = new Wunderground();

class ForecastWunderground {

  public function __construct() {
    add_shortcode('fw', array($this, 'wunderground_forecast'));
  }

  public function wunderground_forecast( $atts ) { // ($city, $state, $year, $month, $day) {
    wp_enqueue_style('weatherformat', plugins_url('css/weatherformat.css', __FILE__));
    wp_enqueue_style('weatherfont', plugins_url('css/weather-icons.css', __FILE__));
    extract( shortcode_atts( array(
      'city' => 'New_York',
      'state' => 'NY',
      'days' => '3'
    ), $atts, 'fw' ) );
    $json_string = file_get_contents('http://api.wunderground.com/api/b8e924a8f008b81e/forecast10day/q/' . $state . '/' . $city . '.json');
    $parsed_json = json_decode($json_string);
    $forecasts = $parsed_json->{'forecast'}->{'simpleforecast'}->{'forecastday'};

    echo '<div class="weatherformat">';
    for ($i = 0; $i < $days; $i++) {
      $forecast = $forecasts[$i];
      echo '<div class="col-1-'.$days.'">';
      echo $forecast->{'date'}->{'weekday'}.'<br>';
      echo substr(strstr($forecast->{'date'}->{'pretty'}, ' on '), 4).'<br><br>';
      echo $this->wunderground_to_forecast_icon($forecast->{'conditions'}, 72).'<br><br>';
      echo $forecast->{'conditions'}.'<br>';
      echo 'High: '.$forecast->{'high'}->{'fahrenheit'}.'°F / '.$forecast->{'high'}->{'celsius'}.'°C<br>';
      echo 'Low: '.$forecast->{'low'}->{'fahrenheit'}.'°F / '.$forecast->{'low'}->{'celsius'}.'°C<br>';
      echo '</div>';
    }

    echo '</div></div>';
  }

  private function wunderground_to_forecast_icon( $status, $size ) {
    $icons = array(
      'Chance of Flurries' => 'wi-day-snow',
      'Chance of Rain' => 'wi-day-rain',
      'Chance Rain' => 'wi-day-rain',
      'Chance of Freezing Rain' => 'wi-day-rain-mix',
      'Chance of Sleet' => 'wi-day-rain-mix',
      'Chance of Snow' => 'wi-day-snow',
      'Chance of Thunderstorms' => 'wi-day-thunderstorm',
      'Chance of a Thunderstorm' => 'wi-day-thunderstorm',
      'Clear' => 'wi-day-sunny',
      'Cloudy' => 'wi-day-cloudy',
      'Fog' => 'wi-smoke',
      'Haze' => 'wi-smog',
      'Mostly Cloudy' => 'wi-day-cloudy',
      'Mostly Sunny' => 'wi-day-sunny',
      'Partly Cloudy' => 'wi-day-cloudy',
      'Partly Sunny' => 'wi-day-sunny',
      'Freezing Rain' => 'wi-day-rain-mix',
      'Rain' => 'wi-rain',
      'Sleet' => 'wi-rain-mix',
      'Snow' => 'wi-snow',
      'Sunny' => 'wi-day-sunny',
      'Thunderstorms' => 'wi-thunderstorm',
      'Thunderstorm' => 'wi-thunderstorm',
      'Unknown' => 'wi-sunny',
      'Overcast' => 'wi-day-sunny-overcast',
      'Scattered Clouds' => 'wi-day-cloudy',
    );
    return '<i style="font-size: '.$size.'px;" class="wi '.$icons[$status].'"></i>';
  }
}