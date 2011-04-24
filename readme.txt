=== Cryptex - EMail Obfuscator+Protector ===
Contributors: Andi Dittrich
Tags: email, obfuscation, protection, image, javascript, encryption, decryption, jquery, mootools, customizable, design, appearance
Requires at least: 3.0
Tested up to: 3.1.1
Stable tag: 1.3.2

Cryptex is a hybrid system to protect EMail addresses on your website (from bots and spiders) by displaying them as images.

== Description ==

The Cryptex plugin for WordPress is used to display email addresses - that are normally expressed in plain text - as an hybrid image automatically. Hybrid means, that the generated addresses, which are displayed on your website, consists of images and text simultanous - simple bots/spiders using image recognition (OCR) of single generated images of addresses have no chance - they have to capture and analyze a screenshot of the whole website to grab the addresses, but this is to performance-heavy and your email addresses are protected ;) It works with telephone numbers, postal addresses also.
Just insert a shortcode like `[cryptex]youraddress@example.com[/cryptex]` to your post - that's it.

= Plugin Features =
* Fully customizable appearance: you can configure font-family, font-size and font-color - everything looks like your theme style
* Protects also EMail hyperlinks by using key-shifting-based encryption/decryption with dynamic keys
* Suitable for high traffic sites - automated caching of dynamic generated images+css
* Native support for MooTools and jQuery is given - also all other frameworks are supported by generic code
    
== Installation ==

= System requirements =
Your webserver configuration has to match the following requirements
- PHP 5
- GD library (v1 or v2)
- GD PNG support

= Installation =
1. Upload the complete `cryptex` folder (Wordpress Plugin) to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings -> Cryptex Obfuscator. In th<e System Info section the first 3 items should be green (*GD Lib installed*, *GD Version*, *PNG Support*) - if not, you cannot use cryptex until you or your hosting provider install the GD library with enabled PNG support. At this time it is normal that *fonts avaible* and *system font path* are marked red.
4. After checking your environment (GD installed) you have to set your systems font path - save these changes. if the path is invalid try another one or upload fonts into the `/wp-content/plugins/cryptex/fonts/` directory and use this as font path
5. Now - all items should be marked green, this means cryptex is ready for use.
6. Go to the bottom of cryptex settings page and select the *font-family*, *font-color* and *font-size* like the styles in your theme
7. You can also use your own/special fonts uploading them into the `/wp-content/plugins/cryptex/fonts/` directory and use this as font path
8. That's it! You're done. You can now enter the following code into a post or page to protect email addresses: [cryptex]youraddress@example.com[/cryptex]

== Frequently Asked Questions ==

= I get an error using the system font pathes, which are shown by the settings page =

This pathes are depending on your hosting environment and can be different - if you don't know the path, please ask your hosting provider or upload the fonts manually into the cryptex-plugin-directory `\wp-content\plugins\cryptex\fonts\` and use this as path. 

= I get an" file permission" error changing the font path =

During security restrictions this pathes, depending on your hosting environment, could be unaccessable. In this case you have to upload TrueTypeFonts (.ttf) into the cryptex-plugin-directory `\wp-content\plugins\cryptex\fonts\` and use this as path. 

= I get an "file permission" php error in my blog =

The directory `/wp-content/plugins/cryptex/cache/` must be writeable - the images will be stored there

= I miss some features / I found a bug =

Well..write a email to Andi Dittrich (andi.dittrich AT a3non.org)

== Screenshots ==

1. Environment informations
2. Plugin appearance configuration
3. Demo post using cryptex
4. Demo post with marked hybrid components (images+text simultanous)

== Changelog ==

= 1.3 =
* First public release.

= 1.3.1 =
* Bugfix: restore of font folder `cryptex/fonts` failed on upgrade

= 1.3.2 =
* Bugfix: restore of css files failed on upgrade
* Bugfix: CSS file only updated after second press on "save changes"
* Bugfix: color parsing error - colors like 0x0000ff not parsed correctly
* Bugfix: CSS font family failure on dynamic generated files
* Added: button to restore default font path `\wp-content\plugins\cryptex\fonts\`
* Added: jQuery [ColorPicker](http://www.eyecon.ro/colorpicker/) to settings page
* Added: support for custom text in cryptex shortcode (like telephone numbers, postal addresses)
* Improoved: new plugin directory structure - moved js+css files
