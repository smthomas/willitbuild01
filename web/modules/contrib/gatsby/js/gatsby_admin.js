/**
 * @file
 * Contains logic for Gatsby admin secret key generation.
 */

Drupal.behaviors.gatsby_admin = {
  attach: function(context) {
    if (context !== document) {
      return;
    }

    document
      .getElementById("gatsby--generate")
      .addEventListener("click", function() {
        document.getElementById("edit-secret-key").value = gatsby_uuidv4();
      });

    function gatsby_uuidv4() {
      return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
        (
          c ^
          (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
        ).toString(16)
      );
    }
  }
};
