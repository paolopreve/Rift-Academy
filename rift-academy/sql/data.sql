-- Dati iniziali: ruoli di corsia e utenti di test.
-- Credenziali di test (da riportare nella prima pagina della relazione):
--   admin  / Admin2026!   (amministratore)
--   utente / User2026!    (utente semplice)
-- Le password sono salvate come hash (password_hash di PHP).
SET NAMES utf8mb4;


INSERT INTO users (username, email, password_hash, role, status) VALUES
  ('admin',  'admin@example.com',  '$2y$10$tF7OvXbT88UiGLCkXKKcv.MhdMfI6nUqfAJCoDb2PtgB6WSuf630W', 'admin', 'active'),
  ('utente', 'utente@example.com', '$2y$10$JPjVKfP8BgJclfQqcAAX2OG8iQqE6qkFVoEdsmZUvDW5DeYdRK8qu',  'user',  'active');

-- Campioni, oggetti e rune: generare con tools/import_datadragon.py oppure
-- inserirli da admin_champions.php.
-- Dati iniziali: ruoli di corsia, utenti di test e dati per tutte le tabelle.
-- Credenziali di test (da riportare nella prima pagina della relazione):
--   admin  / Admin2026!   (amministratore)
--   utente / User2026!    (utente semplice)
-- Le password sono salvate come hash (password_hash di PHP).

INSERT INTO roles (name) VALUES ('Top'), ('Jungle'), ('Mid'), ('Bot'), ('Support');

-- 3. Campioni
INSERT INTO champions (id, name, title, image_path, description) VALUES
  (1, 'Ahri', 'the Nine-Tailed Fox', 'images/champions/ahri.jpg', 'A vastaya who is connected to the latent power of Runeterra.'),
  (2, 'Darius', 'the Hand of Noxus', 'images/champions/darius.jpg', 'There is no better symbol of Noxian might than Darius.'),
  (3, 'Lee Sin', 'the Blind Monk', 'images/champions/leesin.jpg', 'A master of Ionia''s ancient martial arts.'),
  (4, 'Jinx', 'the Loose Cannon', 'images/champions/jinx.jpg', 'A manic and impulsive criminal from Zaun.'),
  (5, 'Thresh', 'the Chain Warden', 'images/champions/thresh.jpg', 'Sadistic and cunning, Thresh is an ambitious and restless spirit.');

-- 4. Relazioni Campioni-Ruoli
INSERT INTO champion_roles (champion_id, role_id) VALUES
  (1, 3), -- Ahri -> Mid
  (2, 1), -- Darius -> Top
  (3, 2), -- Lee Sin -> Jungle
  (4, 4), -- Jinx -> Bot
  (5, 5); -- Thresh -> Support

-- 5. Oggetti
INSERT INTO items (id, name, image_path, description) VALUES
  (1, 'Infinity Edge', 'images/items/infinity_edge.webp', 'Grants critical strike damage boost.'),
  (2, 'Luden''s Tempest', 'images/items/ludens_tempest.webp', 'Deals burst magic damage and grants movement speed.'),
  (3, 'Thornmail', 'images/items/thornmail.webp', 'Provides health, armour and antiheal.'),
  (4, 'Black Cleaver', 'images/items/black_cleaver.webp', 'Reduces enemy armor on physical damage.');

-- 6. Rune
INSERT INTO runes (id, name, tree, image_path) VALUES
  (1, 'Electrocute', 'Domination', 'images/runes/electrocute.webp'),
  (2, 'Conqueror', 'Precision', 'images/runes/conqueror.webp'),
  (3, 'Press the Attack', 'Precision', 'images/runes/pta.webp'),
  (4, 'Aery', 'Sorcery', 'images/runes/aery.webp');

-- 7. Guide
INSERT INTO guides (id, user_id, champion_id, role_id, title, strategy_text, patch, created_at) VALUES
  (1, 1, 1, 3, 'Mastering Ahri Mid: Charm and Burst', 'Focus on hitting your Charm (E) before unleashing your full combo (Q-W-R). Play safe pre-level 6 and roam effectively.', '14.1', NOW()),
  (2, 2, 2, 1, 'Darius Top Lane Dominance', 'Stack your passive (Hemorrhage) in trades and execute enemies with Noxian Guillotine (R). Control minion waves properly.', '14.1', NOW());

-- 8. Oggetti della Guida
INSERT INTO guide_items (guide_id, item_id, position) VALUES
  (1, 2, 1),
  (2, 1, 1),
  (2, 4, 2);

-- 9. Rune della Guida
INSERT INTO guide_runes (guide_id, rune_id, position) VALUES
  (1, 1, 1),
  (2, 2, 1);

-- 10. Commenti
INSERT INTO comments (id, guide_id, user_id, body, created_at) VALUES
  (1, 1, 2, 'Great guide! Helped me climb out of Silver with Ahri.', NOW()),
  (2, 2, 1, 'Very detailed breakdown of wave management for Darius.', NOW());

-- 11. Valutazioni (valori tra 1 e 5)
INSERT INTO ratings (guide_id, user_id, value, created_at) VALUES
  (1, 2, 5, NOW()),
  (2, 1, 4, NOW());

-- 12. Preferiti
INSERT INTO favorites (user_id, guide_id, created_at) VALUES
  (2, 1, NOW()),
  (1, 2, NOW());

-- 13. Segnalazioni (esattamente uno tra guide_id e comment_id valorizzato)
INSERT INTO reports (id, reporter_id, guide_id, comment_id, reason, status, created_at) VALUES
  (1, 2, 1, NULL, 'Spam content in guide description', 'open', NOW()),
  (2, 1, NULL, 1, 'Inappropriate language in comment', 'closed', NOW());