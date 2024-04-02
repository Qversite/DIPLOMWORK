<?php
session_start();
if(!isset($_SESSION['user_id']) || isset($_GET['logout'])){
    $_SESSION['user_id'] = null;
    header('Location: login.php');
    exit;
} else {
    include('../Bd/pdo.php');
    include('../Bd/brain.php');
    $role = checkRoleUser($_SESSION['user_id']);
    $_SESSION['role'] = $role;
}
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/Style.css">
    <title>Заметки</title>
</head>
<body>
<style>
    .main_podval {
        display: flex;
        align-items: center;
        color: white;
        font-size: 24px;
        margin: 265px 48px;
        fill: white;
        width: 50px;
        padding: 5 10px;
    }

    .notes-list {
        list-style: none;
        padding: 0;
    }

    .notes-list li {
        background-color: #f0f0f0;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .note-form {
        display: flex;
        flex-direction: column;
        width: 50%;
        margin: auto;
    }

    .note-form textarea {
        margin-bottom: 10px;
        padding: 10px;
        resize: vertical;
        height: 100px;
    }

    .note-form input[type="submit"] {
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .note-full-view {
        background-color: #f8f9fa;
        padding: 20px;
        margin-top: 20px;
        border-radius: 5px;
    }

    .delete-button {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
    }

    .delete-button:hover {
        background-color: #c82333;
    }
</style>
<div class="main-design container">
    <div class="left-part">
        <a href="Main.php"><div class="logo"><img src="../img/Group.svg" alt=""></div></a>
        <div class="main_menu">
            <a href="Main.php"><div><img src="../img/Главная.svg" alt="">Главная</div></a>
            <a href="Group.php"><div><img src="../img/Группы.svg" alt="">Классы</div></a>
            <a href="Tables.php"><div><img src="../img/Журнал.svg" alt="">Дневник</div></a>
            <a href="write.php"><div><img src="../img/write_day.svg" alt="">Рассписание</div></a>
            <a href="note.php"><div><img src="../img/personal.svg" alt="">Записи</div></a>
            <a href="Profile.php"><div><img src="../img/Настройки.svg" alt="">Профиль</div></a>
        </div>
        <a href="?logout=1" class="logout"><div class="main_podval"><img src="../img/Выйти.svg" alt="">Выйти</div></a>
    </div>
    <div class="right-part">
        <div class="part_header">
            <span>Заметки</span>
            <?php
            $info = getUserInfoById($_SESSION['user_id']);
            ?>
            <div class="logUserInformation">
                <img src="../img/user_img.png" alt="" width="40">
                <label><?php echo $info['lastname'].'. '.mb_substr($info['name'], 0, 1).'. '.mb_substr($info['surname'], 0, 1)?> </label>
            </div>
        </div>
        <div class="part_bottom">
            <div class="bottom_div">

            </div>
            <h2 align=center>Личные заметки</h2>

            <?php
            $user_id = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT id, note, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<ul class='notes-list'>";
            foreach ($notes as $note) {
                echo "<li>";
                echo "<strong>" . htmlspecialchars($note['note']) . "</strong>";
                echo "<button class='delete-button' onclick='deleteNote({$note['id']})'>Удалить</button>";
                echo "</li>";
            }
            echo "</ul>";
            ?>

            <form id="add-note-form" class="note-form" method="post" action="add_note.php">
                <textarea name="note" required></textarea>
                <input type="submit" value="Добавить заметку">
            </form>

            <script>
                function deleteNote(id) {
                    if (confirm("Вы уверены, что хотите удалить эту заметку?")) {
                        fetch(`delete_note.php?id=${id}`, {
                            method: 'DELETE'
                        })
                        .then(response => {
                            if (response.ok) {
                                location.reload();
                            }
                        });
                    }
                }
            </script>
        </div>
    </div>
</div>
</body>
</html>
