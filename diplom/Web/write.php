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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    // Получаем данные из формы
    $dayOfWeek = $_POST['day_of_week'];
    $timeSlot = $_POST['time_slot'];
    $subject = $_POST['subject'];
    $classId = $_POST['class_id'];
    $homework = $_POST['homework']; // Добавлено

    // SQL запрос на добавление данных
    $sql = "INSERT INTO schedule (day_of_week, time_slot, subject, class_id, homework) VALUES (?, ?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$dayOfWeek, $timeSlot, $subject, $classId, $homework]);

    // Перенаправление обратно на страницу администратора или вывод сообщения
    echo "Расписание успешно добавлено!";
    header('Location: write.php');
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
    <style>
        /* Добавляем стили для формы */
        .form-schedule {
            margin: 20px auto;
            width: 300px; /* Или другая ширина по желанию */
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .form-schedule input,
        .form-schedule select,
        .form-schedule button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-schedule button {
            background-color: #5cb85c;
            color: white;
            cursor: pointer;
        }

        .form-schedule button:hover {
            background-color: #449d44;
        }
        .main_podval{
    display: flex;
    align-items: center;
    color: white;
    font-size: 24px;
    margin: 265px 48px;
    fill: white;
    width: 50px;
    padding: 5 10px;
    }

    </style>
    <title>Рассписание</title>
</head>
<body>
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
            <span>Рассписание</span>
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
            <h2 align=center>Рассписание</h2>

            <?php
            if(isset($_GET['class_id']) && is_numeric($_GET['class_id'])) {
                $classId = $_GET['class_id'];
                $query = $pdo->prepare("SELECT s.*, s.homework FROM schedule s WHERE s.class_id = ? ORDER BY s.day_of_week, s.time_slot");
                $query->execute([$classId]);
            } else {
                $query = $pdo->query("SELECT s.*, s.homework FROM schedule s ORDER BY s.day_of_week, s.time_slot");
            }

            $schedule = $query->fetchAll(PDO::FETCH_ASSOC);

            echo '<table border="1">';
            echo '<tr><th>День недели</th><th>Время</th><th>Предмет</th><th>Класс</th><th>Домашнее задание</th><th>Удалить</th></tr>';
            foreach ($schedule as $row) {
                echo "<tr>";
                echo "<td>" . $row['day_of_week'] . "</td>";
                echo "<td>" . $row['time_slot'] . "</td>";
                echo "<td>" . $row['subject'] . "</td>";
                echo "<td>" . $row['class_id'] . "</td>";
                echo "<td>" . ($row['homework'] ?? '-') . "</td>";
                echo "<td><a href=\"delete_schedule.php?id=" . $row['id'] . "\" onclick=\"return confirm('Удалить расписание?');\">Удалить</a></td>";
                echo "</tr>";
            }
            echo '</table>';
            ?>

            <form method="get" action="write.php" class="form-schedule">
                Выберите класс для просмотра расписания:
                <select name="class_id">
                    <?php for($i = 1; $i <= 11; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit">Показать расписание</button>
            </form>

            <?php if ($_SESSION['role'] == 'admin'): ?>
    <div style="margin: auto; width: 50%; padding: 20px; background-color: #dddddd; border-radius: 10px;">
        <h2 align=center>Добавить расписание</h2>
        <form method="post" action="write.php">
            <div style="margin-bottom: 10px;">
                День недели: <input type="text" name="day_of_week" required style="margin-left: 20px;">
            </div>
            <div style="margin-bottom: 10px;">
                Время: <input type="time" name="time_slot" required style="margin-left: 67px;">
            </div>
            <div style="margin-bottom: 10px;">
                Предмет: <input type="text" name="subject" required style="margin-left: 50px;">
            </div>
            <div style="margin-bottom: 10px;">
                Класс: <select name="class_id" style="margin-left: 67px;">
                    <?php for ($i = 1; $i <= 11; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div style="margin-bottom: 10px;">
                Домашнее задание: <textarea name="homework"></textarea>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="add_schedule">Добавить расписание</button>
            </div>
        </form>
    </div>
<?php endif; ?>


        </div>
    </div>
</div>
</body>
</html>
