(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.MyModuleBehavior = {
    attach: function (context, settings) {
      var update_interval = drupalSettings.interval;
      if (update_interval >= 60000) {
        window.onload = function() {
          setTimeout(function () {
            window.location.reload();
          }, update_interval);
        }
      }
    }
  }
})(jQuery, Drupal, drupalSettings);
