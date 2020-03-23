<?php
$title = 'Второе домашнее задание';
$year = date('Y');
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
<?php
/**
 * 1.	Объявить две целочисленные переменные $a и $b и задать им произвольные 
 * начальные значения. Затем написать скрипт, который работает по следующему принципу:
 * a.	Если $a и $b положительные, вывести их разность.
 * b.	Если $а и $b отрицательные, вывести их произведение.
 * c.	Если $а и $b разных знаков, вывести их сумму.
 * Ноль можно считать положительным числом.
 */
echo 'Задание 1.<br/>';
$a = rand(-10,10);
$b = rand(-10,10);
echo 'a = ' , "$a <br/>" , 'b = ' , "$b <br/>";
if ($a >= 0 && $b >= 0) {
    echo $a - $b;
} elseif ($a < 0 && $b < 0) {
    echo $a * $b;
} else {
    echo $a + $b;
}

/**
 * 2.	Присвоить переменной $а значение в промежутке [0..15]. С помощью оператора
 * switch организовать вывод чисел от $a до 15.
 */
echo '<br/>Задание 2.<br/>';

echo 'a = ',$a = rand(0,15) , '<br/>';
switch ($a) {
    case '0':
        echo "0\n";
    case '1':
        echo "0\n";
    case '2':
        echo "0\n";
    case '3':
        echo "3\n";
    case '4':
        echo "4\n";
    case '5':
        echo "5\n";
    case '6':
        echo "6\n";
    case '7':
        echo "7\n";
    case '8':
        echo "8\n";
    case '9':
        echo "9\n";
    case '10':
        echo "10\n";
    case '11':
        echo "11\n";
    case '12':
        echo "12\n";
    case '13':
        echo "13\n";
    case '14':
        echo "14\n";
    case '15':
        echo "15\n";
// Либо такой вариант
//  case $a;
//     for ($i=$a; $i<=15; $i++) echo $i." ";
// break;
}

/**
 * 3.	Реализовать основные 4 арифметические операции в виде функций с двумя параметрами. Обязательно 
 * использовать оператор return.
 */
echo '<br/>Задание 3.<br/>';
$a = rand(-10,10);
$b = rand(-10,10);
//сложение
function addition(int $a, int $b){
    return $a + $b;
}
//вычитание
function subtraction(int $a, int $b){
    return $a - $b;
}
//умножение
function multiplication(int $a, int $b){
    return $a * $b;
}
//деление
function division(int $a, int $b){
    return $b <> 0 ? $a / $b : 'нельзя делить на ноль';
}
echo"Результат сложения $a и $b = " , addition($a, $b) , '<br/>' . 
    "Результат вычитания $a и $b = " , subtraction($a, $b) , '<br/>' . 
    "Результат умножения $a и $b = " , multiplication($a, $b) , '<br/>' . 
    "Результат деления $a и $b = " , division($a, $b);

/**
 * 4.	Реализовать функцию с тремя параметрами: function mathOperation($arg1, $arg2, $operation),
 * где $arg1, $arg2 – значения аргументов, $operation – строка с названием операции. В зависимости 
 * от переданного значения операции выполнить одну из арифметических операций (использовать функции 
 * из пункта 3) и вернуть полученное значение (использовать switch).
 */
echo '<br/>Задание 4.<br/>';

$operations = ['+', '-', '*', '/'];
$arg1 = rand(10, -10);
$arg2 = rand(10, -10);
$operation = $operations[array_rand($operations)];
echo $arg1 , $operation , $arg2<0?"($arg2)":$arg2 , '=';
function mathOperation($arg1, $arg2, $operation){
    switch ($operation) {
        case '+':
            return addition($arg1, $arg2);
            break;
        case '-':
                return subtraction($arg1, $arg2);
            break;
        case '*':
            return multiplication($arg1, $arg2);
            break;
        case '/':
            return division($arg1, $arg2);
            break;
    }
}

echo mathOperation($arg1,$arg2,$operation);
?>
    
</body>
    <footer>Задание 5.<br>А на дворе уже <?= $year?>-й год.</footer>
</html>

<?php
/**
 * 6.	*С помощью рекурсии организовать функцию возведения числа в степень. 
 * Формат: function power($val, $pow), где $val – заданное число, $pow – степень.
 */
echo 'Задание 6.<br/>';

$val = rand(-5, 5);
$pow = rand(-5, 5);
echo $val , '^' , $pow , '=';
function power(int $val, int $pow){
    if ($pow < 0 && $val == 0) {
        return "ноль нельзя возвести в отрицательную степень";
    }
    if ($pow < 0 && $val <> 0) {
        return power(1/$val, -$pow);
    }
    if ($pow == 0) {
        return 1;
    }
    if ($pow == 1) {
        return $val;
    }
    return $val * power($val, $pow-1);
}
echo power($val, $pow);

/**
 * 7.	*Написать функцию, которая вычисляет текущее время и 
 * возвращает его в формате с правильными склонениями, например: 
 * 22 часа 15 минут, 21 час 43 минуты.
 */
echo '<br/>Задание 7.<br/>';
function rus_time(){
    date_default_timezone_set('Europe/Moscow');
    $h = date("G");
    $m = date("i");
    switch ($h) {
        case '1||21':
            $h = $h . ' час';
            break;
        case '2||3||4||22||23':
            $h = $h . ' часа';
            break;
        default:
            $h = $h . ' часов';
            break;
    }
    switch ($m){
        case '1||21||31||41||51':
            $m = $m . ' минута';
            break;
        case '2||3||4||22||23||24||32||33||34||42||43||44||52||53||54':
            $m = $m . ' минуты';
            break;
        default:
            $m = $m . ' минут';
            break;
    }
    echo "Московское время $h $m";
}
rus_time();
?>