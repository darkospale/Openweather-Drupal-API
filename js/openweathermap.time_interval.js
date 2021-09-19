(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.MyModuleBehavior = {
    attach: function (context, settings) {
      var update_interval = drupalSettings.interval;
      setInterval(function () {
        location.reload();
      }, update_interval);
    }
  }
})(jQuery, Drupal, drupalSettings);
