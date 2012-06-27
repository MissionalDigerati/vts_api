Install the Video Translation Service API
-----------------------------------------
Server Requirements
===================

This application has been designed for a Linux based server.  It is highly recommended **NOT** to put this on a shared server.  The FFMPEG library can be resource intensive, and get you banned from the hosting provider.  Your server will need to have the following libraries installed:

* PHP 5.2.8 or greater
* Linux
* MySQL (4 or greater)
* [PEAR](http://pear.php.net/)
* [FFMPEG](http://ffmpeg.org/)
* [FFMPEG-PHP](https://github.com/char0n/ffmpeg-php)
* [MP4Box from GPAC library](http://gpac.wp.mines-telecom.fr/)
* SSH Access to  Web Server

Code Install & Setup
====================

1.  Download the CakePHP 2.0 stable version of [CakePHP](http://www.cakePHP.org/)
2.  Upload the cake files to your web server
3.  Get a copy of the code and its submodules: `git clone --recursive https://github.com/MissionalDigerati/video_translator_service.git` (warning submodules)
4.  remove the *app* directory from your web server, and replace with the VTS code you cloned
5.  Copy *Config/core.php.default* to *Config/core.php*, and change settings to fit your environment. Some things to change:
	* Change *debug* to 0 to hide errors
	* Change *Security.salt* and *Security.cipherSeed* to create stronger passwords
	* Change the path at the end of file to match where your PEAR library is installed.  Login to your webhosting via command line, and type `which pear` to find this path.
6.  Setup a database for the app according to your hosting provider's instruction
7.  Use the sql file at *Config/Schema/initial_db.sql* to setup your database
8.  Copy *Config/database.php.default* to *Config/database.php*, and change settings for *default* to match your database settings
9.  Open *Controllers/AppController.php* and remove 'Debugkit.toolbar' from the $components array
10. Open *Config/bootstrap.php* and comment out "CakePlugin::load('DebugKit');"
