PhantomBot WebPanel
===

This is an alternative to the WebPanel developed by the PhantomBot developers.
If you are looking for theirs head over to [The PhantomBot Website](http://www.phantombot.net).

This software is free to user under GNU GENERAL PUBLIC LICENSE Version 2.  
Some of the modules in PhantomBot aren't yet supported in this panel. But I will add these as soon as I can!

###This repository is constantly being updated with cutting edge, untested features! Need a stable version? Check the releases.###

Installation
---
###Prerequisites:###

  * PhantomBot 1.6.6 or later.
  * A php enabled webserver (Try [Apache](http://www.apache.org/))
  * Make sure that the installation directory is writeable by your webserver!
  
###Required Information:###
  * Bot Address: The IP address of your bot, if you've installed PhantomBot locally this will be *localhost*.
  * Bot Name: The Twitch username of the account your bot is using.
  * Bot Oauth Token: This will be in a file called *botlogin.txt*, within the root of the folder you installed PhantomBot in.
  * Channel Owner Name: This will be your Twitch username (or rather username of the channel the bot is active in).
  
###Actual Installation:###
  1. Copy all the files of the release into your webserver's webroot.
  2. Open the website in a browser (If you've installed apache locally this would be *http://localhost*).
  3. Walk through the presented installer.
  4. That's it! Enjoy!

###Updating###
  1. Download the latest release.
  2. Copy all the files over the old installation.
  3. Run call http://webserver/update.php

###Known Issues###
  * The panel is known for not running smoothly when it's in a subfolder. I us thereby recommended to place it in the webroot of your webserver.
  * Install generates permissions error after first form. To circumvent this go back  in your browser and submit the form again.
