# 1.2.6
- Behebung des Problems, das Formulare mit Pflichtfeldern auch ohne entsprechende Eingaben abgesendet werden konnten

# 1.2.5
- Man kann nun festlegen, ob die Datenschutzbestimmung auch ohne AGB angezeigt oder ein eigener Textbaustein für den Text verwendet werden soll

# 1.2.4
- Es gibt nun unterhalb von "Kunden" und "Anfragen" einen Schalter "Beantwortet", insofern die Erweiterung nur zum Sammeln von Anfragen genutzt wird

# 1.2.3
- Die Herkunft, der Herkunftswert und die individuelle ID der Herkunft werden nun auch in die versteckten Felder des Formulars übertragen, wenn das Formular in einer Modalbox angezeigt wird und der Link auf die Modalbox die CSS-Klasse period-request-form-modal-btn und die Attribute data-origin, data-origin-name und data-origin-id mit den entsprechenden Werten hat

# 1.2.2
- Es werden nun keine leeren Anfragen mehr erzeugt, wenn die Route zum Verarbeiten des Formulars direkt aufgerufen wird
- Der Bestätigungstext nach dem erfolgreichen Absenden des Formulars wird nun nicht mehr in der Konfiguration des Formulars, sondern im Textbaustein mit dem Namen "tmms.periodRequestForm.confirmationText" gepflegt, bei dem alle eingegebenen Daten des Formulars zur Verfügung stehen
- Die Pflichtfeldprüfung des Datumsfeldes wurde verbessert 
- Das Formular wird nun über die Formular-Absenden-Ajax-Funktion direkt von Shopware 6 versendet

# 1.2.1
- Man kann nun bis zu 10 freie Eingaben festlegen und anzeigen lassen

# 1.2.0
- Kompatibilität zu Shopware ab Version 6.6.0.0 hergestellt

# 1.1.7
- Kompatibilität zu Shopware ab Version 6.5.8.2 hergestellt

# 1.1.6
- Kompatibilität zu Shopware ab Version 6.5.8.0 hergestellt

# 1.1.5
- Die Beschriftung des Zeitraum- oder Datumsauswahlfeldes wird nun auch mit in die E-Mail übertragen, insofern im bestehenden E-Mail-Template der Wert "Zeitraum" und "period" durch {{ periodRequestFormData.labelDate }} ersetzt wurde

# 1.1.4
- Man kann nun festlegen, ob eine Zeitraum- oder Datumsauswahl angeboten werden soll
- Man kann nun die Beschriftung des Zeitraum- oder Datumsauswahlfeldes festlegen

# 1.1.3
- Kleinere Fehlerbehebungen

# 1.1.2
- Behebung des Problems, dass das Captcha "Google reCAPTCHA v3" unter Shopware 6.5 nicht mehr geladen wurde

# 1.1.1
- Insofern dem Verkaufskanal eine Mail-Kopf- und -Fußzeile für die E-Mail-Templates zugewiesen wurde, wird diese nun beim Versand der Daten per E-Mail verwendet

# 1.1.0
- Kompatibilität zu Shopware ab Version 6.5.0.0 hergestellt
- Man kann nun festlegen, ob die Datenschutzbestimmung angezeigt werden soll
- Man kann nun festlegen, ob die Datenschutzbestimmung als Pflichtfeld behandelt und somit ein Checkboxfeld angezeigt werden soll
- Man kann nun festlegen, ob die Pflichtfeldinformation angezeigt werden soll
- Man kann nun den Text der Absenden-Schaltfläche festlegen 
- Es ist nun auch möglich, mehrere Captcha-Methoden gleichzeitig aktiv zu haben

# 1.0.12
- Das Feld "Bestätigungstext" in der Konfiguration des CMS-Elements wird nun auch als TinyMCE-Feld bereitgestellt, sodass dessen Inhalt im Frontend als HTML-Code ausgeliefert wird

# 1.0.11
- Beim Versand der Daten per E-Mail wird nun bei den Empfängern die E-Mail des Kunden als Antwortadresse (Reply-To) gesetzt

# 1.0.10
- Beim E-Mail-Versand wird nun statt dem MailService der AbstractMailService verwendet

# 1.0.9
- Man kann nun festlegen, ob nach erfolgreichem Absenden des Formulars der Text des Anfrageformulars angezeigt werden soll
- Man kann nun bis zu 4 freie Eingaben festlegen und anzeigen lassen

# 1.0.8
- Der Feldtyp des Kommentarfeldes und des Feldes für die freie Eingabe wurde auf LONGTEXT geändert, sodass beide Felder mehr Zeichen aufnehmen können
- Alle einzeiligen und mehrzeiligen Eingabefelder verfügen nun über das maxlength-Attribut

# 1.0.7
- Kompatibilität zu Shopware ab Version 6.4.14.0 hergestellt

# 1.0.6
- Die Annotations beim Controller wurden durch Route defaults ersetzt

# 1.0.5
- Das Captcha "Google reCAPTCHA v3", das beim Klick auf die Datenschutz-Checkbox ausgelöst wird, ist nun beim Anfrageformular möglich

# 1.0.4
- Man kann nun die E-Mail auch an mehrere E-Mail-Empfänger, die mit Komma getrennt sind, versenden lassen
- Man kann nun die E-Mail auch zusätzlich an den Anfragenden versenden lassen

# 1.0.3
- Kompatibilität zu Shopware ab Version 6.4.10.0 hergestellt

# 1.0.2
- Man kann nun den Typ der freien Eingabe, also "Auswahlfeld", "Eingabefeld" oder "mehrzeiliges Eingabefeld", auswählen
- Man kann nun beim Formular bei Bedarf auch die Herkunft, den Herkunftswert und die ID der Herkunft der Anfrage manuell festlegen
- Der Code speziell für die Validierung des Captchas "Einfaches Captcha" wurde optimiert 

# 1.0.1
- Das Captcha "Einfaches Captcha" ist nun beim Anfrageformular möglich

# 1.0.0
- Erstveröffentlichung der App
