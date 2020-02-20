Allows a live preview of your Gatsby site built with your Drupal content.

# Installation

1. Download and install the module as you would any other Drupal 8 module.
Using composer is the preferred method.
2. It's easiest to test this by signing up for Gatsby cloud preview at
https://www.gatsbyjs.com/. There is a free tier that will work for most
development and small production sites.
3. You can also configure this to run against a local Gatsby development
server. You need to make sure your Drupal site can communicate with your Gatsby
development server over port 8000. Using a tool such as ngrok can be helpful
for testing this locally.
4. Install the Gatsby Source Drupal plugin on your Gatsby site. There are no
additional configuration options needed for the plugin to work. However, you
can add the optional secret key to match your Drupal configuration's secret key.
```
module.exports = {
  plugins: [
    {
      resolve: `gatsby-source-drupal`,
      options: {
        baseUrl: `...`,
        secret: `any-value-here`
      }
    }
  ]
};
```
5. Navigate to the configuration page for the Gatsby Drupal module.
6. Copy the URL to your Gatsby preview server (either Gatsby cloud or a locally
running instanct). Once you have that, the Gatsby side is set up to receive
updates.
7. Add an optional secret key to match the configuration added to your
gatsby-config.js file as documented above.
8. Select the entity types that should be sent to the Gatsby Preview server.
At minimum you typically will need to check `Content` but may need other entity
types such as Files, Media, Paragraphs, etc.
9. Save the configuration form.
10. If you want to enable the Gatsby Preview button or Preview Iframe, go to the
Content Type edit page and check the boxes for the features you would like to
enable for that specific content type.

Now you're all set up to use preview! Make a change to your content,
press save (keystroke by keystroke updates are not available yet), and watch as
your Gatsby Preview site magically updates!

# Menus

To enable Gatsby menus you will need to install the module until the point 
that the issue in JSON:API Extras is resolved 
https://www.drupal.org/project/jsonapi_extras/issues/2982133.
The menu functionality relies on the overriding the menu_link_content parent 
field to use our "Alias link" formatter.

To expose the menu_link_content endpoint in the JSON:API you will need 
the Gatsby user to have the "Administer menus and menu items" permission.
This can be done using basic_auth with the gatsby-source-drupal plugin 
or by using the key_auth module with Gatsby to an account with that permission.

# Known Issues

- If you enable the Iframe preview on a content type it may cause an issue with
BigPipe loading certain parts of your page. For now you can get around this
issue by disabling BigPipe or not using the Preview Iframe.
