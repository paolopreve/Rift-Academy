-- Dati iniziali di esempio: utenti, ruoli, campioni, oggetti, rune, guide,
-- commenti, voti, preferiti e segnalazioni.
-- Credenziali di test (da riportare nella prima pagina della relazione):
--   admin  / Admin2026!   (amministratore)
--   utente / User2026!    (utente semplice)
-- Gli utenti baronpit, wardbot e zaunite servono solo a popolare il sito
-- (password User2026!). Le password sono salvate come hash (password_hash di PHP).
-- Immagini dei campioni: splash art da Data Dragon (Riot Games), scaricabili
-- con scarica-immagini.bat nella cartella del progetto.
SET NAMES utf8mb4;

-- 1. Utenti
INSERT INTO users (id, username, email, password_hash, role, status) VALUES
  (1, 'admin',    'admin@example.com',    '$2y$10$tF7OvXbT88UiGLCkXKKcv.MhdMfI6nUqfAJCoDb2PtgB6WSuf630W', 'admin', 'active'),
  (2, 'utente',   'utente@example.com',   '$2y$10$JPjVKfP8BgJclfQqcAAX2OG8iQqE6qkFVoEdsmZUvDW5DeYdRK8qu', 'user',  'active'),
  (3, 'baronpit', 'baronpit@example.com', '$2y$10$8ueyNuujUw8RhJItpIUKSejt9ekivvc1QaBl9/wSHtwjNjPiTSOMK', 'user',  'active'),
  (4, 'wardbot',  'wardbot@example.com',  '$2y$10$8ueyNuujUw8RhJItpIUKSejt9ekivvc1QaBl9/wSHtwjNjPiTSOMK', 'user',  'active'),
  (5, 'zaunite',  'zaunite@example.com',  '$2y$10$8ueyNuujUw8RhJItpIUKSejt9ekivvc1QaBl9/wSHtwjNjPiTSOMK', 'user',  'active');

