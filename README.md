# MHC - Magic Home Controller

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg?style=flat-square)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-6.4-blue.svg?style=flat-square)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-3.0.20221231-orange.svg?style=flat-square)](https://github.com/Wilkware/MagicHomeController)
[![Version](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg?style=flat-square)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://img.shields.io/github/actions/workflow/status/wilkware/MagicHomeController/ci.yml?branch=main&label=CI&style=flat-square)](https://github.com/Wilkware/MagicHomeController/actions)

IP-Symcon Modul für die Ansteuerung von WiFi LED Controller der Firma _Magic Home_.

## Inhaltverzeichnis

1. [Funktionsumfang](#user-content-1-funktionsumfang)
2. [Voraussetzungen](#user-content-2-voraussetzungen)
3. [Installation](#user-content-3-installation)
4. [Einrichten der Instanzen in IP-Symcon](#user-content-4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#user-content-5-statusvariablen-und-profile)
6. [Visualisierung](#user-content-6-visualisierung)
7. [PHP-Befehlsreferenz](#user-content-7-php-befehlsreferenz)
8. [Versionshistorie](#user-content-8-versionshistorie)

### 1. Funktionsumfang

Das Modul dient zur Ansteuerung von LED Stripes mittels eines WiFi LED Controllers des Herstellers Magic Home.

Folgende Controller werden unterstützt:

* Original LEDENET
* UFO LED WiFi Controller
* RGBW Controller
* RGBCW Controller
* RGB Controller with MIC
* CCT Ceiling Light
* Smart Switch 1c
* Floor Lamp
* Christmas Light
* Magnetic Light CCT
* Magnetic Light Dimable
* Plant Light
* Smart Socket 2 USB
* Christmas Light
* Spray Light
* Table Light CCT
* Smart Bulb Dimmable
* RGB/WW/CW Controller
* RGB Controller
* Smart Bulb RGBCW
* Single Channel Controller
* Smart Bulb RGBW
* Smart Bulb CCT
* Downlight RGBW
* CCT Controller
* Smart Switch 1C
* Smart Switch 1c Watt
* Smart Switch 2c
* Smart Switch 4c
* Smart Socket 1c
* RGB Symphony v1
* RGB Symphony v2
* RGB Symphony v3
* Digital Light
* Ceiling Light
* Ceiling Light Assist

Erfolgreiche Test wurde mit den Controllern _'RGB Controller'_, _'RGB Symphony v1'_ und _'RGB Symphony v2'_ durchgeführt.
Weiterführende Möglichkeiten und Funktionen wie Warmweiß, Music usw. konnten aus Mangel an Hardware noch nicht implementiert werden.
Aufgrund der technischen Einschränkung von 128 Profilassoziationen werden bei den 'Adressierbaren Effekten' nicht alle 300 Modi unterstützt.

### 2. Voraussetzungen

* IP-Symcon ab Version 6.4

### 3. Installation

* Über den Modul Store das Modul 'Magic Home Controller' installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/MagicHomeController` oder `git://github.com/Wilkware/MagicHomeController.git`

### 4. Einrichten der Instanzen in IP-Symcon

#### Magic Home Discovery

Die Gerätesuche ist über die Glocke oben rechts in der Konsole aufrufbar. Dort über "SYSTEM AUSWÄHLEN" kann das
'Magic Home Discovery'-Modul ausgewählt und installiert werden.

#### Magic Home Controller

Unter "Instanz hinzufügen" ist das _Magic Home Controller_-Modul (Alias: _LED Wifi Controller_, _LED Strips Controller_) unter dem Hersteller _'Magic Home'_ aufgeführt.

__Konfigurationsseite__:

Die Konfiguration beinhaltet die Anzeige der Geräteinformationen (IP-Adresse usw.) und die Auswahl der Farbkanal-Reihenfolge (RGB-Pins).  
Die Reihenfolge der Pins beginnt immer ausgehend vom GND-Pin (12V Pin).

Einstellungsbereich:

> Geräteinformationen ...

Name               | Beschreibung
------------------ | ---------------------------------
Controller-Typ     | Typbezeichnung des Conrtolers (27 mögliche Typen)
Controller-Modell  | Modellbezeichnung des Conrtolers
Controller-IP      | IP-Adresse des Controlers im lokalen WLAN (zb. 192.168.0.10)
Controller-ID      | ID des Controlers (MAC-Adresse des Controlers)

> Erweiterte Einstellungen ...

Name               | Beschreibung
------------------ | ---------------------------------
RGB Pin Belegung   | Reihenfolge der Farb-Kanäle(Pins), Standard ist RGB

Aktionsbereich:

Aktion            | Beschreibung
----------------- | ------------------------------------------------------------
SYNCRONISIEREN    | Auslesen des internen Controler-Setups und Abgleich der betroffenen Statusvariablen.

### 5. Statusvariablen und Profile

Ident         | Name                | Typ       |  Profil                      | Beschreibung
------------- | ------------------- | --------- | ---------------------------- | -------------------------------------------------------
Brightness    | Helligkeit          | Integer   | ~Intensity.100               | Helligkeitswert von 0 bis 100%
Color         | Farbe               | Integer   | ~HexColor                    | Farbwert
Mode          | Modus               | Integer   | MHC.Preset, MHC.Original oder MHC.Addressable | Manueller Farbmodus oder vordefinierter Funktionsmodus (Pattern/Effekte)
Power         | Aktiv               | Boolean   | ~Switch                      | An/Aus Schalter
Speed         | Geschwindigkeit     | Integer   | ~Intensity.100               | Geschwindigkeitswert von 0 bis 100%

Folgende Profile werden angelegt:

Name            | Typ       | Beschreibung
--------------- | --------- | -------------------------------------------------
MHC.Preset      | Integer   | Voreingestelltes Muster (0=Farbmodus, 37 ... 56 = 19 Muster)
MHC.Original    | Integer   | Original Muster (0=Farbmodus, Werte zwischen 1 ... 300 => 126 ausgwählte Effekte)
MHC.Addressable | Integer   | Adressierbare Muster (0=Farbmodus, 1 ... 102, 255 => 103 Effekte)

### 6. Visualisierung

Man kann die Statusvariaben direkt in die Visualisierung verlinken.

### 7. PHP-Befehlsreferenz

```php
void MHC_SetBrightness(int $instanzID, int $brightness);
```

Setzt die Helligkeit auf $brightness. Die Funktion liefert keinerlei Rückgabewert.

```php
void MHC_SetColor(int $instanzID, int $color);
```

Setzt den Farbwert auf $color. Die Funktion liefert keinerlei Rückgabewert.

```php
void MHC_SetMode(int $instanzID, int $mode);
```

Setzt den Anzeigemodus auf auf $mode. Die Funktion liefert keinerlei Rückgabewert.

```php
void MHC_SetPower(int $instanzID, bool $power);
```

Schaltet den Controller Ein(true) bzw Aus(false). Die Funktion liefert keinerlei Rückgabewert.

```php
void MHC_SetSpeed(int $instanzID, int $speed);
```

Setzt die Geschwindigkeit auf $speed. Die Funktion liefert keinerlei Rückgabewert.

### 8. Versionshistorie

v3.0.20221231

* _NEU_: Discovery Modul hinzugefügt
* _NEU_: Erkennen von 27 verschiedene Controllern
* _NEU_: Neues Profilehandling für Effekte (Mode)
* _NEU_: Auslesen des aktuellen Controller-Setups (Sync)
* _NEU_: Kompatibilität auf IPS 6.4 hoch gesetzt
* _FIX_: Pinbelegung der Farbkanäle erweitert und systemweit angewendet
* _FIX_: Controller Modul umbenannt

v2.0.20210701

* _NEU_: Konfigurationsformular vereinheitlicht
* _FIX_: Berechnung der Geschwindigkeit bei Programmen
* _FIX_: Code optimiert und Übersetzungen nachgezogen
* _FIX_: Interne Bibliotheken überarbeitet und vereinheitlicht
* _FIX_: Debug Meldungen überarbeitet
* _FIX_: Dokumentation überarbeitet

v1.2.20190812

* _NEU_: Anpassungen für Module Store
* _NEU_: Vereinheitlichungen, Umstellung auf Libs
* _FIX_: Fehler in Profilen korrigiert

v1.1.20190225

* _NEU_: Umbenennungen, Vereinheitlichungen, StyleCI uvm.

v1.0.20180415

* _NEU_: Initialversion

## Danksagung

Ich möchte mich für die Unterstützung bei der Entwicklung dieses Moduls bedanken bei ...

* _@Spoosie_ : Für die Basisarbeit in seinem Modul (KH\_LEDWiFiController)
* _@Thorsten79_ : Für die Unterstützung und Bereitstellung der 2 Controler (_'RGB Symphony v1'_ & _'RGB Symphony v2'_)
* _@Brutus_, _@lcnrookie_ und _@uwer_ : Als die Unterstützung als Tester

Vielen Dank für die hervorragende und tolle Arbeit!

## Entwickler

Seit nunmehr über 10 Jahren fasziniert mich das Thema Haussteuerung. In den letzten Jahren betätige ich mich auch intensiv in der IP-Symcon Community und steuere dort verschiedenste Skript und Module bei. Ihr findet mich dort unter dem Namen @pitti ;-)

[![GitHub](https://img.shields.io/badge/GitHub-@wilkware-181717.svg?style=for-the-badge&logo=github)](https://wilkware.github.io/)

## Spenden

Die Software ist für die nicht kommerzielle Nutzung kostenlos, über eine Spende bei Gefallen des Moduls würde ich mich freuen.

[![PayPal](https://img.shields.io/badge/PayPal-spenden-00457C.svg?style=for-the-badge&logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

Namensnennung - Nicht-kommerziell - Weitergabe unter gleichen Bedingungen 4.0 International

[![Licence](https://img.shields.io/badge/License-CC_BY--NC--SA_4.0-EF9421.svg?style=for-the-badge&logo=creativecommons)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
