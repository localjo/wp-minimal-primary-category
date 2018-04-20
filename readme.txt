=== Minimal Primary Category ===
Contributors: josiahsprague
Donate link: http://josiahsprague.com/
Tags: category, categories, primary
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: trunk
License: MIT
License URI: http://opensource.org/licenses/MIT

A plugin that lets you designate a primary category for posts.

== Description ==

A plugin that lets you designate a primary category for posts with multiple categories.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I set a primary category for a post? =

First, assign multiple categories to a post. Then you will see a "Make primary" button next to the categories in the post editor. Click on that button to designate a category as the primary.

= How do I display the primary category of a post in a theme? =

You can use `mp_category_get_primary_category()` to access the primary category. Example;

```
if ( function_exists( 'mp_category_get_primary_category' ) ) {
  $primary_cat = mp_category_get_primary_category( get_the_ID() );
  echo $primary_cat->name;
}
```

= 0.0.1 =
* Initial version