-- 2. Ruoli di corsia (id 1-5 nell'ordine: Top, Jungle, Mid, Bot, Support)
INSERT INTO roles (id, name) VALUES (1, 'Top'), (2, 'Jungle'), (3, 'Mid'), (4, 'Bot'), (5, 'Support');

-- 3. Campioni
INSERT INTO champions (id, name, title, image_path, description) VALUES
  (1,  'Ahri',     'la Volpe a Nove Code',          'images/champions/ahri.jpg',     'Una vastaya legata al potere magico nascosto di Runeterra, capace di trasformare la magia in sfere di pura energia.'),
  (2,  'Darius',   'la Mano di Noxus',              'images/champions/darius.jpg',   'Non esiste simbolo migliore della potenza di Noxus: un comandante temuto e implacabile.'),
  (3,  'Lee Sin',  'il Monaco Cieco',               'images/champions/leesin.jpg',   'Un maestro delle antiche arti marziali di Ionia che combatte guidato dallo spirito del drago.'),
  (4,  'Jinx',     'la Mina Vagante',               'images/champions/jinx.jpg',     'Una criminale imprevedibile di Zaun che vive per seminare il caos, armi alla mano.'),
  (5,  'Thresh',   'il Carceriere',                 'images/champions/thresh.jpg',   'Uno spirito crudele e ambizioso che colleziona le anime dei nemici nella sua lanterna.'),
  (6,  'Garen',    'la Forza di Demacia',           'images/champions/garen.jpg',    'Un guerriero orgoglioso e nobile, alla guida dell''Avanguardia Impavida di Demacia.'),
  (7,  'Yasuo',    'il Reietto',                    'images/champions/yasuo.jpg',    'Uno spadaccino di Ionia che domina il vento e cerca di riscattare il proprio nome.'),
  (8,  'Lux',      'la Signora della Luce',         'images/champions/lux.jpg',      'Una maga di Demacia capace di piegare la luce al proprio volere.'),
  (9,  'Ezreal',   'l''Esploratore Prodigo',        'images/champions/ezreal.jpg',   'Un avventuriero brillante e spavaldo che usa un guanto magico trovato in un''antica rovina.'),
  (10, 'Kai''Sa',  'la Figlia del Vuoto',           'images/champions/kaisa.jpg',    'Cresciuta nel Vuoto, è sopravvissuta grazie a una simbiosi con un''armatura vivente.'),
  (11, 'Leona',    'l''Alba Radiosa',               'images/champions/leona.jpg',    'Una guerriera dei Solari che brandisce la forza del sole con spada e scudo.'),
  (12, 'Vi',       'la Guardiana di Piltover',      'images/champions/vi.jpg',       'Ex criminale dei bassifondi, oggi colpisce i nemici con due enormi guanti meccanici.'),
  (13, 'Amumu',    'la Mummia Triste',              'images/champions/amumu.jpg',    'Una piccola anima solitaria di Shurima, alla ricerca di un amico da secoli.'),
  (14, 'Malphite', 'il Frammento del Monolito',     'images/champions/malphite.jpg', 'Un essere di roccia vivente che cerca ordine e stabilità in un mondo caotico.'),
  (15, 'Zed',      'il Maestro delle Ombre',        'images/champions/zed.jpg',      'Il capo dell''Ordine delle Ombre, spietato e capace di dividersi in copie d''ombra.'),
  (16, 'Caitlyn',  'lo Sceriffo di Piltover',       'images/champions/caitlyn.jpg',  'Una tiratrice infallibile che protegge Piltover con il suo fucile di precisione.'),
  (17, 'Nautilus', 'il Titano degli Abissi',        'images/champions/nautilus.jpg', 'Un gigante corazzato che emerge dalle profondità del mare trascinando la sua ancora.'),
  (18, 'Sett',     'il Boss',                       'images/champions/sett.jpg',     'Il capo dei combattimenti clandestini di Ionia, che risolve tutto a mani nude.'),
  (19, 'Ekko',     'il Ragazzo che Frantumò il Tempo', 'images/champions/ekko.jpg',  'Un giovane genio di Zaun che riavvolge il tempo per avere sempre la mossa giusta.');

-- 4. Campioni e ruoli (un campione può avere più ruoli)
INSERT INTO champion_roles (champion_id, role_id) VALUES
  (1, 3),            -- Ahri: Mid
  (2, 1),            -- Darius: Top
  (3, 2),            -- Lee Sin: Jungle
  (4, 4),            -- Jinx: Bot
  (5, 5),            -- Thresh: Support
  (6, 1),            -- Garen: Top
  (7, 3), (7, 1),    -- Yasuo: Mid, Top
  (8, 3), (8, 5),    -- Lux: Mid, Support
  (9, 4),            -- Ezreal: Bot
  (10, 4),           -- Kai'Sa: Bot
  (11, 5),           -- Leona: Support
  (12, 2),           -- Vi: Jungle
  (13, 2),           -- Amumu: Jungle
  (14, 1),           -- Malphite: Top
  (15, 3),           -- Zed: Mid
  (16, 4),           -- Caitlyn: Bot
  (17, 5),           -- Nautilus: Support
  (18, 1),           -- Sett: Top
  (19, 2), (19, 3);  -- Ekko: Jungle, Mid

-- 5. Oggetti (nomi originali del gioco; immagini da Data Dragon con scarica-immagini.bat)
INSERT INTO items (id, name, image_path, description) VALUES
  (1,  'Infinity Edge',             'images/items/infinity_edge.webp',  'Aumenta molto i danni dei colpi critici.'),
  (2,  'Luden''s Tempest',          'images/items/ludens_tempest.webp', 'Danni magici a raffica e velocità di movimento.'),
  (3,  'Thornmail',                 'images/items/thornmail.webp',      'Salute, armatura e riduzione delle cure dei nemici.'),
  (4,  'Black Cleaver',             'images/items/black_cleaver.webp',  'Riduce l''armatura dei nemici colpiti con danni fisici.'),
  (5,  'Trinity Force',             'images/items/trinity_force.png',   'Attacco, velocità d''attacco e un colpo potenziato dopo ogni abilità.'),
  (6,  'Rabadon''s Deathcap',       'images/items/rabadons_deathcap.png', 'Aumenta di molto il potere magico totale.'),
  (7,  'Zhonya''s Hourglass',       'images/items/zhonyas_hourglass.png', 'Rende invulnerabili per pochi secondi, ma immobili.'),
  (8,  'Sunfire Aegis',             'images/items/sunfire_aegis.png',   'Salute e armatura; brucia i nemici vicini.'),
  (9,  'Kraken Slayer',             'images/items/kraken_slayer.png',   'Ogni terzo colpo base infligge danni aggiuntivi.'),
  (10, 'Locket of the Iron Solari', 'images/items/locket_iron_solari.png', 'Uno scudo per tutti i compagni vicini.'),
  (11, 'Berserker''s Greaves',      'images/items/berserkers_greaves.png', 'Stivali con velocità d''attacco.'),
  (12, 'Plated Steelcaps',          'images/items/plated_steelcaps.png', 'Stivali che riducono i danni dei colpi base.'),
  (13, 'Mercury''s Treads',         'images/items/mercurys_treads.png', 'Stivali con resistenza magica e meno durata degli effetti di controllo.'),
  (14, 'Sterak''s Gage',            'images/items/steraks_gage.png',    'Uno scudo enorme quando la salute scende molto.'),
  (15, 'Lord Dominik''s Regards',   'images/items/lord_dominiks_regards.png', 'Penetrazione armatura contro i bersagli resistenti.'),
  (16, 'Phantom Dancer',            'images/items/phantom_dancer.png',  'Velocità d''attacco e di movimento.'),
  (17, 'Void Staff',                'images/items/void_staff.png',      'Penetrazione magica contro chi ha molta resistenza magica.'),
  (18, 'Dead Man''s Plate',         'images/items/dead_mans_plate.png', 'Più velocità quando ci si muove e un colpo potenziato.'),
  (19, 'Blade of the Ruined King',  'images/items/blade_ruined_king.png', 'Danni in base alla salute del bersaglio e rallentamento.'),
  (20, 'Sorcerer''s Shoes',         'images/items/sorcerers_shoes.png', 'Stivali con penetrazione magica.'),
  (21, 'Youmuu''s Ghostblade',      'images/items/youmuus_ghostblade.png', 'Letalità e uno scatto di velocità.'),
  (22, 'Randuin''s Omen',           'images/items/randuins_omen.png',   'Riduce i danni dei colpi critici e rallenta i nemici vicini.'),
  (23, 'Frozen Heart',              'images/items/frozen_heart.png',    'Armatura, mana e velocità d''attacco ridotta per i nemici vicini.'),
  (24, 'Rapid Firecannon',          'images/items/rapid_firecannon.png', 'Un colpo con gittata aumentata dopo aver camminato.'),
  (25, 'Spirit Visage',             'images/items/spirit_visage.png',   'Resistenza magica e cure più efficaci.'),
  (26, 'Knight''s Vow',             'images/items/knights_vow.png',     'Protegge un compagno assorbendo parte dei suoi danni.'),
  (27, 'Ionian Boots of Lucidity',  'images/items/ionian_boots.png',    'Stivali che riducono le ricariche delle abilità.'),
  (28, 'The Collector',             'images/items/the_collector.png',   'Elimina subito i nemici con pochissima salute.'),
  (29, 'Liandry''s Torment',        'images/items/liandrys_torment.png', 'Le abilità bruciano i nemici nel tempo.'),
  (30, 'Ravenous Hydra',            'images/items/ravenous_hydra.png',  'Colpi ad area e rubavita.');

-- 6. Rune (nomi originali del gioco)
INSERT INTO runes (id, name, tree, image_path) VALUES
  (1,  'Electrocute',          'Domination',  'images/runes/electrocute.webp'),
  (2,  'Conqueror',            'Precision',   'images/runes/conqueror.webp'),
  (3,  'Press the Attack',     'Precision',   'images/runes/pta.webp'),
  (4,  'Aery',                 'Sorcery',     'images/runes/aery.webp'),
  (5,  'Grasp of the Undying', 'Resolve',     'images/runes/grasp_undying.png'),
  (6,  'Aftershock',           'Resolve',     'images/runes/aftershock.png'),
  (7,  'Guardian',             'Resolve',     'images/runes/guardian.png'),
  (8,  'Arcane Comet',         'Sorcery',     'images/runes/arcane_comet.png'),
  (9,  'Fleet Footwork',       'Precision',   'images/runes/fleet_footwork.png'),
  (10, 'Lethal Tempo',         'Precision',   'images/runes/lethal_tempo.png'),
  (11, 'Hail of Blades',       'Domination',  'images/runes/hail_of_blades.png'),
  (12, 'Dark Harvest',         'Domination',  'images/runes/dark_harvest.png'),
  (13, 'Phase Rush',           'Sorcery',     'images/runes/phase_rush.png'),
  (14, 'First Strike',         'Inspiration', 'images/runes/first_strike.png');

-- 7. Guide (date diverse, così la home mostra sempre le più recenti).
-- Ogni guida segue lo stesso schema: corsia, metà partita, scontri di squadra, errori da evitare.
INSERT INTO guides (id, user_id, champion_id, role_id, title, strategy_text, patch, created_at) VALUES
  (1, 1, 1, 3, 'Ahri Mid: fascino e burst',
   'Fase di corsia: farma con la Q, che colpisce due volte (all''andata e al ritorno). Scambia solo quando il Fascino (E) va a segno, poi aggiungi Q e W.\nMetà partita: dal livello 6 la Ultimate ha tre scatti. Spingi la wave e fai roam verso Top o Bot quando il tuo jungler è vicino.\nScontri di squadra: aspetta ai lati, cerca un Fascino sul bersaglio più fragile e usa gli scatti per entrare e uscire.\nErrori da evitare: lanciare il Fascino per primo contro chi ha uno scudo o un''abilità di fuga pronta.',
   '14.10', DATE_SUB(NOW(), INTERVAL 20 DAY)),
  (2, 2, 2, 1, 'Darius in Top: domina la corsia',
   'Fase di corsia: accumula la passiva (Emorragia) negli scambi brevi. Con la E tiri verso di te chi prova a scappare, con la Q colpisci sul bordo esterno per curarti.\nMetà partita: a cinque cariche Darius diventa fortissimo. Gioca vicino al tuo jungler e contesta l''Araldo.\nScontri di squadra: entra quando i nemici hanno usato i rallentamenti. Ogni eliminazione con la Ghigliottina (R) la ricarica subito.\nErrori da evitare: inseguire chi ha più gittata di te. Tieni la wave vicino alla tua torre e lascia che sia l''avversario a venire da te.',
   '14.10', DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (3, 1, 3, 2, 'Lee Sin jungle: i primi tre minuti',
   'Inizio: parti dal buff rosso, fai il giro completo della giungla e cerca il primo gank in Bot verso il minuto 3:15.\nGank: usa la Q solo quando sei sicuro di prenderla, altrimenti il gank è finito. La W ti permette di saltare su un ward o su un compagno per avvicinarti.\nScontri di squadra: il calcio (R) serve a spingere il bersaglio verso i compagni o a separarlo dal suo gruppo.\nErrori da evitare: Lee Sin cala nel late game. Se non ottieni vantaggi nei primi quindici minuti, pensa a proteggere i tuoi compagni più forti.',
   '14.10', DATE_SUB(NOW(), INTERVAL 16 DAY)),
  (4, 2, 4, 4, 'Jinx: crescere fino al late game',
   'Fase di corsia: farma con la mitragliatrice e passa ai razzi solo per colpire da lontano o quando i minion sono raggruppati.\nMetà partita: dopo due oggetti diventi la minaccia principale della partita. Resta con il tuo Support e prendi i draghi.\nScontri di squadra: resta dietro ai compagni. Dopo la prima eliminazione la passiva ti dà tantissima velocità: usala per riposizionarti.\nErrori da evitare: avanzare da sola senza visione. Jinx non ha scatti: un solo effetto di controllo può costarti la partita.',
   '14.10', DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (5, 4, 5, 5, 'Thresh: uncini e lanterne',
   'Fase di corsia: non sprecare l''uncino (Q) sui minion. Aspetta che il nemico si avvicini per farmare, poi usa la E per tirarlo verso di te.\nMetà partita: piazza i ward nel fiume prima dei draghi e porta la lanterna (W) nelle altre corsie.\nScontri di squadra: lancia la lanterna prima che il tuo ADC sia in pericolo, non dopo. La Ultimate crea un recinto che rallenta chi lo attraversa.\nErrori da evitare: ingaggiare quando il tuo ADC è senza mana o lontano.',
   '14.10', DATE_SUB(NOW(), INTERVAL 14 DAY)),
  (6, 3, 6, 1, 'Garen per chi inizia',
   'Fase di corsia: Garen perdona gli errori. Quando non subisci danni per qualche secondo ti curi da solo: sfruttalo per restare in corsia senza tornare in base.\nScambi: usa la Q per avvicinarti, gira con la E per qualche secondo e allontanati prima che il nemico risponda.\nScontri di squadra: punta i bersagli più fragili. La Ultimate infligge danni puri: finisci con quella chi ha poca salute.\nErrori da evitare: inseguire per tutta la mappa. Garen non ha effetti di controllo a distanza: torna in corsia e continua a farmare.',
   '14.10', DATE_SUB(NOW(), INTERVAL 13 DAY)),
  (7, 5, 7, 3, 'Yasuo Mid: il muro di vento al momento giusto',
   'Fase di corsia: carica la Q sui minion e arriva al terzo colpo, il tornado. Lo scatto (E) sui minion ti fa avvicinare e allontanare dal nemico.\nMetà partita: Yasuo è forte nei duelli. Spingi la wave e vai a fare roam in Top o in Bot.\nScontri di squadra: il Muro di Vento (W) blocca tutti i proiettili, tienilo per l''abilità più pericolosa del nemico. La Ultimate parte quando un nemico è in aria: combinala con i compagni che lanciano in aria.\nErrori da evitare: usare il Muro di Vento sui colpi base del nemico in corsia.',
   '14.10', DATE_SUB(NOW(), INTERVAL 12 DAY)),
  (8, 2, 8, 3, 'Lux Mid: colpire da lontano',
   'Fase di corsia: Lux vince la corsia stando lontana. Lancia la Singolarità Lucente (E) sui minion per farmare e colpire il nemico allo stesso tempo.\nMetà partita: la Ultimate ha una gittata enorme: usala per finire i bersagli in fuga anche nelle altre corsie.\nScontri di squadra: resta dietro. Se la Q immobilizza un nemico, la Ultimate è quasi sempre a segno.\nErrori da evitare: avvicinarti per lanciare la Q. Lux non ha scatti: la distanza è la tua difesa.',
   '14.10', DATE_SUB(NOW(), INTERVAL 11 DAY)),
  (9, 4, 8, 5, 'Lux Support: scudi e controllo',
   'Fase di corsia: come Support, Lux protegge il suo ADC con lo scudo (W), che passa due volte: all''andata e al ritorno.\nVisione: piazza i ward nei cespugli del fiume prima di ogni drago e rimuovi quelli nemici.\nScontri di squadra: tieni la Q per bloccare chi si avvicina troppo al tuo ADC.\nErrori da evitare: rubare i minion al tuo ADC con la E. Usala solo per colpire il nemico.',
   '14.10', DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (10, 3, 9, 4, 'Ezreal: sicurezza prima di tutto',
   'Fase di corsia: colpisci con la Q il più spesso possibile, anche sui minion: ogni colpo riduce le ricariche di tutte le abilità.\nMetà partita: Ezreal è forte a metà partita grazie a Trinity Force. Fai pressione sulle torri con il tuo Support.\nScontri di squadra: resta al limite della tua gittata e continua a colpire con la Q.\nErrori da evitare: usare il Balzo Arcano (E) per attaccare finché il nemico ha le sue abilità di controllo. È la tua unica via di fuga.',
   '14.10', DATE_SUB(NOW(), INTERVAL 9 DAY)),
  (11, 5, 10, 4, 'Kai''Sa: quando evolvere le abilità',
   'Fase di corsia: farma in sicurezza e scambia quando il tuo Support immobilizza il nemico: la passiva si carica con ogni colpo.\nEvoluzioni: le abilità si evolvono quando gli oggetti ti danno le statistiche giuste. Di solito la prima da evolvere è la Q.\nScontri di squadra: la Ultimate ti porta vicino a un nemico colpito dalla passiva. Usala per raggiungere il bersaglio giusto, non per entrare per prima.\nErrori da evitare: restare in mezzo ai nemici dopo la Ultimate. Colpisci e torna dietro.',
   '14.10', DATE_SUB(NOW(), INTERVAL 8 DAY)),
  (12, 4, 11, 5, 'Leona: aprire i combattimenti',
   'Fase di corsia: dal livello 2 cerca un ingaggio con E e Q. Il tuo ADC deve essere vicino e pronto.\nMetà partita: Leona apre i combattimenti. Accompagna il tuo jungler sui draghi e cerca bersagli isolati.\nScontri di squadra: cerca angoli laterali per la E, poi blocca il nemico con la Q. La Ultimate stordisce il centro dell''area.\nErrori da evitare: un ingaggio senza seguito è solo una morte in più. Non entrare se il tuo ADC è lontano o senza mana.',
   '14.10', DATE_SUB(NOW(), INTERVAL 7 DAY)),
  (13, 1, 12, 2, 'Vi: gank veloci e sicuri',
   'Inizio: Vi pulisce bene i campi. Inizia dal lato opposto a quello dove vuoi fare il primo gank.\nGank: carica la Q nel cespuglio prima di uscire: il nemico ha meno tempo per reagire.\nScontri di squadra: la Ultimate di Vi non si può evitare. Scegli il bersaglio più pericoloso, non quello più vicino.\nErrori da evitare: usare la Q per entrare quando il nemico ha Flash pronto. Aspetta che lo usi.',
   '14.10', DATE_SUB(NOW(), INTERVAL 6 DAY)),
  (14, 3, 13, 2, 'Amumu: la combo del pianto',
   'Inizio: Amumu è debole nei primi minuti. Pensa a pulire bene i campi e a essere presente al primo drago.\nGank: entra con la Q da un lato che il nemico non vede. Hai due cariche: tieni la seconda per seguirlo.\nScontri di squadra: entra con la Q, attiva la Disperazione (W) e chiudi con la Ultimate al centro dei nemici. Più ne prendi, più vince la tua squadra.\nErrori da evitare: entrare senza che i compagni siano vicini. Amumu immobilizza, ma il danno arriva dalla squadra.',
   '14.10', DATE_SUB(NOW(), INTERVAL 5 DAY)),
  (15, 5, 14, 1, 'Malphite contro i campioni fisici',
   'Fase di corsia: contro avversari che fanno danni fisici Malphite diventa quasi indistruttibile. Costruisci armatura e lascia che si stanchino contro di te.\nMetà partita: farma in sicurezza con la Q, che ti dà anche velocità di movimento.\nScontri di squadra: la Ultimate è il momento più importante della partita. Aspetta che i nemici siano vicini tra loro prima di lanciarla.\nErrori da evitare: usare la Ultimate su un solo bersaglio quando tra pochi secondi potresti prenderne tre.',
   '14.10', DATE_SUB(NOW(), INTERVAL 4 DAY)),
  (16, 2, 15, 3, 'Zed: gestire le ombre',
   'Fase di corsia: lascia l''ombra (W) in avanti, colpisci con Q ed E e torna indietro prima che il nemico risponda.\nMetà partita: Zed è un assassino. Cerca bersagli isolati, soprattutto nelle corsie laterali.\nScontri di squadra: aspetta ai lati che il nemico più fragile sia scoperto. La Ultimate ti rende irraggiungibile per un attimo.\nErrori da evitare: entrare quando il bersaglio ha un''abilità di invulnerabilità pronta. Aspetta che la usi.',
   '14.10', DATE_SUB(NOW(), INTERVAL 3 DAY)),
  (17, 4, 16, 4, 'Caitlyn: pressione dalla distanza',
   'Fase di corsia: Caitlyn ha la gittata più lunga tra gli ADC. Usala per colpire il nemico ogni volta che si avvicina ai minion.\nTrappole: mettile (W) nei cespugli e sotto i nemici immobilizzati dal tuo Support. Ogni trappola presa è un colpo potenziato gratuito.\nScontri di squadra: resta al limite della tua gittata. La rete (E) ti allontana dai nemici che ti saltano addosso.\nErrori da evitare: restare indietro quando vinci la corsia. Usa il vantaggio per abbattere la torre.',
   '14.10', DATE_SUB(NOW(), INTERVAL 2 DAY)),
  (18, 3, 17, 5, 'Nautilus: catene e controllo',
   'Fase di corsia: Nautilus ha più effetti di controllo di quasi tutti i Support: l''uncino (Q), la passiva al primo colpo e la Ultimate.\nMetà partita: usa l''uncino sui muri per muoverti velocemente nella mappa.\nScontri di squadra: lancia la Ultimate sul bersaglio più importante, anche se è dietro: attraversa tutti e lo blocca a lungo.\nErrori da evitare: lanciare l''uncino nei cespugli senza visione.',
   '14.10', DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (19, 5, 18, 1, 'Sett: scambi a mani nude',
   'Fase di corsia: Sett vince gli scambi lunghi. Alterna i pugni per attivare la passiva e aspetta che la barra della W sia piena prima di usarla.\nMetà partita: Sett è resistente e fa molti danni. Gioca con il tuo jungler sull''Araldo.\nScontri di squadra: la Ultimate afferra un nemico e lo scaglia sugli altri. Più è pesante il bersaglio, più danni fai al gruppo.\nErrori da evitare: usare la W quando la barra è vuota: lo scudo sarà minimo.',
   '14.10', DATE_SUB(NOW(), INTERVAL 12 HOUR)),
  (20, 1, 19, 2, 'Ekko Jungle: tornare indietro nel tempo',
   'Inizio: Ekko pulisce i campi in fretta con la Q. Cerca il primo gank dopo il primo giro completo.\nGank: attraversa i muri con la E e usa la W in anticipo, dove il nemico sarà tra un paio di secondi.\nScontri di squadra: la Ultimate riporta Ekko dove era quattro secondi prima, curandolo. Usala per entrare in profondità e uscire indenne.\nErrori da evitare: entrare senza guardare dov''è la tua ombra: è il punto in cui tornerai con la Ultimate.',
   '14.10', DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- 8. Oggetti consigliati nelle guide (position = ordine di acquisto)
INSERT INTO guide_items (guide_id, item_id, position) VALUES
  (1, 2, 1),  (1, 20, 2), (1, 7, 3),  (1, 6, 4),  (1, 17, 5),
  (2, 5, 1),  (2, 12, 2), (2, 14, 3), (2, 18, 4), (2, 3, 5),
  (3, 4, 1),  (3, 13, 2), (3, 14, 3), (3, 18, 4),
  (4, 11, 1), (4, 9, 2),  (4, 1, 3),  (4, 24, 4), (4, 15, 5),
  (5, 10, 1), (5, 13, 2), (5, 26, 3),
  (6, 5, 1),  (6, 11, 2), (6, 18, 3), (6, 14, 4),
  (7, 19, 1), (7, 11, 2), (7, 1, 3),  (7, 16, 4),
  (8, 2, 1),  (8, 20, 2), (8, 6, 3),  (8, 17, 4),
  (9, 2, 1),  (9, 27, 2), (9, 7, 3),
  (10, 5, 1), (10, 27, 2), (10, 4, 3),
  (11, 9, 1), (11, 11, 2), (11, 16, 3), (11, 1, 4),
  (12, 12, 1), (12, 10, 2), (12, 8, 3), (12, 26, 4),
  (13, 4, 1), (13, 12, 2), (13, 14, 3), (13, 18, 4),
  (14, 8, 1), (14, 13, 2), (14, 3, 3), (14, 29, 4),
  (15, 12, 1), (15, 8, 2), (15, 3, 3), (15, 23, 4), (15, 22, 5),
  (16, 21, 1), (16, 27, 2), (16, 28, 3), (16, 4, 4),
  (17, 11, 1), (17, 1, 2), (17, 28, 3), (17, 24, 4), (17, 15, 5),
  (18, 12, 1), (18, 10, 2), (18, 26, 3), (18, 23, 4),
  (19, 14, 1), (19, 12, 2), (19, 18, 3), (19, 4, 4), (19, 25, 5),
  (20, 20, 1), (20, 7, 2), (20, 6, 3), (20, 17, 4);

-- 9. Rune consigliate nelle guide (la prima è la runa principale)
INSERT INTO guide_runes (guide_id, rune_id, position) VALUES
  (1, 1, 1),
  (2, 2, 1),
  (3, 2, 1),
  (4, 10, 1),
  (5, 6, 1),
  (6, 2, 1),
  (7, 10, 1),
  (8, 8, 1),
  (9, 8, 1),  (9, 4, 2),
  (10, 2, 1), (10, 14, 2),
  (11, 3, 1), (11, 11, 2),
  (12, 6, 1),
  (13, 2, 1),
  (14, 6, 1),
  (15, 5, 1),
  (16, 1, 1),
  (17, 3, 1), (17, 9, 2),
  (18, 7, 1),
  (19, 2, 1), (19, 5, 2),
  (20, 12, 1), (20, 13, 2);

-- 10. Commenti
INSERT INTO comments (id, guide_id, user_id, body, created_at) VALUES
  (1,  1,  2, 'Ottima guida! Mi ha aiutato a uscire dall''Argento con Ahri.', DATE_SUB(NOW(), INTERVAL 19 DAY)),
  (2,  2,  1, 'Spiegazione molto chiara sulla gestione delle wave con Darius.', DATE_SUB(NOW(), INTERVAL 17 DAY)),
  (3,  3,  4, 'Il consiglio sul primo gank al minuto 3:15 funziona davvero.', DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (4,  4,  3, 'Aggiungerei qualcosa su come posizionarsi contro gli assassini.', DATE_SUB(NOW(), INTERVAL 14 DAY)),
  (5,  6,  2, 'Perfetta per chi inizia, l''ho girata a un amico.', DATE_SUB(NOW(), INTERVAL 12 DAY)),
  (6,  7,  4, 'Il muro di vento sulla Ultimate nemica è la chiave, giustissimo.', DATE_SUB(NOW(), INTERVAL 11 DAY)),
  (7,  12, 5, 'Finalmente qualcuno che dice di non entrare se l''ADC è lontano.', DATE_SUB(NOW(), INTERVAL 6 DAY)),
  (8,  14, 2, 'Amumu è più forte di quanto pensassi, grazie!', DATE_SUB(NOW(), INTERVAL 4 DAY)),
  (9,  17, 3, 'Le trappole sotto i nemici immobilizzati fanno una differenza enorme.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (10, 20, 4, 'Provata ieri sera: tre vittorie di fila con Ekko.', DATE_SUB(NOW(), INTERVAL 1 HOUR));

-- 11. Valutazioni (da 1 a 5, una per utente e per guida)
INSERT INTO ratings (guide_id, user_id, value, created_at) VALUES
  (1, 2, 5, NOW()), (1, 3, 4, NOW()), (1, 4, 5, NOW()),
  (2, 1, 4, NOW()), (2, 5, 3, NOW()),
  (3, 4, 5, NOW()), (3, 2, 4, NOW()),
  (4, 3, 4, NOW()), (4, 1, 4, NOW()),
  (5, 2, 5, NOW()),
  (6, 2, 5, NOW()), (6, 1, 4, NOW()),
  (7, 4, 4, NOW()),
  (8, 5, 3, NOW()),
  (10, 1, 4, NOW()),
  (12, 5, 5, NOW()), (12, 2, 4, NOW()),
  (13, 3, 4, NOW()),
  (14, 2, 5, NOW()),
  (17, 3, 5, NOW()),
  (20, 4, 5, NOW());

-- 12. Preferiti
INSERT INTO favorites (user_id, guide_id, created_at) VALUES
  (2, 1, NOW()), (2, 6, NOW()), (2, 14, NOW()),
  (1, 2, NOW()),
  (3, 17, NOW()),
  (4, 3, NOW()), (4, 20, NOW());

-- 13. Segnalazioni (va valorizzato esattamente uno tra guide_id e comment_id)
INSERT INTO reports (id, reporter_id, guide_id, comment_id, reason, status, created_at) VALUES
  (1, 2, 1,    NULL, 'Testo pubblicitario nella descrizione della guida', 'open',   NOW()),
  (2, 1, NULL, 1,    'Linguaggio inappropriato nel commento',             'closed', NOW()),
  (3, 5, 8,    NULL, 'Guida copiata da un altro sito',                    'open',   NOW());
