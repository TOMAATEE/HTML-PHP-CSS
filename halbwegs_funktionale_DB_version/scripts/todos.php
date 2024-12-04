<?php
header('Content-Type: application/json');
session_start();

require_once("../inc/dbconfig.php");

$mysqli = new mysqli($dbconfig["host"], $dbconfig["user"], $dbconfig["password"], $dbconfig["database"]);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Datenbankverbindung fehlgeschlagen: " . $mysqli->connect_error]);
    exit;
}
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));
$requestedId = end($uriParts); // Letzter Teil der URL ist die ID

if (!is_numeric($requestedId)) {
    $requestedId = null; // Keine ID vorhanden
}

// API-Logik
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET': // To-Dos abrufen
        $result = $mysqli->query("SELECT * FROM todo");
        $todos = [];
        while ($row = $result->fetch_assoc()) {
            $todos[] = $row;
        }
        echo json_encode($todos);
        break;

    case 'POST': // Neues To-Do hinzufügen
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['beschreibung'])) {
            http_response_code(400);
            echo json_encode(["error" => "Beschreibung fehlt"]);
            exit;
        }
        $beschreibung = $mysqli->real_escape_string($input['beschreibung']);
        $priority = $mysqli->real_escape_string($input['priority'] ?? 1);
        $mysqli->query("INSERT INTO todo (beschreibung, erledigt, priority) VALUES ('$beschreibung', 0, $priority)");
        echo json_encode(["id" => $mysqli->insert_id, "beschreibung" => $beschreibung, "erledigt" => 0, "priority" => $priority]);
        break;

    case 'PUT': // To-Do aktualisieren
        if (!$requestedId) {
            http_response_code(400);
            echo json_encode(["error" => "ID fehlt"]);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $fields = [];
        if (isset($input['beschreibung'])) {
            $beschreibung = $mysqli->real_escape_string($input['beschreibung']);
            $fields[] = "beschreibung='$beschreibung'";
        }
        if (isset($input['erledigt'])) {
            $erledigt = $input['erledigt'] ? 1 : 0;
            $fields[] = "erledigt=$erledigt";
        }
        if ($fields) {
            $mysqli->query("UPDATE todo SET " . implode(', ', $fields) . " WHERE id=$requestedId");
            echo json_encode(["id" => $requestedId, "message" => "Erfolgreich aktualisiert"]);
        }
        break;

    case 'DELETE': // To-Do löschen
        if (!$requestedId) {
            http_response_code(400);
            echo json_encode(["error" => "ID fehlt"]);
            exit;
        }
        $mysqli->query("DELETE FROM todo WHERE id=$requestedId");
        echo json_encode(["id" => $requestedId, "message" => "Erfolgreich gelöscht"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Methode nicht erlaubt"]);
        break;
}

$mysqli->close();