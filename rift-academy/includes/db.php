<?php
// Connessione al database con PDO. Usare SEMPRE prepared statement:
//   $st = db()->prepare('SELECT * FROM guides WHERE id = ?');
//   $st->execute([$id]);
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
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
