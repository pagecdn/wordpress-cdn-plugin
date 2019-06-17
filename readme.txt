=== PageCDN – Better CDN Plugin ===
Contributors: pagecdn
Tags: CDN, Content Delivery Network, Public CDN, Opensource CDN, Private CDN, Shared CDN, SEO, WordPress CDN, WP CDN, Optimize, Performance, Pagespeed
Requires at least: 4.6
Tested up to: 5.2
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Serves static assets from CDN with brotli-11 compression, plus optimizes Public assets and fonts aggressively.


== Description ==

Instead of just replacing hostname with CDN hostname, PageCDN WordPress Plugin serves your static assets such as images, fonts, CSS or JavaScript files in the best way possible. 

For this, it takes each resource separately and tries to get its most optimized version from PageCDN using its Public CDN API. The benefit of loading resources through Public CDN is that such resources are most of the times already available in your visitor's browser saving some bandwidth and loading time. However, if a resource is not available on Public CDN then only as fallback, it simply links the resources through PageCDN, as any other CDN plugin would do, and still get you high brotli compression benefits for compressible resources.

Please note that loading resources from Public CDN reduces your CDN bandwidth cost as Public CDN is available for free.

Also, with PageCDN WordPress Plugin, standard CDN **setup is 10X easier** compared to any other CDN.


= Which resources are available on PageCDN's Public CDN? =

PageCDN's Public CDN hosts the following type of content for free. If this plugin links to such content, **it will not be considered for your CDN bandwidth cost calculation**.
* Opensource Libraries like [jQuery](https://pagecdn.com/lib/jquery), [bootstrap](https://pagecdn.com/lib/bootstrap), [font awesome](https://pagecdn.com/lib/font-awesome), [angular](https://pagecdn.com/lib/angular.js) etc.
* Opensource WordPress Themes (mostly hosted on WordPress Themes).
* Opensource HTML5 themes - like Bootstrap Themes, etc
* Easy Fonts - A replacement of Google fonts with better caching and easy to use CSS font classes.
* Patterns from Subtlepatterns.
* Opensource WordPress Plugins (Coming Soon).

in addition to the above, commercial theme developers may also host their theme files on PageCDN for better cache reuse and performance. However, such Commercial Content is not a part of Public CDN. To know more about whether a theme avails such performance benefits from PageCDN, please consult the theme developer.


== Installation ==
1. Download the archive by clicking the 'Download' button above.
2. Upload the entire `pagecdn` folder to the `/wp-content/plugins/` directory on your site
3. Activate the plugin through the 'Plugins' menu in WordPress admin panel
4. Click the Settings button on plugin and provide PageCDN API key
5. Hit 'Save Changes'.
6. Done :)


== Features ==
**[PageCDN](https://pagecdn.com) Features**
* Full HTTPS and HTTP/2 support
* **Brotli-11 compression** for compressible resources
* Cache reuse across websites where possible so that even your first time visitors gets a chance to load your site as quickly as it does for repeat visitor
* HTTP/2 Server Push support (through PageCDN settings panel)
* **Immutable Caching** support (configurable through PageCDN settings panel)

**Plugin Features**
* 10X easier setup 
* Loads assets through PageCDN Global Edge Network
* Remove Query String from static resources to make them more cacheable
* Cache reuse across sites that use opensource libraries and opensource WordPress themes
* Set directories to be optimized through plugin
* Set directories and file extensions to be ignored
* Automatically optimize DNS lookups and HTTP caching by loading [better optimized fonts](https://pagecdn.com/lib/easyfonts)
* Automatically optimize DNS lookups and delivery by changing resources that load from different Public CDNs to load from single [Public CDN](https://pagecdn.com/dashboard) instead
* Automatically optimize HTTP caching and delivery by searching and linking opensource library files from [Opensource Libraries CDN](https://pagecdn.com/lib)
* Automatically optimize HTTP caching and delivery by searching and linking theme files from [Opensource Themes CDN](https://pagecdn.com/theme)
* Automatically optimize content by searching and linking minified version of files from Public CDN


= System Requirements =
* PHP >=5.6
* WordPress >=4.6


= Contribute =
* Anyone is welcome to contribute to the plugin on [GitHub](https://github.com/pagecdn/better-wordpress-cdn).


= Author =
* [PageCDN](https://pagecdn.com "PageCDN")


== Changelog ==

= 0.1 =
* First release


== Screenshots ==

1. Plugin settings page
