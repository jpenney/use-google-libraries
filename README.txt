=== Use Google Libraries ===
Contributors: jczorkmid
Donate link: http://jasonpenney.net/donate
Tags: javascript, performance, CDN, Google, jQuery, Prototype, MooTools, Dojo, Google AJAX Libraries API, YSlow, Page Speed
Requires at least: 2.9.1
Tested up to: 3.0
Stable tag: 1.1

Allows your site to use common javascript libraries from Google's AJAX 
Libraries CDN, rather than from WordPress's own copies.

== Description ==

A number of the javascript libraries distributed with Wordpress are also 
hosted on Google's [AJAX Libraries API](http://code.google.com/apis/ajaxlibs/).
This plugin allows your Wordpress site to use the content distribution 
network side of Google's AJAX Library API, rather than serving these files from your WordPress install directly.

This provides numerous potential performance benefits:

* increases the chance that a user already has these files cached
* takes load off your server
* uses compressed versions of the libraries (where available)
* Google's servers are set up to negotiate HTTP compression with the requesting browser

For a more detailed look see Dave Ward's [3 reasons why you should let
Google host jQuery for
you](http://encosia.com/2008/12/10/3-reasons-why-you-should-let-google-host-jquery-for-you/).

= Supported Libraries and Components =

* [Dojo](http://dojotoolkit.org/)
* [jQuery](http://jquery.com/)
* [jQuery UI](http://ui.jquery.com/)
* [MooTools](http://mootools.net/)
* [Prototype](http://www.prototypejs.org/)
* [script.aculo.us](http://script.aculo.us/)
* [swfobject](http://code.google.com/p/swfobject/)

== Installation ==

# Upload the `use-google-libraries` folder to the `/wp-content/plugins/` folder.
# Activate **Use Google Libraries** through the 'Plugins' menu in WordPress.
# Er... That's it really.

== Frequently Asked Questions ==

= What happens when Google updates their library versions? =

Google has stated that they intend to keep every file they've hosted 
available indefinitely, so you shouldn't need to worry about them 
disappearing.  

= Why isn't in doing anything? =

Firstly, if you are using a caching plugin, flush the cache or
temporarily disable it to be sure it's not doing anything.  That said,
I've done my best to make **Use Google Libraries** gracefully step out
of the way when things are not as expected.  While not, perhaps,
giving you the greatest benefit it helps ensure you site doesn't just
flat out stop working.

In general, anything that calls wp_register_script and/or
wp_eneque_script before 'init' causes trouble for **Use Google
Libraries**.  I've made an effort to force it to try and run anyhow,
so please report any issues with this.  If you have 'WP_DEBUG'
enabled, a message will be logged letting you know this is happening.

Please see the section on **Incompatible Plugins** and
**Incompatible Themes** for specific information. 

== Incompatible Plugins ==

== Incompatible Themes ==

= K2 =

I've had scattered reports that UGL is stepping out of the way when
using K2.

== Changelog ==

= 1.1 =

+ No longer disable script concatenation when using WordPress 3.0 or
greater
+ Attempt to detect when another plugin or theme has called
'wp_register_script' and/or 'wp_enque_script' before 'init' and work
around it.
+ Limited debugging output when WP_DEBUG is enabled.

= 1.0.9.2 =

+ Hopefully fix issue with plugin loading for some users

= 1.0.9.1 =

+ Added **Incompatible Plugins** and **Incompatible Themes** sections
to the README

= 1.0.9 =

+ more https detection
+ inline jQuery.noConflict()

= 1.0.7.1 =

+ fix previous fix (whoops!)

= 1.0.7 =

+ Quick and dirty workaround for scriptaculous loading (thanks to
[Gregory Lam for bringing it to my
attention](https://twitter.com/gregorylam/statuses/2279304842)

= 1.0.6.1 =

+ moved location of the Changelog section in the README

= 1.0.6 = 

+ Disables script concatenation in WordPress 2.8, since it seems to have
issues when some of the dependencies are outside of the concatenation.
+ Persists flag to load scripts in the footer in WordPress 2.8

= 1.0.5 =

Implemented a pair of
[suggestions](http://jasonpenney.net/wordpress-plugins/use-google-libraries/comment-page-1/#comment-32427)
from  [Peter  Wilson](http://peterwilson.cc/).

+ It should detect when a page is loaded over https and load the libraries over https accordingly
+ It no longer drops the micro version number from the url.  The reasons for this are twofold:
  + It ensures the version requested is the version received.
  + Google's servers set the expires header for 12 months for these
  urls, as opposed to 1 hour.  This allows clients to cache the file
  for up to a year without needing to retrieve it again from Google's
  servers.  If the version requested by your WordPress install
  changes, so will the URL so there's no worry that you'll keep
  loading an old version.

== Technical Details ==

**Use Google Libraries** uses the following hooks (each with a priority of 1000).

= wp_default_scripts =

**Use Google Libraries** compares it's list of supported scripts to those 
registered, and replaces the standard registrations `src` with ones that 
point to Google's servers.  Other attributes (like dependencies) are left 
intact.

= print_scripts_array =

If jQuery is enqued **Use Google Libraries** will inject a small javascript file 
to ensure that it is running in 
[noConflict mode](http://docs.jquery.com/Core/jQuery.noConflict) as it would 
with the standard WordPress version.

= script_loader_src =

**Use Google Libraries** removes the `ver=x.y.z` query string from the URL
used to load the requested library *if* it is going to load the library from
`ajax.googleapis.com`.  Otherwise the URL is left unaltered.  This both 
improves the chances of the given URL already being cached, and prevents 
**script.aculo.us** from including scripts multiple times.


== References ==

Parts of this plugin (specificly, the dropping of the micro number,
which has since been removed for better caching performance) were 
inspired by John Blackbourn's 
**[Google AJAX Libraries](http://lud.icro.us/wordpress-plugin-google-ajax-libraries/)**, 
which has very similar goals to this plugin.

== Future Plans ==

+ add ability to enable/disable loading from Google for specific libraries
+ add ability to request a newer version than your WordPress install registers by default
