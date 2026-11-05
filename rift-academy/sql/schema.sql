-- Rift Academy - schema MySQL (3FN)
-- Eseguire su un database vuoto. Motore InnoDB, codifica utf8mb4.
-- Compatibile con MySQL 5.6 o superiore (FULLTEXT su InnoDB). Se la versione del
-- server e' piu' vecchia, rimuovere l'indice FULLTEXT e cercare con LIKE.
-- Regole non imposte dal database (da validare in PHP): ratings.value tra 1 e 5;
-- in reports deve essere valorizzato esattamente uno tra guide_id e comment_id.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS reports, comments, ratings, favorites, guide_runes, guide_items,
                     guides, champion_roles, runes, items, champions, roles, users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username      VARCHAR(30)  NOT NULL,
  email         VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('user','admin') NOT NULL DEFAULT 'user',
  status        ENUM('active','suspended') NOT NULL DEFAULT 'active',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE roles (
  id   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(20) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE champions (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(50)  NOT NULL,
  title       VARCHAR(100) NULL,
  image_path  VARCHAR(255) NOT NULL,
  description TEXT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_champions_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE champion_roles (
  champion_id INT UNSIGNED NOT NULL,
  role_id     TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (champion_id, role_id),
  CONSTRAINT fk_cr_champion FOREIGN KEY (champion_id) REFERENCES champions (id) ON DELETE CASCADE,
  CONSTRAINT fk_cr_role     FOREIGN KEY (role_id)     REFERENCES roles (id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE items (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(80)  NOT NULL,
  image_path  VARCHAR(255) NOT NULL,
  description TEXT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_items_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE runes (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(80)  NOT NULL,
  tree       VARCHAR(30)  NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_runes_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guides (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       INT UNSIGNED NOT NULL,
  champion_id   INT UNSIGNED NOT NULL,
  role_id       TINYINT UNSIGNED NOT NULL,
  title         VARCHAR(120) NOT NULL,
  strategy_text TEXT NOT NULL,
  patch         VARCHAR(10)  NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NULL DEFAULT NULL,  -- impostare con NOW() negli UPDATE
  PRIMARY KEY (id),
  KEY idx_guides_champion (champion_id),
  KEY idx_guides_role (role_id),
  KEY idx_guides_user (user_id),
  FULLTEXT KEY ft_guides_text (title, strategy_text),
  CONSTRAINT fk_guides_user     FOREIGN KEY (user_id)     REFERENCES users (id)     ON DELETE CASCADE,
  CONSTRAINT fk_guides_champion FOREIGN KEY (champion_id) REFERENCES champions (id) ON DELETE RESTRICT,
  CONSTRAINT fk_guides_role     FOREIGN KEY (role_id)     REFERENCES roles (id)     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guide_items (
  guide_id INT UNSIGNED NOT NULL,
  item_id  INT UNSIGNED NOT NULL,
  position TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (guide_id, item_id),
  CONSTRAINT fk_gi_guide FOREIGN KEY (guide_id) REFERENCES guides (id) ON DELETE CASCADE,
  CONSTRAINT fk_gi_item  FOREIGN KEY (item_id)  REFERENCES items (id)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guide_runes (
  guide_id INT UNSIGNED NOT NULL,
  rune_id  INT UNSIGNED NOT NULL,
  position TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (guide_id, rune_id),
  CONSTRAINT fk_gr_guide FOREIGN KEY (guide_id) REFERENCES guides (id) ON DELETE CASCADE,
  CONSTRAINT fk_gr_rune  FOREIGN KEY (rune_id)  REFERENCES runes (id)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE comments (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  guide_id   INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  body       TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_comments_guide (guide_id),
  KEY idx_comments_user (user_id),
  CONSTRAINT fk_comments_guide FOREIGN KEY (guide_id) REFERENCES guides (id) ON DELETE CASCADE,
  CONSTRAINT fk_comments_user  FOREIGN KEY (user_id)  REFERENCES users (id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ratings (
  guide_id   INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  value      TINYINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (guide_id, user_id),
  KEY idx_ratings_user (user_id),
  CONSTRAINT fk_ratings_guide FOREIGN KEY (guide_id) REFERENCES guides (id) ON DELETE CASCADE,
  CONSTRAINT fk_ratings_user  FOREIGN KEY (user_id)  REFERENCES users (id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE favorites (
  user_id    INT UNSIGNED NOT NULL,
  guide_id   INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, guide_id),
  KEY idx_favorites_guide (guide_id),
  CONSTRAINT fk_fav_user  FOREIGN KEY (user_id)  REFERENCES users (id)  ON DELETE CASCADE,
  CONSTRAINT fk_fav_guide FOREIGN KEY (guide_id) REFERENCES guides (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reports (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  reporter_id INT UNSIGNED NOT NULL,
  guide_id    INT UNSIGNED NULL,
  comment_id  INT UNSIGNED NULL,
  reason      VARCHAR(255) NOT NULL,
  status      ENUM('open','closed') NOT NULL DEFAULT 'open',
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_reports_status (status),
  KEY idx_reports_reporter (reporter_id),
  KEY idx_reports_guide (guide_id),
  KEY idx_reports_comment (comment_id),
  CONSTRAINT fk_reports_reporter FOREIGN KEY (reporter_id) REFERENCES users (id)    ON DELETE CASCADE,
  CONSTRAINT fk_reports_guide    FOREIGN KEY (guide_id)    REFERENCES guides (id)   ON DELETE CASCADE,
  CONSTRAINT fk_reports_comment  FOREIGN KEY (comment_id)  REFERENCES comments (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
