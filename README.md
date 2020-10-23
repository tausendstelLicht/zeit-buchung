# Zeitbuchung

## Einleitung
Dieses Kommandozeilentool unterstützt die Erfassung der Zeit für die spätere oder direkte Zeitbuchung.

## Installation

Um alle benötigten Abhängigkeiten zu installieren, müssen diese mit Hilfe von [Composer](https://getcomposer.org) installiert werden.

```
composer install
```

## Verwendung
Das Tool wird über die Kommandozeile mithilfe von PHP aufgerufen.

`php bin/zeit-buchung.php`

Ohne Mitgabe eines weiteren Parameters/Kommandos, wird eine Übersicht aller Kommandos ausgeben und eine Hilfe angeboten. Mit dem entsprechenden Kommando kann man die Aufnahmesession starten und stoppen. Dabei wird jeweils nur ein Eintrag in eine tägliche Logdatei unter `recordFiles/` geschrieben. Es läuft also kein Hintergrundprozess.

## Verwendung als Docker Image
Das Tool kann auch als Docker Image geladen und ausgeführt werden. Dazu muss das Docker Image lokal erstellt werden und das Wrapperskript `zeitbuchung.sh` in die PATH-Variable aufgenommen werden. Bei Verwendung des Wrapperskriptes werden die Logdateien automatisch im Home-Verzeichnis unter `.zeitbuchung/records` angelegt.

Kommandos werden einfach bei der Ausführung des Wrappers angehängt. Um einen Zeiteintrag zu starten genügt folgender Aufruf: `zeiteintrag.sh start "Eine Nachricht"`

## Docker Image erstellen
Das Docker Image kann mit folgendem Kommando erzeugt werden:

`docker build -t zeitbuchung .`

Alle Abhängigkeiten zu PHP und den Symfony Bibliotheken werden im Buildprozess automatisch installiert.

## Kommandos

### start
`php bin/zeit-buchung.php start [options] <message> [<startTime>]`

Der Startbefehl erzeugt einen Eintrag in der heutigen Logdatei. Das Argument `message` ist obligatorisch.  
Sollte der vorherige Eintrag noch nicht abgeschlossen sein, so wird dieser automatisch beendet. Sollte die Datei nicht existieren, so wird sie automatisch angelegt.
Optional kann das Argument `<startTime>` mitgegeben werden, um den Logeintrag zeitlich unabhängig von der Befehlsausführung zu starten. Ist die Eingabe ungültig, dann wird stattdessen der Zeitpunkt der Befehlsausführung verwendet. 
Die unterstützen Zeitformate sind im 24h Format. Die führende Null muss nicht angegeben werden: 
* hh:mm:ss (z.B. 12:34:16)
* hh:mm (z.B. 06:17)

Zusätzlich kann über die Option `--task` (`-t`) eine Task-ID zur eintragübergreifenden Gruppierung vergeben werden (z.B. `Issue #14`).

Folgende Informationen werden gespeichert:
* Uhrzeit
* Message
* Status
* Task-ID

### stop
`php bin/zeit-buchung.php stop [<stopTime>]`

Der Stopbefehl beendet den letzten Eintrag aus der Logdatei und gibt diesen im Terminal aus. 
Sollte die Datei nicht existieren oder der letzte Logeintrag bereits beendet sein, so wird eine Warnung ausgegeben.
Optional kann das Argument `<stopTime>` mitgegeben werden, um den letzten Logeintrag zeitlich unabhängig von der Befehlsausführung zu stoppen. Ist die Eingabe ungültig oder liegt die angegebene Zeit vor dem Startzeitpunkt, dann wird stattdessen der Zeitpunkt der Befehlsausführung verwendet. 
Die unterstützen Zeitformate sind im 24h Format. Die führende Null muss nicht angegeben werden: 
* hh:mm:ss (z.B. 12:34:16)
* hh:mm (z.B. 06:17)

Folgende Informationen werden gespeichert:
* Uhrzeit
* Status

### status
`php bin/zeit-buchung.php status`

Der Statusbefehl gibt den laufenden Eintrag aus der Logdatei zurück und berechnet die aktuelle Laufzeit. Zusätzlich wird die erfasste Zeit des Tages inklusiver der aktuellen Laufzeit als Summe ausgegeben.

### report
`php bin/zeit-buchung.php report [options] [<date>]`

Der Reportbefehl gibt alle Einträge der Logdatei tabellarisch zurück. Zusätzlich wird die erfasste Zeit als Summe ausgegeben. Mit dem optionalen Argument `<date>` kann ein Datum mitgegeben werden, um eine Logdatei der Vergangenheit aufzurufen. Über die Option `--sort` (`-s`) werden die Einträge nach Task-ID (task) und nach Beschreibung (message) sortiert aufgelistet. Besitzt eine Sortiergruppierung mehr als einen Eintrag, so wird zusätzlich die Summe der Gruppierung ausgegeben. 

Folgende Informationen werden als Spalte ausgegeben:
* start (Startzeit)
* stop (Stopzeit)
* message (Beschreibung)
* time (Zeitdauer)

## Lizenz
Dieses Paket wird unter der [MIT LICENSE](LICENSE) zur Verfügung gestellt.
