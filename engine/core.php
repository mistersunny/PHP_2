<?php
// первоначальный запуск (сессия, константы, окружение)
define('ROOT', dirname(__DIR__));//определяем константу
// включаем показ ошибок
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/**
 * Для более понятного вывода данных
 */
function vardump($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
  }

require(ROOT . '/config/main.php');// подключаем конфиг

/**
 * Подключаем все файлы из папки engine, кроме core.php и кроме ранее подключенных
 */
$filelist = glob(ROOT.'\engine\\'.'*.php');//массив с путями всех файлов  этой папки
foreach ($filelist as $file) {//для каждого значения массива
    if ($file != __FILE__ && !in_array($file, get_included_files())) {//проверяем если это не этот же файл и не был ранее подключен, то
        require($file);//подключаем его
    }
}

// запускаем сессию
session_start();
