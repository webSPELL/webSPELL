[ ![Codeship Status for webSPELL/webSPELL](https://codeship.com/projects/655247c0-f3d1-0131-ce29-02087275ebc8/status?branch=dev)](https://codeship.com/projects/27970) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webSPELL/webSPELL/badges/quality-score.png?b=dev)](https://scrutinizer-ci.com/g/webSPELL/webSPELL/?branch=dev) [![Build Status](https://scrutinizer-ci.com/g/webSPELL/webSPELL/badges/build.png?b=dev)](https://scrutinizer-ci.com/g/webSPELL/webSPELL/build-status/dev) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/ecedfa24-b238-45fb-9e5e-47850bfb499d/mini.png)](https://insight.sensiolabs.com/projects/ecedfa24-b238-45fb-9e5e-47850bfb499d) [![Build Status](https://snap-ci.com/webSPELL/webSPELL/branch/dev/build_image)](https://snap-ci.com/webSPELL/webSPELL/branch/dev) [![Dependency Status](https://david-dm.org/webspell/webspell.svg)](https://david-dm.org/webspell/webspell) [![devDependency Status](https://david-dm.org/webspell/webspell/dev-status.svg)](https://david-dm.org/webspell/webspell#info=devDependencies) [![Crowdin](https://d322cqt584bo4o.cloudfront.net/webspell-cms/localized.png)](http://translate.webspell.org/project/webspell-cms)

```
                       /                        /   /
      -----------__---/__---__------__----__---/---/-
       | /| /  /___) /   ) (_ `   /   ) /___) /   /
      _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___
                   Free Content / Management System
                               /
```
  Copyright 2005-2015 by webSPELL.org visit webSPELL.org to get webSPELL for free
  * Script runs under the GNU GENERAL PUBLIC LICENSE
  * It's NOT allowed to remove this copyright-tag
  * http://www.fsf.or/licensing/licenses/gpl.html

Code based on webSPELL Clanpackage (Michael Gruber - webSPELL.at),
Far Development by Development Team - webSPELL.org


webSPELL is a free Content Management System (CMS), which is available for free at www.webSPELL.org. The following information should should help you getting started and will give you a first impression of the functionality.


### 1. License

	webSPELL is published under GNU General Public License (GPL). It guarantees the free usage, modification and distribution of the webSPELL script withing the rules of the GPL.
	You are able to find additional information about license at http://www.webSPELL.org/?site=license

### 2. Installation

	1. Requirements
	2. Upload webSPELL to your webspace
	3. Setting the correct file/folder rights
	4. Do the webSPELL install
	5. Cleaning up

	1. Requirements

	    * Webspace with PHP and mySQL support (PHP >= 5.3, MySQL >= 4.1)
	    * (g)unzip/tar to extract the downloaded webSPELL release
	    * A FTP program to upload the webSPELL files to your webspace - we recommend SmartFTP



	2. Upload webSPELL to your webspace

	    * Start your above downloaded FTP programm
	    * Connect with this FTP program to your webspace FTP server (you will get the access data for this from your webhoster)
	    * Upload ALL the extracted webSPELL files and folders to your webspace



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



	4. Do the webSPELL install

	    * Open your webbrowser
	    * Enter the path to the webSPELL install folder http://[hostnameofyouwebspace]/install (substitute [hostnameofyouwebspace] with the correct domain name (and maybe additional path name if you uploaded webSPELL to some sub-folder) where you have uploaded webSPELL.
	    * Follow the installation steps and enter the correct data



	5. Cleaning up

	    * Reset the access rights of _mysql.php back to 644 with the FTP program
	    * Delete the complete install/ folder from your webspace with the FTP program

	Now your webSPELL Page is ready.

### 3. Related links

	http://www.webSPELL.org/?site=forum
		Bulletin boards for support and communication between user (problems, modifications, templates, addons, etc.)
	http://www.webSPELL.org/?site=faq
		Frequently Asked Questions (FAQ) for webSPELL. Answers and solutions for frequently asked questions and problems
	http://github.com/webSPELL/webSPELL
		Official webSPELL Github Repo (code, issues, wiki)
