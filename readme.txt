=== Plugin Name ===
Turtle Ad Network
Plugin URI: https://www.turtleadnetwork.com
Contributors: gord0b
Donate link: https://t.me/turtleadnetwork
Tags: Ad, Ad Network, blockchain, Turtle Network, TN, peer to peer
Requires at least: 4.9.8
Tested up to: 5.7.1
Stable tag: 1.0.13
Requires PHP: 5.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl.html

== Description ==
The Turtle Ad Network offer Peer-to-Peer Ads. Simple, fast and open source.
- Ad Network utilizes a Wordpress plugin for managing and hosting Ads.
- Ad Network utilizes the Turtle Network blockchain for sending and purchasing Ads.

Website: https://www.turtleadnetwork.com

**Key features & Highlights:**
  * Text Ad Network
  * Cost per Impression (CPI) Ads
  * Peer-to-Peer transactions
  * TANstats - Ad statistics & monitoring
  * Ad payments in TrueUSD (tUSD) or $TN (Receive 100%, no middlemen)
  * Send Ad text and payment in one easy transaction
  * Blacklist, Spam Management, Ad Stop|Start
  * Auto or manual Ad Approval
  * Multi-Ad support, Auto Rotate Ads
  * 0.02 $TN transaction fee per Ad
  * Zero license costs
  
**Requirements:**
  - Wordpress.
  - Wallet address: https://wallet.turtlenetwork.eu
  - Note: tUSD & $TN can be purchased in wallet.
  - Note: View transactions: https://explorer.turtlenetwork.eu

== Installation ==

1. Activate the plugin

2: Visit Wordpress Dashboard > Turtle Ad Network > Settings.
- 'Set API Server' = Server hardset but can be changed.
- 'Payment Type' = Set payment to tUSD or TN.
- 'Minimum Amount' = "Set the Minimum payment required for an Ad to be displayed", a payment that does not meet the minimum will be ignored.
- 'Ad Display Cost / Impressions' = "Set the cost, per amount of Ad impressions displayed, for all Ad Segments, multiple ads will round-robin", monitor Ad status in the 'Ad Approval' page.
- 'BlackList / Spam Management' = "Insert blacklist details, allow auto Ad blocking based on wallet addresses, words or expressions. In the comma separated format 'word1,address,word2'", Ads will be ignored based on blacklist data.
- 'Ad Approval' = "Enable or Disable Ad approvals, if enabled, manually managed in Ad Approvals page, by approve or reject action".

3: Visit Wordpress Dashboard > Turtle Ad Network > Wallet Address.
- 'Address Label' = "Insert a reference label of the Wallet Address"
- 'Wallet Address' = "Insert a TurtleNetwork Wallet Address"
Note: Only use one Wallet Address per Ad Segment.

4: Visit Wordpress Dashboard > Turtle Ad Network > Ad Segments.
- 'Ad Segment Name' = "Insert an Ad Segment reference name"
- 'Assign Wallet Address' = "Assign a Wallet Address, configured in section 3
- 'Ad Size' = "Select an Ad display size"
Click 'Add Ad Segment' to complete the Ad Segment setup and note the new 'Shortcode' created for the Ad Segment.

5: Visit Wordpress Dashboard > Turtle Ad Network > Ad Approval.
- When 'Ad Approval' is activated in the 'Settings' page, Ads will be required to be manually approved by 'Approving' or 'Rejecting' the Ad.
Note: All Ads will be shown on the 'Ad Approval' page except incorrectly formatted Ads, blacklisted Ads.

6: Inserting an Ad Segment into a Wordpress website.
Visit the 'Ad Segments' page and copy the required 'Shortcode', Insert the 'Shortcode' into a Wordpress page, blog, sidebar, widget etc.
Note: Publish your advertising details (wallet, costs etc) - You are now ready to receive an Ad.

