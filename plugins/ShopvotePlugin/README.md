Installationsanleitung
====

# Important
This is possibly outdated!

### Installation
Aktivieren Sie zunächst die gewünschte Funktion im SHOPVOTE Händlerbereich.

### EasyReviews
Zur Einbindung müssen Sie im Händlerbereich (unter "EasyReviews" -> "URL der Checkout-Seite")
die Zieladresse der Bestellbestätigungsseite hinterlegen. Sofern Sie keine Sonderlösung haben,
lautet die Adresse https://ihrshop.de/checkout/finish.
Der HTML Code und das JavaScript werden vom Plugin automatisch auf der Checkout Seite ausgespielt.
Jedoch wird dazu der API Key benötigt, für welches ein Konfigurationsfeld im Plugin hinteregt ist.

### RatingStars
Die Integration erfolgt ganz einfach über die Verwendung folgender
SHOPVOTE-Grafiken "AllVotes", "Schwebendes Badge", "VoteBadge klein" und
"VoteBadge groß".
Die Grafiken finden Sie im Händerbereich von SHOPVOTE unter "Grafiken & Siegel"
Haben Sie sich eie Grafik ausgewählt, lassen Sie sich den Code anzeigen.
Kopieren Sie den Code in das Feld "RatingStars Code".

### Produktbewertungen
Aktivieren Sie die Produktbewertungen über das Konfigurationsformular "Produktbewertungen aktivieren".
Nach Aktivierung muss das Theme neu kompiliert werden, damit benötigtes JavaScript und Less auf
der Artikeldetailseite ausgespielt wird.
Im Checkout werden gekaufte Artikel nun an SHOPVOTE übertragen. Im Nachgang werden Ihre Kunden zur Bewertung aufgefordert.
Abgegebene Bewertungen werden per Schnittstelle von der Artikeldetailseite angefordert und im Tab für Bewertungen dargestellt.

### Konfiguration speichern
Nachdem Sie die Felder ausgefüllt haben, speichern Sie die Konfiguration und
leeren sie danach den Shopcache, damit die Änderungen auch im Frontend
angezeigt werden. Mit dem Feature der Produktbewertungen wird zusätzliches JavaScript und
Less benötigt. Dieses wird beim kompilieren des Themes automatisch mit aufgenommen und
steht daraufhin im Frontend zur Verfügung.

### Maßgeschneidertes Template?
Das Plugin erweitert das Standardtemplate "frontend/index/index.tpl" von Shopware.
Darin befindet sich ein Block "frontend_index_page_wrap" welcher erweitert wird.
Wird ein anderes Template verwendet, welches diesen Block nicht mehr implementiert,
kann die Template-Erweiterung nicht greifen. Bitte binden Sie den Block in Ihr
Angepasstes Template wieder ein. Da JavaScript ausgespielt wird, sollte der
Block nach des schießenden body Tags hinterlegt werden.
