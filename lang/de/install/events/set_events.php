<?
$MESS ['ADV_BANNER_STATUS_CHANGE_DESC'] = "#EMAIL_TO# - E-Mail des Nachrichtenempfängers (#OWNER_EMAIL#)
#ADMIN_EMAIL# - E-Mail der User mit \"Banner Manger\" und \"Administrator\" Rollen
#ADD_EMAIL# - E-Mail der Banner Manager
#STAT_EMAIL# - E-Mail der User mit der Berechtigung Bannerstatistik einzusehen
#EDIT_EMAIL# - E-Mail der User mit der Berechtigung einige der Vertragsfelder zu ändern
#OWNER_EMAIL# - E-Mail der User, die alle Rechte für den Vertrag besitzen
#BCC# - BCC (#ADMIN_EMAIL#)
#ID# - Banner ID
#CONTRACT_ID# - Vertrags ID
#CONTRACT_NAME# - Vertragsüberschrift
#TYPE_SID# - Typ ID
#TYPE_NAME# - Typüberschrift
#STATUS# - Status
#STATUS_COMMENTS# - Kommentare zum Status
#NAME# - Bannerüberschrift
#GROUP_SID# - Bannergruppe
#INDICATOR# - Wird der Banner auf der Seite angezeigt?
#ACTIVE# - Flag der Banneraktivität [J | N]
#MAX_SHOW_COUNT# - Maximale Anzahl der angezeigten Banner
#SHOW_COUNT# - Wie oft wurde der Banner auf der Seite angezeigt
#MAX_CLICK_COUNT# - Maximale Anzahl der Bannerklicks
#CLICK_COUNT# - Wie oft wurde auf den Banner geklickt
#DATE_LAST_SHOW# - Datum der letzten Banneranzeige
#DATE_LAST_CLICK# - Datum des letzten Bannerklicks
#DATE_SHOW_FROM# - Startdatum der Banneranzeige
#DATE_SHOW_TO# - Enddatum der Banneranzeige
#IMAGE_LINK# - Link zum Bannerbild
#IMAGE_ALT# - Tooltip des Bildes
#URL# - URL zum Bild
#URL_TARGET# - Wo soll die URL geöffnet werden
#CODE# - Bannercode
#CODE_TYPE# - Bannercode-Typ (text | html)
#COMMENTS# - Kommentare zum Banner
#DATE_CREATE# - Datum der Bannererstellung
#CREATED_BY# - Von wem wurde der Banner erstellt
#DATE_MODIFY# - Datum der Banneränderung
#MODIFIED_BY# - Von wem wurde der Banner geändert
";
$MESS ['ADV_CONTRACT_INFO_DESC'] = "#MESSAGE# - Nachricht
#EMAIL_TO# - E-Mail des Nachrichtenempfängers (#OWNER_EMAIL#)
#ADMIN_EMAIL# - E-Mail der User mit \"Banner Manger\" und \"Administrator\" Rollen
#ADD_EMAIL# - E-Mail der Banner Manager
#STAT_EMAIL# - E-Mail der User mit der Berechtigung die Bannerstatistik einzusehen
#EDIT_EMAIL# - E-Mail der User mit der Berechtigung einige der Vertragfelder zu ändern
#OWNER_EMAIL# - E-Mail der User, die alle Rechte für den Vertrag haben
#BCC# - BCC (#ADMIN_EMAIL#)
#ID# - Vertrags ID
#INDICATOR# - Sollen die Vertragsbanner auf der Seite angezeigt werden?
#ACTIVE# - Flag der Banneraktivität [J | N]
#NAME# - Vertragsüberschrift
#DESCRIPTION# - Vertragsbeschreibung
#MAX_SHOW_COUNT# - Maximale Anzahl der Anzeige für alle Vertragsbanner
#SHOW_COUNT# - Wie oft wurden die Vertragsbanner insgesamt angezeigt
#MAX_CLICK_COUNT# - Maximale Anzahl der Klicks auf alle Vertragsbanner
#CLICK_COUNT# - Anzahl der Klicks auf alle Vertragsbanner
#BANNERS# - Anzahl der Vertragsbanner
#DATE_SHOW_FROM# - Startdatum der Banneranzeige
#DATE_SHOW_TO# - Enddatum der Banneranzeige
#DATE_CREATE# - Datum der Vertragserstellung
#CREATED_BY# - Von wem wurde der Vertrag erstellt
#DATE_MODIFY# - Datum der Vertragsänderung
#MODIFIED_BY# - Von wem wurde der Vertrag geändert
";
$MESS ['ADV_CONTRACT_INFO_MESSAGE'] = "#MESSAGE#
Vertrag: [#ID#] #NAME#
#DESCRIPTION#
>================== Vertragsparameter ==============================

Aktiv: #INDICATOR#

Zeitraum    - [#DATE_SHOW_FROM# - #DATE_SHOW_TO#]
Angezeigt  - #SHOW_COUNT# / #MAX_SHOW_COUNT#
Angeklickt  - #CLICK_COUNT# / #MAX_CLICK_COUNT#
Aktivitätsflag - [#ACTIVE#]

Banner  - #BANNERS#
>=====================================================================

Erstellt  - #CREATED_BY# [#DATE_CREATE#]
Geändert- #MODIFIED_BY# [#DATE_MODIFY#]

Um die Vertragsparameter anzusehen, klicken Sie bitte auf den folgenden Link:
http://#SERVER_NAME#/bitrix/admin/adv_contract_edit.php?ID=#ID#&lang=#LANGUAGE_ID#

Dies ist eine automatisch generierte Nachricht.
";
$MESS ['ADV_BANNER_STATUS_CHANGE_SUBJECT'] = "[BID##ID#] #SITE_NAME#: Bannerstatus wurde geändert - [#STATUS#]";
$MESS ['ADV_CONTRACT_INFO_SUBJECT'] = "[CID##ID#] #SITE_NAME#: Werbevertragsparameter";
$MESS ['ADV_BANNER_STATUS_CHANGE_MESSAGE'] = "Bannerstatus Nr. #ID# hat sich zu [#STATUS#] geändert.

>===================== Bannerparameter ===============================

Banner   - [#ID#] #NAME#
Vertrag  - [#CONTRACT_ID#] #CONTRACT_NAME#
Typ      - [#TYPE_SID#] #TYPE_NAME#
Gruppe   - #GROUP_SID#

----------------------------------------------------------------------

Aktivität: #INDICATOR#

Zeitraum       - [#DATE_SHOW_FROM# - #DATE_SHOW_TO#]
Angezeigt      - #SHOW_COUNT# / #MAX_SHOW_COUNT# [#DATE_LAST_SHOW#]
Angeklickt     - #CLICK_COUNT# / #MAX_CLICK_COUNT# [#DATE_LAST_CLICK#]
Aktivitätsflag - [#ACTIVE#]
Status         - [#STATUS#]
Kommentar:
#STATUS_COMMENTS#
----------------------------------------------------------------------

Bild        - [#IMAGE_ALT#] #IMAGE_LINK#
URL         - [#URL_TARGET#] #URL#

Code: [#CODE_TYPE#]
#CODE#

>=====================================================================

Erstellt  - #CREATED_BY# [#DATE_CREATE#]
Geändert  - #MODIFIED_BY# [#DATE_MODIFY#]

Um die Bannerparameter anzusehen, klicken Sie bitte auf den folgenden Link:
http://#SERVER_NAME#/bitrix/admin/adv_banner_edit.php?ID=#ID#&CONTRACT_ID=#CONTRACT_ID#&lang=#LANGUAGE_ID#

Dies ist eine automatisch generierte Nachricht.
";
$MESS ['ADV_BANNER_STATUS_CHANGE_NAME'] = "Der Bannerstatus wurde geändert";
$MESS ['ADV_CONTRACT_INFO_NAME'] = "Werbevertrag-Parameter";
?>