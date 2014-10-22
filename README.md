```
                       /                        /   /
      -----------__---/__---__------__----__---/---/-
       | /| /  /___) /   ) (_ `   /   ) /___) /   /
      _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___
                   Free Content / Management System
                               /
```
  Copyright 2005-2011 by webspell.org visit webSPELL.org to get webSPELL for free
  * Script runs under the GNU GENERAL PUBLIC LICENSE
  * It's NOT allowed to remove this copyright-tag
  * http://www.fsf.or/licensing/licenses/gpl.html

Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),
Far Development by Development Team - webspell.org


webSPELL is a free Content Management System (CMS), which is available for free at www.webspell.org. The following information should should help you getting started and will give you a first impression of the functionality.


### 1. License

	webSPELL is published under GNU General Public License (GPL). It guarantees the free usage, modification and distribution of the webSPELL script withing the rules of the GPL.
	You are able to find additional information about license at http://www.webspell.org/?site=license

### 2. Installation

	1. Requirements
	2. Upload WebSpell to your webspace
	3. Setting the correct file/folder rights
	4. Do the WebSpell install
	5. Cleaning up

	1. Requirements

	    * Webspace with PHP and mySQL support (PHP >= 4.3, MySQL >= 4.1)
	    * WinRAR to extract the downloaded WebSpell release ---> get it here: http://www.win-rar.com
	    * A FTP program to upload the WebSpell files to your webspace - we recommend SmartFTP



	2. Upload webSPELL to your webspace

	    * Start your above downloaded FTP programm
	    * Connect with this FTP program to your webspace FTP server (you will get the access data for this from your webhoster)
	    * Upload ALL the extracted WebSpell files and folders to your webspace



	3. Setting the correct file/folder rights

		webSPELL needs special access rights on some files and folders. You are able to set this rights with the FTP
		program. For doing this make a right click in the FTP program on the desired files or folders, look for
		Properties/CHMOD (might be named different according to the used ftp program) and click it. There you have to set the permissions for all following files and folders to 777:

	    * demos/
	    * downloads/
	    * images/articles-pics
	    * images/avatars
	    * images/banner
	    * images/bannerrotation
	    * images/clanwar-screens
	    * images/flags
	    * images/gallery/large
	    * images/gallery/thumb
	    * images/games
		  * images/icons/ranks
	    * images/links
	    * images/linkus
	    * images/news-pics
	    * images/news-rubrics
	    * images/partners
	    * images/smileys
	    * images/sponsors
	    * images/squadicons
	    * images/userpics
	    * _mysql.php
	    * _stylesheet.css
	    * tmp/



	4. Do the WebSpell install

	    * Open your webbrowser
	    * Enter the path to the webspell install folder http://[hostnameofyouwebspace]/install (substitute [hostnameofyouwebspace] with the correct domain name (and maybe additional path name if you uploaded webSPELL to some sub-folder) where you have uploaded webSPELL.
	    * Follow the installation steps and enter the correct data



	5. Cleaning up

	    * Reset the access rights of _mysql.php back to 644 with the FTP program
	    * Delete the complete install/ folder from your webspace with the FTP program

	Now your webSPELL Page is ready.

### 3. Related links

	http://www.webspell.org/?site=forum
		Bulletin boards for support and communication between user (problems, modifications, templates, addons, etc.)
	http://www.webspell.org/?site=faq
		Frequently Asked Questions (FAQ) for webSPELL. Answers and solutions for frequently asked questions and problems
	http://www.webspell.org/?site=irc
		Access via browser to webSPELL chat (IRC) for live support. Channel: #webspell @ Quakenet
