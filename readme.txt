=== PageCDN - Easy Speedup ===
Contributors: pagecdn
Tags: CDN, Optimization, Performance, Pagespeed, SEO, Cache, Content Delivery Network, Public CDN, Opensource CDN, Private CDN, Shared CDN, WordPress CDN, WP CDN, fastest, super, brotli
Requires at least: 4.3
Tested up to: 5.2
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



10x easy setup. Serves static assets from CDN with brotli-11 compression. Speed up public & private files and fonts.


== Description ==

== Free CDN ==

[PageCDN](https://pagecdn.com)'s free CDN accelerates delivery of opensource content, thousands of WordPress themes and plugins, 
fonts and more. It takes each resource separately and intelligently optimizes it using the best information available.

Instead of just replacing hostname with CDN hostname, PageCDN WordPress Plugin serves your static assets such as images, fonts, CSS or 
javascript files in the best way possible. For this, it takes each resource separately and tries to get its most optimized version 
from PageCDN using its Public CDN API. The benefit of loading resources through Public CDN is that such resources are most of the 
times already available in your visitor's browser cache saving some bandwidth and loading time.

Please note that loading resources from Public CDN reduces your CDN bandwidth cost as Public CDN is available for free.


== Premium CDN ==

If a resource is not available on Public CDN then you will need to optimize it using Premium CDN feature. For resources like your 
website images and commercial theme/plugin files, etc this plugin simply links the resources through PageCDN using PageCDN Private API, 
and gets you brotli compression, geographic delivery, HTTP/2 and other benefits that PageCDN offers.

Also, with PageCDN Plugin, standard CDN **setup is 10X easier** compared to any other CDN Plugin.


== Which resources are available on PageCDN's Public CDN? ==

[PageCDN](https://pagecdn.com/)'s Public CDN hosts the following type of content for free. Bandwidth from Public CDN is not considered 
for your CDN bandwidth cost calculation.
* Opensource Libraries.
* Opensource WordPress Themes.
* Opensource HTML5 themes.
* [Easy Fonts](https://pagecdn.com/lib/easyfonts) - A replacement of Google fonts with better caching and easy to use CSS font classes.
* [Patterns](https://pagecdn.com/lib/subtlepatterns) from Subtlepatterns.
* Opensource WordPress Plugins.

in addition to the above, commercial theme developers may also host their theme files on PageCDN for better cache reuse and 
performance. However, such Commercial Content is not a part of Public CDN. To know more about whether a theme avails such performance 
benefits from PageCDN, please consult the theme developer.


== Available Opensource Libraries ==

There are currently [100s of libraries](https://pagecdn.com/lib) available on Public CDN. Some noteable libraries are listed on below 
listed CDN pages:

* [jQuery CDN](https://pagecdn.com/lib/jquery)
* [Bootstrap CDN](https://pagecdn.com/lib/bootstrap)
* [Font Awesome CDN](https://pagecdn.com/lib/font-awesome)
* [AngularJS CDN](https://pagecdn.com/lib/angularjs)
* [Zepto CDN](https://pagecdn.com/lib/zepto)
* [Easy Fonts CDN](https://pagecdn.com/lib/easyfonts)
* [Waypoints CDN](https://pagecdn.com/lib/waypoints)
* [Lazysizes CDN](https://pagecdn.com/lib/lazysizes)
* [Framework7 CDN](https://pagecdn.com/lib/framework7)
* [Semantic ui CDN](https://pagecdn.com/lib/semantic-ui)
* [Selectize js CDN](https://pagecdn.com/lib/selectize)
* [Slim scroll CDN](https://pagecdn.com/lib/jquery-slimscroll)
* [Backbone CDN](https://pagecdn.com/lib/backbone)
* [Leaflet CDN](https://pagecdn.com/lib/leaflet)


== Installation ==
Detailed setup guide is available [here](https://docs.pagecdn.com/quick-start#wordpress-integration). For quick installation, please 
follow these steps:
1. Install the plugin from WordPress, and activate it.
2. Activation will instantly enable all Public CDN features.
3. Open PageCDN Plugin page from Settings menu.
4. Get API Key from [PageCDN Settings](https://pagecdn.com/account/settings) and paste to relevant box in Premium CDN section on Plugin settings page.
5. Hit 'Save Changes'.
6. Done :)


== CDN Features ==
* Full HTTPS and HTTP/2 support
* Brotli-11 compression (configurable through PageCDN dashboard)
* Cache reuse across websites where possible so that even your first time visitors gets a chance to load your site as quickly as it does for repeat visitor
* HTTP/2 Server Push support (configurable through PageCDN dashboard)
* Immutable Caching support (configurable through PageCDN dashboard)
* Content delivery from datacenter geographically close to your website visitors

== Plugin Features ==
* 10X easier setup 
* Loads assets through PageCDN Global Edge Network
* Remove Query String from static resources to make them more cacheable
* Cache reuse across sites that use opensource libraries and opensource WordPress themes
* Leverage browser caching for fonts
* Set directories to be optimized through plugin
* Set directories and file extensions to be ignored
* Automatically optimize DNS lookups and HTTP caching by loading [better optimized fonts](https://pagecdn.com/lib/easyfonts)
* Automatically optimize DNS lookups and delivery by changing resources that load from different Public CDNs to load from single [Public CDN](https://pagecdn.com/dashboard) instead
* Automatically optimize HTTP caching and delivery by searching and linking opensource library files from [Opensource Libraries CDN](https://pagecdn.com/lib)
* Automatically optimize HTTP caching and delivery by searching and linking theme files from [Opensource Themes CDN](https://pagecdn.com/theme)
* Automatically optimize content by searching and linking minified version of files from Public CDN
* Delivery private content through Premium CDN

== System Requirements ==
* PHP >=5.6
* WordPress >=4.6


== Contribute ==
* Anyone is welcome to contribute to the plugin on [GitHub](https://github.com/pagecdn/better-wordpress-cdn).


== Author ==
* [PageCDN](https://pagecdn.com "PageCDN")


== Changelog ==

= 2.0 =
* Several bug fixes.
* Update PageCDN API to v2.
* Better error reporting.
* Speedup Public CDN files lookup by providing data.json that holds common opensource libraries.
* Speedup Public CDN files lookup by ignoring minified files that have less chance of match on Public CDN.
* Re-written the entire plugin.
* No Signup needed to use Free features.
* Zero Config for Free CDN. Plugin now starts working immediately after installation.
* Revamped setting page.
* Better Easy Fonts handling and normalizing.
* Fallback to single fonts.css file if nore than 2 fonts are used.
* Plugin is now compatible with "minify and merge" feature of Autoptimize, W3Total Cache, Hummingbird Performance, SG Optimizer and LiteSpeed Cache.

= 1.0 =
* Several bug fixes.

= 0.1 =
* First release.



== Screenshots ==

1. Free CDN Configuration
2. Premium CDN Configuration
