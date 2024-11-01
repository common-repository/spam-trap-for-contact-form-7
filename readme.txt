=== Spam Trap for Contact Form 7 ===
Contributors: softsmart.co.za
Tags: spam, contact form 7, contact form 7 anti spam, anti spam, comment spam
Requires at least: 5.0
Tested up to: 6.1.1
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds spam checks to catch contact form 7 spam. It does so in the background without annoying your legitimate users with captchas.

It adds a hidden field to the your contact form 7 form and then checks when a contact form 7 is submitted for that field. If its not present, or its filled in, then we can be sure that it was a bot of some sort.

It also adds the ability to check for links in the your-message field (assuming you used it) and if there are more than n links (where n is configurable in the admin area) then it also assumes its spam.

It also allows you to turn on / off email notices of blocked spam or legitimate message. This should be off under normal circumstances but its nice to test with initially to see that it works!


== Description ==

This plugin adds spam checks to catch contact form 7 spam. It does so in the background without annoying your legitimate users with captchas. Simply install and activate. Optionally set limits for how many links you'll allow in a comment and set up email notices of blocked or allowed form entries (useful for test and evaluation)

== Installation ==

1. Use Wordpress's  auto plugin installer. Search for SPAM TRAP FOR CONTACT FORM 7
2. Download a zip version from http://softsmart.co.za/2017/01/08/spam-trap-contact-form-7/ and use wordpress plugin uploader

== Changelog ==

= 1.0.0 =
* Initial version

= 1.0.1 =
* Fixed bugs in saving settings.
* Added message field name to change which field is filtered in the contact form 7 form.
* Changed filter to work in the skip_mail filter rather than the before send action because in before send the skip_mail property has been made private so we can't set it if needs be

= 1.0.2 =
* Checks for blank messages and counts as spam if found. This is because spammers are now putting their message into the subject and leaving the message blank

= 1.0.3 =
* We were filter_santizing_string'ing the CF7 message before counting links. The problem is that if there are spammy links like <a href=\"http://www.example.com\">example</a> then the filter_var removes those and they're not counted (not present)

= 1.0.4 =
* Added an icon to the assets directory

= 1.0.5 =
* Added an icon to the assets directory and fixed a typo

= 1.0.6 =
* removed assets directory incorrectly placed in trunk

= 1.1.0 =
* Added block email addresses (in addition to blocked words and block name fields)
* Added in option to send blocked messages to dnsbl.softsmart.co.za for review and addition into that block list

= 1.1.1 =
* Code review and tyding up (escaping output / santizing input, code sniffer etc)