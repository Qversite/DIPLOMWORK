<?php
include('../Bd/pdo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $homeworkDate = $_POST['homework_date'];
    $classId = $_POST['class_id'];
    $subject = $_POST['subject'];
    $homework = $_POST['homework'];

    $sql = "INSERT INTO homework (date, class_id, subject, homework) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$homeworkDate, $classId, $subject, $homework]);

    echo "Домашнее задание успешно добавлено!";
    // Редирект обратно на страницу
    header('Location: write.php');
    exit();
}
?>
