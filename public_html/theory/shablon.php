<?php
$topic = 'А ты знаешь какой сейчас год?';
$title = 'Первое домашнее задание';
$year = date('Y');
$a = rand(0,10);
$b = rand(0,10);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?></title>
</head>
<body>
    <h1><?= $topic ?></h1>
    <p>Пздравляю тебя, мой друг, сейчас на дворе <?= $year ?>-й год.</p>
    А сейчас мы будем менять значение переменных местами.<br>
    У нас есть переменные a = <?= $a ?> и b = <?= $b ?>.<br>
    <?php
    $a+=$b;
    $b=$a-$b;
    $a-=$b;
    ?>
    А теперь a = <?= $a ?> и b = <?= $b ?>.
</body>
<footer>
    Усли ты забыл какой сейчас год, то напоминаю <?=$year?>-й.
</footer>
</html>