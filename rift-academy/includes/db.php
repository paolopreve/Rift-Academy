<?php
// Connessione al database con PDO. Usare SEMPRE prepared statement:
//   $st = db()->prepare('SELECT * FROM guides WHERE id = ?');
//   $st->execute([$id]);
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        // Legge le variabili d'ambiente (Docker) con fallback sulle costanti di config.php o localhost
        $host = getenv('DB_HOST') ?: (defined('DB_HOST') ? DB_HOST : 'localhost');
        $db   = getenv('DB_NAME') ?: (defined('DB_NAME') ? DB_NAME : 'rift_academy');
        $user = getenv('DB_USER') ?: (defined('DB_USER') ? DB_USER : 'root');
        $pass = getenv('DB_PASS') ?: (defined('DB_PASS') ? DB_PASS : '');

        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$db;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $ex) {
            // Non mostrare mai i dettagli (potrebbero includere le credenziali).
            error_log($ex->getMessage());
            http_response_code(500);
            exit('Servizio temporaneamente non disponibile.');
        }
    }
    return $pdo;
}