(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.MyModuleBehavior = {
    attach: function (context, settings) {
      var lat = drupalSettings.lat;
      var lon = drupalSettings.lon;

      // Google Maps part, all is done inline in one JS file
      const API_KEY = 'AIzaSyD5ddME14jf3jqniSvYPDQpxbOFxnXx9WE';
      // Need to call the Google Maps function first because of variables
      // Create the script tag, set the appropriate attributes
      var script = document.createElement('script');
      script.src = 'https://maps.googleapis.com/maps/api/js?key='+API_KEY+'&callback=initMap';
      script.async = true;

      // Attach your callback function to the `window` object
      window.initMap = function () {
        this.myLatLng = new google.maps.LatLng(lat, lon);

        map = new google.maps.Map(document.getElementById("map"), {
          center: myLatLng,
          zoom: 8,
        });

        console.log(this.myLatLng);

        new google.maps.Marker({
          position: myLatLng,
          map,
        });
      };

      // Append the 'script' element to 'head'
      document.head.appendChild(script);
    }
  }

})(jQuery, Drupal, drupalSettings);
