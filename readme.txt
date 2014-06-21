=== WP External Links (nofollow new window seo) ===
Contributors: freelancephp
Tags: links, external, icon, target, _blank, _new, _none, rel, nofollow, new window, new tab, javascript, xhtml, seo
Requires at least: 3.4.0
Tested up to: 3.9.1
Stable tag: 1.54

Open external links in a new window or tab, adding "nofollow", set link icon, styling, SEO friendly options and more. Easy install and go.

== Description ==

Configure settings for all external links on your site.

= Features =
* Open in new window or tab
* Add "nofollow"
* Choose from 20 icons
* Set other link options (like classes, title etc)
* Make it SEO friendly

= Easy to use =
After activating the plugin all options are already set to make your external links SEO friendly. Optionally you can also set the target for opening in a new window or tab or styling options, like adding an icon.

= On the fly =
The plugin will change the output of the (external) links on the fly. So when you deactivate the plugin, all contents will remain the same as it was before installing the plugin.

= Sources =
* [Documentation](http://wordpress.org/extend/plugins/wp-external-links/other_notes/)
* [FAQ](http://wordpress.org/extend/plugins/wp-external-links/faq/)
* [Github](https://github.com/freelancephp/WP-External-Links)

= Like this plugin? =
[Send your review](http://wordpress.org/support/view/plugin-reviews/wp-external-links-plugin).


== Installation ==

1. Go to `Plugins` in the Admin menu
1. Click on the button `Add new`
1. Search for `WP External Links` and click 'Install Now' OR click on the `upload` link to upload `wp-external-links.zip`
1. Click on `Activate plugin`

== Frequently Asked Questions ==

= I want internal links to be treated as external links. How? =

You could add `rel="external"` to those internal links that should be treated as external. The plugin settings will also be applied to those links.

= Links to my own domain are treated as external links. Why? =

Links pointing to your WordPress site (`wp_url`) are internal links. All other links will be treated as external links.

= All links to my subdomains should be treated internal links. How? =

Add your main domain to the option "Ingore links (URL) containing..." and they will not be treated as external.

[Do you have a question? Please ask me](http://www.freelancephp.net/contact/)

== Screenshots ==

1. Link Icon on the Site
1. Admin Settings Page

== Documentation ==

After activating the plugin all options are already set to make your external links SEO friendly. Optionally you can also set the target for opening in a new window or tab or styling options, like adding an icon.

= Action hook: wpel_ready =
The plugin also has a hook when ready, f.e. to add extra filters:
`function extra_filters($filter_callback, $object) {
	add_filter('some_filter', $filter_callback);
}
add_action('wpel_ready', 'extra_filters');`

= Filter hook 1: wpel_external_link =
The wpel_external_link filter gives you the possibility to manipulate output of the mailto created by the plugin, like:
`function special_external_link($created_link, $original_link, $label, $attrs, $is_ignored_link) {
	// skip links that contain the class "not-external"
	if (isset($attrs['class']) && strpos($attrs['class'], 'not-external') !== false) {
		return $original_link;
	}

	return '<b>'. $created_link .'</b>';
}

add_filter('wpel_external_link', 'special_external_link', 10, 5);`

Now all external links will be processed and wrapped around a `<b>`-tag. And links containing the class "not-external" will not be processed by the plugin at all (and stay the way they are).


= Filter hook 2: wpel_internal_link =
With the internal filter you can manipulate the output of the internal links on your site. F.e.:
`
function special_internal_link($link, $label, $attrs) {
    return '<b>'. $link  .'</b>';
}

add_filter('wpel_internal_link', 'special_internal_link', 10, 3);`

In this case all internal links will be made bold.

= Credits =
* [jQuery Tipsy Plugin](http://plugins.jquery.com/project/tipsy) made by [Jason Frame](http://onehackoranother.com/)
* [phpQuery](http://code.google.com/p/phpquery/) made by [Tobiasz Cudnik](http://tobiasz123.wordpress.com)
* [Icon](http://findicons.com/icon/164579/link_go?id=427009) made by [FatCow Web Hosting](http://www.fatcow.com/)

== Changelog ==

= 1.54 =
* Fixed bug opening links containing html tags (like <b>)

= 1.53 =
* Fixed bug also opening ignored URL's on other tab/window when using javascript
* Changed javascript open method (data-attribute)

= 1.52  =
* Added filter hook wpel_internal_link
* Fixed use_js option bug
* Fixed bug loading non-existing stylesheet
* Minified javascripts

= 1.51 =
* Fixed also check url's starting with //
* Fixed wpel_external_link also applied on ignored links

= 1.50 =
* Removed stylesheet file to save extra request
* Added option for loading js file in wp_footer
* Fixed bug with data-* attributes
* Fixed bug url's with hash at the end
* Fixed PHP errors

= 1.41 =
* Fixed Bug: wpmel_external_link filter hook was not working correctly

= 1.40 =
* Added action hook wpel_ready
* Added filter hook wpel_external_link
* Added output flush on wp_footer
* Fixed Bug: spaces before url in href-attribute not recognized as external link
* Fixed Bug: external links not processed (regexpr tag conflict starting with an a, like <aside> or <article>)
* Cosmetic changes: added "Admin Settings", replaced help icon, restyled tooltip texts, removed "About this plugin" box

= 1.31 =
* Fixed passing arguments by reference using & (deprecated for PHP 5.4+)
* Fixed options save failure by adding a non-ajax submit fallback

= 1.30 =
* Re-arranged options in metaboxes
* Added option for no icons on images

= 1.21 =
* Fixed phpQuery bugs (class already exists and loading stylesheet)
* Solved php notices

= 1.20 =
* Added option to ignore certain links or domains
* Solved tweet button problem by adding link to new ignore option
* Made JavaScript method consistent to not using JS
* Solved PHP warnings
* Solved bug adding own class
* Changed bloginfo "url" to "wpurl"

= 1.10 =
* Resolved old parsing method (same as version 0.35)
* Option to use phpQuery for parsing (for those who didn't experience problems with version 1.03)

= 1.03 =
* Workaround for echo DOCTYPE bug (caused by attributes in the head-tag)

= 1.02 =
* Solved the not working activation hook

= 1.01 =
* Solved bug after live testing

= 1.00 =
* Added option for setting title-attribute
* Added option for excluding filtering certain external links
* Added Admin help tooltips using jQuery Tipsy Plugin
* Reorginized files and refactored code to PHP5 (no support for PHP4)
* Added WP built-in meta box functionallity (using the `WP_Meta_Box_Page` Class)
* Reorganized saving options and added Ajax save method (using the `WP_Option_Forms` Class)
* Removed Regexp and using phpQuery
* Choose menu position for this plugin (see "Screen Options")
* Removed possibility to convert all `<a>` tags to xhtml clean code (so only external links will be converted)
* Removed "Solve problem" options

= 0.35 =
* Widget Logic options bug

= 0.34 =
* Added option only converting external `<a>` tags to XHTML valid code
* Changed script attribute `language` to `type`
* Added support for widget_content filter of the Logic Widget plugin

= 0.33 =
* Added option to fix js problem
* Fixed PHP / WP notices

= 0.32 =
* For jQuery uses live() function so also opens dynamicly created links in given target
* Fixed bug of changing `<abbr>` tag
* Small cosmetical adjustments

= 0.31 =
* Small cosmetical adjustments

= 0.30 =
* Improved Admin Options, f.e. target option looks more like the Blogroll target option
* Added option for choosing which content should be filtered

= 0.21 =
* Solved bug removing icon stylesheet

= 0.20 =
* Put icon styles in external stylesheet
* Can use "ext-icon-..." to show a specific icon on a link
* Added option to set your own No-Icon class
* Made "Class" optional, so it's not used for showing icons anymore
* Added 3 more icons

= 0.12 =
* Options are organized more logical
* Added some more icons

= 0.11 =
* JavaScript uses window.open() (tested in FireFox Opera, Safari, Chrome and IE6+)
* Also possible to open all external links in the same new window
* Some layout changes on the Admin Options Page

= 0.10 =
* Features: opening in a new window, set link icon, set "external", set "nofollow", set css-class
* Replaces external links by clean XHTML <a> tags
* Internalization implemented (no language files yet)
