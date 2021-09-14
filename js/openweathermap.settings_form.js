/**
 * @file
 * Add the "Select2" features to the select elements of the settings form.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.openweathermapAddSelect2 = {

    attach: function (context) {

      $("select[name='country']").once('edit-country-select2').select2({
        allowClear: true,
        placeholder: Drupal.t('Select...')
      });

      $("select[name='city']").once('edit-city-select2').select2({
        allowClear: true,
        placeholder: Drupal.t('Select...')
      });
    }
  };

})(jQuery, Drupal);
