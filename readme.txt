=== Cusmin URL Shortener ===
Contributors: cusmin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=url.shortener@cusmin.com&item_name=Support+for+Cusmin+URL+Shortener+Plugin+Development
Tags: admin, url shortener, goo.gl, admin panel, google, shortener, url,
Requires at least: 4.8
Requires PHP: 5.6
Tested up to: 4.9.1
Stable tag: 1.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.txt

== Description ==
Generate short URLs in the admin panel with ease. Plugin uses Google Shortener API.

For more information please visit the official [Cusmin URL Shortener](https://cusmin.com/url-shortener?ref=wporg) page.

Feel free to add any suggestions or ideas for improvement in our [support forum](https://wordpress.org/support/plugin/cusmin-url-shortener).

== Features ==
* **ADMIN BAR HELPER**: Paste your URL in this field, and get back a short URL in a second. It's easily accessible on all admin pages.
* **IN-FIELD SHORTENING**: Any form fields in the admin panel that are recognized as URL fields (have a valid URL) support out-of-the box Cusmin URL shortening. A small button appears inside the field that can be used for URL shortening.
* **IN-FIELD UNSHORTENING**: Click on the shortened URL to restore the original URL
* **SILENT SHORTENING**: Shorten URLs on post save in the background
* **PERMALINK SHORTENING**: Shorten your post permalink in the background

(see screenshots)

Cusmin URL Shortener is created with an idea to support all text fields in admin panel that contain valid URLs.

These are some of the places where you can use it:

= Supported fields =
* WordPress custom fields
* ACF fields
* Woocomerce URLS (e.g. affiliate external URL)
* WordPress settings
* Menu links

== We Recommend ==
> <strong>[CUSMIN](https://cusmin.com?ref=wr-cus)</strong><br>
> Premium WordPress Admin Branding Manager.
> A must-have tool for professionals who prepare WordPress sites for their clients.<br><br>
> <strong>[AG Custom Admin](https://wordpress.org/plugins/ag-custom-admin/)</strong><br>
> A free lightweight version of the Cusmin plugin. Good for personal sites.<br>

== Screenshots ==

1. Cusmin URL shortening process
2. Cusmin URL un-shortening process (reverse process)
3. Cusmin Admin Bar URL Shortening Helper, available on all admin pages
4. Cusmin URL Shortening in custom fields and ACF fields
5. Cusmin URL Shortening inside Woocommerce
6. Cusmin URL Shortening in the WordPress Settings

== Installation ==

1. Upload `cusmin-url-shortener` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to plugin's settings page: 'Settings => Cusmin URL Shortener'
4. Fill in the Google Shortener API Key
5. Set other settings (optional) and click on Save button

== Frequently Asked Questions ==

= How to Get a Google API Key? =
Please check the official [Google API](https://console.developers.google.com/apis/library/urlshortener.googleapis.com?q=shortener) page

= I'm Unable to Find the Key, What Should I Do? =
Please check our official [Cusmin URL Shortener](https://cusmin.com/url-shortener?ref=org-faq) documentation

= I Get an Error in the Field =
Something is wrong with URL shortening. Either your API key is not correct, or you need to check Google API settings.
Please make sure that your API key is correct. Also please check if your site domain is added to allowed domains in the Google API Management Dashboard.
We recommend disabling any domain restrictions in the Google dashboard prior testing. Once everything works, you may experiment by adding new restrictions.


== Change Log ==

= 1.4 =
* Fixed notices

= 1.3 =
* Added option for forcing custom field shortening on every post save
* Saving hidden cus_permalink field now only on published posts, not drafts

= 1.2 =
* Added support for shortened permalink shortcode

= 1.1 =
* Added option to disable In-field URL shortening
* Added option to automatically shorten URLS on post save
* Added support for shortened post permalink

= 1.0 =
Initial version.

== Upgrade Notice ==

= 1.4 =
* Bug fix

= 1.3 =
Improvements

= 1.2 =
Added support for shortened permalink shortcode

= 1.1 =
Added new options for more shortening control

= 1.0 =
Initial version.