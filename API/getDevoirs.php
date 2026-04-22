<?php

require_once '../db.php';


function getDevoirs($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM devoirs ORDER BY date_rendu ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json; charset=utf-8');

try {
    $devoirs = getDevoirs($pdo);

    echo json_encode(
        $devoirs,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur serveur"
    ]);
}