<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2011 by webspell.org                                  #
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
##########################################################################
*/

$language_array = Array(

/* do not edit above this line */

  'access_denied'=>'Hozzáférés megtagadva',
  'additional_options'=>'További beállítások',
  'admin_email'=>'Admin E-mail',
  'admin_name'=>'Admin neve',
  'allow_usergalleries'=>'Felhasználói galériák engedélyezése',
  'archive'=>'Arhívum',
  'articles'=>'Cikkek',
  'autoresize'=>'Kép átméretezés funkció',
  'autoresize_js'=>'JavaScript-el',
  'autoresize_off'=>'Kikapcsolva',
  'autoresize_php'=>'PHP-vel',
  'awards'=>'Díjak',
  'captcha'=>'Captcha',
  'captcha_autodetect'=>'Automatikus',
  'captcha_bgcol'=>'Háttérszín',
  'captcha_both'=>'Mindkettő',
  'captcha_fontcol'=>'Betűszín',
  'captcha_image'=>'Kép',
  'captcha_linenoise'=>'Zavaró vonalak',
  'captcha_noise'=>'Zavaró pixelek',
  'captcha_only_math'=>'Csak számok',
  'captcha_only_text'=>'Csak szöveg',
  'captcha_text'=>'Szöveg',
  'captcha_type'=>'Captcha típus',
  'captcha_style'=>'Captcha stílus',
  'clan_name'=>'Klán neve',
  'clan_tag'=>'Klán TAG',
  'clanwars'=>'Warok',
  'comments'=>'Hozzászólások',
  'content_size'=>'Tartalom mérete',
  'default_language'=>'Alapértelmezett nyelv',
  'demos'=>'Demók',
  'forum'=>'Fórum',
  'forum_posts'=>'Fórum bejegyzések',
  'forum_topics'=>'Fórum témák',
  'gallery'=>'Galéria',
  'guestbook'=>'Vendégkönyv',
  'headlines'=>'Címsorok',
  'insert_links'=>'Tagok linkjeinek beillesztése',
  'latest_articles'=>'Legfrissebb cikkek',
  'latest_results'=>'Legfrissebb eredmények',
  'latest_topics'=>'Legfrissebb témák',
  'login_duration'=>'Bejelentkezés hossza',
  'max_length_headlines'=>'Címsorok max. hossza',
  'max_length_latest_articles'=>'Legfrissebb cikkek max. hossza',
  'max_length_latest_topics'=>'Legfrissebb témák max. hossza',
  'max_length_topnews'=>'Kiemelt hírek max. hossza',
  'max_wrong_pw'=>'Max. hibás jelszó',
  'messenger'=>'Üzenetküldő',
  'msg_on_gb_entry'=>'Üzenet új vendégkönyv bejegyzéskor',
  'news'=>'Hírek',
  'other'=>'Egyéb',
  'page_title'=>'Kezdőlap címe',
  'page_url'=>'Kezdőlap URL',
  'pagelock'=>'Karbantartás',
  'pictures'=>'Képek',
  'profile_last_posts'=>'Friss profil bejegyzések',
  'public_admin'=>'Közös terület admin',
  'registered_users'=>'Regisztrált tagok',
  'search_min_length'=>'Keresés min. hossza',
  'settings'=>'Beállítások',
  'shoutbox'=>'Üzenőfal',
  'shoutbox_all_messages'=>'Üzenőfal összes üzenet',
  'shoutbox_refresh'=>'Üzenőfal frissítése',
  'space_user'=>'Felhasználónkénti szabad hely (MByte)',
  'thumb_width'=>'Előnézet szélesség',
  'tooltip_1'=>'Ez az oldalad címe pld. (yourdomain.com/path/webspell).<br /> "http://" előtag nélkül és "/" jel nélkül a végén!<br />Így kell kinéznie:',
  'tooltip_2'=>'Ez weboldalad címe, mely a címsorban jelenik meg',
  'tooltip_3'=>'A szervezeted neve',
  'tooltip_4'=>'A szervezeted rövid neve [TAG]',
  'tooltip_5'=>'A webmester neve = a te neved',
  'tooltip_6'=>'A webmester e-mail címe',
  'tooltip_7'=>'A megjelenítendő hírek száma',
  'tooltip_8'=>'Fórum témák oldalanként',
  'tooltip_9'=>'Képek oldalanként',
  'tooltip_10'=>'Az arhívumban megjelenített hírek száma oldalanként',
  'tooltip_11'=>'Fórum bejegyzések oldalanként',
  'tooltip_12'=>'A galériában megjelenő előnézet mérete (szélessége)',
  'tooltip_13'=>'Az "sc_headlines" által felsorolt címsorok',
  'tooltip_14'=>'A legfrissebb témákként megjelenített témák',
  'tooltip_15'=>'Fenntartott szabad hely a felhasználói galériák számára Mbyte-ban megadva',
  'tooltip_16'=>'A címsorok max. hossza az "sc_headlines"-ban',
  'tooltip_17'=>'A keresési feltételek minimális hossza',
  'tooltip_18'=>'Minden felhasználó számára biztosítani akarsz saját galériát?',
  'tooltip_19'=>'A galéria képeit a saját oldaladon szeretnéd adminisztrálni? (ajánlott)',
  'tooltip_20'=>'Cikkek oldalanként',
  'tooltip_21'=>'Díjak oldalanként',
  'tooltip_22'=>'Az "sc_articles" által felsorolt cikkek',
  'tooltip_23'=>'Demók oldalanként',
  'tooltip_24'=>'A cikkek neveinek max. hossza az "sc_articles"-ben',
  'tooltip_25'=>'Vendégkönyv bejegyzések oldalanként',
  'tooltip_26'=>'Hozzászólások oldalanként',
  'tooltip_27'=>'Üzenetek oldalanként',
  'tooltip_28'=>'Warok oldalanként',
  'tooltip_29'=>'Regisztrált tagok oldalanként',
  'tooltip_30'=>'Az "sc_results" által felsorolt eredmények',
  'tooltip_31'=>'A profilokban felsorolt friss postok',
  'tooltip_32'=>'Az "sc_upcoming" által felsorolt bejegyzések',
  'tooltip_33'=>'A bejelentkezés időtartama [órában] (0 = 20 perc)',
  'tooltip_34'=>'A tartalom max. mérete (szélessége) (képek, szöveges területek stb.) (0 = kikapcsolva)',
  'tooltip_35'=>'A tartalom max. mérete (magasság) (képek) (0 = kikapcsolva)',
  'tooltip_36'=>'A visszajelzésekért felelős adminok kapjanak üzenetet új vendégkönyv bejegyzés érkezésekor?',
  'tooltip_37'=>'Megjelenő Üzenőfal bejegyzések',
  'tooltip_38'=>'Az elmentett Üzenőfal bejegyzések max. száma',
  'tooltip_39'=>'Az Üzenőfal frissítésének időköze (másodpercben',
  'tooltip_40'=>'A weboldal alapértelmezett nyelve',
  'tooltip_41'=>'A linkek beillesztése automatikusan a tagok profiljába?',
  'tooltip_42'=>'A témák max. hossza a legfrissebb témákban',
  'tooltip_43'=>'Az IP ban előtti maximális rossz jelszavak száma',
  'tooltip_44'=>'A captcha kijelzésének típusa',
  'tooltip_45'=>'A captcha háttérszíne',
  'tooltip_46'=>'A captcha betűszíne',
  'tooltip_47'=>'A captcha tartalmának stílusa',
  'tooltip_48'=>'A zavaró pixelek száma',
  'tooltip_49'=>'A zavaró vonalak száma',
  'tooltip_50'=>'Az automatikus képátméretező típusának kiválasztása',
  'tooltip_51'=>'A kiemelt hírek max. hossza az "sc_topnews"-ban',
  'transaction_invalid'=>'A művelet azonosítója érvénytelen',
  'upcoming_actions'=>'Közeledő események',
  'update'=>'Frissítés'
);
?>