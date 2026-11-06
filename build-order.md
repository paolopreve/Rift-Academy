# Rift Academy — Ordine di completamento dei file

Guida per far completare i `TODO` del progetto a Qwen (via Continue), un file alla volta, nell'ordine giusto. Vedi anche `project-conventions.md` e `accessibility.md`, che devono stare in `.continue/rules/`.

Per ogni file:
1. Allega con `@` solo il file e le dipendenze indicate.
2. Chiedi di completare SOLO quel file, es.: *"Completa i TODO in login.php. Segui le regole del progetto e di accessibilità. Usa solo gli helper già esistenti. Non modificare il template."*
3. Controlla il diff (SQL con prepared statement, `e()` sull'output, controlli di permesso/CSRF, sintassi PHP 7.3).
4. Testa: `php -l <file>`, poi la pagina nel browser.
5. Solo dopo, passa al file successivo: ogni fase dipende dalla precedente.

---

## Fase 0 — Dati (nessun TODO, solo verifica)

- [ ] `sql/schema.sql` — già completo, da importare per primo.
- [ ] `sql/data.sql` — già completo (ruoli, utenti di test, dati di esempio). Importare dopo lo schema e verificare che non dia errori.

## Fase 1 — Autenticazione

Nulla funziona senza login: fare questi due per primi.

- [ ] `login.php` — allegare `includes/auth.php`, `includes/functions.php`, `includes/db.php`, `templates/login.html`.
- [ ] `register.php` — allegare `includes/auth.php`, `includes/functions.php`, `includes/db.php`, `templates/register.html`.

**Verifica di fase:** registrare un nuovo utente, fare logout, rifare login con `admin` / `Admin2026!` e con `utente` / `User2026!`.

## Fase 2 — Pagine pubbliche di sola lettura

Non toccano permessi né scritture: buon seguito per abituare il modello allo stile delle query.

- [ ] `index.php` — allegare `includes/db.php`, `includes/functions.php`, `templates/index.html`.
- [ ] `champions.php` — allegare `includes/db.php`, `includes/functions.php`, `templates/champions.html`.
- [ ] `search.php` — allegare `includes/db.php`, `includes/functions.php`, `templates/search.html`.

**Verifica di fase:** la home mostra le ultime guide, il catalogo campioni filtra per ruolo, la ricerca trova una guida per titolo.

## Fase 3 — Creazione e modifica guida

Fare `create_guide.php` per primo: `edit_guide.php` riusa lo stesso schema di form e di validazione.

- [ ] `create_guide.php` — allegare `includes/auth.php`, `includes/db.php`, `includes/functions.php`, `templates/create_guide.html`, `sql/schema.sql`.
- [ ] `edit_guide.php` — allegare gli stessi file di sopra più `templates/edit_guide.html`. Menzionare nel prompt: *"Segui lo stesso schema di create_guide.php, ma con controllo is_owner_or_admin()."*

**Verifica di fase:** da utente loggato, creare una guida nuova e poi modificarla; provare a modificare una guida di un altro utente e verificare che risponda 403.

## Fase 4 — Azioni POST sulla guida

Piccoli script simili tra loro: `delete_guide.php` prima (serve a ripulire i test di Fase 3), poi gli altri in un ordine qualsiasi.

- [ ] `delete_guide.php` — allegare `includes/auth.php`, `includes/db.php`.
- [ ] `comment_action.php` — allegare `includes/auth.php`, `includes/db.php`, `includes/functions.php`.
- [ ] `rate_guide.php` — allegare `includes/auth.php`, `includes/db.php`.
- [ ] `favorite_toggle.php` — allegare `includes/auth.php`, `includes/db.php`.
- [ ] `report_content.php` — allegare `includes/auth.php`, `includes/db.php`, `includes/functions.php`.

**Verifica di fase:** da una guida, provare commento, voto, preferito e segnalazione; cancellare una guida propria.

## Fase 5 — Dettaglio guida

Va dopo la Fase 4 perché mostra commenti, voti e i pulsanti che quegli script gestiscono.

- [ ] `guide_detail.php` — allegare `includes/auth.php`, `includes/db.php`, `includes/functions.php`, `templates/guide_detail.html`.

**Verifica di fase:** aprire una guida e vedere build, media voti, commenti e i pulsanti valuta/preferito/segnala/modifica, corretti in base a chi è loggato.

## Fase 6 — Profilo utente

- [ ] `profile.php` — allegare `includes/auth.php`, `includes/db.php`, `includes/functions.php`, `templates/profile.html`.

**Verifica di fase:** il profilo elenca le proprie guide (con link a modifica/elimina) e i preferiti salvati.

## Fase 7 — Amministrazione

Indipendenti tra loro, in un ordine qualsiasi.

- [ ] `admin_dashboard.php` — allegare `includes/auth.php`, `includes/db.php`, `includes/functions.php`, `templates/admin_dashboard.html`.
- [ ] `admin_users.php` — allegare `includes/auth.php`, `includes/db.php`, `templates/admin_users.html`.
- [ ] `admin_champions.php` — allegare `includes/auth.php`, `includes/db.php`, `includes/functions.php`, `templates/admin_champions.html`, `sql/schema.sql`.

**Verifica di fase:** da `admin`, moderare una segnalazione, sospendere e riattivare l'utente di test, aggiungere un campione dal pannello.

## Fase 8 — Pagine semplici (nessun TODO sostanziale)

- [ ] `logout.php` — verificare solo che distrugga la sessione e reindirizzi.
- [ ] `404.php` — verificare solo che compaia su un URL inesistente.

---

## Dopo ogni fase

- `php -l` su ogni file toccato.
- Nessuna query costruita per concatenazione di stringhe.
- Nessun dato utente stampato senza `e()`.
- HTML compatibile XML (tag chiusi, attributi tra virgolette).
- Mobile-first su qualsiasi CSS toccato.

## Prima della consegna

- [ ] Nessun `TODO` rimasto in nessun file.
- [ ] Validazione W3C di tutte le pagine.
- [ ] Prova completa solo da tastiera.
- [ ] Zoom al 400% e controllo contrasti.
- [ ] Import da zero di `schema.sql` + `data.sql` su un database vuoto.
- [ ] Credenziali di test funzionanti, riportate nella relazione.
