RIFT ACADEMY - Progetto Tecnologie Web
=======================================

Questo e' uno SCHELETRO: le pagine hanno gia' struttura, controlli di accesso e
TODO da completare. Cercare "TODO" in tutti i file per vedere cosa manca.

STRUTTURA
  *.php          pagine e script di azione (tutti nella radice, link relativi)
  includes/      codice PHP condiviso (config, db, auth/CSRF, funzioni)
  templates/     HTML con segnaposto {{nome}}, riempiti da render_page()
  css/ js/ img/  presentazione, comportamento, immagini (in locale)
  sql/           schema.sql (tabelle) e data.sql (ruoli e utenti di test)
  tools/         strumenti locali: NON caricare sul server

PROVA IN LOCALE
  1. Creare un database MySQL vuoto e importare sql/schema.sql poi sql/data.sql
  2. Compilare includes/config.php
  3. Dalla cartella del progetto:  php -S localhost:8000
  4. Aprire http://localhost:8000/

REQUISITI DA VERIFICARE SUL SERVER DEL CORSO (tecweb.studenti.math.unipd.it)
  - Versione di PHP: il codice usa PHP 7.3 o superiore
  - Versione di MySQL: lo schema usa InnoDB e FULLTEXT (MySQL 5.6 o superiore)
  - Permessi: file 644, cartelle 755
  - Solo link relativi (css/style.css, mai /css/style.css)

REGOLE PER I TEMPLATE HTML (sintassi compatibile XML, richiesta dalle specifiche)
  - tag in minuscolo e sempre chiusi:  <br />, <input ... />, <meta ... />
  - attributi sempre tra virgolette; gli attributi booleani hanno un valore:
    required="required"
  - nessuno style="" e nessun onclick="" nel markup

CREDENZIALI DI TEST (da cambiare e da riportare nella relazione)
  admin  / Admin2026!
  utente / User2026!

NOTA SUI RUOLI DEI CAMPIONI
  I dati statici di Riot non contengono Top/Jungle/Mid/Bot/Support: assegnarli
  dalla pagina admin_champions.php.

PRIMA DELLA CONSEGNA
  - Installazione da zero partendo dal dump
  - Validazione W3C di tutte le pagine
  - Prova solo da tastiera, zoom al 400%, controllo dei contrasti
  - Dump finale del database (schema + dati) e relazione PDF nello zip per Moodle
