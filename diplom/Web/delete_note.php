<?php
session_start();
if(!isset($_SESSION['user_id'])){
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    include('../Bd/pdo.php');

    $note_id = $_GET['id'] ?? null;
    if (!$note_id) {
        http_response_code(400);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        http_response_code(204); // No content
        exit;
    }
}

http_response_code(403);
exit;
?>
