<?php
/**
 * функция по подключению к БД
 */
$connection = mysqli_connect(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['password'],
    $config['db']['database']
) or die ('Не удалось подключиться к базе данных');//если подключение не удалось выводит сообщение

/**
 * Простое выполнение SQL запроса к базе данных
 * @param string $sql
 * @return bool|mysqli_result
 */
function execute(string $sql) {
    global $connection;
    $result = mysqli_query($connection, $sql) or print('Не удалось выполнить запрос: '.$sql);//помещаем в переменную результат запроса или сообщение о неудаче
    // var_dump($result);
    return $result;//возвращаем результат запроса
}

/**
 * Получение строки или строк из базы данных по SQL запросу
 * @param string $sql
 * @return array
 */
function getItemArray(string $sql, string $param="", string $param2=""){
    $result = execute($sql);//получаем результат запроса
    $number_rows = mysqli_num_rows($result);//получаемых количество полученных строк
    $rows = [];// объявляем пустой массив
    if (!empty($param)) {// проверяем получили мы параметр при вызове функции, еси да то
        while ($row = mysqli_fetch_assoc($result)) {//запускаем цикл по каждой полученной строке
            $rows[] = $row[$param];// помещаем в массив все значения с указанным параметром
        }
    }
    if (!empty($param2)) {// проверяем получили мы параметр при вызове функции, еси да то
        while ($row = mysqli_fetch_assoc($result)) {//запускаем цикл по каждой полученной строке
            $rows[$row[$param2]] = $row;// в массиве rows содаем массив ключом которого является значение ключа param2 
        }
    }
    if ($number_rows>1) {// если количество полученных строк >1 то
        while ($row = mysqli_fetch_assoc($result)) {//запускаем цикл по каждой полученной строке
            $rows[] = $row;// помещаем в массив массивы по каждой строке
        }
    }
    if ($number_rows == 1) {// если количество полученных строк =1 то
        while ($row = mysqli_fetch_assoc($result)) {
            $rows = $row;// помещаем в массив значения этой строки
        }
    }
    // var_dump($rows);
    return $rows;// возвращаем сформированный массив
    mysqli_free_result($result);// освобождаем память
}


/**
 * Выполнение нескольких простых запросов
 * @param string $sql
 * @return bool|mysqli_result
 */
function multi_execute(string $sql){
    global $connection;
    $result = mysqli_multi_query($connection, $sql) or print('Не удалось выполнить запросы: '.$sql);//помещаем в переменную результат запроса или сообщение о неудаче
    return $result;//возвращаем результат запроса

}

/**
 * Возврат ID последней операции вставки
 * @return int
 */
function lastInsertedId() {
    global $connection;
    $result = mysqli_insert_id($connection);//помещаем в переменную id последней операции
    return (int)$result;//возвращаем результат запроса
}
?>