7: ## Process for Sending an Ad ##
Send a transaction on the Turtle Network, using the wallet, with the amount of tUSD or $TN required to purchase impressions. In the transaction attachment section, insert the correctly formatted Text Ad details and SEND.

8: Ad details;
- Text Ad Format: 'Ad (headline)(description)(url)'
- Text Ad Example: Ad (Turtle Ad Network)(Ad Network using the TurtleNetwork blockchain)(https://t.me/turtleadnetwork)
- Text Ad submission process: Send a transaction on the TurtleNetwork, with an attachment in the above format, to an assigned address. An assigned address is configured/linked to an Ad Segment.
- Text Ad Note: Maximum of 140 characters allowed, Headline text is bold with 35 character limit & URL is clickable in Ad. Utilize a URL shortener service to track analytics and shorten URL's.
Note: Ads that dont meet to format requirements will be ignored.

9: TANstats: Ad statistics & monitoring.
Note: Query stats by submitting Ad wallet address, no login or personal info required.
- URL query format: https://yourdomain/tanstats?address=submitted_address
- URL query example: https://www.turtleadnetwork.com/tanstats/?address=3Je4mC5SXP7eQ39WmhdHS8PDKfgLNDbxdnF

== Frequently Asked Questions ==

= Will there be Image Ads in future? =

This is an option that is under review.

== Screenshots ==

1. Setting page.
2. Wallet Address page.
3. Ad Segments page.
4. Ad Approval page.
5. TANstats page.

== Changelog ==

= 1.0.13 =
* [Change]: DB tables renamed to standards, backup address & ad segment settings prior.
* [Review]: Wordpress 5.5 support confirmed.
* [Change]: Create buttons renamed.

= 1.0.12.2 =
* [Change]: Ad Approvals" name change to "Ad Manager.
* [Review]: Wordpress 5.4 support confirmed.
* [New]: Plugin Website on github, https://www.turtleadnetwork.com.

= 1.0.12 =
* [New]: TrueUSD (tUSD) payment method added.

= 1.0.11 =
* [New]: TANstats, Ad statistics & monitoring.

= 1.0.10.1 =
* [Fixed]: turtle-ad-network.php-DB error when installing.

= 1.0.10 =
* [Fixed]: DB errors in error_log.
* [Fixed]: Ad display, changed tx image to '#' sign, fix css formatting issues.
* [Fixed]: Ad shortcode didnt work in HTML widget.

= 1.0.9.1 =
* [Fixed]: Ad display issue after enabling v1.0.9.

= 1.0.9 =
* Updated explorer link in Ad from blackturtle.eu to turtlenetwork.eu.
* [New]: Ad Approvals Page: In 'Action' column, added an option to Stop/Start an Ad.
* [New]: Created an Action Flag 'Completed' if the Ads current impression reached purchased impression.
* [Fixed]: Style issue in Ad size 728 X 90, the url was not into the box, adjusted by reducing font size.

= 1.0.8 =
* Initial Production release.
* Settings Page: Hardset a default API server to 'https://ninjastar.ninjaturtle.co.za'.
* Settings Page: Changed the notes formatting, remove space requirement between ')('.
* Ad Approval Page: 'Start Time' changed to 'Start Date' and included the Date & Time the Ad starts.
* Ad Approval Page: 'Txid' column, text changed to clickable hyperlink to the web explorer.
* [Fixed]: Removed the 'Action' column in 'Ad Approval page, when Ad approvals are disabled.
* [Fixed]: Special character escaping in Ad text.

== Upgrade Notice ==

= 1.0.8 =
This version is the initial production release.

= 1.0.9 =
TAN: Two new features added, one minor url update (brand changed) and one issue fixed.

= 1.0.9.1 =
TAN: One issue fixed.

= 1.0.10 =
TAN: Three issues fixed.

= 1.0.10.1 =
TAN: DB install issue fixed, Enjoy your day!.

= 1.0.11 =
TAN: New TANstats feature.

= 1.0.12 =
TAN: New TrueUSD (tUSD) payment method.