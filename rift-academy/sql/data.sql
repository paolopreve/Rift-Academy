-- Dati iniziali: ruoli di corsia e utenti di test.
-- Credenziali di test (da riportare nella prima pagina della relazione):
--   admin  / Admin2026!   (amministratore)
--   utente / User2026!    (utente semplice)
-- Le password sono salvate come hash (password_hash di PHP).
SET NAMES utf8mb4;

INSERT INTO roles (name) VALUES ('Top'), ('Jungle'), ('Mid'), ('Bot'), ('Support');

INSERT INTO users (username, email, password_hash, role, status) VALUES
  ('admin',  'admin@example.com',  '$2y$10$tF7OvXbT88UiGLCkXKKcv.MhdMfI6nUqfAJCoDb2PtgB6WSuf630W', 'admin', 'active'),
  ('utente', 'utente@example.com', '$2y$10$JPjVKfP8BgJclfQqcAAX2OG8iQqE6qkFVoEdsmZUvDW5DeYdRK8qu',  'user',  'active');

-- Campioni, oggetti e rune: generare con tools/import_datadragon.py oppure
-- inserirli da admin_champions.php.
