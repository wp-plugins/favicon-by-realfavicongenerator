=== Favicon by RealFaviconGenerator ===
Contributors: phbernard
Tags: favicon, apple-touch-icon, realfavicongenerator
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 1.1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create and install your favicon for all platforms: PC/Mac of course, but also iPhone/iPad, Android devices, Windows 8 tablets...

== Description ==

Generate and setup a favicon for desktop browsers, iPhone/iPad, Android devices, Windows 8 tablets and more. In a matter of seconds, design an icon that looks great on all major platforms.

Favicon is not just a single `favicon.ico` file dropped in the middle of your site. Nowadays, with so many different platforms and devices, you need a bunch of pictures to get the job done. With RealFaviconGenerator, generate all the icons you need for desktop browsers, iPhone/iPad, Android devices, Windows 8 devices, and more.

iOS devices use a high resolution Apple touch icon to illustrate bookmarks and home screen shortcuts. A first generation iPhone needs a 57x57 picture, whereas a brand new iPad with Retina screen looks for a 152x152 picture. Android Chrome also use these pictures if it finds them. Windows 8 takes another route with a dedicated set of icons and HTML declarations.

Favicon is not only a matter of pictures with different resolutions. The various platforms coms with different UI guidelines. For example, the classic desktop favicons often use transparency. But iOS requires opaque icons. And Windows 8 has its own recommendations.

Save hours of research and image edition with RealFaviconGenerator and its companion plugin. In a matter of seconds, you setup a favicon compatible with:

-  Windows (IE, Chrome, Firefox, Opera, Safari)
-  Mac (Safari, Chrome, Firefox, Opera, Camino)
-  iOS (Safari, Chrome, Coast)
-  Android (Chrome, Firefox)
-  Surface (IE)
-  And more

We take compatibility very seriously. See http://realfavicongenerator.net/favicon_compatibility for the full list.

** Localization **

* English (`en_EN`) by [Philippe Bernard](http://realfavicongenerator.net/)
* French (`fr_FR`) by [Philippe Bernard](http://realfavicongenerator.net/)
* Swedish (`sv_SE`) by [Linus Wileryd](https://twitter.com/wileryd)

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Favicon by RealFaviconGenerator'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard
5. Navigate to the 'Favicon' entry in the Appearance menu
6. Select a master picture from the Media Library (optional)
7. Click the 'Generate Favicon' button and follow the instructions.

= Using FTP =

1. Download `favicon-by-realfavicongenerator.zip`
2. Extract the `favicon-by-realfavicongenerator` directory to your computer
3. Upload the `favicon-by-realfavicongenerator` directory to the `/wp-content/plugins/` directory
4. Activate the plugin on the Plugin dashboard
5. Navigate to the 'Favicon' entry in the Appearance menu
6. Select a master picture from the Media Library (optional)
7. Click the 'Generate Favicon' button and follow the instructions.

== Screenshots ==

1. Initial favicon setup screen. You are invited to setup your favicon.
2. Select a master picture from the Media Library (optional)
3. Once you hit the Generate Favicon button, you are redirected to <a href="http://realfavicongenerator.net/">RealFaviconGenerator</a>, 
where you can design your favicon: adding a background to your iOS picture, using a saturated version of your master picture for Windows 8...
4. When you are done with the favicon editor, you are redirected to the WordPress Dashboard. The favicon is installed automatically.
This screen presents you a preview of the favicon you various platforms, so you know how your blog looks like on various platforms.

== Changelog ==

= 1.1.0 =

- Run RealFaviconGenerator's favicon checker from the admin interface.

= 1.0.7 =

- Deactivate default Genesis favicon when one is configured in FbRFG.

= 1.0.6 =

- Error management improved during favicon install.

= 1.0.5 =

- Do not try to rewrite the favicon files URL when .htaccess is not writable.

= 1.0.4 =

- Option to not rewrite the favicon files URL, even when this is possible.

= 1.0.3 =

- Plugin code syntax changed to fit older versions of PHP.

= 1.0.2 =

- Callback URL was too long for some servers. It has been shorten.

= 1.0.1 =

- Favicon admin settings are now in the Appearance menu.
- Fix in favicon package download.
- Fix in error management during favicon installation. 

= 1.0 =
Initial version.
