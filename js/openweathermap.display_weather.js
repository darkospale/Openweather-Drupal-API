(function (drupalSettings) {

  'use strict';

  // http://api.openweathermap.org/data/2.5/weather?q=London&appid=0b03b272416260313f36b14e12b20908&units=metric
  // Use for viewing JSON data

  const GridLoader = VueSpinner.GridLoader;
  const urlWeather = drupalSettings['urlWeather'];
  const urlAirPollution = drupalSettings['urlAirPollution'];

  const units_of_measurement = drupalSettings['units_of_measurement'];
  const lat = drupalSettings['lat'];
  const lon = drupalSettings['lon'];
  // Translated names of weather characteristics.
  const weather_characteristics = drupalSettings['weather_characteristics'];
  const air_pollution_characteristics = drupalSettings['air_pollution_characteristics'];

  const app = Vue.createApp({
    data: () => ({
      city: null,
      country: null,
      lat: null,
      lon: null,
      feels_like: null,
      description: null,
      wind_speed: null,
      wind_deg: null,
      temperature: null,
      pressure: null,
      humidity: null,
      icon: null,
      reconnection_limit: 10,
      reconnection_attempts: 0,
      service_unavailable: false,
      // Display the loader if connection failed before next reconnection.
      display_loader: false,
      wind_icon: null,
      flag: null,
      aqi: null,
      aqi_desc: null,
      co: null,
      no: null,
      no2: null,
      o3: null,
      so2: null,
      pm2_5: null,
      pm10: null,
      nh3: null,
      time: null,
      timezone: null,
    }),

    components:{
      GridLoader
    },

    methods: {
      getWeather(){
        this.service_unavailable = false;

        axios
        .get(urlWeather)
        .then(response => this.prepareData(response.data))
        .catch(error => this.errorHandler(error));
      },

      getAirPollution(){
        this.service_unavailable = false;

        axios
        .get(urlAirPollution)
        .then(response => this.prepareAirPollutionData(response.data))
        .catch(error => this.errorHandler(error))
      },

      prepareData(data){
        this.display_loader = false;
        this.reconnection_attempts = 0;
        // If a country name is not available, then display a country code.
        this.country = drupalSettings['country_name'] ? drupalSettings['country_name'] : data.sys.country;
        this.city = data.name;
        this.lat = lat;
        this.lon = lon;
        this.icon_url = 'sites/default/files/weather-icons/icons/' + data.weather[0].icon + '.png';
        this.flag = 'sites/default/files/weather-icons/flags/' + data.sys.country.toLowerCase() + '.png';
        this.wind_deg_icon = 'sites/default/files/weather-icons/wind/';
        this.feels_like = weather_characteristics['feels_like'] + ': ' + data.main.feels_like;
        const description = data.weather[0].description;
        // Capitalize the first letter.
        this.description = description.charAt(0).toUpperCase() + description.slice(1);
        this.wind_speed = weather_characteristics['wind_speed'] + ': ' + data.wind.speed;
        this.wind_deg = weather_characteristics['wind_deg'] + ': ' + data.wind.deg;
        this.temperature = weather_characteristics['temperature'] + ': ' + data.main.temp;
        this.pressure = weather_characteristics['pressure'] + ': ' + data.main.pressure;
        this.humidity = weather_characteristics['humidity'] + ': ' + data.main.humidity;
        this.addUnitsSuffixes();

        if(data.wind.deg >= 0 && data.wind.deg <= 45){
          this.wind_deg_icon += "wind-n.jpg";
        } else if(data.wind.deg >= 46 && data.wind.deg <= 90){
          this.wind_deg_icon += "wind-ne.jpg";
        } else if(data.wind.deg >= 91 && data.wind.deg <= 135){
          this.wind_deg_icon += "wind-e.jpg";
        } else if(data.wind.deg >= 136 && data.wind.deg <= 180){
          this.wind_deg_icon += "wind-se.jpg";
        } else if(data.wind.deg >= 181 && data.wind.deg <= 225){
          this.wind_deg_icon += "wind-s.jpg";
        } else if(data.wind.deg >= 226 && data.wind.deg <= 270){
          this.wind_deg_icon += "wind-sw.jpg";
        } else if(data.wind.deg >= 271 && data.wind.deg <= 315){
          this.wind_deg_icon += "wind-w.jpg";
        } else if(data.wind.deg >= 316 && data.wind.deg <= 360){
          this.wind_deg_icon += "wind-nw.jpg";
        }

        if (drupalSettings['update_periodically']) {
          // Update the weather data at a specified time interval.
          setInterval(() => this.getWeather(), drupalSettings['update_interval']);
          setInterval(() => this.getAirPollution(), drupalSettings['update_interval']);
        }

        this.time = new Date(data.dt * 1000 + (data.timezone * 1000));
        this.timezone = 'Timezone: GMT ' + data.timezone / 3600 + ':00';
      },

      prepareAirPollutionData(data){
        this.display_loader = false;
        this.reconnection_attempts = 0;

        this.aqi = air_pollution_characteristics['aqi'] + ': ' + data.list[0].main.aqi;
        this.co = air_pollution_characteristics['co'] + ': ' + data.list[0].components.co;
        this.no = air_pollution_characteristics['no'] + ': ' + data.list[0].components.no;
        this.no2 = air_pollution_characteristics['no2'] + ': ' + data.list[0].components.no2;
        this.o3 = air_pollution_characteristics['o3'] + ': ' + data.list[0].components.o3;
        this.so2 = air_pollution_characteristics['so2'] + ': ' + data.list[0].components.so2;
        this.pm2_5 = air_pollution_characteristics['pm2_5'] + ': ' + data.list[0].components.pm2_5;
        this.pm10 = air_pollution_characteristics['pm10'] + ': ' + data.list[0].components.pm10;
        this.nh3 = air_pollution_characteristics['nh3'] + ': ' + data.list[0].components.nh3;

        switch (data.list[0].main.aqi){
          case 1:
            this.aqi_desc = "Good";
            break;
          case 2:
            this.aqi_desc = "Fair";
            break;
          case 3:
            this.aqi_desc = "Moderate";
            break;
          case 4:
            this.aqi_desc = "Poor";
            break;
          case 5:
            this.aqi_desc = "Very Poor";
            break;
          default:
            this.aqi_desc = "Undefined value";
            break;
        }

        this.addPollutionSuffixes();
      },

      errorHandler(error){
        if (this.reconnection_attempts < this.reconnection_limit) {
          this.display_loader = true;
          this.getWeather();
          this.reconnection_attempts++;
        }
        else {
          this.display_loader = false;
          this.reconnection_attempts = 0;
          this.service_unavailable = true;
        }
      },

      addUnitsSuffixes(){
        // Add the degree symbol.
        this.temperature += '\xB0';
        this.feels_like += '\xB0';
        this.wind_deg += '\xB0';
        this.pressure += "mbar";

        switch (units_of_measurement) {
          case 'standard':
            this.temperature += 'K';
            this.feels_like += 'K';
            this.wind_speed += "m/s";
            break;

          case 'metric':
            this.temperature += 'C';
            this.feels_like += 'C';
            //data.wind.speed *= 3.6;
            this.wind_speed += "m/s";
            break;

          case 'imperial':
            this.temperature += 'F';
            this.feels_like += 'F';
            this.wind_speed += "mph";
            break;
        }
      },

      addPollutionSuffixes(){
        this.co += " μg/m3";
        this.no += " μg/m3";
        this.no2 += " μg/m3";
        this.o3 += " μg/m3";
        this.so2 += " μg/m3";
        this.pm2_5 += " μg/m3";
        this.pm10 += " μg/m3";
        this.nh3 += " μg/m3";
      }
    },

    created() {
      this.getWeather();
      this.getAirPollution();
    }
  });

  app.mount('#weather');

})(drupalSettings);
