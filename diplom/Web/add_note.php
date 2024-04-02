<?php
session_start();
include('../Bd/pdo.php'); // Убедитесь, что подключение к БД настроено правильно

if(isset($_POST['note'])) {
    $note = $_POST['note'];
    $user_id = $_SESSION['user_id']; // Убедитесь, что user_id сохранён в сессии при авторизации

    // Здесь ваш код для вставки заметки в БД
    $stmt = $pdo->prepare("INSERT INTO notes (user_id, note) VALUES (?, ?)");
    $stmt->execute([$user_id, $note]);

    header('Location: note.php'); // Возвращаем пользователя обратно на главную страницу или к списку его заметок
    exit;
}
?>
