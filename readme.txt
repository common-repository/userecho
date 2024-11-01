=== UserEcho for Wordpress ===
Contributors: sstukov, mvr
Donate link: https://userecho.com/
Tags: feedback, users community, widget, ideas, helpdesk, livechat
Requires at least: 2.8
Tested up to: 5.6.2
Stable tag: 1.0.26

Integrate UserEcho - customer feedback and helpdesk system into your blog. Using widget or link. Support SSO.

== Description ==

[UserEcho](https://userecho.com) - this is a new approach for working with clients their wishes ideas and requests. Allows you to quickly create a comfortable environment to communicate with users.

UserEcho for Wordpress plugin - allow to add UserEcho widget to your Wordpress blog.

UserEcho is a web-app that provides a useful way for companies to solicit feedback from customers online. Businesses of all sizes can get feedback from their clients, customers, and followers, and then respond directly to people who submit particularly interesting comments or insightful ideas. Companies that use UserEcho can interact with clients directly and potentially get help generating a few innovative business ideas in the process.

In addition to being able to leave feedback, customers can also vote on the suggestions left by other users. As a business owner, you can then go in and quickly see which ideas have received the most votes as a way to decide which new features to implement first. For businesses that want a better way to monitor their online communities and solicit feedback from interested customers, UserEcho offers an ever-expanding list of features that can be used to connect businesses to their clients online.

[UserEcho SSO integration instruction](https://feedback.userecho.com/topics/11962-sso-login-via-wordpress/)
[Plugin support community](https://feedback.userecho.com/list/1-general/?category=20)

== Installation ==

Follow simple steps to add UserEcho widget to your blog.

1. Upload plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Open plugin settings and fill required fields, only your UserEcho community url and forum id required for initial setup.

== Frequently Asked Questions ==

= Where Can I request suggestions additional for additional features? =

Feel free to ask your suggestions on our [support community](http://feedback.userecho.com/list/1-general/?category=1570).

== Screenshots ==

1. Find out UserEcho feedback widget at the right side.
2. UserEcho widget expands when user clicks on widget tab.
3. Settings screen.
4. Support community dashboard view.

== Changelog ==

= 1.0.26 =
* User avatars support - avatars from WP auto-sync with UserEcho
* added support for Wordpress 5.6.2
= 1.0.25 =
* added support for Wordpress 5.0
= 1.0.24 =
* bug fixes
= 1.0.12 =
* SSO integration - fix bugs in some specific cases
= 1.0.11 =
* SSO integration - mcrypt module is deprecated in php 7.2.0 (replace with openssl)
= 1.0.10 =
* SSO integration - Add case when the WordPress login page is not default (wp-login.php)
= 1.0.9 =
* SSO integration - the bug fixes.
= 1.0.8 =
= 1.0.7 =
* SSO integration - redirect to settings domain only.
* SSO integration - check if redirect URL has any GET parameters when add sso_token
= 1.0.6 =
* Improved SSO integration
* Fixed "Warning" messages in the Plugin settings
= 1.0.5 =
* Updated to support latest SSO version
* Added SSO parameter "email_verified"
* Updated to support last list of languages
* Removed "sso_token" parameter from links if empty
= 1.0.3 =
* Updated to support wordpress up to 3.9.2
* Some description updates
* Added top & bottom positions to tab orientation settings
= 1.0.2 =
* Added support for wordpress from 2.8 and upper
= 1.0.1 =
* Readme files updated to be more informative
= 1.0 =
* Initial release
