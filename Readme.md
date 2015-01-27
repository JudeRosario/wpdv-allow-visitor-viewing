## Addon for the Post Voting Plugin (WPMUDEV)

__This is a Tiny Add on to the [Post Voting Plugin](https://premium.wpmudev.org/project/post-voting-plugin/), it allows for limiting voting to only logged-in users, but have the vote totals visible to everyone including visitors.__

As of this writing the base plugin can either

(a) Allow voting for everyone and vote totals are visible to non-logged in users, OR
(b) Allow voting for logged-in users only and vote totals are invisible for non-logged users.

This addon supports both stars and the icons sets from the core plugin and echos a disabled widget if user is not logged in.

### Usage 

![Screenshot](http://i59.tinypic.com/rbwppu.png)

#### Addon Mode

Simply download or copy paste the PHP file into the `wpmu-dev-post-votes/lib/plugins` folder and you will have a new option to as part of the addons list. Simply click activate and you are done.

#### Standalone plugin 

Download the zip file and upload it as part of the upload new plugin screen from the admin. It will show up in the list of installed plugins, simply click activate and it will work. You will have a new option added as part of the addons list.

#### MU Plugin

Open up your `mu-plugins` folder for your WordPress Directory (or create one) usually located within `wp-content` folder of your `public_html` folder. Select the PHP file `wdpv-allow-visitor-viewing.php` and place it into your `wp-content/mu-plugin` folder in your WordPress Directory. You will have a new option added as part of the addons list, click activate and you are done.

