/**
 * @file
 * Contains all javascript logic for Gatsby preview button.
 */

(function ($, Drupal) {

  Drupal.behaviors.gatsby_preview = {
    attach: function (context, settings) {
      // Get the alias on page load, not the alias that might be edited
      // and thus trigger a 404.
      if (context == document) {
        // Remove trailing slash.
        var gatsby_url = settings.gatsby_preview_url.replace(/\/$/, '');
        var alias = '';

        // Show home if node add page.
        if (!settings.gatsby_show_home) {
          alias = $("#edit-path-0-alias").val();
        }

        $("#edit-gatsby-preview").on("click", function(event) {
          event.preventDefault();

          // Open the full Gatsby page URL.
          window.open(gatsby_url + alias);
        });
      }
    }
  };

})(jQuery, Drupal);
