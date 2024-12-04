<?php
//ini_set("display_errors", 1);
//error_reporting(E_ALL);

require_once("dbconfig.php");

try {
    // Verbindung zur MySQL-Server (ohne spezifische Datenbank)
    $pdo = new PDO("mysql:host=" . $dbconfig["host"], $dbconfig["user"], $dbconfig["password"]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prüfen, ob die Datenbank existiert, und sie ggf. erstellen
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . $dbconfig["database"]);
    
    // Verbindung zur spezifischen Datenbank
    $pdo->exec("USE " . $dbconfig["database"]);

    // Tabelle 'users' erstellen
    $createUserTableSQL = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            anrede VARCHAR(1),
            email VARCHAR(255) UNIQUE,
            vorname VARCHAR(255),
            nachname VARCHAR(255),
            passwort VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
    ";
    $pdo->exec($createUserTableSQL);

    // Tabelle 'todo' erstellen
    $createTodoTableSQL = "
        CREATE TABLE IF NOT EXISTS todo (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            erledigt BOOLEAN DEFAULT FALSE,
            beschreibung VARCHAR(255),
            priority INT,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ";
    $pdo->exec($createTodoTableSQL);

    echo "Datenbank und Tabellen wurden erfolgreich erstellt.";
} catch (PDOException $e) {
    exit("Fehler: " . $e->getMessage());
}
?>
