 ########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2014 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
 ########################################################################


DEUTSCH

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Vielen Dank, dass du dich für webSPELL 4 entschieden hast.
webSPELL ist ein freies Content Management System (CMS), welches kostenlos auf www.webspell.org zur Verfügung steht. Die nachfolgenden Informationen sollen den Einstieg in das System erleichtern und einen ersten Eindruck der Funktionsweise vermitteln.

1. Lizenz
2. Installation
3. Weiterführende Links

1. Lizenz

	webSPELL wird unter der GNU General Public License (GPL) veröffentlicht. Diese gestattet die freie Verwendung, Modifikation und Weitergabe des webSPELL-Skriptes im Rahmen der Regeln der GPL.
	Weiterführende Informationen gibt es unter http://www.webspell.org/?site=license

2. Installation

	1. Anforderungen
	2. webSPELL auf den Webspace heraufladen
	3. Die korrekten Datei- und Ordnerrechte setzen
	4. webSPELL Installation ausführen
	5. Aufräumen
	
	1. Anforderungen
	
	    * Webspace mit PHP- und MySQL-Unterstützung (PHP >= 4.3, MySQL >= 4.1)
	    * WinRAR zum Entpacken des webSPELL Paketes ---> Download unter http://www.winrar.de
	    * Ein FTP-Programm um webSPELL auf den Webspace hochzuladen - Empfehlung: SmartFTP (http://www.smartftp.com)
	
	2. webSPELL auf den Webspace hochladen
	
	    * Starte dein zuvor installiertes FTP-Programm
	    * Verbinde mit diesem FTP-Programm zu dem FTP-Server des Webspaces (die Zugangsdaten bekommst du von deinem Webhoster)
	    * Lade alle entpackten Dateien und Ordner des webSPELL Paketes auf deinen Webspace
	
	3. Die richtigen Datei- und Ordnerrechte setzen
	
		webSPELL benötigt spezielle Zugriffsrechte für einige Dateien und Ordner. Diese Rechte können mit dem FTP-Programm vergeben werden. Um dies umzusetzen, klicke in dem FTP-Programm rechts auf die gewünschten Dateien und Ordner und suche nach etwas namens "Eigenschaften", "chmod", "Properties" etc. (die bezeichnung variiert von FTP-Programm zu FTP-Programm). Folgende Dateien und Ordner nun auf chmod 777 setzen:
		
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
	
	4. webSPELL Installation ausführen
	
	    * Öffne deinen Webbrowser
	    * Gib den Pfad zur webSPELL Installation http://[hostnamedeinerseite]/install ein (ersetze [hostnamedeinerseite] mit der Domain (und evtl. einer Pfaderweiterung, falls du webSPELL in irgendeinen Unterordner hochgeladen hast), wo du webSPELL hochgeladen hast)
	    * Folge den Anweisungen der Installationsschritte und gib die erforderlichen Daten ein
	
	5. Aufräumen
	
	    * Setze die Zugriffsrechte der _mysql.php mit dem FTP-Programm auf 644 zurück
	    * Lösche den kompletten install-Ordner von deinem Webspace mit dem FTP-Programm
	
	Nun sollte deine webSPELL Seite fertig eingerichtet sein.


3. Weiterführende Links

	http://www.webspell.org/?site=forum
		Forum für Support und Austausch zwischen Nutzern (Probleme, Modifikationen, Templates, Addons, etc.)
	http://wiki.webspell.org
		Wiki für webSPELL. Hilfe im Umgang mit dem System, Linksammlungen, etc.
	http://www.webspell.org/?site=faq
		Frequently Asked Questions (FAQ) für webSPELL. Antworten und Lösungsansätze für häufige gestellte Fragen und Probleme
	http://www.webspell.org/?site=irc
		Zugang via Browser zum webSPELL Chat (IRC) für Live-Support. Channel: #webspell @ Quakenet